<?php

/**
 * Script simple pour vérifier les communautés
 */

require __DIR__ . '/../vendor/autoload.php';

echo "=== VÉRIFICATION DES COMMUNAUTÉS ===\n\n";

try {
    // Initialiser Laravel
    $app = require __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    echo "✅ Laravel initialisé\n";

    // Test de connexion DB
    echo "🔍 Test de connexion à la base de données...\n";
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=ecoEvent', 'root', '');
    echo "✅ Connexion DB réussie\n";

    // Vérifier si la table communities existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'communities'");
    $tableExists = $stmt->fetch();

    if (!$tableExists) {
        echo "❌ Table 'communities' n'existe pas !\n";
        echo "💡 Solution: Exécuter 'php artisan migrate'\n";
        exit(1);
    }

    echo "✅ Table 'communities' existe\n";

    // Compter les communautés
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM communities");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'];

    echo "📊 Nombre de communautés: {$count}\n";

    if ($count === 0) {
        echo "\n⚠️  Aucune communauté dans la base de données !\n";
        echo "\n🔧 SOLUTIONS:\n";
        echo "1. Créer des communautés via l'interface web\n";
        echo "2. Utiliser le seeder: php artisan db:seed --class=CommunitySeeder\n";
        echo "3. Créer manuellement via l'interface organisateur\n";

        // Proposer de créer des communautés de test
        echo "\n🤖 Créer des communautés de test ? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim($line) === 'y' || trim($line) === 'Y') {
            createTestCommunities($pdo);
        }
    } else {
        // Lister les communautés
        echo "\n📋 Communautés existantes:\n";
        $stmt = $pdo->query("SELECT id, name, category, location, is_active, created_at FROM communities ORDER BY created_at DESC");
        $communities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($communities as $community) {
            $status = $community['is_active'] ? '✅ Active' : '❌ Inactive';
            $location = $community['location'] ? " ({$community['location']})" : '';
            echo "- {$community['name']} - {$community['category']}{$location} - {$status}\n";
            echo "  ID: {$community['id']}, Créée: {$community['created_at']}\n";
        }

        // Statistiques
        echo "\n📈 Statistiques:\n";
        $stmt = $pdo->query("SELECT
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            FROM communities");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "- Total: {$stats['total']}\n";
        echo "- Actives: {$stats['active']}\n";
        echo "- Inactives: {$stats['inactive']}\n";
    }

    echo "\n✅ Vérification terminée !\n";

} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

function createTestCommunities($pdo) {
    echo "\n🔧 Création de communautés de test...\n";

    // Créer un utilisateur organisateur de test
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute(['Organisateur Test', 'organizer@test.com', password_hash('password', PASSWORD_DEFAULT), 'organizer']);

    $organizerId = $pdo->lastInsertId();
    echo "✅ Utilisateur organisateur créé (ID: {$organizerId})\n";

    $testCommunities = [
        ['Énergies Vertes Paris', 'Communauté dédiée aux énergies renouvelables', 'energie', 'Paris', 200, $organizerId, '["énergie", "solaire", "renouvelable"]'],
        ['Zéro Déchet Lyon', 'Réduction des déchets et mode de vie durable', 'recyclage', 'Lyon', 150, $organizerId, '["recyclage", "zéro déchet", "compostage"]'],
        ['Biodiversité Marseille', 'Protection de la biodiversité et de la nature', 'biodiversite', 'Marseille', 100, $organizerId, '["biodiversité", "nature", "protection"]'],
        ['Transport Durable Toulouse', 'Promotion des transports écologiques', 'transport', 'Toulouse', 120, $organizerId, '["transport", "vélo", "durable"]'],
        ['Agriculture Bio Nantes', 'Agriculture biologique et circuits courts', 'jardinage', 'Nantes', 80, $organizerId, '["agriculture", "biologique", "local"]']
    ];

    $stmt = $pdo->prepare("INSERT INTO communities (name, description, category, location, max_members, organizer_id, is_active, keywords, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, ?, NOW(), NOW())");

    foreach ($testCommunities as $community) {
        $stmt->execute($community);
        echo "✅ Communauté créée: {$community[0]}\n";
    }

    echo "\n🎉 " . count($testCommunities) . " communautés de test créées avec succès !\n";
}
