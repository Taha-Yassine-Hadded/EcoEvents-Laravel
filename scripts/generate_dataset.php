<?php

/**
 * Script pour générer un dataset d'entraînement pour le modèle de recommandation
 * Dataset de 10,000 lignes avec des interactions utilisateur-communauté
 */

require __DIR__ . '/../vendor/autoload.php';

// Configuration
$numUsers = 2000;
$numCommunities = 500;
$numInteractions = 10000;

// Centres d'intérêt écologiques
$ecoInterests = [
    'recyclage', 'compostage', 'énergie renouvelable', 'biodiversité', 'climat',
    'pollution', 'eau', 'forêt', 'agriculture biologique', 'transport durable',
    'zéro déchet', 'économie circulaire', 'énergies vertes', 'protection animale',
    'permaculture', 'énergies solaires', 'éolien', 'géothermie', 'hydroélectricité',
    'vélo', 'transport public', 'covoiturage', 'alimentation locale', 'circuits courts',
    'consommation responsable', 'upcycling', 'réparation', 'partage', 'location',
    'énergies propres', 'efficacité énergétique', 'isolation', 'rénovation thermique'
];

// Catégories de communautés
$communityCategories = [
    'Énergies Renouvelables', 'Recyclage & Zéro Déchet', 'Biodiversité & Nature',
    'Transport Durable', 'Agriculture Écologique', 'Climat & Environnement',
    'Consommation Responsable', 'Éducation Environnementale', 'Innovation Verte',
    'Protection Animale', 'Urbanisme Durable', 'Économie Circulaire'
];

// Localisations
$locations = [
    'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg',
    'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims', 'Toulon', 'Grenoble',
    'Dijon', 'Angers', 'Nîmes', 'Villeurbanne', 'Saint-Étienne', 'Le Havre'
];

function generateUserProfiles($numUsers, $ecoInterests, $locations) {
    $users = [];

    for ($i = 0; $i < $numUsers; $i++) {
        // Sélectionner 3-8 centres d'intérêt aléatoires
        $numInterests = rand(3, 8);
        $interests = array_rand(array_flip($ecoInterests), $numInterests);

        // Ajouter des poids d'intérêt (1-5)
        $interestWeights = [];
        foreach ($interests as $interest) {
            $interestWeights[$interest] = rand(1, 5);
        }

        $users[] = [
            'user_id' => $i + 1,
            'age' => rand(18, 65),
            'location' => $locations[array_rand($locations)],
            'interests' => $interests,
            'interest_weights' => $interestWeights,
            'activity_level' => ['low', 'medium', 'high'][rand(0, 2)],
            'join_date' => date('Y-m-d H:i:s', time() - rand(1, 365) * 24 * 3600)
        ];
    }

    return $users;
}

function generateCommunities($numCommunities, $communityCategories, $ecoInterests, $locations) {
    $communities = [];

    for ($i = 0; $i < $numCommunities; $i++) {
        $category = $communityCategories[array_rand($communityCategories)];

        // Générer des mots-clés basés sur la catégorie
        if (strpos($category, 'Énergies') !== false) {
            $keywords = array_rand(array_flip(['énergie', 'solaire', 'éolien', 'renouvelable', 'vert']), 3);
        } elseif (strpos($category, 'Recyclage') !== false) {
            $keywords = array_rand(array_flip(['recyclage', 'zéro déchet', 'compostage', 'réduction']), 3);
        } elseif (strpos($category, 'Biodiversité') !== false) {
            $keywords = array_rand(array_flip(['biodiversité', 'nature', 'protection', 'faune', 'flore']), 3);
        } elseif (strpos($category, 'Transport') !== false) {
            $keywords = array_rand(array_flip(['transport', 'vélo', 'durable', 'mobilité', 'électrique']), 3);
        } else {
            $keywords = array_rand(array_flip($ecoInterests), 3);
        }

        $communities[] = [
            'community_id' => $i + 1,
            'name' => "Communauté {$category} " . ($i + 1),
            'category' => $category,
            'keywords' => $keywords,
            'location' => $locations[array_rand($locations)],
            'member_count' => rand(10, 1000),
            'activity_score' => round(rand(10, 100) / 100, 2),
            'description' => "Communauté dédiée à " . strtolower($category),
            'created_date' => date('Y-m-d H:i:s', time() - rand(1, 730) * 24 * 3600)
        ];
    }

    return $communities;
}

function calculateSimilarity($userInterests, $communityKeywords) {
    // Jaccard similarity
    $userSet = array_flip($userInterests);
    $communitySet = array_flip($communityKeywords);

    $intersection = count(array_intersect_key($userSet, $communitySet));
    $union = count(array_unique(array_merge($userInterests, $communityKeywords)));

    return $union > 0 ? $intersection / $union : 0.0;
}

