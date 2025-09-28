<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignComment; // Ajout du modèle Comment
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CampaignExport;

class CampaignController extends Controller
{
    /**
     * Afficher la liste des campagnes
     */
    public function index()
    {
        try {
            $campaigns = Campaign::with('creator')->orderBy('created_at', 'desc')->paginate(10);
            $campaignsForJs = $campaigns->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'content' => strip_tags($campaign->content),
                    'category' => $campaign->category,
                    'status' => $this->calculateStatus($campaign->start_date, $campaign->end_date),
                    'start_date' => $campaign->start_date->toDateString(),
                    'end_date' => $campaign->end_date->toDateString(),
                    'views_count' => $campaign->views_count,
                    'shares_count' => $campaign->shares_count,
                    'thumbnail' => !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && Storage::disk('public')->exists($campaign->media_urls['images'][0])
                        ? Storage::url($campaign->media_urls['images'][0])
                        : 'https://via.placeholder.com/60x60?text=Image',
                    'created_at' => $campaign->created_at->toDateTimeString(),
                    'creator' => $campaign->creator ? [
                        'name' => $campaign->creator->name,
                        'email' => $campaign->creator->email
                    ] : null
                ];
            });

            return view('pages.backOffice.campaigns.All', [
                'campaigns' => $campaigns,
                'campaignsForJs' => $campaignsForJs
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des campagnes: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Afficher le formulaire de création d'une campagne
     */
    public function create()
    {
        try {
            return view('pages.backOffice.campaigns.create');
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du formulaire de création: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }




    /**
     * Supprimer une campagne
     */
    public function destroy($id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            // Supprimer les images associées
            if (!empty($campaign->media_urls['images'])) {
                foreach ($campaign->media_urls['images'] as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $campaign->delete();

            return response()->json(['success' => true, 'message' => 'Campagne supprimée avec succès']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    
    /**
     * Enregistrer une nouvelle campagne
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|min:10|max:255',
                'content' => 'required|string|min:100|max:2000',
                'category' => 'required|in:recyclage,climat,biodiversite,eau,energie,transport,alimentation,pollution,sensibilisation',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'objectives.*' => 'nullable|string|max:500',
                'actions.*' => 'nullable|string|max:500',
                'contact_info' => 'nullable|string|max:1000',
                'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'video_url' => 'nullable|url|max:255',
                'website_url' => 'nullable|url|max:255',
                'terms' => 'required|accepted'
            ]);

            $campaign = new Campaign();
            $campaign->title = $validated['title'];
            $campaign->content = $validated['content'];
            $campaign->category = $validated['category'];
            $campaign->start_date = Carbon::parse($validated['start_date']);
            $campaign->end_date = Carbon::parse($validated['end_date']);
            $campaign->status = $this->calculateStatus($campaign->start_date, $campaign->end_date);
            $campaign->created_by = Auth::id();
            $campaign->objectives = array_filter($request->input('objectives', []));
            $campaign->actions = array_filter($request->input('actions', []));
            $campaign->contact_info = $validated['contact_info'] ?? null;

            // Gérer l'upload d'image et autres médias
            $mediaUrls = ['images' => [], 'videos' => [], 'website' => null];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('campaigns', 'public');
                    $mediaUrls['images'][] = $path;
                }
            }
            if ($request->filled('video_url')) {
                $mediaUrls['videos'][] = $validated['video_url'];
            }
            if ($request->filled('website_url')) {
                $mediaUrls['website'] = $validated['website_url'];
            }
            $campaign->media_urls = array_filter($mediaUrls, fn($value) => !empty($value) || $value !== null);

            $campaign->save();

            return response()->json([
                'success' => true,
                'campaign_id' => $campaign->id,
                'message' => 'Campagne créée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la campagne: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création'], 500);
        }
    }






    /**
     * Afficher les détails d'une campagne
     */
    public function show($id)
    {
        try {
            // Vérifier si l'ID est numérique
            if (!is_numeric($id) || $id <= 0) {
                Log::error('ID de campagne invalide : ' . $id);
                return response()->json(['error' => 'ID de campagne invalide'], 400);
            }

            $campaign = Campaign::with('creator:id,name,email,created_at')
                ->findOrFail($id);

            // Mettre à jour le statut
            $campaign->status = $this->calculateStatus($campaign->start_date, $campaign->end_date);

            // Charger les commentaires
            $comments = CampaignComment::where('campaign_id', $id)
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('pages.backOffice.campaigns.show', compact('campaign', 'comments'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Campagne non trouvée pour l\'ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Campagne non trouvée'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur inattendue lors du chargement de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Mettre à jour une campagne
     */
    public function update(Request $request, $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|min:10|max:255',
                'content' => 'required|string|min:100|max:2000',
                'category' => 'required|in:recyclage,climat,biodiversite,eau,energie,transport,alimentation,pollution,sensibilisation',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'objectives.*' => 'nullable|string|max:500',
                'actions.*' => 'nullable|string|max:500',
                'contact_info' => 'nullable|string|max:1000',
                'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'video_url' => 'nullable|url|max:255',
                'website_url' => 'nullable|url|max:255',
                'deleted_images' => 'nullable|json'
            ]);

            // Mettre à jour les champs
            $campaign->title = $validated['title'];
            $campaign->content = $validated['content'];
            $campaign->category = $validated['category'];
            $campaign->start_date = Carbon::parse($validated['start_date']);
            $campaign->end_date = Carbon::parse($validated['end_date']);
            $campaign->status = $this->calculateStatus($campaign->start_date, $campaign->end_date);
            $campaign->objectives = array_filter($request->input('objectives', []));
            $campaign->actions = array_filter($request->input('actions', []));
            $campaign->contact_info = $validated['contact_info'] ?? null;

            // Gérer les images
            $mediaUrls = $campaign->media_urls ?? ['images' => [], 'videos' => [], 'website' => null];

            // Supprimer les images marquées comme supprimées
            if ($request->has('deleted_images')) {
                $deletedImages = json_decode($request->input('deleted_images'), true) ?? [];
                foreach ($deletedImages as $imagePath) {
                    if (isset($mediaUrls['images']) && in_array($imagePath, $mediaUrls['images'])) {
                        Storage::disk('public')->delete($imagePath);
                        $mediaUrls['images'] = array_diff($mediaUrls['images'], [$imagePath]);
                    }
                }
                $mediaUrls['images'] = array_values($mediaUrls['images'] ?? []);
            }

            // Gérer l'upload de nouvelles images
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $index => $file) {
                    if ($file && $file->isValid()) {
                        // Supprimer l'image existante à cet index si elle existe
                        if (isset($mediaUrls['images'][$index])) {
                            Storage::disk('public')->delete($mediaUrls['images'][$index]);
                        }
                        $path = $file->store('campaigns', 'public');
                        $mediaUrls['images'][$index] = $path;
                    }
                }
                $mediaUrls['images'] = array_values(array_filter($mediaUrls['images'] ?? []));
            }

            // Gérer les URLs de vidéo et site web
            if ($request->filled('video_url')) {
                $mediaUrls['videos'] = [$validated['video_url']];
            } else {
                $mediaUrls['videos'] = $mediaUrls['videos'] ?? [];
            }
            // Vérifier l'existence de la clé 'website' avant accès
            $mediaUrls['website'] = $validated['website_url'] ?? (isset($mediaUrls['website']) ? $mediaUrls['website'] : null);

            $campaign->media_urls = array_filter($mediaUrls, fn($value) => !empty($value) || $value !== null);

            $campaign->save();

            return response()->json([
                'success' => true,
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'category' => $campaign->category,
                    'status' => $campaign->status,
                    'updated_at' => $campaign->updated_at->format('d/m/Y H:i'),
                    'media_urls' => $campaign->media_urls
                ],
                'message' => 'Campagne mise à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Dupliquer une campagne
     */
    public function duplicate($id)
    {
        try {
            $original = Campaign::findOrFail($id);
            $newMediaUrls = $original->media_urls ?? ['images' => [], 'videos' => [], 'website' => null];

            // Copier les images
            if (!empty($newMediaUrls['images'])) {
                $newImages = [];
                foreach ($newMediaUrls['images'] as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        $newPath = 'campaigns/' . uniqid() . '.' . pathinfo($image, PATHINFO_EXTENSION);
                        Storage::disk('public')->copy($image, $newPath);
                        $newImages[] = $newPath;
                    }
                }
                $newMediaUrls['images'] = $newImages;
            }

            $newCampaign = Campaign::create([
                'title' => $original->title . ' (Copie)',
                'content' => $original->content,
                'category' => $original->category,
                'start_date' => $original->start_date,
                'end_date' => $original->end_date,
                'contact_info' => $original->contact_info,
                'media_urls' => $newMediaUrls,
                'objectives' => $original->objectives,
                'actions' => $original->actions,
                'created_by' => Auth::id(),
                'status' => 'upcoming',
                'views_count' => 0,
                'likes_count' => 0,
                'comments_count' => 0,
                'shares_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'campaign_id' => $newCampaign->id,
                'message' => 'Campagne dupliquée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la duplication de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la duplication'], 500);
        }
    }










    /**
     * Exporter les données d'une campagne
     */
    public function export($id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
            return Excel::download(new CampaignExport($campaign), "campaign-{$id}-data.csv");
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'exportation de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'exportation'], 500);
        }
    }

    /**
     * Afficher les commentaires d'une campagne
     */
    public function comments($id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
            $comments = CampaignComment::where('campaign_id', $id)->with('user')->orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'comments' => $comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'user' => $comment->user ? $comment->user->name : 'Anonyme',
                        'created_at' => $comment->created_at->format('d/m/Y H:i')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des commentaires de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du chargement des commentaires'], 500);
        }
    }

    /**
     * Supprimer un commentaire
     */
    public function deleteComment($id, $commentId)
    {
        try {
            $comment = CampaignComment::where('campaign_id', $id)->findOrFail($commentId);
            $comment->delete();
            return response()->json(['success' => true, 'message' => 'Commentaire supprimé']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du commentaire ID ' . $commentId . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression du commentaire'], 500);
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
}
