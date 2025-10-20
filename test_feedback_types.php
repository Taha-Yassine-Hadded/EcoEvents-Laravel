<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Test de l'API getFeedbackTypes:\n";

try {
    // Simuler une requête
    $request = new \Illuminate\Http\Request();
    $request->auth = \App\Models\User::find(15); // Sponsor
    
    $controller = new \App\Http\Controllers\SponsorshipFeedbackController();
    $response = $controller->getFeedbackTypes($request);
    
    echo "Réponse: " . $response->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