function generateInteractions($users, $communities, $numInteractions) {
    $interactions = [];

    for ($i = 0; $i < $numInteractions; $i++) {
        $user = $users[array_rand($users)];
        $community = $communities[array_rand($communities)];

        // Calculer la similarité
        $similarity = calculateSimilarity($user['interests'], $community['keywords']);

        // Probabilité d'interaction basée sur la similarité
        $interactionProb = $similarity * 0.8 + (rand(0, 20) / 100);

        if (rand(0, 100) / 100 < $interactionProb) {
            // Types d'interactions
            $interactionTypes = ['join', 'like', 'comment', 'share', 'post'];
            $weights = [30, 25, 20, 15, 10];
            $interactionType = $interactionTypes[array_rand($interactionTypes)];

            // Score d'engagement (1-5)
            $engagementScore = max(1, intval($similarity * 5) + rand(-1, 1));

            $interactions[] = [
                'user_id' => $user['user_id'],
                'community_id' => $community['community_id'],
                'interaction_type' => $interactionType,
                'engagement_score' => $engagementScore,
                'similarity_score' => round($similarity, 3),
                'timestamp' => date('Y-m-d H:i:s', time() - rand(1, 365) * 24 * 3600),
                'user_age' => $user['age'],
                'user_location' => $user['location'],
                'user_activity_level' => $user['activity_level'],
                'community_category' => $community['category'],
                'community_member_count' => $community['member_count'],
                'community_activity_score' => $community['activity_score']
            ];
        }
    }

    return $interactions;
}

function saveDataset($interactions, $users, $communities) {
    // Sauvegarder le dataset principal en CSV
    $csvFile = __DIR__ . '/community_recommendation_dataset.csv';
    $fp = fopen($csvFile, 'w');

    if (!empty($interactions)) {
        // Écrire l'en-tête
        fputcsv($fp, array_keys($interactions[0]));

        // Écrire les données
        foreach ($interactions as $interaction) {
            fputcsv($fp, $interaction);
        }
    }
    fclose($fp);

    // Sauvegarder les métadonnées
    file_put_contents(__DIR__ . '/users_metadata.json', json_encode($users, JSON_PRETTY_PRINT));
    file_put_contents(__DIR__ . '/communities_metadata.json', json_encode($communities, JSON_PRETTY_PRINT));

    echo "Dataset sauvegardé:\n";
    echo "- community_recommendation_dataset.csv\n";
    echo "- users_metadata.json\n";
    echo "- communities_metadata.json\n";
}

function analyzeDataset($interactions) {
    echo "\n=== ANALYSE DU DATASET ===\n";
    echo "Nombre total d'interactions: " . count($interactions) . "\n";

    $userIds = array_unique(array_column($interactions, 'user_id'));
    $communityIds = array_unique(array_column($interactions, 'community_id'));

    echo "Utilisateurs uniques: " . count($userIds) . "\n";
    echo "Communautés uniques: " . count($communityIds) . "\n";

    // Distribution des types d'interactions
    $interactionTypes = array_count_values(array_column($interactions, 'interaction_type'));
    echo "\nDistribution des types d'interactions:\n";
    foreach ($interactionTypes as $type => $count) {
        echo "- {$type}: {$count}\n";
    }

    // Scores d'engagement
    $engagementScores = array_column($interactions, 'engagement_score');
    echo "\nDistribution des scores d'engagement:\n";
    echo "- Moyenne: " . round(array_sum($engagementScores) / count($engagementScores), 3) . "\n";
    echo "- Médiane: " . round(array_median($engagementScores), 3) . "\n";

    // Scores de similarité
    $similarityScores = array_column($interactions, 'similarity_score');
    echo "\nDistribution des scores de similarité:\n";
    echo "- Moyenne: " . round(array_sum($similarityScores) / count($similarityScores), 3) . "\n";
    echo "- Médiane: " . round(array_median($similarityScores), 3) . "\n";
}

function array_median($array) {
    sort($array);
    $count = count($array);
    $middle = floor(($count - 1) / 2);

    if ($count % 2) {
        return $array[$middle];
    } else {
        return ($array[$middle] + $array[$middle + 1]) / 2;
    }
}

// Exécution principale
echo "=== GÉNÉRATION DU DATASET DE RECOMMANDATION ===\n";
echo "Génération de {$numInteractions} interactions...\n";

// Générer les données
echo "Génération des profils utilisateurs...\n";
$users = generateUserProfiles($numUsers, $ecoInterests, $locations);

echo "Génération des communautés...\n";
$communities = generateCommunities($numCommunities, $communityCategories, $ecoInterests, $locations);

echo "Génération des interactions...\n";
$interactions = generateInteractions($users, $communities, $numInteractions);

// Analyser le dataset
analyzeDataset($interactions);

// Sauvegarder
saveDataset($interactions, $users, $communities);

echo "\n=== DATASET GÉNÉRÉ AVEC SUCCÈS ===\n";
echo "Le dataset est prêt pour l'entraînement du modèle de recommandation!\n";
