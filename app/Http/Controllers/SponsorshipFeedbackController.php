<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SponsorshipFeedback;
use App\Models\FeedbackLike;
use App\Models\Event;
use App\Models\SponsorshipTemp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SponsorshipFeedbackController extends Controller
{
    /**
     * Afficher la page de feedback
     */
    public function showFeedbackPage(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }
        
        return view('pages.backOffice.sponsor-feedback', compact('user'));
    }

    /**
     * Afficher les feedbacks pour un événement
     */
    public function index(Request $request, $eventId = null)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $query = SponsorshipFeedback::with(['user', 'event', 'children', 'likes'])
            ->published()
            ->withCount('likes');

        if ($eventId) {
            $query->forEvent($eventId);
        }

        // Filtres
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('user_id')) {
            $query->forUser($request->user_id);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validation des paramètres de tri
        $allowedSortFields = ['created_at', 'updated_at', 'rating', 'title'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        $feedbacks = $query->paginate($request->get('per_page', 15));

        // Statistiques
        $stats = null;
        if ($eventId) {
            $stats = SponsorshipFeedback::getEventFeedbackStats($eventId);
        }

        return response()->json([
            'success' => true,
            'feedbacks' => $feedbacks,
            'stats' => $stats
        ]);
    }

    /**
     * Créer un nouveau feedback
     */
    public function store(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'sponsorship_temp_id' => 'nullable|exists:sponsorships_temp,id',
            'feedback_type' => 'required|in:pre_event,post_event,package_feedback,organizer_feedback,general_comment,improvement_suggestion,experience_sharing',
            'rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'is_anonymous' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'parent_feedback_id' => 'nullable|exists:sponsorship_feedbacks,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Gérer les fichiers joints
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('feedback-attachments', 'public');
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType()
                    ];
                }
            }

            $feedback = SponsorshipFeedback::create([
                'sponsorship_temp_id' => $request->sponsorship_temp_id,
                'event_id' => $request->event_id,
                'user_id' => $user->id,
                'feedback_type' => $request->feedback_type,
                'rating' => $request->rating,
                'title' => $request->title,
                'content' => $request->content,
                'is_anonymous' => $request->boolean('is_anonymous', false),
                'status' => 'published',
                'parent_feedback_id' => $request->parent_feedback_id,
                'tags' => $request->tags,
                'attachments' => $attachments,
                'metadata' => [
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip()
                ]
            ]);

            $feedback->load(['user', 'event', 'parentFeedback']);

            return response()->json([
                'success' => true,
                'message' => 'Feedback créé avec succès',
                'feedback' => $feedback
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création du feedback: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un feedback spécifique
     */
    public function show(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $feedback = SponsorshipFeedback::with([
            'user', 
            'event', 
            'sponsorshipTemp',
            'parentFeedback',
            'children.user',
            'likes.user'
        ])->findOrFail($id);

        // Vérifier si l'utilisateur peut voir ce feedback
        if ($feedback->status === 'hidden' && $feedback->user_id !== $user->id) {
            return response()->json(['error' => 'Feedback non accessible.'], 403);
        }

        return response()->json([
            'success' => true,
            'feedback' => $feedback
        ]);
    }

    /**
     * Mettre à jour un feedback
     */
    public function update(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $feedback = SponsorshipFeedback::findOrFail($id);

        // Vérifier si l'utilisateur peut modifier ce feedback
        if ($feedback->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez pas modifier ce feedback.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10|max:2000',
            'is_anonymous' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $feedback->update([
                'rating' => $request->rating,
                'title' => $request->title,
                'content' => $request->content,
                'is_anonymous' => $request->boolean('is_anonymous', $feedback->is_anonymous),
                'tags' => $request->tags
            ]);

            $feedback->load(['user', 'event']);

            return response()->json([
                'success' => true,
                'message' => 'Feedback mis à jour avec succès',
                'feedback' => $feedback
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un feedback
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $feedback = SponsorshipFeedback::findOrFail($id);

        // Vérifier si l'utilisateur peut supprimer ce feedback
        if ($feedback->user_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez pas supprimer ce feedback.'], 403);
        }

        try {
            // Supprimer les fichiers joints
            if ($feedback->attachments) {
                foreach ($feedback->attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment['path'])) {
                        Storage::disk('public')->delete($attachment['path']);
                    }
                }
            }

            $feedback->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feedback supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liker/Unliker un feedback
     */
    public function toggleLike(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $feedback = SponsorshipFeedback::findOrFail($id);

        $existingLike = FeedbackLike::where('user_id', $user->id)
            ->where('sponsorship_feedback_id', $id)
            ->first();

        if ($existingLike) {
            // Supprimer le like existant
            $existingLike->delete();
            $action = 'unliked';
        } else {
            // Créer un nouveau like
            FeedbackLike::create([
                'user_id' => $user->id,
                'sponsorship_feedback_id' => $id,
                'is_like' => true
            ]);
            $action = 'liked';
        }

        $likesCount = $feedback->likes()->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $likesCount
        ]);
    }

    /**
     * Obtenir les statistiques de feedback pour un événement
     */
    public function getEventStats(Request $request, $eventId)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $event = Event::findOrFail($eventId);
        $stats = SponsorshipFeedback::getEventFeedbackStats($eventId);

        return response()->json([
            'success' => true,
            'event' => $event,
            'stats' => $stats
        ]);
    }

    /**
     * Obtenir les feedbacks les plus utiles
     */
    public function getMostHelpful(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $limit = $request->get('limit', 10);
        $feedbacks = SponsorshipFeedback::getMostHelpful($limit);

        return response()->json([
            'success' => true,
            'feedbacks' => $feedbacks
        ]);
    }

    /**
     * Obtenir les types de feedback disponibles
     */
    public function getFeedbackTypes(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $types = [
            [
                'value' => 'pre_event',
                'label' => 'Avant l\'événement',
                'description' => 'Partagez vos attentes et questions avant l\'événement',
                'icon' => 'fas fa-calendar-check'
            ],
            [
                'value' => 'post_event',
                'label' => 'Après l\'événement',
                'description' => 'Donnez votre avis sur votre expérience après l\'événement',
                'icon' => 'fas fa-calendar-times'
            ],
            [
                'value' => 'package_feedback',
                'label' => 'Feedback sur le package',
                'description' => 'Commentez la valeur et les bénéfices du package choisi',
                'icon' => 'fas fa-box'
            ],
            [
                'value' => 'organizer_feedback',
                'label' => 'Feedback sur l\'organisateur',
                'description' => 'Évaluez la qualité de l\'organisation et de la communication',
                'icon' => 'fas fa-user-tie'
            ],
            [
                'value' => 'general_comment',
                'label' => 'Commentaire général',
                'description' => 'Partagez vos pensées générales sur l\'événement',
                'icon' => 'fas fa-comment'
            ],
            [
                'value' => 'improvement_suggestion',
                'label' => 'Suggestion d\'amélioration',
                'description' => 'Proposez des améliorations pour de futurs événements',
                'icon' => 'fas fa-lightbulb'
            ],
            [
                'value' => 'experience_sharing',
                'label' => 'Partage d\'expérience',
                'description' => 'Partagez votre expérience avec d\'autres sponsors',
                'icon' => 'fas fa-share-alt'
            ]
        ];

        return response()->json([
            'success' => true,
            'types' => $types
        ]);
    }

    /**
     * Obtenir les événements disponibles pour les feedbacks
     */
    public function getEvents(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        // Récupérer tous les événements publiés pour l'instant
        $events = Event::where('status', 'published')
            ->select('id', 'title', 'date as start_date', 'date as end_date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events
        ]);
    }

    /**
     * Obtenir les sponsorships de l'utilisateur pour les feedbacks
     */
    public function getUserSponsorships(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $sponsorships = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['event:id,title', 'package:id,name'])
            ->select('id', 'event_id', 'package_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'sponsorships' => $sponsorships
        ]);
    }
}
