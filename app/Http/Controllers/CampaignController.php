<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
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
            $campaign = Campaign::with('creator')->findOrFail($id);
            $campaign->status = $this->calculateStatus($campaign->start_date, $campaign->end_date);

            return view('pages.backOffice.campaigns.show', compact('campaign'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Campagne non trouvée'], 404);
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
                'website_url' => 'nullable|url|max:255'
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

            // Gérer l'upload d'image et autres médias
            $mediaUrls = $campaign->media_urls ?? ['images' => [], 'videos' => [], 'website' => null];
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    $path = $file->store('campaigns', 'public');
                    $mediaUrls['images'][] = $path;
                }
            }
            if ($request->filled('video_url')) {
                $mediaUrls['videos'] = array_unique(array_merge($mediaUrls['videos'] ?? [], [$validated['video_url']]));
            }
            if ($request->filled('website_url')) {
                $mediaUrls['website'] = $validated['website_url'];
            }
            $campaign->media_urls = array_filter($mediaUrls, fn($value) => !empty($value) || $value !== null);

            $campaign->save();

            return response()->json([
                'success' => true,
                'campaign' => [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'content' => $campaign->content,
                    'category' => $campaign->category,
                    'status' => $campaign->status,
                    'updated_at' => $campaign->updated_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
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

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    /**
     * Dupliquer une campagne
     */
    public function duplicate($id)
    {
        try {
            $original = Campaign::findOrFail($id);
            $newCampaign = $original->replicate();
            $newCampaign->title = $original->title . ' (Copie)';
            $newCampaign->created_at = now();
            $newCampaign->updated_at = now();
            $newCampaign->save();

            return response()->json(['success' => true, 'campaign_id' => $newCampaign->id]);
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
     * Envoyer une notification aux participants
     */
    public function notify(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:500'
            ]);

            // Logique d'envoi de notification (par exemple, via email ou notification push)
            Log::info('Notification envoyée pour la campagne ID ' . $id . ': ' . $validated['message']);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification pour la campagne ID ' . $id . ': ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'envoi de la notification'], 500);
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
