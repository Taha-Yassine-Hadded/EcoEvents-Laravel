import os
import re
import json
import logging
import time
from typing import Dict, Any, List, Tuple
from fastapi import FastAPI
from pydantic import BaseModel
from collections import defaultdict, Counter
import nltk
from nltk.tokenize import word_tokenize
from nltk.corpus import stopwords
from unidecode import unidecode

# Téléchargement des ressources NLTK
nltk.download('punkt')
nltk.download('stopwords')

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = FastAPI(title="EMOLEX + NRC + Multilingual Enhanced")

LEXICONS_PATH = "./data/lexicons"
ONEFILE_PATH = "./data/OneFilePerEmotion"
EMOJIS_PATH = "./data/emojis.json"

class LexiconAnalyzer:
    def __init__(self):
        self.emolex_words = defaultdict(lambda: defaultdict(list))
        self.nrc_emotions = {}
        self.tunisian_words = {}
        self.emoji_emotions = {}

        # Keywords étendus pour le français
        self.french_keywords = set([
            'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles',
            'le', 'la', 'les', 'de', "d'", 'à', 'au', 'aux', 'et', 'ou',
            'mais', 'donc', 'or', 'car', 'triste', 'génial', 'joyeux',
            'heureux', 'heureuse', 'content', 'contente', 'belle', 'beau',
            'belles', 'beaux', 'joli', 'jolie', "j'adore", 'super', 'formidable',
            'merveilleux', 'fantastique', 'tristesse', 'colère', 'peur', 'joie',
            'êtes', 'es', 'est', 'suis', 'superbe', 'magnifique', 'ravissant',
            'chouette', 'déçu', 'frustré', 'énervé', 'anxieux', 'anxiété'
        ])
        self.tunisian_keywords = set([
            'ana', 'barcha', 'farhèn', 'nebki', 'mridha', 'khayef', 'khouya',
            'zwin', 'yallah', 'kif', 'zwina', 'fer7an', 'mouch', 'chokran'
        ])
        self.english_keywords = set([
            'i', 'you', 'he', 'she', 'it', 'we', 'they', 'the', 'a', 'an',
            'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with',
            'happy', 'sad', 'angry', 'fear', 'joy', 'love', 'awesome', 'great',
            'idiot', 'lying', 'horrible', 'worry', 'threaten'
        ])

        self.emotions_list = ['anger', 'anticipation', 'disgust', 'fear', 'joy',
                             'negative', 'positive', 'sadness', 'surprise', 'trust']
        self.stop_words = {
            'french': set(stopwords.words('french')),
            'english': set(stopwords.words('english'))
        }
        self.load_lexicons()
        self.load_emoji_emotions()

    def load_emoji_emotions(self):
        """Charge les émotions des emojis à partir du fichier emojis.json"""
        try:
            with open(EMOJIS_PATH, 'r', encoding='utf-8') as f:
                emoji_data = json.load(f)
                for item in emoji_data:
                    emoji = item['emoji']
                    emotions = item['emotions']
                    self.emoji_emotions[emoji] = {emo: score for emo, score in emotions.items() if score > 0}
                logger.info(f"✅ Emojis chargés: {len(self.emoji_emotions)} emojis")
        except Exception as e:
            logger.error(f"Erreur lors du chargement des emojis: {e}")

    def load_lexicons(self):
        self.load_emolex_arabic()
        self.load_emolex_french()
        self.load_emolex_tunisian()
        self.load_nrc_emotions()
        logger.info(f"✅ Tunisian: {len(self.tunisian_words)}, NRC: {len(self.nrc_emotions)}")

    def load_emolex_tunisian(self):
        filepath = os.path.join(LEXICONS_PATH, 'tunisian_emolex.txt')
        if os.path.exists(filepath):
            try:
                with open(filepath, 'r', encoding='utf-8') as f:
                    next(f)  # Ignorer l'en-tête
                    for line in f:
                        parts = [p.strip() for p in line.split('\t')]
                        if len(parts) >= 12:
                            tunisian_word = parts[-1].lower()
                            tunisian_word_no_accent = unidecode(tunisian_word)
                            emotion_scores = [int(parts[i]) for i in range(1, 11)]
                            word_emotions = []
                            for i, emotion in enumerate(self.emotions_list):
                                if emotion_scores[i] == 1:
                                    word_emotions.append(emotion)
                                    self.emolex_words[emotion]['tunisian'].append(tunisian_word)
                                    self.emolex_words[emotion]['tunisian'].append(tunisian_word_no_accent)
                            if word_emotions:
                                self.tunisian_words[tunisian_word] = word_emotions
                                self.tunisian_words[tunisian_word_no_accent] = word_emotions
                logger.info(f"✅ Tunisian: {len(self.tunisian_words)} mots")
            except Exception as e:
                logger.error(f"Tunisian error: {e}")

    def load_emolex_french(self):
        filepath = os.path.join(LEXICONS_PATH, 'french_emolex.txt')
        if os.path.exists(filepath):
            self._parse_emolex_file(filepath, 'french')

    def load_emolex_arabic(self):
        filepath = os.path.join(LEXICONS_PATH, 'arabic_emolex.txt')
        if os.path.exists(filepath):
            self._parse_emolex_file(filepath, 'arabic')

    def _parse_emolex_file(self, filepath: str, lang: str):
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                next(f)  # Ignorer l'en-tête
                for line in f:
                    parts = [p.strip() for p in line.split('\t')]
                    if len(parts) >= 12:
                        target_word = parts[-1].lower()
                        target_word_no_accent = unidecode(target_word)
                        emotion_scores = [int(parts[i]) for i in range(1, 11)]
                        for i, emotion in enumerate(self.emotions_list):
                            if emotion_scores[i] == 1:
                                self.emolex_words[emotion][lang].append(target_word)
                                self.emolex_words[emotion][lang].append(target_word_no_accent)
            logger.info(f"✅ {lang} EMOLEX")
        except Exception as e:
            logger.error(f"{lang} error: {e}")

    def load_nrc_emotions(self):
        try:
            for filename in os.listdir(ONEFILE_PATH):
                if '-NRC-Emotion-Lexicon.txt' in filename:
                    emotion = filename.replace('-NRC-Emotion-Lexicon.txt', '').lower()
                    filepath = os.path.join(ONEFILE_PATH, filename)
                    words = set()
                    with open(filepath, 'r', encoding='utf-8') as f:
                        for line in f:
                            parts = line.strip().split('\t')
                            if len(parts) == 2 and parts[1] == '1':
                                words.add(parts[0].lower())
                                words.add(unidecode(parts[0].lower()))
                    self.nrc_emotions[emotion] = words
                    logger.info(f"✅ NRC {emotion}: {len(words)} mots")
        except Exception as e:
            logger.error(f"NRC error: {e}")

    def preprocess_text(self, text: str, lang: str) -> Tuple[List[str], List[str]]:
        """Prétraitement avec NLTK pour gérer les accents et les mots composés, et extraction des emojis"""
        text_lower = text.lower()
        # Extraire les emojis avec une regex
        emoji_pattern = re.compile(r'[\U0001F600-\U0001F64F\U0001F300-\U0001F5FF\U0001F680-\U0001F6FF\U0001F1E0-\U0001F1FF]', flags=re.UNICODE)
        emojis = emoji_pattern.findall(text)
        # Supprimer les emojis du texte pour l'analyse lexicale
        text_no_emojis = emoji_pattern.sub('', text_lower)
        tokens = word_tokenize(text_no_emojis, language='french' if lang in ['french', 'tunisian'] else 'english')
        # Supprimer les mots vides et les mots courts
        tokens = [t for t in tokens if t not in self.stop_words.get(lang, set()) and len(t) >= 2]
        # Ajouter une version sans accents pour correspondance
        tokens.extend([unidecode(t) for t in tokens])
        # Ajouter les mots composés comme "j'adore"
        if "j'adore" in text_lower:
            tokens.append("j'adore")
        return list(set(tokens)), emojis

    def detect_language_first(self, text: str) -> str:
        """🌐 Détection de langue améliorée"""
        tokens, _ = self.preprocess_text(text, 'french')  # Utiliser français par défaut pour tokenisation
        french_count = sum(1 for w in tokens if w in self.french_keywords or unidecode(w) in self.french_keywords)
        tunisian_count = sum(1 for w in tokens if w in self.tunisian_keywords)
        english_count = sum(1 for w in tokens if w in self.english_keywords or re.match(r'^[a-z]{3,}$', w))

        # Priorité basée sur les mots détectés
        if tunisian_count > 0:
            return 'tunisian'
        if french_count >= max(english_count * 0.5, 1):  # Avantage accru au français
            return 'french'
        if english_count >= 2:  # Exiger plus de mots anglais pour détecter 'english'
            return 'english'

        # Fallback avec EMOLEX
        emolex_matches = self._quick_emolex_check(tokens)
        if emolex_matches.get('french', 0) >= emolex_matches.get('english', 0):
            return 'french'
        return 'english'

    def _quick_emolex_check(self, words: List[str]) -> Dict:
        """🔍 Vérification rapide EMOLEX avec support des accents"""
        matches = defaultdict(int)
        for word in words:
            word_no_accent = unidecode(word)
            for emotion, langs in self.emolex_words.items():
                for lang, lang_words in langs.items():
                    if word in lang_words or word_no_accent in lang_words:
                        matches[lang] += 1
        return dict(matches)

    def analyze_text_matches(self, text: str, detected_lang: str) -> Dict:
        """🔍 Analyse rigoureuse par langue avec prise en charge des emojis"""
        words, emojis = self.preprocess_text(text, detected_lang)
        lang_matches = defaultdict(lambda: {'count': 0, 'words': set(), 'emotions': Counter()})
        nrc_matches = Counter()
        tunisian_matches = []
        emoji_matches = Counter()

        # 1. TUNISIAN (priorité)
        if detected_lang == 'tunisian':
            for word in words:
                word_no_accent = unidecode(word)
                if word in self.tunisian_words or word_no_accent in self.tunisian_words:
                    target_word = word if word in self.tunisian_words else word_no_accent
                    emotions = self.tunisian_words[target_word]
                    tunisian_matches.append((target_word, emotions))
                    lang_matches['tunisian']['count'] += 1
                    lang_matches['tunisian']['words'].add(target_word)
                    for emo in emotions:
                        lang_matches['tunisian']['emotions'][emo] += 1

        # 2. EMOLEX (français ou arabe)
        target_langs = [detected_lang] if detected_lang in ['french', 'arabic'] else ['french', 'arabic']
        for lang in target_langs:
            for word in words:
                word_no_accent = unidecode(word)
                for emotion, langs in self.emolex_words.items():
                    if lang in langs and (word in langs[lang] or word_no_accent in langs[lang]):
                        lang_matches[lang]['count'] += 1
                        lang_matches[lang]['words'].add(word)
                        lang_matches[lang]['emotions'][emotion] += 1

        # 3. ENGLISH NRC
        if detected_lang == 'english':
            for word in words:
                word_no_accent = unidecode(word)
                for emotion, emotion_words in self.nrc_emotions.items():
                    if word in emotion_words or word_no_accent in emotion_words:
                        nrc_matches[emotion] += 1
                        lang_matches['english']['count'] += 1
                        lang_matches['english']['words'].add(word)
                        lang_matches['english']['emotions'][emotion] += 1

        # 4. EMOJIS
        for emoji in emojis:
            if emoji in self.emoji_emotions:
                emoji_matches.update(self.emoji_emotions[emoji])

        # Sélectionner la meilleure langue
        best_lang = detected_lang if detected_lang in lang_matches else max(
            lang_matches, key=lambda x: lang_matches[x]['count'], default='french'  # Français par défaut
        )
        best_matches = lang_matches[best_lang]
        compound = self._calculate_compound(best_matches['emotions'], nrc_matches, emoji_matches)

        matched_words = list(best_matches['words'])
        if detected_lang == 'english':
            matched_words.extend([w for emo in nrc_matches for w in words if w in self.nrc_emotions.get(emo, set())][:5])
        matched_words.extend(emojis)  # Ajouter les emojis aux mots correspondants

        return {
            'detected_language': detected_lang,
            'best_language': best_lang,
            'lang_matches': dict(lang_matches),
            'best_matches': best_matches,
            'nrc_matches': dict(nrc_matches),
            'tunisian_matches': tunisian_matches,
            'emoji_matches': dict(emoji_matches),
            'compound': compound,
            'total_words': len(words) + len(emojis),
            'matched_words': list(set(matched_words))[:10]
        }

    def _calculate_compound(self, emolex_emotions: Counter, nrc_emotions: Counter, emoji_emotions: Counter) -> float:
        """📊 Calcul du score composé avec prise en charge des emojis"""
        pos_emolex = emolex_emotions.get('positive', 0) + emolex_emotions.get('joy', 0)
        neg_emolex = (emolex_emotions.get('negative', 0) + emolex_emotions.get('anger', 0) +
                      emolex_emotions.get('sadness', 0) + emolex_emotions.get('fear', 0))

        pos_nrc = nrc_emotions.get('positive', 0) + nrc_emotions.get('joy', 0)
        neg_nrc = (nrc_emotions.get('negative', 0) + nrc_emotions.get('anger', 0) +
                   nrc_emotions.get('sadness', 0) + nrc_emotions.get('fear', 0))

        pos_emoji = emoji_emotions.get('positive', 0) + emoji_emotions.get('joy', 0)
        neg_emoji = (emoji_emotions.get('negative', 0) + emoji_emotions.get('anger', 0) +
                     emoji_emotions.get('sadness', 0) + emoji_emotions.get('fear', 0))

        total_pos = pos_emolex + pos_nrc + pos_emoji
        total_neg = neg_emolex + neg_nrc + neg_emoji
        total = total_pos + total_neg
        return (total_pos - total_neg) / max(total, 1) if total > 0 else 0.0

    def get_dominant_emotion(self, analysis: Dict, detected_lang: str) -> str:
        """🎯 Déterminer l'émotion dominante avec prise en charge des emojis"""
        emotions = Counter()

        if detected_lang == 'tunisian' and analysis['tunisian_matches']:
            for _, word_emotions in analysis['tunisian_matches']:
                emotions.update(word_emotions)
        elif detected_lang == 'english' and analysis['nrc_matches']:
            emotions.update(analysis['nrc_matches'])
        emotions.update(analysis['best_matches']['emotions'])
        emotions.update(analysis['emoji_matches'])

        compound = analysis['compound']
        total_matches = sum(emotions.values())

        if total_matches == 0:  # Aucun mot ou emoji émotionnel détecté
            return 'neutral'
        if compound > 0.2:
            return max(['joy', 'positive'], key=lambda x: emotions.get(x, 0), default='joy')
        elif compound < -0.2:
            candidates = ['anger', 'negative', 'sadness', 'fear']
            return max(candidates, key=lambda x: emotions.get(x, 0), default='negative')
        return 'neutral'

