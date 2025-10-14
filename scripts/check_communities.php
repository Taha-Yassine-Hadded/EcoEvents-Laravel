<?php

/**
 * Script pour vérifier les communautés dans la base de données
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

echo "=== VÉRIFICATION DES COMMUNAUTÉS DANS LA BASE DE DONNÉES ===\n\n";

try {
    // 1. Compter les communautés
    $totalCommunities = \App\Models\Community::count();
    echo "📊 Nombre total de communautés: {$totalCommunities}\n";

    if ($totalCommunities === 0) {
        echo "⚠️  Aucune communauté trouvée dans la base de données !\n";
        echo "\n🔧 SOLUTIONS:\n";
        echo "1. Créer des communautés via l'interface organisateur\n";
        echo "2. Utiliser le seeder pour créer des données de test\n";
        echo "3. Ajouter des communautés manuellement\n";

        // Proposer de créer des communautés de test
        echo "\n🤖 Voulez-vous créer des communautés de test ? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) === 'y' || trim($line) === 'Y') {
            createTestCommunities();
        }

        return;
    }

    // 2. Lister les communautés
    echo "\n📋 Liste des communautés:\n";
    $communities = \App\Models\Community::select('id', 'name', 'category', 'location', 'is_active', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get();

    foreach ($communities as $community) {
        $status = $community->is_active ? '✅ Active' : '❌ Inactive';
        $location = $community->location ? " ({$community->location})" : '';
        echo "- {$community->name} - {$community->category}{$location} - {$status}\n";
        echo "  Créée le: {$community->created_at->format('d/m/Y H:i')}\n";
    }

    // 3. Statistiques
    echo "\n📈 Statistiques:\n";
    $activeCommunities = \App\Models\Community::where('is_active', true)->count();
    $inactiveCommunities = \App\Models\Community::where('is_active', false)->count();

    echo "- Communautés actives: {$activeCommunities}\n";
    echo "- Communautés inactives: {$inactiveCommunities}\n";

    // 4. Catégories
    echo "\n🏷️  Catégories:\n";
    $categories = \App\Models\Community::selectRaw('category, COUNT(*) as count')
        ->groupBy('category')
        ->orderBy('count', 'desc')
        ->get();

    foreach ($categories as $category) {
        echo "- {$category->category}: {$category->count} communautés\n";
    }

    // 5. Membres
    echo "\n👥 Membres des communautés:\n";
    $communitiesWithMembers = \App\Models\Community::withCount('members')->get();

    foreach ($communitiesWithMembers as $community) {
        echo "- {$community->name}: {$community->members_count} membres\n";
    }

    echo "\n✅ Vérification terminée !\n";

} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

function createTestCommunities() {
    echo "\n🔧 Création de communautés de test...\n";

    // Créer un utilisateur organisateur de test
    $organizer = \App\Models\User::firstOrCreate(
        ['email' => 'organizer@test.com'],
        [
            'name' => 'Organisateur Test',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]
    );

    $testCommunities = [
        [
            'name' => 'Énergies Vertes Paris',
            'description' => 'Communauté dédiée aux énergies renouvelables et à la transition écologique',
            'category' => 'energie',
            'location' => 'Paris',
            'max_members' => 200,
            'organizer_id' => $organizer->id,
            'is_active' => true,
            'keywords' => ['énergie', 'solaire', 'renouvelable', 'vert']
        ],
        [
            'name' => 'Zéro Déchet Lyon',
            'description' => 'Réduction des déchets et mode de vie durable',
            'category' => 'recyclage',
            'location' => 'Lyon',
            'max_members' => 150,
            'organizer_id' => $organizer->id,
            'is_active' => true,
            'keywords' => ['recyclage', 'zéro déchet', 'compostage', 'réduction']
        ],
        [
            'name' => 'Biodiversité Marseille',
            'description' => 'Protection de la biodiversité et de la nature',
            'category' => 'biodiversite',
            'location' => 'Marseille',
            'max_members' => 100,
            'organizer_id' => $organizer->id,
            'is_active' => true,
            'keywords' => ['biodiversité', 'nature', 'protection', 'faune', 'flore']
        ],
        [
            'name' => 'Transport Durable Toulouse',
            'description' => 'Promotion des transports écologiques',
            'category' => 'transport',
            'location' => 'Toulouse',
            'max_members' => 120,
            'organizer_id' => $organizer->id,
            'is_active' => true,
            'keywords' => ['transport', 'vélo', 'durable', 'mobilité', 'électrique']
        ],
        [
            'name' => 'Agriculture Bio Nantes',
            'description' => 'Agriculture biologique et circuits courts',
            'category' => 'jardinage',
            'location' => 'Nantes',
            'max_members' => 80,
            'organizer_id' => $organizer->id,
            'is_active' => true,
            'keywords' => ['agriculture', 'biologique', 'local', 'permaculture']
        ]
    ];

    foreach ($testCommunities as $communityData) {
        $community = \App\Models\Community::create($communityData);
        echo "✅ Communauté créée: {$community->name}\n";
    }

    echo "\n🎉 {$count} communautés de test créées avec succès !\n";
}
