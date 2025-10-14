<?php

require __DIR__ . '/../vendor/autoload.php';

function envval(string $key, ?string $default = null): ?string {
    $v = getenv($key);
    if ($v === false || $v === '') {
        return $default;
    }
    return $v;
}

$apiKey = envval('OPENAI_API_KEY');
$baseUrl = rtrim(envval('OPENAI_BASE_URL', 'https://openrouter.ai/api/v1'), '/');
$model  = envval('OPENAI_MODEL', 'meta-llama/llama-3.1-8b-instruct:free');

if (!$apiKey) {
    // Try load from project .env using Dotenv
    $root = realpath(__DIR__ . '/..');
    if ($root && file_exists($root . '/.env')) {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable($root);
            $dotenv->safeLoad();
            $apiKey = envval('OPENAI_API_KEY');
        } catch (Throwable $e) {
            // ignore
        }
    }
    if (!$apiKey) {
        fwrite(STDERR, "ERROR: OPENAI_API_KEY is not set.\n" .
            "Set it in your environment or .env and re-run.\n");
        exit(1);
    }
}

$payload = [
    'model' => $model,
    'messages' => [
        [ 'role' => 'user', 'content' => "Bonjour, donne 2 idées d'événements écologiques." ]
    ],
];

$url = $baseUrl . '/chat/completions';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
]);

$response = curl_exec($ch);
if ($response === false) {
    fwrite(STDERR, 'cURL error: ' . curl_error($ch) . "\n");
    exit(2);
}
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode < 200 || $httpCode >= 300) {
    fwrite(STDERR, "HTTP $httpCode\n$response\n");
    exit(3);
}

$data = json_decode($response, true);
$text = $data['choices'][0]['message']['content'] ?? null;

echo "Model: $model\n";
echo "Base URL: $baseUrl\n\n";
if (is_string($text) && trim($text) !== '') {
    echo "Reply:\n$text\n";
} else {
    echo "Raw response:\n$response\n";
}


