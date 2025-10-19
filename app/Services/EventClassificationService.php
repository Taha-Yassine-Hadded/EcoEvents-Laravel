<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EventClassificationService
{
    /**
     * The base URL for the classification API
     */
    private string $apiUrl;

    /**
     * Cache duration in seconds (1 hour)
     */
    private int $cacheDuration = 3600;

    public function __construct()
    {
        $this->apiUrl = config('services.classification_api.url', 'http://localhost:8001');
    }

    /**
     * Classify an event and return the top 2 predicted labels
     *
     * @param string $title
     * @param string|null $description
     * @param string|null $category
     * @param string|null $keywords
     * @return array|null
     */
    public function classifyEvent(
        string $title,
        ?string $description = null,
        ?string $category = null,
        ?string $keywords = null
    ): ?array {
        // Create a cache key based on the input
        $cacheKey = 'event_classification_' . md5($title . $description . $category . $keywords);

        // Try to get from cache first
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($title, $description, $category, $keywords) {
            try {
                $response = Http::timeout(5)->post("{$this->apiUrl}/classify", [
                    'title' => $title,
                    'description' => $description ?? '',
                    'category' => $category ?? '',
                    'keywords' => $keywords ?? ''
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['success'] ?? false) {
                        return $this->formatClassificationResult($data);
                    }
                }

                Log::warning('Classification API returned unsuccessful response', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);

                return null;

            } catch (\Exception $e) {
                Log::error('Event classification error', [
                    'message' => $e->getMessage(),
                    'title' => $title
                ]);

                return null;
            }
        });
    }

    /**
     * Format the classification result to return top 2 labels
     *
     * @param array $data
     * @return array
     */
    private function formatClassificationResult(array $data): array
    {
        $confidenceScores = $data['confidence_scores'] ?? [];
        
        // Sort by confidence (already sorted from API, but ensuring)
        arsort($confidenceScores);
        
        // Get top 2 labels
        $topLabels = array_slice($confidenceScores, 0, 2, true);
        
        return [
            'primary_label' => [
                'name' => array_key_first($topLabels),
                'confidence' => reset($topLabels)
            ],
            'secondary_label' => [
                'name' => count($topLabels) > 1 ? array_keys($topLabels)[1] : null,
                'confidence' => count($topLabels) > 1 ? array_values($topLabels)[1] : null
            ],
            'all_scores' => $confidenceScores
        ];
    }

    /**
     * Get label icon based on label name
     *
     * @param string $label
     * @return string
     */
    public static function getLabelIcon(string $label): string
    {
        $icons = [
            'Nettoyage' => 'fa-broom',
            'Plantation' => 'fa-seedling',
            'Éco-atelier' => 'fa-tools',
            'Sensibilisation' => 'fa-bullhorn',
            'Collecte de fonds' => 'fa-hand-holding-usd'
        ];

        return $icons[$label] ?? 'fa-leaf';
    }

    /**
     * Get label color based on label name
     *
     * @param string $label
     * @return string
     */
    public static function getLabelColor(string $label): string
    {
        $colors = [
            'Nettoyage' => '#3498db',      // Blue
            'Plantation' => '#27ae60',     // Green
            'Éco-atelier' => '#e67e22',    // Orange
            'Sensibilisation' => '#9b59b6', // Purple
            'Collecte de fonds' => '#e74c3c' // Red
        ];

        return $colors[$label] ?? '#95a5a6';
    }

    /**
     * Check if the classification API is available
     *
     * @return bool
     */
    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Clear classification cache for a specific event
     *
     * @param string $title
     * @param string|null $description
     * @param string|null $category
     * @param string|null $keywords
     * @return void
     */
    public function clearCache(
        string $title,
        ?string $description = null,
        ?string $category = null,
        ?string $keywords = null
    ): void {
        $cacheKey = 'event_classification_' . md5($title . $description . $category . $keywords);
        Cache::forget($cacheKey);
    }
}