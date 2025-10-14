<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

try {
    $bot = $app->make(\App\Services\EcoBotService::class);
    echo "Testing EcoBot service...\n";

    $response = $bot->generateReply('Donne 2 idees d evenements ecologiques');
    echo "Response: " . $response . "\n";

    echo "Should respond to '@EcoChatBot': " . ($bot->shouldRespond('@EcoChatBot bonjour') ? 'YES' : 'NO') . "\n";
    echo "Should respond to 'compostage': " . ($bot->shouldRespond('compostage') ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
