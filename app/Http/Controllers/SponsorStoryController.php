<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SponsorStory;
use App\Models\Event;
use App\Models\SponsorshipTemp;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SponsorStoryController extends Controller
{
    // ==================== AFFICHAGE DES STORIES ====================

    /**
     * Afficher toutes les stories disponibles
     */
    public function index(Request $request)
    {
        try {
            $stories = SponsorStory::available()
                ->with(['sponsor', 'event', 'sponsorship'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return view('pages.backOffice.sponsor-stories.index', compact('stories'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur index', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors du chargement des stories');
        }
    }

    /**
     * Afficher les stories d'un sponsor spécifique
     */
    public function myStories(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }

            $stories = SponsorStory::bySponsor($user->id)
                ->with(['event', 'sponsorship'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $stats = [
                'total_stories' => SponsorStory::bySponsor($user->id)->count(),
                'active_stories' => SponsorStory::bySponsor($user->id)->available()->count(),
                'total_views' => SponsorStory::bySponsor($user->id)->sum('views_count'),
                'total_likes' => SponsorStory::bySponsor($user->id)->sum('likes_count'),
            ];

            return view('pages.backOffice.sponsor-stories.my-stories', compact('stories', 'stats'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur myStories', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors du chargement de vos stories');
        }
    }

    /**
     * Afficher une story spécifique
     */
    public function show(Request $request, $id)
    {
        try {
            $story = SponsorStory::available()
                ->with(['sponsor', 'event', 'sponsorship'])
                ->findOrFail($id);

            // Incrémenter le nombre de vues
            $story->incrementViews();

            return view('pages.backOffice.sponsor-stories.show', compact('story'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur show', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Story non trouvée ou expirée');
        }
    }

    // ==================== CRÉATION ET GESTION ====================

    /**
     * Afficher le formulaire de création de story
     */
    public function create(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }

            // Récupérer les événements sponsorisés du sponsor
            $sponsoredEvents = SponsorshipTemp::where('user_id', $user->id)
                ->where('status', 'approved')
                ->with('event')
                ->get()
                ->pluck('event')
                ->filter()
                ->unique('id');

            return view('pages.backOffice.sponsor-stories.create', compact('sponsoredEvents'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur create', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors du chargement du formulaire');
        }
    }

    /**
     * Créer une nouvelle story
     */
    public function store(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            // Validation flexible
            $rules = [
                'content' => 'required|string|max:500',
                'media_type' => 'required|in:image,video,text',
            ];
            
            // Validation conditionnelle des champs optionnels
            if ($request->has('title')) {
                $rules['title'] = 'nullable|string|max:100';
            }
            
            if ($request->hasFile('media_file')) {
                $rules['media_file'] = 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240';
            }
            
            if ($request->has('event_id')) {
                $rules['event_id'] = 'nullable|exists:events,id';
            }
            
            if ($request->has('sponsorship_id')) {
                $rules['sponsorship_id'] = 'nullable|exists:sponsorships_temp,id';
            }
            
            if ($request->has('background_color')) {
                $rules['background_color'] = 'nullable|string|max:7';
            }
            
            if ($request->has('text_color')) {
                $rules['text_color'] = 'nullable|string|max:7';
            }
            
            if ($request->has('font_size')) {
                $rules['font_size'] = 'nullable|in:small,medium,large';
            }
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $mediaPath = null;
            $mediaUrl = null;

            // Gérer l'upload du fichier média
            if ($request->hasFile('media_file')) {
                $file = $request->file('media_file');
                $mediaPath = $file->store('stories', 'public');
                $mediaUrl = asset('storage/' . $mediaPath);
            }

            // Créer la story
            $story = SponsorStory::create([
                'sponsor_id' => $user->id,
                'event_id' => $request->event_id,
                'sponsorship_id' => $request->sponsorship_id,
                'title' => $request->title,
                'content' => $request->content,
                'media_type' => $request->media_type,
                'media_path' => $mediaPath,
                'media_url' => $mediaUrl,
                'background_color' => $request->background_color ?? '#3498db',
                'text_color' => $request->text_color ?? '#ffffff',
                'font_size' => $request->font_size ?? 'medium',
                'expires_at' => now()->addHours(24),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Story créée avec succès !',
                'data' => [
                    'story' => $story,
                    'redirect_url' => route('sponsor.stories.my-stories')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur store', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all(),
                'user_id' => $user->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la story',
                'error' => $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return redirect()->route('home')->with('error', 'Accès non autorisé.');
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);

            // Vérifier si la story peut encore être modifiée (pas expirée)
            if ($story->is_expired) {
                return redirect()->back()->with('error', 'Cette story a expiré et ne peut plus être modifiée');
            }

            $sponsoredEvents = SponsorshipTemp::where('user_id', $user->id)
                ->where('status', 'approved')
                ->with('event')
                ->get()
                ->pluck('event')
                ->filter()
                ->unique('id');

            return view('pages.backOffice.sponsor-stories.edit', compact('story', 'sponsoredEvents'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur edit', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Story non trouvée');
        }
    }

    /**
     * Mettre à jour une story
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);

            // Vérifier si la story peut encore être modifiée
            if ($story->is_expired) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette story a expiré et ne peut plus être modifiée'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:100',
                'content' => 'required|string|max:500',
                'media_type' => 'required|in:image,video,text',
                'media_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
                'event_id' => 'nullable|exists:events,id',
                'sponsorship_id' => 'nullable|exists:sponsorships_temp,id',
                'background_color' => 'nullable|string|max:7',
                'text_color' => 'nullable|string|max:7',
                'font_size' => 'nullable|in:small,medium,large',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'title' => $request->title,
                'content' => $request->content,
                'media_type' => $request->media_type,
                'event_id' => $request->event_id,
                'sponsorship_id' => $request->sponsorship_id,
                'background_color' => $request->background_color ?? '#3498db',
                'text_color' => $request->text_color ?? '#ffffff',
                'font_size' => $request->font_size ?? 'medium',
            ];

            // Gérer l'upload d'un nouveau fichier média
            if ($request->hasFile('media_file')) {
                // Supprimer l'ancien fichier
                if ($story->media_path) {
                    Storage::disk('public')->delete($story->media_path);
                }

                $file = $request->file('media_file');
                $mediaPath = $file->store('stories', 'public');
                $updateData['media_path'] = $mediaPath;
                $updateData['media_url'] = asset('storage/' . $mediaPath);
            }

            $story->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Story mise à jour avec succès !',
                'data' => ['story' => $story]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur update', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la story'
            ], 500);
        }
    }

    /**
     * Supprimer une story
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);

            // Supprimer le fichier média
            if ($story->media_path) {
                Storage::disk('public')->delete($story->media_path);
            }

            $story->delete();

            return response()->json([
                'success' => true,
                'message' => 'Story supprimée avec succès !'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur destroy', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la story'
            ], 500);
        }
    }

    // ==================== ACTIONS SPÉCIALES ====================

    /**
     * Marquer une story comme en vedette
     */
    public function markAsFeatured(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);

            if ($story->is_expired) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette story a expiré'
                ], 422);
            }

            $story->markAsFeatured();

            return response()->json([
                'success' => true,
                'message' => 'Story mise en vedette !'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur markAsFeatured', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise en vedette'
            ], 500);
        }
    }

    /**
     * Retirer une story de la vedette
     */
    public function unmarkAsFeatured(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);
            $story->unmarkAsFeatured();

            return response()->json([
                'success' => true,
                'message' => 'Story retirée de la vedette !'
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur unmarkAsFeatured', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du retrait de la vedette'
            ], 500);
        }
    }

    /**
     * Prolonger une story (pour les stories en vedette)
     */
    public function extend(Request $request, $id)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $story = SponsorStory::bySponsor($user->id)->findOrFail($id);

            if (!$story->canBeExtended()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette story ne peut pas être prolongée'
                ], 422);
            }

            $hours = $request->get('hours', 24);
            $story->extend($hours);

            return response()->json([
                'success' => true,
                'message' => "Story prolongée de {$hours} heures !"
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur extend', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la prolongation'
            ], 500);
        }
    }

    /**
     * Liker une story
     */
    public function like(Request $request, $id)
    {
        try {
            $story = SponsorStory::available()->findOrFail($id);
            $story->incrementLikes();

            return response()->json([
                'success' => true,
                'message' => 'Story likée !',
                'data' => ['likes_count' => $story->likes_count]
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur like', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du like'
            ], 500);
        }
    }

    // ==================== API ENDPOINTS ====================

    /**
     * API: Obtenir les stories disponibles
     */
    public function apiIndex(Request $request)
    {
        try {
            $stories = SponsorStory::available()
                ->with(['sponsor:id,name,company_name,profile_image', 'event:id,title,date'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $stories
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur apiIndex', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des stories'
            ], 500);
        }
    }

    /**
     * API: Obtenir les statistiques des stories
     */
    public function apiStats(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $stats = [
                'my_stories' => [
                    'total' => SponsorStory::bySponsor($user->id)->count(),
                    'active' => SponsorStory::bySponsor($user->id)->available()->count(),
                    'featured' => SponsorStory::bySponsor($user->id)->featured()->count(),
                    'total_views' => SponsorStory::bySponsor($user->id)->sum('views_count'),
                    'total_likes' => SponsorStory::bySponsor($user->id)->sum('likes_count'),
                ],
                'global_stats' => SponsorStory::getGlobalStats()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur apiStats', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques'
            ], 500);
        }
    }

    // ==================== ADMIN FUNCTIONS ====================

    /**
     * Vue admin: Gérer toutes les stories
     */
    public function adminIndex(Request $request)
    {
        try {
            $stories = SponsorStory::with(['sponsor', 'event'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $stats = SponsorStory::getGlobalStats();

            return view('admin.sponsor-stories.index', compact('stories', 'stats'));
        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur adminIndex', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors du chargement des stories');
        }
    }

    /**
     * Admin: Supprimer les stories expirées
     */
    public function adminCleanupExpired(Request $request)
    {
        try {
            $deletedCount = SponsorStory::deleteExpiredStories();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} stories expirées supprimées !"
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorStoryController: Erreur adminCleanupExpired', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage'
            ], 500);
        }
    }
}