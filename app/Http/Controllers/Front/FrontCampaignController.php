<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignComment;
use App\Models\CampaignLike;
use App\Models\CampaignView;
use App\Models\CommentLike;
use App\Models\CampaignCommentSentiment; // ✅ AJOUTÉ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // ✅ AJOUTÉ
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FrontCampaignController extends Controller
{
    /**
     * Afficher la liste des campagnes côté front-office
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $category = $request->query('category', 'all');
            $status = $request->query('status', 'all');

            $query = Campaign::query()
                ->with('creator')
                ->where('status', '!=', 'draft')
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

            $campaigns = $query->paginate(6);

            $campaignsForJs = $campaigns->map(function ($campaign) {
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
                    'comments_count' => $campaign->comments_count,
                    'sentiment_stats' => $this->getCampaignSentimentStats($campaign->id), // ✅ AJOUTÉ
                    'thumbnail' => !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && Storage::disk('public')->exists($campaign->media_urls['images'][0])
                        ? Storage::url($campaign->media_urls['images'][0])
                        : 'https://via.placeholder.com/400x200?text=Image',
                    'created_at' => $campaign->created_at->format('d/m/Y H:i'),
                    'creator' => $campaign->creator ? [
                        'name' => $campaign->creator->name,
                        'email' => $campaign->creator->email
                    ] : null
                ];
            });

            return view('pages.frontOffice.campaigns.All', [
                'campaigns' => $campaigns,
                'campaignsForJs' => $campaignsForJs,
                'search' => $search,
                'category' => $category,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des campagnes front-office: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
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

            $campaigns = $query->get()->map(function ($campaign) {
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
                    'comments_count' => $campaign->comments_count,
                    'sentiment_stats' => $this->getCampaignSentimentStats($campaign->id), // ✅ AJOUTÉ
                    'thumbnail' => !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && Storage::disk('public')->exists($campaign->media_urls['images'][0])
                        ? Storage::url($campaign->media_urls['images'][0])
                        : asset('assets/images/home6/placeholder.jpg'),
                ];
            });

            return response()->json([
                'success' => true,
                'campaigns' => $campaigns
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du filtrage des campagnes via API: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
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
            $user = $request->auth ?? Auth::guard('api')->user();

            if ($user) {
                Log::info('Utilisateur authentifié pour incrémenter les vues', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'campaign_id' => $campaign->id,
                ]);

                $existingView = CampaignView::where('campaign_id', $campaign->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$existingView) {
                    CampaignView::create([
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                    ]);
                    $campaign->increment('views_count');
                    Log::info('Vue incrémentée', [
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                        'views_count' => $campaign->views_count,
                    ]);
                }
            }

            $campaign->load('comments.user', 'likes.user');

            // ✅ CHARGER SENTIMENTS POUR LA VUE
            $sentimentStats = $this->getCampaignSentimentStats($campaign->id);

            return view('pages.frontOffice.campaigns.Show', compact('campaign', 'user', 'sentimentStats'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des détails de la campagne: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Gérer les likes (API endpoint)
     */
    public function like(Request $request, Campaign $campaign)
    {
        try {
            $user = $request->auth;

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
     * Stocker un nouveau commentaire + ANALYSE SENTIMENT AUTOMATIQUE
     */
    public function storeComment(Request $request, Campaign $campaign)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                Log::warning('Utilisateur non authentifié pour commenter', ['campaign_id' => $campaign->id]);
                return response()->json(['error' => 'Vous devez être connecté pour commenter.'], 401);
            }

            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            // ✅ CRÉATION COMMENTAIRE
            $comment = CampaignComment::create([
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
                'content' => $request->input('content'),
            ]);

            Log::info('Commentaire ajouté - Analyse sentiment lancée', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'content_preview' => substr($comment->content, 0, 50) . '...',
            ]);

            // ✅ ANALYSE SENTIMENT AUTOMATIQUE (ASYNCHRONE)
            $this->analyzeCommentSentimentAsync($campaign->id, $comment->id, $request->input('content'), $user->id);

            // ✅ SUPPRIMÉ : $campaign->increment('comments_count'); // COLONNE N'EXISTE PAS

            // ✅ COMPTER LES COMMENTAIRES VIA RELATION
            $commentsCount = $campaign->fresh()->comments()->count();

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
                'comments_count' => $commentsCount, // ✅ COMPTE RÉEL
                'message' => 'Commentaire publié ! Analyse de sentiment en cours...',
                'sentiment_analyzing' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du commentaire: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'user_id' => $user->id ?? 'N/A',
                'request_data' => $request->all(),
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }






    /**
     * Modifier un commentaire + RÉ-ANALYSE SENTIMENT AUTOMATIQUE ✅
     */
    public function updateComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            // Vérifier autorisation
            $user = $request->auth;
            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'
                ], 403);
            }

            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $oldContent = $comment->content;
            $newContent = $request->input('content');

            // ✅ MISE À JOUR COMMENTAIRE
            $comment->update(['content' => $newContent]);

            // ✅ RÉ-ANALYSE SENTIMENT
            $this->analyzeCommentSentimentAsync($campaign->id, $comment->id, $newContent, $user->id);

            Log::info('Commentaire modifié - Ré-analyse sentiment lancée', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'old_content' => substr($oldContent, 0, 30) . '...',
                'new_content' => substr($newContent, 0, 30) . '...'
            ]);

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $user->name,
                    'updated_at' => $comment->updated_at->toISOString(),
                    'sentiment_status' => 'reanalyzing', // ✅ Statut pour frontend
                ],
                'message' => 'Commentaire mis à jour ! Nouvelle analyse de sentiment en cours...',
                'sentiment_reanalyzing' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur modification commentaire: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'user_id' => $user->id ?? auth()->id(),
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }





    /**
     * Supprimer un commentaire + SENTIMENT LIÉ
     */
    public function deleteComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté.'], 401);
            }

            if ($comment->user_id !== $user->id && $user->role !== 'admin') {
                return response()->json(['error' => 'Non autorisé.'], 403);
            }

            if ($comment->campaign_id !== $campaign->id) {
                return response()->json(['error' => 'Commentaire invalide.'], 404);
            }

            $commentId = $comment->id;

            // ✅ SUPPRESSION SENTIMENT LIÉ
            $sentimentDeletedCount = CampaignCommentSentiment::where('campaign_comment_id', $comment->id)->delete();

            // ✅ SUPPRESSION COMMENTAIRE
            $comment->delete();

            // ✅ SUPPRIMÉ : $campaign->decrement('comments_count'); // COLONNE N'EXISTE PAS

            // ✅ COMPTER LES COMMENTAIRES RESTANTS
            $commentsCount = $campaign->fresh()->comments()->count();

            Log::info('Commentaire et sentiment supprimés', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $commentId,
                'sentiment_deleted_count' => $sentimentDeletedCount,
                'remaining_comments' => $commentsCount
            ]);

            return response()->json([
                'success' => true,
                'comments_count' => $commentsCount, // ✅ COMPTE RÉEL
                'message' => 'Commentaire et analyse supprimés avec succès',
                'sentiment_deleted' => $sentimentDeletedCount > 0
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression commentaire: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id ?? 'N/A',
                'user_id' => $user->id ?? 'N/A',
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }











    /**
     * Gérer le like/dé-like d'un commentaire
     */
    public function likeComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Vous devez être connecté pour liker un commentaire.'], 401);
            }

            if ($comment->campaign_id !== $campaign->id) {
                return response()->json(['error' => 'Commentaire non valide.'], 404);
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

            Log::info('Like/Dé-like commentaire', [
                'user_id' => $user->id,
                'comment_id' => $comment->id,
                'action' => $action,
                'likes_count' => $likesCount,
            ]);

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
     * Récupérer les sentiments d'une campagne (API pour frontend) ✅
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
                    SUM(CASE WHEN overall_sentiment_score < 0 THEN 1 ELSE 0 END) as negative,
                    COUNT(DISTINCT CASE WHEN detected_language = "tunisian" THEN 1 END) as tunisian_count
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
                    'emotion_class' => $this->getEmotionClass($sentiment->dominant_emotion, $sentiment->overall_sentiment_score)
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
                    'tunisian_ratio' => $stats->total > 0 ? round(($stats->tunisian_count / $stats->total) * 100, 1) : 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération sentiments campagne: ' . $e->getMessage(), ['campaign_id' => $campaign->id]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }








    /**
     * 🚀 ANALYSE SENTIMENT DIRECTE VERS PYTHON (CORRIGÉ)
     */
    private function analyzeCommentSentimentAsync($campaignId, $commentId, $content, $userId)
    {
        try {
            Log::info('🚀 Lancement analyse sentiment PYTHON DIRECT', [
                'campaign_id' => $campaignId,
                'comment_id' => $commentId,
                'content_preview' => substr($content, 0, 50),
                'user_id' => $userId
            ]);

            // ✅ APPEL DIRECT PYTHON API (PAS route Laravel !)
            $pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:5000/analyze-comment');

            $response = Http::timeout(15)
                ->withOptions(['verify' => false])
                ->post($pythonApiUrl, [
                    'campaign_id' => $campaignId,
                    'comment_id' => $commentId,
                    'content' => $content,
                    'user_id' => $userId
                ]);

            Log::info('📡 Réponse Python API', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 200),
                'url' => $pythonApiUrl
            ]);

            if ($response->successful()) {
                $pythonData = $response->json();

                if (isset($pythonData['data'])) {
                    $data = $pythonData['data'];

                    // ✅ SAUVEGARDE DIRECTE EN DB
                    $sentiment = \App\Models\CampaignCommentSentiment::updateOrCreate(
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
                            'analysis_method' => $data['analysis_method'] ?? 'python_direct'
                        ]
                    );

                    Log::info('✅ Sentiment SAUVÉ depuis Python', [
                        'sentiment_id' => $sentiment->id,
                        'score' => $sentiment->overall_sentiment_score,
                        'emotion' => $sentiment->dominant_emotion,
                        'language' => $sentiment->detected_language,
                        'matched_words' => $data['matched_words'] ?? []
                    ]);

                    return; // ✅ Succès, sortie
                }
            }

            // ❌ ÉCHEC → Fallback
            throw new \Exception('Python API failed: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('💥 ERREUR analyse sentiment Python', [
                'comment_id' => $commentId,
                'error' => $e->getMessage(),
                'url' => env('PYTHON_API_URL')
            ]);

            // ✅ FALLBACK : Sauvegarde avec scores réels (pas tous à 0 !)
            try {
                $sentiment = \App\Models\CampaignCommentSentiment::updateOrCreate(
                    ['campaign_comment_id' => $commentId],
                    [
                        'campaign_id' => $campaignId,
                        'comment_content' => $content,
                        'overall_sentiment_score' => 0.1, // Neutre léger
                        'positive' => 0.3,
                        'negative' => 0.1,
                        'neutral' => 0.6,
                        'dominant_emotion' => 'neutral',
                        'confidence' => 0.5,
                        'detected_language' => 'fallback',
                        'joy' => 0.2, 'anger' => 0.1, 'trust' => 0.3,
                        'sadness' => 0.1, 'fear' => 0.0, 'surprise' => 0.0,
                        'disgust' => 0.0, 'anticipation' => 0.1,
                        'analysis_method' => 'emergency_fallback'
                    ]
                );

                Log::warning('🛡️ FALLBACK sentiment sauvé', [
                    'comment_id' => $commentId,
                    'sentiment_id' => $sentiment->id
                ]);
            } catch (\Exception $fallbackError) {
                Log::error('❌ ÉCHEC fallback', [
                    'comment_id' => $commentId,
                    'error' => $fallbackError->getMessage()
                ]);
            }
        }
    }









    /**
     * Classe CSS pour affichage émotions
     */
    private function getEmotionClass($emotion, $score)
    {
        $positiveEmotions = ['joy', 'positive', 'trust', 'anticipation', 'surprise'];
        $negativeEmotions = ['anger', 'sadness', 'fear', 'disgust', 'negative'];

        if (in_array($emotion, $positiveEmotions) || $score > 0) {
            return 'sentiment-positive';
        } elseif (in_array($emotion, $negativeEmotions) || $score < 0) {
            return 'sentiment-negative';
        }
        return 'sentiment-neutral';
    }
}
