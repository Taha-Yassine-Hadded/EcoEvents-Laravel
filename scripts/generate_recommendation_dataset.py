#!/usr/bin/env python3
"""
Script pour générer un dataset d'entraînement pour le modèle de recommandation de communautés
Dataset de 10,000 lignes avec des interactions utilisateur-communauté
"""

import pandas as pd
import numpy as np
import random
from datetime import datetime, timedelta
import json

# Configuration
np.random.seed(42)
random.seed(42)

# Paramètres du dataset
NUM_USERS = 2000
NUM_COMMUNITIES = 500
NUM_INTERACTIONS = 10000

# Centres d'intérêt écologiques
ECO_INTERESTS = [
    'recyclage', 'compostage', 'énergie renouvelable', 'biodiversité', 'climat',
    'pollution', 'eau', 'forêt', 'agriculture biologique', 'transport durable',
    'zéro déchet', 'économie circulaire', 'énergies vertes', 'protection animale',
    'permaculture', 'énergies solaires', 'éolien', 'géothermie', 'hydroélectricité',
    'vélo', 'transport public', 'covoiturage', 'alimentation locale', 'circuits courts',
    'consommation responsable', 'upcycling', 'réparation', 'partage', 'location',
    'énergies propres', 'efficacité énergétique', 'isolation', 'rénovation thermique'
]

# Catégories de communautés
COMMUNITY_CATEGORIES = [
    'Énergies Renouvelables', 'Recyclage & Zéro Déchet', 'Biodiversité & Nature',
    'Transport Durable', 'Agriculture Écologique', 'Climat & Environnement',
    'Consommation Responsable', 'Éducation Environnementale', 'Innovation Verte',
    'Protection Animale', 'Urbanisme Durable', 'Économie Circulaire'
]

# Localisations
LOCATIONS = [
    'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg',
    'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims', 'Toulon', 'Grenoble',
    'Dijon', 'Angers', 'Nîmes', 'Villeurbanne', 'Saint-Étienne', 'Le Havre'
]

def generate_user_profiles():
    """Génère des profils utilisateurs avec centres d'intérêt"""
    users = []

    for i in range(NUM_USERS):
        # Sélectionner 3-8 centres d'intérêt aléatoires
        num_interests = random.randint(3, 8)
        interests = random.sample(ECO_INTERESTS, num_interests)

        # Ajouter des poids d'intérêt (1-5)
        interest_weights = {interest: random.randint(1, 5) for interest in interests}

        user = {
            'user_id': i + 1,
            'age': random.randint(18, 65),
            'location': random.choice(LOCATIONS),
            'interests': interests,
            'interest_weights': interest_weights,
            'activity_level': random.choice(['low', 'medium', 'high']),
            'join_date': datetime.now() - timedelta(days=random.randint(1, 365))
        }
        users.append(user)

    return users

def generate_communities():
    """Génère des communautés avec mots-clés et caractéristiques"""
    communities = []

    for i in range(NUM_COMMUNITIES):
        category = random.choice(COMMUNITY_CATEGORIES)

        # Générer des mots-clés basés sur la catégorie
        if 'Énergies' in category:
            keywords = random.sample(['énergie', 'solaire', 'éolien', 'renouvelable', 'vert'], 3)
        elif 'Recyclage' in category:
            keywords = random.sample(['recyclage', 'zéro déchet', 'compostage', 'réduction'], 3)
        elif 'Biodiversité' in category:
            keywords = random.sample(['biodiversité', 'nature', 'protection', 'faune', 'flore'], 3)
        elif 'Transport' in category:
            keywords = random.sample(['transport', 'vélo', 'durable', 'mobilité', 'électrique'], 3)
        else:
            keywords = random.sample(ECO_INTERESTS, 3)

        community = {
            'community_id': i + 1,
            'name': f"Communauté {category} {i+1}",
            'category': category,
            'keywords': keywords,
            'location': random.choice(LOCATIONS),
            'member_count': random.randint(10, 1000),
            'activity_score': random.uniform(0.1, 1.0),
            'description': f"Communauté dédiée à {category.lower()}",
            'created_date': datetime.now() - timedelta(days=random.randint(1, 730))
        }
        communities.append(community)

    return communities

def calculate_similarity(user_interests, community_keywords):
    """Calcule la similarité entre intérêts utilisateur et mots-clés communauté"""
    # Jaccard similarity
    user_set = set(user_interests)
    community_set = set(community_keywords)

    intersection = len(user_set.intersection(community_set))
    union = len(user_set.union(community_set))

    if union == 0:
        return 0.0

    return intersection / union

