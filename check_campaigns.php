<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VÉRIFICATION DES CAMPAGNES ===\n\n";

$count = \App\Models\EchofyCampaign::count();
echo "Nombre de campagnes en base: {$count}\n\n";

if ($count > 0) {
    $campaigns = \App\Models\EchofyCampaign::all();
    foreach ($campaigns as $campaign) {
        echo "- {$campaign->title} (ID: {$campaign->id})\n";
        echo "  Description: " . substr($campaign->description, 0, 50) . "...\n";
        echo "  Date début: {$campaign->start_date}\n";
        echo "  Date fin: {$campaign->end_date}\n\n";
    }
} else {
    echo "Aucune campagne trouvée en base de données.\n";
}

echo "=== TEST DE LA ROUTE ===\n";
echo "URL à tester: http://127.0.0.1:8000/sponsor/campaigns\n";
echo "Assurez-vous d'être connecté en tant que sponsor.\n";