# INITIALISER
analyzer = LexiconAnalyzer()

class CommentRequest(BaseModel):
    campaign_id: int
    comment_id: int
    content: str
    user_id: int = 0

@app.post("/analyze-comment")
async def analyze_comment(request: CommentRequest):
    start_time = time.time()
    content = request.content.strip()
    logger.info(f"📥 '{content[:50]}...'")

    try:
        detected_lang = analyzer.detect_language_first(content)
        analysis = analyzer.analyze_text_matches(content, detected_lang)
        dominant_emotion = analyzer.get_dominant_emotion(analysis, detected_lang)
        compound = analysis['compound']

        # Émotions complètes
        emotions = Counter(analysis['best_matches']['emotions'])
        emotions.update(analysis['emoji_matches'])
        if detected_lang == 'english':
            emotions.update(analysis['nrc_matches'])

        for emo in analyzer.emotions_list + ['neutral']:
            emotions[emo] = emotions.get(emo, 0)

        total = sum(emotions.values()) or 1
        emotions_normalized = {k: v/total for k, v in emotions.items()}
        emotions_normalized[dominant_emotion] = min(emotions_normalized[dominant_emotion] + 0.3, 0.95)

        # Confiance ajustée
        matches = (len(analysis['tunisian_matches']) + analysis['best_matches']['count'] +
                   sum(analysis['nrc_matches'].values()) + sum(analysis['emoji_matches'].values()))
        confidence = min(0.95, matches * 0.2 + abs(compound) * 0.3)
        if len(content.split()) <= 3 and matches == 0:
            confidence = 0.0  # Réduire la confiance pour les textes très courts

        result_data = {
            "campaign_id": request.campaign_id,
            "campaign_comment_id": request.comment_id,
            "comment_content": content,
            "detected_language": detected_lang,
            "overall_sentiment_score": round(compound, 4),
            "positive": round(emotions_normalized.get('positive', 0), 4),
            "negative": round(emotions_normalized.get('negative', 0), 4),
            "neutral": round(emotions_normalized.get('neutral', 0), 4),
            "dominant_emotion": dominant_emotion,
            "confidence": round(confidence, 4),
            "joy": round(emotions_normalized['joy'], 4),
            "anger": round(emotions_normalized['anger'], 4),
            "sadness": round(emotions_normalized['sadness'], 4),
            "fear": round(emotions_normalized['fear'], 4),
            "disgust": round(emotions_normalized['disgust'], 4),
            "surprise": round(emotions_normalized['surprise'], 4),
            "trust": round(emotions_normalized['trust'], 4),
            "anticipation": round(emotions_normalized['anticipation'], 4),
            "matched_words": analysis['matched_words'],
            "nrc_matches": sum(analysis['nrc_matches'].values()),
            "tunisian_matches": len(analysis['tunisian_matches']),
            "emoji_matches": sum(analysis['emoji_matches'].values())
        }

        logger.info(f"✅ {detected_lang} - {compound:.3f} ({dominant_emotion}) - Matches:{matches}")
        return {"success": True, "data": result_data}

    except Exception as e:
        logger.error(f"Erreur: {e}")
        return {"success": True, "data": {"error": str(e)}}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=5000, reload=True)
