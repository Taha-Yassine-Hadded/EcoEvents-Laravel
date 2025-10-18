<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignComment;
use App\Models\CampaignLike;
use App\Models\CampaignView;
use App\Models\CommentLike;
use App\Models\CampaignCommentSentiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class FrontCampaignController extends Controller
{
    private $cacheTtl = 3600; // 1 heure de cache pour recommandations
    private $viewTtl = 1800; // 30 minutes pour vues

    /**
     * Afficher la liste des campagnes côté front-office
     */

    public function index(Request $request)
    {
        try {
            // ✅ Utiliser utilisateur du middleware VerifyJWT
            $user = $request->auth;

            Log::info('🚀 INDEX DEBUG - Recommandations', [
                'user_id' => $user?->id,
                'auth_check' => (bool)$user,
                'email' => $user?->email,
                'method' => 'middleware_jwt'
            ]);

            // 🎯 GESTION DES RECOMMANDATIONS (récupérer en premier pour exclure)
            $recommendedCampaigns = [];
            $recommendedCampaignIds = [];

            if ($user) {
                Log::info('✅ Utilisateur authentifié détecté', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);

                try {
                    $recommendedCampaigns = $this->getUserRecommendations($user);

                    // ✅ GARANTIR DONNÉES NON NULLES
                    if (empty($recommendedCampaigns) || !is_array($recommendedCampaigns)) {
                        Log::warning('⚠️ Recommandations vides - Fallback appliqué', ['user_id' => $user->id]);
                        $recommendedCampaigns = $this->getFallbackRecommendations();
                    }

                    // Récupérer les IDs des campagnes recommandées
                    $recommendedCampaignIds = array_column($recommendedCampaigns, 'id');

                    Log::info('📊 Recommandations chargées', [
                        'user_id' => $user->id,
                        'count' => count($recommendedCampaigns),
                        'categories' => array_unique(array_column($recommendedCampaigns, 'category')),
                        'recommended_ids' => $recommendedCampaignIds
                    ]);

                    // DEBUG : Vérifier recommandations
                    foreach ($recommendedCampaigns as $index => $rec) {
                        Log::info("🔍 DEBUG RECOMMANDATION {$index}", [
                            'id' => $rec['id'] ?? 'null',
                            'title' => $rec['title'] ?? 'null',
                            'category' => $rec['category'] ?? 'null',
                            'score' => $rec['recommendation_score'] ?? 'null'
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Erreur recommandations', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $recommendedCampaigns = $this->getFallbackRecommendations();
                    $recommendedCampaignIds = array_column($recommendedCampaigns, 'id');
                }
            } else {
                Log::info('❌ Non connecté - Recommandations populaires par défaut');
                $recommendedCampaigns = $this->getFallbackRecommendations();
                $recommendedCampaignIds = array_column($recommendedCampaigns, 'id');
            }

            // Récupération et filtrage des campagnes principales
            $search = $request->query('search', '');
            $category = $request->query('category', 'all');
            $status = $request->query('status', 'all');

            $query = Campaign::query()
                ->with('creator')
                ->withCount(['likes', 'comments']) // ✅ Retirer 'views' de withCount
                ->where('status', '!=', 'draft')
                ->whereNotIn('id', $recommendedCampaignIds) // ✅ Exclure les campagnes recommandées
                ->orderBy('created_at', 'desc');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if ($category !== 'all') {
                $query->where('category', $category);
            }

            if ($status !== 'all') {
                $today = Carbon::today();
                if ($status === 'upcoming') {
                    $query->where('start_date', '>', $today);
                } elseif ($status === 'active') {
                    $query->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today);
                } elseif ($status === 'ended') {
                    $query->where('end_date', '<', $today);
                }
            }

            // ✅ Récupérer campagnes avec pagination
            $campaigns = $query->paginate(6);

            // ✅ Ajouter comptage manuel des vues
            $campaigns->getCollection()->transform(function ($campaign) {
                $campaign->views_count = CampaignView::where('campaign_id', $campaign->id)->count();
                return $campaign;
            });

            // ✅ DEBUG : Vérifier les données
            Log::info('🔍 DEBUG CAMPAGNES - Données brutes', [
                'total_campaigns' => $campaigns->total(),
                'current_page' => $campaigns->currentPage(),
                'campaigns_count' => $campaigns->count(),
                'has_campaigns' => $campaigns->count() > 0,
                'search' => $search,
                'category' => $category,
                'status' => $status,
                'excluded_recommended_ids' => $recommendedCampaignIds,
                'query_sql' => $query->toSql(),
                'query_bindings' => $query->getBindings()
            ]);

            // DEBUG : Vérifier chaque campagne
            foreach ($campaigns as $index => $campaign) {
                Log::info("🔍 DEBUG CAMPAGNE {$index}", [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'status' => $campaign->status,
                    'category' => $campaign->category,
                    'likes_count' => $campaign->likes_count,
                    'views_count' => $campaign->views_count,
                    'comments_count' => $campaign->comments_count,
                    'has_creator' => !is_null($campaign->creator)
                ]);
            }

            // Préparer données pour JS
            $campaignsForJs = $campaigns->map(function ($campaign) use ($user) {
                $isLiked = $user ? CampaignLike::where('campaign_id', $campaign->id)
                    ->where('user_id', $user->id)->exists() : false;

                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'content' => strip_tags($campaign->content),
                    'category' => $campaign->category,
                    'status' => $this->calculateStatus($campaign->start_date, $campaign->end_date),
                    'start_date' => $campaign->start_date->format('d/m/Y'),
                    'end_date' => $campaign->end_date->format('d/m/Y'),
                    'views_count' => $campaign->views_count ?? 0,
                    'likes_count' => $campaign->likes_count ?? 0,
                    'comments_count' => $campaign->comments_count ?? 0,
                    'sentiment_stats' => $this->getCampaignSentimentStats($campaign->id),
                    'thumbnail' => $this->getCampaignImageUrl($campaign),
                    'created_at' => $campaign->created_at->format('d/m/Y H:i'),
                    'is_liked' => $isLiked,
                    'creator' => $campaign->creator ? [
                        'name' => $campaign->creator->name,
                        'email' => $campaign->creator->email
                    ] : null
                ];
            })->toArray();

            Log::info('🎯 INDEX COMPLET - Préparation vue', [
                'total_campaigns' => $campaigns->total(),
                'recommended_count' => count($recommendedCampaigns),
                'user_authenticated' => (bool)$user,
                'campaigns_for_js_count' => count($campaignsForJs)
            ]);

            return view('pages.frontOffice.campaigns.All', [
                'campaigns' => $campaigns,
                'recommendedCampaigns' => $recommendedCampaigns,
                'campaignsForJs' => $campaignsForJs,
                'search' => $search,
                'category' => $category,
                'status' => $status,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('💥 ERREUR CRITIQUE - Chargement campagnes', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback simple
            $fallbackCampaigns = Campaign::where('status', '!=', 'draft')
                ->orderBy('created_at', 'desc')
                ->paginate(6);

            // Ajouter comptage manuel des vues
            $fallbackCampaigns->getCollection()->transform(function ($campaign) {
                $campaign->views_count = CampaignView::where('campaign_id', $campaign->id)->count();
                $campaign->likes_count = CampaignLike::where('campaign_id', $campaign->id)->count();
                $campaign->comments_count = CampaignComment::where('campaign_id', $campaign->id)->count();
                return $campaign;
            });

            return view('pages.frontOffice.campaigns.All', [
                'campaigns' => $fallbackCampaigns,
                'recommendedCampaigns' => $this->getFallbackRecommendations(),
                'campaignsForJs' => [],
                'search' => $request->query('search', ''),
                'category' => 'all',
                'status' => 'all',
                'error' => 'Une erreur est survenue lors du chargement des campagnes.'
            ]);
        }
    }









    /**
     * 🆕 Récupérer l'utilisateur authentifié via multiple méthodes
     */
    private function getAuthenticatedUser(Request $request)
    {
        // ✅ SIMPLIFICATION : Utiliser directement l'utilisateur du middleware
        $user = $request->auth;

        if ($user) {
            Log::debug('✅ Utilisateur récupéré via middleware VerifyJWT', ['user_id' => $user->id]);

            // S'assurer que l'utilisateur est aussi disponible dans Auth pour la compatibilité
            if (!Auth::check()) {
                Auth::setUser($user);
            }
        } else {
            Log::debug('❌ Aucun utilisateur authentifié trouvé via middleware');
        }

        return $user;
    }









    /**
     * 🆕 Méthode de fallback pour récupérer l'utilisateur via le token
     */
    private function getUserFromToken(Request $request)
    {
        try {
            $token = $request->bearerToken()
                ?? $request->cookie('jwt_token')
                ?? $request->header('X-JWT-Token');

            if (!$token) {
                Log::debug('🔐 Aucun token JWT trouvé');
                return null;
            }

            Log::debug('🔐 Tentative décodage token manuel', ['token_present' => true]);

            // Décodage manuel du token JWT
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                Log::warning('🔐 Format token invalide');
                return null;
            }

            $payload = json_decode(base64_decode($parts[1]), true);
            if (!$payload || !isset($payload['sub'])) {
                Log::warning('🔐 Payload token invalide', ['payload' => $payload]);
                return null;
            }

            $userId = $payload['sub'];
            $user = \App\Models\User::find($userId);

            if ($user) {
                Log::info('🔐 Utilisateur récupéré via token manuel', ['user_id' => $user->id]);
                // Connecter l'utilisateur dans Auth pour les requêtes suivantes
                Auth::setUser($user);
            }

            return $user;

        } catch (\Exception $e) {
            Log::error('🔐 Erreur décodage token manuel', [
                'error' => $e->getMessage(),
                'token_preview' => $token ? substr($token, 0, 50) . '...' : 'null'
            ]);
            return null;
        }
    }

    /**
     * 🆕 Déterminer la méthode utilisée pour récupérer l'utilisateur
     */
    private function getUserMethod($user, Request $request)
    {
        if (!$user) return 'none';

        if ($user === JWTAuth::user()) return 'jwt';
        if ($user === Auth::user()) return 'auth';
        if ($user === $request->auth) return 'request_auth';

        return 'manual_token';
    }

    /**
     * 🆕 Obtenir les recommandations personnalisées avec cache
     */
    public function recommendations(Request $request)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json(['error' => 'Utilisateur non authentifié'], 401);
            }

            $cacheKey = "recommendations_user_{$user->id}";

            // Vérifier si cache invalide à cause d'interaction récente
            if ($this->hasRecentUserInteraction($user->id)) {
                Cache::forget($cacheKey);
                Cache::forget("user_interactions_{$user->id}");
            }

            $recommendedCampaigns = Cache::remember($cacheKey, $this->cacheTtl, function () use ($user) {
                return $this->getUserRecommendations($user);
            });

            if (empty($recommendedCampaigns)) {
                Log::warning('⚠️ Recommandations API vides - Fallback', ['user_id' => $user->id]);
                $recommendedCampaigns = $this->getFallbackRecommendations();
            }

            return response()->json([
                'success' => true,
                'campaigns' => $recommendedCampaigns,
                'count' => count($recommendedCampaigns),
                'cache_key' => $cacheKey,
                'last_updated' => Cache::get($cacheKey . '_timestamp', now()->toISOString())
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur recommandations: ' . $e->getMessage(), ['user_id' => $user->id ?? null]);
            // Fallback sans cache
            $user = $this->getAuthenticatedUser($request);
            $fallback = $user ? $this->getUserRecommendations($user) : [];
            return response()->json(['success' => true, 'campaigns' => $fallback, 'count' => count($fallback), 'fallback' => true]);
        }
    }

    /**
     * 🆕 Invalider cache après interaction
     */
    public function invalidateRecommendationsCache(Request $request)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $cacheKey = "recommendations_user_{$user->id}";
            $interactionKey = "user_interactions_{$user->id}";

            Cache::forget($cacheKey);
            Cache::forget($interactionKey);

            // Marquer interaction récente
            Cache::put("user_interaction_trigger_{$user->id}", now(), 300);

            Log::info('Cache recommandations invalidé', ['user_id' => $user->id, 'cache_key' => $cacheKey]);

            return response()->json([
                'success' => true,
                'message' => 'Cache invalidé avec succès',
                'cleared_keys' => [$cacheKey, $interactionKey]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur invalidation cache: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * 🆕 Logique de recommandation basée sur historique
     */
    private function getUserRecommendations($user)
    {
        Log::info('🧠 DÉBUT getUserRecommendations', ['user_id' => $user?->id]);

        // Fallback pour utilisateurs non connectés
        if (!$user) {
            Log::info('🔄 Fallback pour utilisateur non connecté');
            return $this->getFallbackRecommendations();
        }

        $userInteractions = $this->analyzeUserInteractions($user->id);
        $recommendedCampaigns = [];

        if (!empty($userInteractions['preferred_categories'])) {
            Log::info('🎯 Recherche par catégories préférées', [
                'categories' => $userInteractions['preferred_categories']
            ]);

            $today = Carbon::today();
            $campaigns = Campaign::whereIn('category', $userInteractions['preferred_categories'])
                ->whereIn('status', ['active', 'upcoming'])
                ->whereNotIn('id', $userInteractions['interacted_campaigns'])
                ->where(function ($query) use ($today) {
                    $query->where('start_date', '>', $today) // upcoming
                    ->orWhere(function ($q) use ($today) {
                        $q->where('start_date', '<=', $today)
                            ->where('end_date', '>=', $today); // active
                    });
                })
                ->withCount(['likes', 'comments'])
                ->orderByRaw("
                FIELD(category, '" . implode("','", $userInteractions['preferred_categories']) . "') ASC,
                likes_count DESC,
                created_at DESC
            ")
                ->limit(3) // Limiter à 3 campagnes
                ->get();

            // Ajouter comptage manuel des vues
            $campaigns->transform(function ($campaign) {
                $campaign->views_count = CampaignView::where('campaign_id', $campaign->id)->count();
                return $campaign;
            });

            $recommendedCampaigns = $campaigns->map(fn($c) => $this->formatCampaignForRecommendation($c, $user))->toArray();
        }

        // Compléter si moins de 3 campagnes
        if (count($recommendedCampaigns) < 3) {
            $needed = 3 - count($recommendedCampaigns);
            Log::info('➕ Complément nécessaire', ['needed' => $needed]);
            $additional = $this->getSimilarCampaigns($userInteractions, $needed);
            $recommendedCampaigns = array_merge($recommendedCampaigns, $additional);
        }

        // Fallback ultime
        if (empty($recommendedCampaigns)) {
            Log::warning('⚠️ Aucune recommandation - Fallback populaire');
            $recommendedCampaigns = $this->getFallbackRecommendations();
        }

        // Mélanger et limiter à 3
        shuffle($recommendedCampaigns);
        $final = array_slice($recommendedCampaigns, 0, 3);

        Log::info('✅ FIN getUserRecommendations', [
            'user_id' => $user->id,
            'count' => count($final)
        ]);

        return $final;
    }
    /**
     * 🆕 Analyser l'historique des interactions utilisateur
     */
    private function analyzeUserInteractions($userId)
    {
        $cacheKey = "user_interactions_{$userId}";
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userId) {

            $likedCampaigns = CampaignLike::where('user_id', $userId)
                ->pluck('campaign_id')
                ->toArray();

            $recentViews = CampaignView::where('user_id', $userId)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->pluck('campaign_id')
                ->toArray();

            $commentedCampaigns = CampaignComment::where('user_id', $userId)
                ->where('created_at', '>=', Carbon::now()->subDays(90))
                ->pluck('campaign_id')
                ->toArray();

            $interactedCampaigns = array_unique(array_merge(
                $likedCampaigns,
                $recentViews,
                $commentedCampaigns
            ));

            $categories = [];

            // Poids: likes(3) > comments(2) > views(1)
            $interactions = [
                'likes' => $likedCampaigns,
                'comments' => $commentedCampaigns,
                'views' => $recentViews
            ];

            foreach ($interactions as $type => $campaignIds) {
                $weight = $type === 'likes' ? 3 : ($type === 'comments' ? 2 : 1);
                $campaignCategories = Campaign::whereIn('id', $campaignIds)
                    ->pluck('category')
                    ->groupBy('category')
                    ->map(fn($items) => count($items) * $weight)
                    ->toArray();

                foreach ($campaignCategories as $cat => $score) {
                    $categories[$cat] = ($categories[$cat] ?? 0) + $score;
                }
            }

            arsort($categories);
            $preferredCategories = array_keys(array_slice($categories, 0, 3, true));

            $result = [
                'preferred_categories' => $preferredCategories,
                'interacted_campaigns' => $interactedCampaigns,
                'scores' => $categories,
                'interaction_counts' => [
                    'likes' => count($likedCampaigns),
                    'views' => count($recentViews),
                    'comments' => count($commentedCampaigns)
                ],
                'last_analyzed' => now()
            ];

            Log::info('Analyse interactions utilisateur terminée', [
                'user_id' => $userId,
                'preferred_categories' => $preferredCategories,
                'total_interactions' => count($interactedCampaigns)
            ]);

            return $result;
        });
    }

    /**
     * 🆕 Vérifier interaction récente pour invalidation cache
     */
    private function hasRecentUserInteraction($userId)
    {
        $recentTime = Carbon::now()->subMinutes(5);
        return CampaignLike::where('user_id', $userId)
                ->where('created_at', '>=', $recentTime)
                ->exists() ||
            CampaignView::where('user_id', $userId)
                ->where('created_at', '>=', $recentTime)
                ->exists() ||
            CampaignComment::where('user_id', $userId)
                ->where('updated_at', '>=', $recentTime)
                ->exists();
    }

    /**
     * 🆕 Obtenir campagnes similaires
     */
    /**
     * 🆕 Obtenir campagnes similaires
     */
    private function getSimilarCampaigns($userInteractions, $limit = 3)
    {
        Log::info('🔍 Recherche campagnes similaires', ['limit' => $limit]);

        $today = Carbon::today();
        $query = Campaign::whereIn('status', ['active', 'upcoming'])
            ->whereNotIn('id', $userInteractions['interacted_campaigns'])
            ->where(function ($query) use ($today) {
                $query->where('start_date', '>', $today) // upcoming
                ->orWhere(function ($q) use ($today) {
                    $q->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today); // active
                });
            })
            ->withCount(['likes', 'comments']);

        if (!empty($userInteractions['preferred_categories'])) {
            $query->whereIn('category', $userInteractions['preferred_categories']);
        }

        $campaigns = $query->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        // Ajouter comptage manuel des vues
        $campaigns->transform(function ($campaign) {
            $campaign->views_count = CampaignView::where('campaign_id', $campaign->id)->count();
            return $campaign;
        });

        return $campaigns->map(fn($c) => $this->formatCampaignForRecommendation($c, Auth::user()))->toArray();
    }

    /**
     * Calculer le statut en fonction des dates
     */
    private function calculateStatus($startDate, $endDate)
    {
        $today = Carbon::today();
        if ($startDate->gt($today)) {
            return 'upcoming';
        } elseif ($endDate->lt($today)) {
            return 'ended';
        } else {
            return 'active';
        }
    }

    /**
     * Récupérer les stats de sentiment rapides pour une campagne
     */
    private function getCampaignSentimentStats($campaignId)
    {
        try {
            $stats = CampaignCommentSentiment::where('campaign_id', $campaignId)
                ->selectRaw('
                    COUNT(*) as total_sentiments,
                    AVG(overall_sentiment_score) as avg_score,
                    SUM(CASE WHEN overall_sentiment_score > 0 THEN 1 ELSE 0 END) as positive_count,
                    COUNT(DISTINCT CASE WHEN detected_language = "tunisian" THEN 1 END) as tunisian_count
                ')
                ->first();

            return [
                'total' => $stats->total_sentiments ?? 0,
                'avg_score' => round($stats->avg_score ?? 0, 2),
                'positive_ratio' => $stats->total_sentiments > 0 ? round(($stats->positive_count / $stats->total_sentiments) * 100, 1) : 0,
                'tunisian_ratio' => $stats->total_sentiments > 0 ? round(($stats->tunisian_count / $stats->total_sentiments) * 100, 1) : 0
            ];
        } catch (\Exception $e) {
            Log::warning('Erreur stats sentiment rapides', ['campaign_id' => $campaignId, 'error' => $e->getMessage()]);
            return ['total' => 0, 'avg_score' => 0, 'positive_ratio' => 0, 'tunisian_ratio' => 0];
        }
    }

    /**
     * Afficher les détails d'une campagne
     */
    public function show(Request $request, Campaign $campaign)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if ($user) {
                Log::info('Utilisateur authentifié pour incrémenter les vues', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'campaign_id' => $campaign->id,
                ]);

                $existingView = CampaignView::where('campaign_id', $campaign->id)
                    ->where('user_id', $user->id)
                    ->where('created_at', '>=', Carbon::now()->subDay())
                    ->exists();

                if (!$existingView) {
                    CampaignView::create([
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                    ]);
                    $campaign->increment('views_count');

                    // 🆕 Tracker vue pour recommandations
                    $this->trackUserView($user->id, $campaign->id);
                }
            }

            $campaign->load('comments.user', 'likes.user');
            $sentimentStats = $this->getCampaignSentimentStats($campaign->id);

            return view('pages.frontOffice.campaigns.Show', compact('campaign', 'user', 'sentimentStats'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails de la campagne: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * 🆕 Tracker vue utilisateur pour recommandations
     */
    private function trackUserView($userId, $campaignId)
    {
        $cacheKey = "user_views_{$userId}";
        $views = Cache::get($cacheKey, []);

        $views[$campaignId] = now()->timestamp;
        if (count($views) > 50) { // Limite pour performance
            array_shift($views);
        }

        Cache::put($cacheKey, $views, $this->viewTtl);
        Cache::put("user_interaction_trigger_{$userId}", now(), 300);
    }

    /**
     * Gérer les likes (API endpoint) + invalider cache
     */
    public function like(Request $request, Campaign $campaign)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour aimer une campagne.'], 401);
            }

            $existingLike = CampaignLike::where('campaign_id', $campaign->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $action = 'unliked';
            } else {
                CampaignLike::create([
                    'campaign_id' => $campaign->id,
                    'user_id' => $user->id,
                ]);
                $action = 'liked';
            }

            $likesCount = $campaign->likes()->count();

            // 🆕 Invalider cache après like
            Cache::forget("recommendations_user_{$user->id}");
            Cache::forget("user_interactions_{$user->id}");
            Cache::put("user_interaction_trigger_{$user->id}", now(), 300);

            Log::info("{$action} campagne", ['user_id' => $user->id, 'campaign_id' => $campaign->id]);

            return response()->json([
                'success' => true,
                'likes_count' => $likesCount,
                'action' => $action,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'action like: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Stocker un nouveau commentaire + ANALYSE SENTIMENT AUTOMATIQUE + cache
     */
    public function storeComment(Request $request, Campaign $campaign)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                Log::warning('Utilisateur non authentifié pour commenter', ['campaign_id' => $campaign->id]);
                return response()->json(['error' => 'Vous devez être connecté pour commenter.'], 401);
            }

            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $comment = CampaignComment::create([
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
                'content' => $request->input('content'),
            ]);

            Log::info('Commentaire ajouté - Analyse sentiment lancée', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
            ]);

            $this->analyzeCommentSentimentAsync($campaign->id, $comment->id, $request->input('content'), $user->id);

            $commentsCount = $campaign->fresh()->comments()->count();

            // 🆕 Invalider cache après commentaire
            Cache::forget("recommendations_user_{$user->id}");
            Cache::forget("user_interactions_{$user->id}");
            Cache::put("user_interaction_trigger_{$user->id}", now(), 300);

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                    'created_at' => $comment->created_at->toISOString(),
                    'sentiment_status' => 'analyzing',
                ],
                'comments_count' => $commentsCount,
                'message' => 'Commentaire publié ! Analyse de sentiment en cours...',
                'sentiment_analyzing' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du commentaire: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'user_id' => $user->id ?? 'N/A',
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Modifier un commentaire + RÉ-ANALYSE SENTIMENT + cache
     */
    public function updateComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if ($comment->user_id !== $user->id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $request->validate(['content' => 'required|string|max:1000']);
            $newContent = $request->input('content');

            $comment->update(['content' => $newContent]);
            $this->analyzeCommentSentimentAsync($campaign->id, $comment->id, $newContent, $user->id);

            // 🆕 Invalider cache
            Cache::forget("recommendations_user_{$user->id}");
            Cache::forget("user_interactions_{$user->id}");

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'sentiment_status' => 'reanalyzing',
                ],
                'message' => 'Commentaire mis à jour !'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur modification commentaire: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Supprimer un commentaire + cache
     */
    public function deleteComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user || ($comment->user_id !== $user->id && $user->role !== 'admin')) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $sentimentDeletedCount = CampaignCommentSentiment::where('campaign_comment_id', $comment->id)->delete();
            $comment->delete();

            $commentsCount = $campaign->fresh()->comments()->count();

            // 🆕 Invalider cache
            if ($user) {
                Cache::forget("recommendations_user_{$user->id}");
                Cache::forget("user_interactions_{$user->id}");
            }

            return response()->json([
                'success' => true,
                'comments_count' => $commentsCount,
                'sentiment_deleted' => $sentimentDeletedCount > 0
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression commentaire: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Gérer le like/dé-like d'un commentaire + cache
     */
    public function likeComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            // ✅ CORRECTION : Utiliser la même méthode que index()
            $user = $this->getAuthenticatedUser($request);

            if (!$user) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }

            $existingLike = CommentLike::where('comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                $existingLike->delete();
                $action = 'unliked';
            } else {
                CommentLike::create([
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                ]);
                $action = 'liked';
            }

            $likesCount = $comment->likes()->count();

            // 🆕 Invalider cache (comment likes influencent moins directement)
            Cache::put("user_interaction_trigger_{$user->id}", now(), 300);

            return response()->json([
                'success' => true,
                'likes_count' => $likesCount,
                'action' => $action,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur like commentaire: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Récupérer les sentiments d'une campagne
     */
    public function getCampaignSentiments(Request $request, Campaign $campaign)
    {
        try {
            $sentiments = CampaignCommentSentiment::where('campaign_id', $campaign->id)
                ->with(['comment.user:id,name'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $stats = CampaignCommentSentiment::where('campaign_id', $campaign->id)
                ->selectRaw('
                    COUNT(*) as total,
                    AVG(overall_sentiment_score) as avg_score,
                    SUM(CASE WHEN overall_sentiment_score > 0 THEN 1 ELSE 0 END) as positive,
                    SUM(CASE WHEN overall_sentiment_score < 0 THEN 1 ELSE 0 END) as negative
                ')
                ->first();

            $sentimentsData = $sentiments->map(function ($sentiment) {
                return [
                    'id' => $sentiment->id,
                    'comment_id' => $sentiment->campaign_comment_id,
                    'content' => substr($sentiment->comment_content, 0, 100) . '...',
                    'user_name' => $sentiment->comment->user->name ?? 'Anonyme',
                    'language' => $sentiment->detected_language,
                    'emotion' => $sentiment->dominant_emotion,
                    'score' => round($sentiment->overall_sentiment_score, 3),
                    'confidence' => round($sentiment->confidence * 100, 1) . '%',
                    'analyzed_at' => $sentiment->created_at->format('d/m H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'sentiments' => $sentimentsData,
                'pagination' => [
                    'current_page' => $sentiments->currentPage(),
                    'total' => $sentiments->total(),
                    'per_page' => $sentiments->perPage(),
                    'last_page' => $sentiments->lastPage()
                ],
                'stats' => [
                    'total_comments' => $stats->total ?? 0,
                    'avg_sentiment' => round($stats->avg_score ?? 0, 3),
                    'positive_ratio' => $stats->total > 0 ? round(($stats->positive / $stats->total) * 100, 1) : 0,
                    'negative_ratio' => $stats->total > 0 ? round(($stats->negative / $stats->total) * 100, 1) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération sentiments campagne: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Analyse sentiment asynchrone
     */
    private function analyzeCommentSentimentAsync($campaignId, $commentId, $content, $userId)
    {
        try {
            $pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:5000/analyze-comment');

            $response = Http::timeout(15)
                ->withOptions(['verify' => false])
                ->post($pythonApiUrl, [
                    'campaign_id' => $campaignId,
                    'comment_id' => $commentId,
                    'content' => $content,
                    'user_id' => $userId
                ]);

            if ($response->successful() && isset($response->json()['data'])) {
                $data = $response->json()['data'];

                \App\Models\CampaignCommentSentiment::updateOrCreate(
                    ['campaign_comment_id' => $commentId],
                    [
                        'campaign_id' => $campaignId,
                        'comment_content' => $content,
                        'overall_sentiment_score' => $data['overall_sentiment_score'] ?? 0,
                        'positive' => $data['positive'] ?? 0,
                        'negative' => $data['negative'] ?? 0,
                        'neutral' => $data['neutral'] ?? 0,
                        'dominant_emotion' => $data['dominant_emotion'] ?? 'neutral',
                        'confidence' => $data['confidence'] ?? 0.5,
                        'detected_language' => $data['detected_language'] ?? 'unknown',
                        'joy' => $data['joy'] ?? 0,
                        'anger' => $data['anger'] ?? 0,
                        'sadness' => $data['sadness'] ?? 0,
                        'fear' => $data['fear'] ?? 0,
                        'surprise' => $data['surprise'] ?? 0,
                        'disgust' => $data['disgust'] ?? 0,
                        'trust' => $data['trust'] ?? 0,
                        'anticipation' => $data['anticipation'] ?? 0,
                        'raw_scores' => json_encode($data['raw_scores'] ?? []),
                        'matched_words' => json_encode($data['matched_words'] ?? []),
                        'analysis_method' => 'python_api'
                    ]
                );
            } else {
                // Fallback
                \App\Models\CampaignCommentSentiment::updateOrCreate(
                    ['campaign_comment_id' => $commentId],
                    [
                        'campaign_id' => $campaignId,
                        'comment_content' => $content,
                        'overall_sentiment_score' => 0,
                        'positive' => 0.33, 'negative' => 0.33, 'neutral' => 0.34,
                        'dominant_emotion' => 'neutral',
                        'confidence' => 0.5,
                        'detected_language' => 'fallback',
                        'analysis_method' => 'emergency_fallback'
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur analyse sentiment: ' . $e->getMessage());
        }
    }

    /**
     * Helper pour obtenir l'URL de l'image
     */
    private function getCampaignImageUrl($campaign)
    {
        if (!empty($campaign->media_urls['images']) &&
            is_array($campaign->media_urls['images']) &&
            isset($campaign->media_urls['images'][0])) {

            $imagePath = $campaign->media_urls['images'][0];
            return Storage::disk('public')->exists($imagePath)
                ? Storage::url($imagePath)
                : asset('assets/images/home6/placeholder.jpg');
        }

        return asset('assets/images/home6/placeholder.jpg');
    }

    /**
     * 🆕 Fallback : TOP campagnes populaires (GARANTIT BADGES VISIBLES)
     */
    private function getFallbackRecommendations()
    {
        Log::info('🔄 Fallback: Recommandations populaires activées');

        $today = Carbon::today();
        $campaigns = Campaign::whereIn('status', ['active', 'upcoming'])
            ->where(function ($query) use ($today) {
                $query->where('start_date', '>', $today) // upcoming
                ->orWhere(function ($q) use ($today) {
                    $q->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today); // active
                });
            })
            ->withCount(['likes', 'comments'])
            ->orderBy('likes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3) // Limiter à 3 campagnes
            ->get();

        // Ajouter comptage manuel des vues
        $campaigns->transform(function ($campaign) {
            $campaign->views_count = CampaignView::where('campaign_id', $campaign->id)->count();
            return $campaign;
        });

        return $campaigns->map(fn($c) => $this->formatCampaignForRecommendation($c, null))->toArray();
    }

    private function formatCampaignForRecommendation($campaign, $user = null)
    {
        $isLiked = $user ? CampaignLike::where('campaign_id', $campaign->id)
            ->where('user_id', $user->id)->exists() : false;

        $score = $this->calculateRecommendationScore($campaign, $user);

        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'description' => Str::limit(strip_tags($campaign->content), 150),
            'category' => $campaign->category,
            'status' => $this->calculateStatus($campaign->start_date, $campaign->end_date),
            'image_url' => $this->getCampaignImageUrl($campaign),
            'views_count' => $campaign->views_count ?? 0,
            'likes_count' => $campaign->likes_count ?? 0,
            'comments_count' => $campaign->comments_count ?? 0,
            'is_liked' => $isLiked,
            'recommendation_score' => $score,
            'start_date' => $campaign->start_date->format('Y-m-d'), // Format ISO pour compatibilité
            'end_date' => $campaign->end_date->format('Y-m-d'), // Format ISO pour compatibilité
            'created_at' => $campaign->created_at->toISOString(),
            'debug' => ['score_raw' => $score]
        ];
    }

    /**
     * 🆕 Score de recommandation amélioré
     */
    private function calculateRecommendationScore($campaign, $user)
    {
        $likes = $campaign->likes_count ?? 0;
        $views = $campaign->views_count ?? 0;
        $comments = $campaign->comments()->count();

        // Score de base
        $score = ($likes * 0.4) + ($views * 0.3) + ($comments * 0.3);

        // Bonus fraîcheur (7 derniers jours)
        if ($campaign->created_at->gt(Carbon::now()->subDays(7))) {
            $score *= 1.5;
        }

        // Bonus si utilisateur a déjà interagi
        if ($user && CampaignLike::where('campaign_id', $campaign->id)->where('user_id', $user->id)->exists()) {
            $score *= 2;
        }

        return max(round($score, 1), 1.0); // ✅ MINIMUM 1.0 pour badge visible
    }

    /**
     * Filtrer les campagnes pour l'API
     */
    public function filter(Request $request)
    {
        try {
            $category = $request->input('category', 'all');
            $status = $request->input('status', 'all');

            $query = Campaign::query()
                ->with('creator')
                ->where('status', '!=', 'draft')
                ->orderBy('created_at', 'desc');

            if ($category !== 'all') {
                $query->where('category', $category);
            }

            if ($status !== 'all') {
                $today = Carbon::today();
                if ($status === 'upcoming') {
                    $query->where('start_date', '>', $today);
                } elseif ($status === 'active') {
                    $query->where('start_date', '<=', $today)
                        ->where('end_date', '>=', $today);
                } elseif ($status === 'ended') {
                    $query->where('end_date', '<', $today);
                }
            }

            // ✅ CORRECTION : Utiliser paginate() au lieu de get()
            $campaigns = $query->paginate(6); // ou le nombre d'éléments par page que vous voulez

            $campaignsData = $campaigns->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'content' => strip_tags($campaign->content),
                    'category' => $campaign->category,
                    'status' => $this->calculateStatus($campaign->start_date, $campaign->end_date),
                    'start_date' => $campaign->start_date->format('d/m/Y'),
                    'end_date' => $campaign->end_date->format('d/m/Y'),
                    'views_count' => $campaign->views_count,
                    'likes_count' => $campaign->likes_count,
                    'comments_count' => $campaign->comments()->count(),
                    'sentiment_stats' => $this->getCampaignSentimentStats($campaign->id),
                    'thumbnail' => $this->getCampaignImageUrl($campaign),
                ];
            });

            return response()->json([
                'success' => true,
                'campaigns' => $campaignsData,
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'per_page' => $campaigns->perPage(),
                    'total' => $campaigns->total(),
                    'next_page_url' => $campaigns->nextPageUrl(),
                    'prev_page_url' => $campaigns->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du filtrage des campagnes via API: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }



















}