def generate_interactions(users, communities):
    """Génère des interactions utilisateur-communauté basées sur la similarité"""
    interactions = []

    for _ in range(NUM_INTERACTIONS):
        user = random.choice(users)
        community = random.choice(communities)

        # Calculer la similarité
        similarity = calculate_similarity(user['interests'], community['keywords'])

        # Probabilité d'interaction basée sur la similarité
        interaction_prob = similarity * 0.8 + random.uniform(0, 0.2)

        if random.random() < interaction_prob:
            # Types d'interactions
            interaction_types = ['join', 'like', 'comment', 'share', 'post']
            interaction_type = random.choices(
                interaction_types,
                weights=[0.3, 0.25, 0.2, 0.15, 0.1]
            )[0]

            # Score d'engagement (1-5)
            engagement_score = max(1, int(similarity * 5) + random.randint(-1, 1))

            interaction = {
                'user_id': user['user_id'],
                'community_id': community['community_id'],
                'interaction_type': interaction_type,
                'engagement_score': engagement_score,
                'similarity_score': similarity,
                'timestamp': datetime.now() - timedelta(days=random.randint(1, 365)),
                'user_age': user['age'],
                'user_location': user['location'],
                'user_activity_level': user['activity_level'],
                'community_category': community['category'],
                'community_member_count': community['member_count'],
                'community_activity_score': community['activity_score']
            }
            interactions.append(interaction)

    return interactions

def generate_dataset():
    """Génère le dataset complet"""
    print("Génération des profils utilisateurs...")
    users = generate_user_profiles()

    print("Génération des communautés...")
    communities = generate_communities()

    print("Génération des interactions...")
    interactions = generate_interactions(users, communities)

    # Créer le DataFrame principal
    df = pd.DataFrame(interactions)

    # Ajouter des features dérivées
    df['days_since_interaction'] = (datetime.now() - df['timestamp']).dt.days
    df['user_community_match'] = df['similarity_score'] * df['engagement_score']

    # Features catégorielles encodées
    df['activity_level_encoded'] = df['user_activity_level'].map({'low': 1, 'medium': 2, 'high': 3})

    print(f"Dataset généré: {len(df)} interactions")
    print(f"Utilisateurs uniques: {df['user_id'].nunique()}")
    print(f"Communautés uniques: {df['community_id'].nunique()}")

    return df, users, communities

def save_dataset(df, users, communities):
    """Sauvegarde le dataset"""
    # Sauvegarder le dataset principal
    df.to_csv('community_recommendation_dataset.csv', index=False)

    # Sauvegarder les métadonnées
    with open('users_metadata.json', 'w', encoding='utf-8') as f:
        json.dump(users, f, indent=2, default=str)

    with open('communities_metadata.json', 'w', encoding='utf-8') as f:
        json.dump(communities, f, indent=2, default=str)

    print("Dataset sauvegardé:")
    print("- community_recommendation_dataset.csv")
    print("- users_metadata.json")
    print("- communities_metadata.json")

def analyze_dataset(df):
    """Analyse le dataset généré"""
    print("\n=== ANALYSE DU DATASET ===")
    print(f"Nombre total d'interactions: {len(df)}")
    print(f"Utilisateurs uniques: {df['user_id'].nunique()}")
    print(f"Communautés uniques: {df['community_id'].nunique()}")

    print("\nDistribution des types d'interactions:")
    print(df['interaction_type'].value_counts())

    print("\nDistribution des scores d'engagement:")
    print(df['engagement_score'].value_counts().sort_index())

    print("\nDistribution des scores de similarité:")
    print(f"Moyenne: {df['similarity_score'].mean():.3f}")
    print(f"Médiane: {df['similarity_score'].median():.3f}")
    print(f"Écart-type: {df['similarity_score'].std():.3f}")

    print("\nTop 10 communautés les plus populaires:")
    top_communities = df['community_id'].value_counts().head(10)
    print(top_communities)

if __name__ == "__main__":
    print("=== GÉNÉRATION DU DATASET DE RECOMMANDATION ===")
    print(f"Génération de {NUM_INTERACTIONS} interactions...")

    # Générer le dataset
    df, users, communities = generate_dataset()

    # Analyser le dataset
    analyze_dataset(df)

    # Sauvegarder
    save_dataset(df, users, communities)

    print("\n=== DATASET GÉNÉRÉ AVEC SUCCÈS ===")
    print("Le dataset est prêt pour l'entraînement du modèle de recommandation!")
