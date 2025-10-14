<?php

namespace App\Services;

use Illuminate\Support\Str;
use GuzzleHttp\Client;

class EcoBotService
{
    private ?string $apiKey;
    private Client $http;
    private string $baseUrl;
    private string $model;

    public function __construct(?string $apiKey, ?string $baseUrl = null, ?string $model = null)
    {
        // Forcer les valeurs si elles ne sont pas définies
        $this->apiKey = $apiKey ?: env('OPENAI_API_KEY') ?: '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';
        $this->baseUrl = $baseUrl ?: env('OPENAI_BASE_URL') ?: 'https://openrouter.ai/api/v1';
        $this->model = $model ?: env('OPENAI_MODEL') ?: 'mistralai/mistral-7b-instruct';
        $this->http = new Client([
            'timeout' => 12,
        ]);
    }

    public function shouldRespond(string $content): bool
    {
        $text = Str::lower($content);
        if (Str::contains($text, ['@ecochatbot','@ecobot'])) {
            return true;
        }
        $keywords = [
            'écologie','ecologie','eco','climat','recycl','recyclage','tri','compost','compostage',
            'biodiversité','energie','énergie','solaire','carbone','co2','déchet','dechet','durable',
            'zero dechet','zéro déchet','pollution','eau','forêt','foret','plastique','vélo','velo'
        ];
        foreach ($keywords as $kw) {
            if (Str::contains($text, $kw)) {
                return true;
            }
        }
        return false;
    }

    public function generateReply(string $prompt): string
    {
        // Forcer l'utilisation de l'IA - ne jamais utiliser les fallbacks
        $this->apiKey = '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';
        $this->baseUrl = 'https://openrouter.ai/api/v1';
        $this->model = 'mistralai/mistral-7b-instruct';

        if ($this->apiKey) {
            try {
                $response = $this->http->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => $this->model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Tu es EcoChatBot, un assistant concis et positif. Réponds en français, 1-3 phrases max, avec un emoji écologique approprié. Donne des conseils pratiques et sûrs. Si on sort du thème écologie/climat, réponds brièvement et recadre vers un sujet écologique relié.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                        ],
                        'temperature' => 0.7,
                        'max_tokens' => 180,
                    ],
                ]);
                $data = json_decode((string) $response->getBody(), true);
                $msg = $data['choices'][0]['message']['content'] ?? null;
                if (is_string($msg) && trim($msg) !== '') {
                    // \Illuminate\Support\Facades\Log::info('EcoBot API success', ['response' => $msg]);
                    return trim($msg);
                }
            } catch (\Throwable $e) {
                // En cas d'erreur, retourner un message d'erreur au lieu des fallbacks
                return "Désolé, je rencontre un problème technique. Réessayez dans quelques instants. 🤖";
            }
        }

        // Ne jamais utiliser les fallbacks - forcer l'IA
        return "Désolé, je rencontre un problème technique. Réessayez dans quelques instants. 🤖";
    }
}




