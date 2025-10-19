<?php
// Script de test simple pour créer des données de test

echo "=== CRÉATION DES DONNÉES DE TEST ===\n\n";

// Vérifier si Laravel est accessible
if (!file_exists('vendor/autoload.php')) {
    echo "❌ Laravel non trouvé. Assurez-vous d'être dans le bon répertoire.\n";
    exit(1);
}

require_once 'vendor/autoload.php';

try {
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "✅ Laravel initialisé avec succès\n\n";
    
    // Vérifier la connexion à la base de données
    $userCount = \App\Models\User::count();
    echo "✅ Connexion DB OK - {$userCount} utilisateurs existants\n\n";
    
    // Créer un utilisateur sponsor de test
    echo "1. Création d'un utilisateur sponsor de test...\n";
    
    $existingSponsor = \App\Models\User::where('email', 'sponsor@test.com')->first();
    if ($existingSponsor) {
        echo "⚠️  Utilisateur sponsor@test.com existe déjà\n";
        $sponsor = $existingSponsor;
    } else {
        $sponsor = \App\Models\User::create([
            'name' => 'Test Sponsor',
            'email' => 'sponsor@test.com',
            'password' => bcrypt('password123'),
            'role' => 'sponsor',
            'phone' => '+216 20 123 456',
            'city' => 'Tunis',
            'address' => '123 Rue de Test',
            'bio' => 'Entreprise de test pour le sponsoring écologique'
        ]);
        echo "✅ Utilisateur sponsor créé: {$sponsor->email}\n";
    }
    
    // Créer des campagnes de test
    echo "\n2. Création des campagnes de test...\n";
    
    $campaigns = [
        [
            'title' => 'Festival Écologique 2024',
            'description' => 'Un festival dédié à la protection de l\'environnement avec des conférences, ateliers et stands.',
            'content' => 'Rejoignez-nous pour ce grand événement écologique qui rassemble experts, entreprises et citoyens autour de la protection de notre planète.',
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-03',
            'category' => 'Événement',
            'views_count' => 150,
            'shares_count' => 25,
            'media_urls' => json_encode(['images' => ['festival-eco.jpg']])
        ],
        [
            'title' => 'Marathon Vert',
            'description' => 'Course pour la planète - 10km écologique',
            'content' => 'Participez à notre marathon écologique où chaque kilomètre parcouru contribue à la reforestation.',
            'start_date' => '2024-11-15',
            'end_date' => '2024-11-15',
            'category' => 'Sport',
            'views_count' => 89,
            'shares_count' => 12,
            'media_urls' => json_encode(['images' => ['marathon-vert.jpg']])
        ],
        [
            'title' => 'Conférence Climat 2024',
            'description' => 'Conférence internationale sur le changement climatique',
            'content' => 'Experts et scientifiques se réunissent pour discuter des solutions au changement climatique.',
            'start_date' => '2024-10-20',
            'end_date' => '2024-10-22',
            'category' => 'Conférence',
            'views_count' => 203,
            'shares_count' => 45,
            'media_urls' => json_encode(['images' => ['conference-climat.jpg']])
        ]
    ];
    
    $createdCampaigns = 0;
    foreach ($campaigns as $campaignData) {
        $existingCampaign = \App\Models\EchofyCampaign::where('title', $campaignData['title'])->first();
        if (!$existingCampaign) {
            $campaign = \App\Models\EchofyCampaign::create($campaignData);
            echo "✅ Campagne créée: {$campaign->title}\n";
            $createdCampaigns++;
        } else {
            echo "⚠️  Campagne '{$campaignData['title']}' existe déjà\n";
        }
    }
    
    echo "\n=== RÉSUMÉ DES DONNÉES DE TEST ===\n";
    echo "Utilisateur sponsor: sponsor@test.com\n";
    echo "Mot de passe: password123\n";
    echo "Campagnes créées: {$createdCampaigns}\n";
    echo "Total campagnes disponibles: " . \App\Models\EchofyCampaign::count() . "\n\n";
    
    echo "=== INSTRUCTIONS DE TEST ===\n";
    echo "1. Ouvrez votre navigateur sur: http://127.0.0.1:8000\n";
    echo "2. Allez sur la page de connexion\n";
    echo "3. Connectez-vous avec: sponsor@test.com / password123\n";
    echo "4. Vous devriez être redirigé vers le dashboard sponsor\n\n";
    
    echo "=== PAGES À TESTER ===\n";
    echo "• Dashboard: http://127.0.0.1:8000/sponsor-dashboard\n";
    echo "• Mon Profil: http://127.0.0.1:8000/sponsor/profile\n";
    echo "• Mon Entreprise: http://127.0.0.1:8000/sponsor/company\n";
    echo "• Campagnes: http://127.0.0.1:8000/sponsor/campaigns\n";
    echo "• Sponsorships: http://127.0.0.1:8000/sponsor/sponsorships\n";
    echo "• Statistiques: http://127.0.0.1:8000/sponsor/statistics\n\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez que votre base de données est configurée correctement.\n";
}
