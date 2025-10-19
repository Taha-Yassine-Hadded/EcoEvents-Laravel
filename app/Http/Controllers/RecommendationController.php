<?php
// app/Http/Controllers/Api/RecommendationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignLike;
use App\Models\CampaignView;
use App\Models\CampaignComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    private $pythonUrl = 'http://localhost:6000/recommendations';

    public function getRecommendations(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => true,
                'recommendations' => $this->getPopularCampaigns(5),
                'type' => 'popular'
            ]);
        }

        // Cache 30min par user
        $cacheKey = "recommendations_user_{$userId}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json($cached);
        }

        try {
            // 1. Collecter données user
            $userData = $this->collectUserData($userId);

            // 2. Collecter données campaigns
            $campaignsData = $this->collectCampaignsData();

            // 3. Appel Python
            $response = Http::timeout(10)->post($this->pythonUrl, [
                'user_id' => $userId,
                'user_data' => $userData,
                'campaigns_data' => $campaignsData,
                'context' => [
                    'page' => $request->query('page', 'all'),
                    'current_campaign_id' => $request->query('current_campaign_id')
                ]
            ]);

            if ($response->successful()) {
                $pythonResult = $response->json();

                if ($pythonResult['success']) {
                    $recommendations = $this->formatRecommendations(
                        $pythonResult['recommendations'],
                        $userId
                    );

                    $result = [
                        'success' => true,
                        'recommendations' => $recommendations,
                        'type' => $pythonResult['type'] ?? 'hybrid',
                        'confidence' => $pythonResult['confidence'] ?? 0.8,
                        'model_version' => $pythonResult['model_version'] ?? 'v1.0'
                    ];

                    // Cache
                    Cache::put($cacheKey, $result, 1800); // 30min

                    return response()->json($result);
                }
            }

            // Fallback
            Log::warning("Python recommendations failed, using popular", [
                'user_id' => $userId,
                'python_error' => $response->body()
            ]);

            return response()->json([
                'success' => true,
                'recommendations' => $this->getPopularCampaigns(8),
                'type' => 'popular_fallback'
            ]);

        } catch (\Exception $e) {
            Log::error("Recommendation error: " . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => true,
                'recommendations' => $this->getPopularCampaigns(8),
                'type' => 'error_fallback'
            ]);
        }
    }

    private function collectUserData($userId)
    {
        // Likes récents (30 derniers jours)
        $likes = CampaignLike::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->with('campaign')
            ->get()
            ->map(function ($like) {
                return [
                    'campaign_id' => $like->campaign_id,
                    'title' => $like->campaign->title,
                    'category' => $like->campaign->category,
                    'content' => substr(strip_tags($like->campaign->content), 0, 200),
                    'weight' => 3.0, // Like = poids élevé
                    'timestamp' => $like->created_at->timestamp
                ];
            })->toArray();

        // Views récentes
        $views = CampaignView::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->with('campaign')
            ->get()
            ->map(function ($view) {
                return [
                    'campaign_id' => $view->campaign_id,
                    'title' => $view->campaign->title,
                    'category' => $view->campaign->category,
                    'content' => substr(strip_tags($view->campaign->content), 0, 200),
                    'weight' => 1.0, // View = poids faible
                    'timestamp' => $view->created_at->timestamp
                ];
            })->toArray();

        // Commentaires
        $comments = CampaignComment::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->with('campaign')
            ->get()
            ->map(function ($comment) {
                return [
                    'campaign_id' => $comment->campaign_id,
                    'title' => $comment->campaign->title,
                    'category' => $comment->campaign->category,
                    'content' => substr($comment->content, 0, 200),
                    'weight' => 2.0, // Comment = poids moyen
                    'timestamp' => $comment->created_at->timestamp
                ];
            })->toArray();

        return [
            'interactions' => array_merge($likes, $views, $comments),
            'total_likes' => count($likes),
            'total_views' => count($views),
            'total_comments' => count($comments),
            'preferred_categories' => collect($likes)->pluck('category')->countBy()->toArray()
        ];
    }

    private function collectCampaignsData()
    {
        $campaigns = Campaign::where('status', 'active')
            ->orWhere('status', 'upcoming')
            ->limit(50) // Top candidates
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'category' => $campaign->category,
                    'content' => strip_tags($campaign->content),
                    'objectives' => implode(' ', $campaign->objectives ?? []),
                    'actions' => implode(' ', $campaign->actions ?? []),
                    'start_date' => $campaign->start_date->timestamp,
                    'end_date' => $campaign->end_date->timestamp,
                    'views_count' => $campaign->views_count,
                    'likes_count' => $campaign->likes_count,
                    'popularity_score' => $this->calculatePopularity($campaign)
                ];
            })->toArray();

        return $campaigns;
    }

    private function calculatePopularity($campaign)
    {
        $score = $campaign->likes_count * 3 +
            $campaign->views_count * 0.1 +
            $campaign->comments_count * 2;

        // Boost récence
        $daysOld = now()->diffInDays($campaign->start_date);
        if ($daysOld < 7) $score *= 1.5;

        return min($score, 1000); // Cap
    }

    private function formatRecommendations($pythonRecos, $userId)
    {
        $campaignIds = array_column($pythonRecos, 'campaign_id');
        $campaigns = Campaign::whereIn('id', $campaignIds)
            ->with(['creator', 'likes', 'comments'])
            ->get()
            ->keyBy('id');

        return collect($pythonRecos)->map(function ($reco) use ($campaigns, $userId) {
            $campaign = $campaigns->get($reco['campaign_id']);
            if (!$campaign) return null;

            $userLiked = CampaignLike::where([
                'user_id' => $userId,
                'campaign_id' => $campaign->id
            ])->exists();

            return [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'category' => $campaign->category,
                'main_image_url' => $campaign->main_image_url,
                'status' => $campaign->status,
                'start_date' => $campaign->start_date->format('d/m/Y'),
                'likes_count' => $campaign->likes_count,
                'views_count' => $campaign->views_count,
                'user_liked' => $userLiked,
                'recommendation_score' => $reco['score'],
                'reason' => $reco['reason'] ?? 'Recommandé pour vous',
                'diversity_score' => $reco['diversity_score'] ?? 0.5
            ];
        })->filter()->values()->toArray();
    }

    private function getPopularCampaigns($limit = 8)
    {
        return Campaign::where('status', 'active')
            ->orWhere('status', 'upcoming')
            ->orderByRaw('
                (likes_count * 3 + views_count * 0.1 + comments_count * 2) DESC
            ')
            ->limit($limit)
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'category' => $campaign->category,
                    'main_image_url' => $campaign->main_image_url,
                    'status' => $campaign->status,
                    'start_date' => $campaign->start_date->format('d/m/Y'),
                    'likes_count' => $campaign->likes_count,
                    'views_count' => $campaign->views_count,
                    'user_liked' => false,
                    'recommendation_score' => 0.5,
                    'reason' => 'Populaire'
                ];
            })->toArray();
    }
}
