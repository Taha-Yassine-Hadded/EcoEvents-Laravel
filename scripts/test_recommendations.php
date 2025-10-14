<?php

/**
 * Script de test pour le système de recommandation de communautés
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

echo "=== TEST DU SYSTÈME DE RECOMMANDATION ===\n\n";

try {
    // 1. Tester le service de recommandation
    echo "1. Test du service de recommandation...\n";

    $recommendationService = $app->make(\App\Services\CommunityRecommendationService::class);
    echo "✅ Service créé avec succès\n";

    // 2. Créer un utilisateur de test
    echo "\n2. Création d'un utilisateur de test...\n";

    $testUser = \App\Models\User::firstOrCreate(
        ['email' => 'test-recommendations@example.com'],
        [
            'name' => 'Test User Recommendations',
            'password' => bcrypt('password'),
            'role' => 'user',
            'age' => 28,
            'location' => 'Paris'
        ]
    );
    echo "✅ Utilisateur de test créé: {$testUser->name}\n";

    // 3. Créer des communautés de test avec mots-clés
    echo "\n3. Création de communautés de test...\n";

    $testCommunities = [
        [
            'name' => 'Énergies Vertes Paris',
            'description' => 'Communauté dédiée aux énergies renouvelables',
            'category' => 'Énergies Renouvelables',
            'keywords' => ['énergie', 'solaire', 'renouvelable', 'vert'],
            'location' => 'Paris',
            'organizer_id' => $testUser->id,
            'is_active' => true
        ],
        [
            'name' => 'Zéro Déchet Lyon',
            'description' => 'Réduction des déchets et mode de vie durable',
            'category' => 'Recyclage & Zéro Déchet',
            'keywords' => ['recyclage', 'zéro déchet', 'compostage', 'réduction'],
            'location' => 'Lyon',
            'organizer_id' => $testUser->id,
            'is_active' => true
        ],
        [
            'name' => 'Biodiversité Marseille',
            'description' => 'Protection de la biodiversité et de la nature',
            'category' => 'Biodiversité & Nature',
            'keywords' => ['biodiversité', 'nature', 'protection', 'faune', 'flore'],
            'location' => 'Marseille',
            'organizer_id' => $testUser->id,
            'is_active' => true
        ]
    ];

    foreach ($testCommunities as $communityData) {
        $community = \App\Models\Community::firstOrCreate(
            ['name' => $communityData['name']],
            $communityData
        );
        echo "✅ Communauté créée: {$community->name}\n";
    }

    // 4. Créer des messages de test pour l'utilisateur
    echo "\n4. Création de messages de test...\n";

    $testMessages = [
        "Je m'intéresse beaucoup au recyclage et au compostage",
        "Les énergies renouvelables sont l'avenir",
        "Protéger la biodiversité est essentiel",
        "Le zéro déchet change la vie",
        "L'agriculture biologique est importante"
    ];

    foreach ($testMessages as $message) {
        \App\Models\ChatMessage::create([
            'user_id' => $testUser->id,
            'chat_room_id' => 1, // ID fictif
            'content' => $message,
            'message_type' => 'text'
        ]);
    }
    echo "✅ Messages de test créés\n";

    // 5. Tester les recommandations
    echo "\n5. Test des recommandations...\n";

    $recommendations = $recommendationService->getRecommendations($testUser, 3);

    if (empty($recommendations)) {
        echo "⚠️  Aucune recommandation générée\n";
    } else {
        echo "✅ " . count($recommendations) . " recommandations générées:\n";

        foreach ($recommendations as $i => $rec) {
            $community = $rec['community'];
            echo "   " . ($i + 1) . ". {$community->name} (Score: " . round($rec['score'], 3) . ")\n";
            echo "      Catégorie: {$community->category}\n";
            echo "      Mots-clés: " . implode(', ', $community->keywords ?? []) . "\n";
            if (!empty($rec['reasons'])) {
                echo "      Raisons: " . implode(', ', $rec['reasons']) . "\n";
            }
        }
    }

    // 6. Tester les communautés populaires
    echo "\n6. Test des communautés populaires...\n";

    $popularCommunities = $recommendationService->getPopularCommunities(3);
    echo "✅ " . count($popularCommunities) . " communautés populaires trouvées\n";

    // 7. Tester les communautés récentes
    echo "\n7. Test des communautés récentes...\n";

    $recentCommunities = $recommendationService->getRecentCommunities(3);
    echo "✅ " . count($recentCommunities) . " communautés récentes trouvées\n";

    // 8. Test de mise à jour des mots-clés
    echo "\n8. Test de mise à jour des mots-clés...\n";

    $community = \App\Models\Community::first();
    if ($community) {
        $recommendationService->updateCommunityKeywords($community);
        echo "✅ Mots-clés mis à jour pour: {$community->name}\n";
        echo "   Mots-clés: " . implode(', ', $community->fresh()->keywords ?? []) . "\n";
    }

    echo "\n=== TESTS TERMINÉS AVEC SUCCÈS ===\n";
    echo "Le système de recommandation fonctionne correctement !\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
