<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\EchofyCampaign;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CRÉATION DES DONNÉES DE TEST ===\n\n";

// 1. Créer un utilisateur sponsor
echo "1. Création d'un utilisateur sponsor...\n";
$sponsor = User::create([
    'name' => 'Test Sponsor',
    'email' => 'sponsor@test.com',
    'password' => bcrypt('password123'),
    'role' => 'sponsor',
    'phone' => '+216 20 123 456',
    'city' => 'Tunis',
    'address' => '123 Rue de Test',
    'bio' => 'Entreprise de test pour le sponsoring écologique'
]);
echo "✅ Utilisateur sponsor créé: {$sponsor->email}\n\n";

// 2. Créer des campagnes de test
echo "2. Création des campagnes de test...\n";

$campaigns = [
    [
        'title' => 'Festival Écologique 2024',
        'description' => 'Un festival dédié à la protection de l\'environnement',
        'content' => 'Rejoignez-nous pour ce grand événement écologique...',
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-03',
        'category' => 'Événement',
        'views_count' => 150,
        'shares_count' => 25,
        'media_urls' => json_encode(['images' => ['campaign1.jpg']])
    ],
    [
        'title' => 'Marathon Vert',
        'description' => 'Course pour la planète',
        'content' => 'Participez à notre marathon écologique...',
        'start_date' => '2024-11-15',
        'end_date' => '2024-11-15',
        'category' => 'Sport',
        'views_count' => 89,
        'shares_count' => 12,
        'media_urls' => json_encode(['images' => ['campaign2.jpg']])
    ],
    [
        'title' => 'Conférence Climat',
        'description' => 'Conférence sur le changement climatique',
        'content' => 'Experts et scientifiques se réunissent...',
        'start_date' => '2024-10-20',
        'end_date' => '2024-10-22',
        'category' => 'Conférence',
        'views_count' => 203,
        'shares_count' => 45,
        'media_urls' => json_encode(['images' => ['campaign3.jpg']])
    ]
];

foreach ($campaigns as $campaignData) {
    $campaign = EchofyCampaign::create($campaignData);
    echo "✅ Campagne créée: {$campaign->title}\n";
}

echo "\n=== DONNÉES DE TEST CRÉÉES ===\n";
echo "Utilisateur sponsor: sponsor@test.com\n";
echo "Mot de passe: password123\n";
echo "Campagnes créées: " . count($campaigns) . "\n\n";

echo "=== INSTRUCTIONS DE TEST ===\n";
echo "1. Ouvrez votre navigateur sur: http://127.0.0.1:8000\n";
echo "2. Connectez-vous avec: sponsor@test.com / password123\n";
echo "3. Testez toutes les pages sponsor listées ci-dessous\n\n";
