<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignComment;
use App\Models\CampaignLike;
use App\Models\CampaignView;
use App\Models\CommentLike;
use Illuminate\Http\Request;
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
            $status = $request->query('status', 'all'); // New status filter

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
                'status' => $status // Pass status to the view
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
     * Afficher les détails d'une campagne
     */
    public function show(Request $request, Campaign $campaign)
    {
        try {
            // Récupérer l'utilisateur authentifié via le guard 'api'
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
                } else {
                    Log::info('Vue déjà enregistrée pour cet utilisateur', [
                        'campaign_id' => $campaign->id,
                        'user_id' => $user->id,
                    ]);
                }
            } else {
                Log::warning('Aucun utilisateur authentifié pour incrémenter les vues', [
                    'campaign_id' => $campaign->id,
                ]);
            }

            // Charger les commentaires et likes associés
            $campaign->load('comments.user', 'likes.user');
            return view('pages.frontOffice.campaigns.Show', compact('campaign', 'user'));
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
            // Le middleware VerifyJWT a déjà authentifié l'utilisateur et défini $request->auth
            $user = $request->auth;

            if (!$user) {
                Log::warning('Aucun utilisateur authentifié pour liker la campagne', ['campaign_id' => $campaign->id]);
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

            // Utiliser l'accesseur pour obtenir le nombre de likes
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
     * Stocker un nouveau commentaire
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

            $comment = CampaignComment::create([
                'campaign_id' => $campaign->id,
                'user_id' => $user->id,
                'content' => $request->input('content'), // Corrigé : utiliser input('content')
            ]);

            Log::info('Commentaire ajouté', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'content' => $comment->content,
            ]);

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $user->name,
                ],
                'comments_count' => $campaign->comments_count,
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
     * Modifier un commentaire
     */
    public function updateComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            // Vérifier que l'utilisateur est le propriétaire du commentaire
            if ($comment->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'
                ], 403);
            }

            // Valider les données
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            // Mettre à jour le commentaire
            $comment->update([
                'content' => $request->input('content'),
            ]);

            // Journalisation
            Log::info('Commentaire modifié', [
                'user_id' => auth()->id(),
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'content' => $request->input('content')
            ]);

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                ],
                'message' => 'Commentaire modifié avec succès.'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du commentaire: ' . $e->getMessage(), [
                'campaign_id' => $campaign->id,
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Supprimer un commentaire
     */
    public function deleteComment(Request $request, Campaign $campaign, CampaignComment $comment)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                Log::warning('Utilisateur non authentifié pour supprimer un commentaire', [
                    'campaign_id' => $campaign->id,
                    'comment_id' => $comment->id,
                ]);
                return response()->json(['error' => 'Vous devez être connecté pour supprimer un commentaire.'], 401);
            }

            if ($comment->user_id !== $user->id) {
                Log::warning('Utilisateur non autorisé à supprimer le commentaire', [
                    'user_id' => $user->id,
                    'comment_id' => $comment->id,
                ]);
                return response()->json(['error' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'], 403);
            }

            if ($comment->campaign_id !== $campaign->id) {
                Log::warning('Commentaire non valide pour la campagne', [
                    'campaign_id' => $campaign->id,
                    'comment_id' => $comment->id,
                ]);
                return response()->json(['error' => 'Commentaire non valide.'], 404);
            }

            $commentId = $comment->id; // Sauvegarde pour le log
            $comment->delete();

            Log::info('Commentaire supprimé', [
                'user_id' => $user->id,
                'campaign_id' => $campaign->id,
                'comment_id' => $commentId,
            ]);

            return response()->json([
                'success' => true,
                'comments_count' => $campaign->comments_count,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du commentaire: ' . $e->getMessage(), [
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
                Log::warning('Aucun utilisateur authentifié pour liker le commentaire', [
                    'campaign_id' => $campaign->id,
                    'comment_id' => $comment->id,
                ]);
                return response()->json(['error' => 'Vous devez être connecté pour liker un commentaire.'], 401);
            }

            if ($comment->campaign_id !== $campaign->id) {
                Log::warning('Le commentaire n\'appartient pas à la campagne', [
                    'campaign_id' => $campaign->id,
                    'comment_id' => $comment->id,
                ]);
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

            Log::info('Like/Dé-like de commentaire effectué', [
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
            Log::error('Erreur lors du like/dé-like du commentaire: ' . $e->getMessage(), [
                'user_id' => $user->id ?? 'N/A',
                'comment_id' => $comment->id,
                'campaign_id' => $campaign->id,
            ]);
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
}
