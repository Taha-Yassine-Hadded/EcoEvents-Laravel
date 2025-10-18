

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import joblib
import logging
from typing import Dict
import nltk
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
from nltk.stem import SnowballStemmer
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
import os

# Configuration du logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Téléchargement des ressources NLTK
nltk.download('punkt')
nltk.download('stopwords')

app = FastAPI()

# Liste des catégories valides
VALID_CATEGORIES = [
    'recyclage', 'climat', 'biodiversite', 'eau', 'energie',
    'transport', 'alimentation', 'pollution', 'sensibilisation'
]

# Prétraitement du texte
stemmer = SnowballStemmer('french')
stop_words = set(stopwords.words('french'))

def preprocess_text(text: str) -> str:
    tokens = word_tokenize(text.lower())
    tokens = [stemmer.stem(token) for token in tokens if token not in stop_words]
    return ' '.join(tokens)

# Créer le dossier models/ s'il n'existe pas
models_dir = 'models'
if not os.path.exists(models_dir):
    os.makedirs(models_dir)
    logger.info(f"✅ Dossier {models_dir} créé avec succès")

# Charger et filtrer les données
try:
    df = pd.read_csv('data/category.csv', sep=';')
    # Filtrer les catégories valides
    df = df[df['category'].isin(VALID_CATEGORIES)]
    if df.empty:
        raise ValueError("Aucune catégorie valide trouvée dans category.csv")
    logger.info(f"✅ Données chargées avec succès : {len(df)} catégories valides")
except Exception as e:
    logger.error(f"Erreur lors du chargement des données : {str(e)}")
    raise Exception(f"Erreur lors du chargement des données : {str(e)}")

# Préparer les données pour l'entraînement
X = df['keywords'].apply(preprocess_text)
y = df['category']

# Vérifier si les données sont suffisantes
if len(X) < 1:
    logger.error("Données insuffisantes pour l'entraînement : minimum 1 échantillon requis")
    raise ValueError("Données insuffisantes pour l'entraînement")

# Vectorisation et entraînement du modèle
vectorizer = TfidfVectorizer(max_features=1000)
X_vectorized = vectorizer.fit_transform(X)

model = LogisticRegression(multi_class='multinomial', solver='lbfgs', max_iter=1000)
model.fit(X_vectorized, y)
logger.info("✅ Modèle entraîné avec succès")

# Sauvegarder le modèle et le vectoriseur
try:
    joblib.dump(vectorizer, os.path.join(models_dir, 'tfidf_vectorizer.pkl'))
    joblib.dump(model, os.path.join(models_dir, 'classification_model.pkl'))
    logger.info("✅ Modèle et vectoriseur sauvegardés avec succès")
except Exception as e:
    logger.error(f"Erreur lors de la sauvegarde du modèle : {str(e)}")
    raise Exception(f"Erreur lors de la sauvegarde du modèle : {str(e)}")

class TextInput(BaseModel):
    text: str

@app.post("/predict-category")
async def predict_category(input: TextInput) -> Dict:
    try:
        logger.info(f"📥 Texte reçu : {input.text[:50]}...")
        processed_text = preprocess_text(input.text)
        if len(processed_text.split()) < 10:
            raise HTTPException(status_code=400, detail="Texte trop court pour une prédiction fiable")
        text_vector = vectorizer.transform([processed_text])
        probabilities = model.predict_proba(text_vector)[0]
        predicted_category = model.classes_[probabilities.argmax()]

        # Vérifier si la catégorie prédite est valide
        if predicted_category not in VALID_CATEGORIES:
            logger.warning(f"Catégorie prédite non valide : {predicted_category}. Retour à la catégorie la plus probable parmi les valides.")
            valid_indices = [i for i, cat in enumerate(model.classes_) if cat in VALID_CATEGORIES]
            if not valid_indices:
                raise HTTPException(status_code=500, detail="Aucune catégorie valide trouvée dans le modèle")
            valid_probabilities = probabilities[valid_indices]
            valid_category_index = valid_indices[valid_probabilities.argmax()]
            predicted_category = model.classes_[valid_category_index]

        confidence = float(probabilities.max())
        probabilities_dict = {model.classes_[i]: float(prob) for i, prob in enumerate(probabilities)}

        response = {
            "success": True,
            "data": {
                "category": predicted_category,
                "confidence": confidence,
                "all_probabilities": probabilities_dict
            }
        }
        return response
    except Exception as e:
        logger.error(f"Erreur lors de la prédiction : {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))






if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=6000, reload=True)
