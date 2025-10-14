<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$config = $app->make('config');

echo "API Key: " . (strlen($config->get('services.openai.key')) > 0 ? 'SET (' . strlen($config->get('services.openai.key')) . ' chars)' : 'NOT SET') . PHP_EOL;
echo "Base URL: " . $config->get('services.openai.base_url') . PHP_EOL;
echo "Model: " . $config->get('services.openai.model') . PHP_EOL;

// Test direct du service
try {
    $bot = $app->make(\App\Services\EcoBotService::class);
    echo "Service created successfully\n";

    // Test avec un message simple
    $response = $bot->generateReply('test');
    echo "Response: " . $response . "\n";

} catch (Exception $e) {
    echo "Error creating service: " . $e->getMessage() . "\n";
}
