<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST D'AUTHENTIFICATION SPONSOR ===\n\n";

// Vérifier l'utilisateur sponsor
$sponsor = \App\Models\User::where('email', 'sponsor@test.com')->first();

if ($sponsor) {
    echo "✅ Utilisateur sponsor trouvé:\n";
    echo "   - Nom: {$sponsor->name}\n";
    echo "   - Email: {$sponsor->email}\n";
    echo "   - Rôle: {$sponsor->role}\n";
    echo "   - ID: {$sponsor->id}\n\n";
    
    // Test de la méthode showCampaigns
    echo "=== TEST DE LA MÉTHODE showCampaigns ===\n";
    
    try {
        $campaigns = \App\Models\EchofyCampaign::orderBy('created_at', 'desc')->paginate(12);
        echo "✅ Campagnes récupérées: {$campaigns->count()} éléments\n";
        echo "✅ Total: {$campaigns->total()} campagnes\n\n";
        
        if ($campaigns->count() > 0) {
            echo "Première campagne:\n";
            $firstCampaign = $campaigns->first();
            echo "   - Titre: {$firstCampaign->title}\n";
            echo "   - Description: " . substr($firstCampaign->description, 0, 50) . "...\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur lors de la récupération des campagnes: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Utilisateur sponsor@test.com non trouvé\n";
    echo "Créez-le d'abord avec le script create_test_data.php\n";
}

echo "\n=== INSTRUCTIONS DE TEST ===\n";
echo "1. Ouvrez: http://127.0.0.1:8000\n";
echo "2. Connectez-vous avec: sponsor@test.com / password123\n";
echo "3. Allez sur: http://127.0.0.1:8000/sponsor/campaigns\n";
echo "4. Ou cliquez sur 'Campagnes' dans le menu\n";
