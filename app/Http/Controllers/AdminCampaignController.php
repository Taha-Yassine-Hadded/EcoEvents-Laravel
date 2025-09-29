<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\SponsorshipTemp;
use Illuminate\Support\Facades\Storage;

class AdminCampaignController extends Controller
{
    /**
     * Afficher la liste des campagnes
     */
    public function index(Request $request)
    {
        $query = Campaign::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    /**
     * Retourner les campagnes en JSON pour l'API
     */
    public function getCampaignsData(Request $request)
    {
        $query = Campaign::query();

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->get();

        return response()->json($campaigns);
    }

    /**
     * Afficher les détails d'une campagne
     */
    public function show(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        
        // Si c'est une requête AJAX (pour la modal), retourner JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($campaign);
        }
        
        // Sinon, retourner la vue normale
        $sponsorships = SponsorshipTemp::where('campaign_id', $campaign->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques
        $stats = [
            'total_sponsorships' => $sponsorships->count(),
            'total_amount' => $sponsorships->sum('amount'),
            'pending_sponsorships' => $sponsorships->where('status', 'pending')->count(),
            'approved_sponsorships' => $sponsorships->where('status', 'approved')->count(),
            'completed_sponsorships' => $sponsorships->where('status', 'completed')->count(),
        ];

        return view('admin.campaigns.show', compact('campaign', 'sponsorships', 'stats'));
    }

    /**
     * Créer une nouvelle campagne
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:event,festival,conference,sport,culture',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,paused,completed,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $campaignData = $validated;

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $campaignData['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create($campaignData);

        return response()->json([
            'success' => true,
            'message' => 'Campagne créée avec succès !',
            'campaign' => $campaign
        ]);
    }

    /**
     * Mettre à jour une campagne
     */
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:event,festival,conference,sport,culture',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,paused,completed,cancelled',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $updateData = $validated;

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }
            $updateData['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Campagne mise à jour avec succès !',
            'campaign' => $campaign->fresh()
        ]);
    }

    /**
     * Activer/Désactiver une campagne
     */
    public function toggleStatus($id)
    {
        $campaign = Campaign::findOrFail($id);
        
        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        $campaign->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Campagne {$newStatus} avec succès !",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Supprimer une campagne
     */
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        
        // Supprimer les sponsorships associés
        SponsorshipTemp::where('campaign_id', $campaign->id)->delete();
        
        // Supprimer l'image
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json([
            'success' => true,
            'message' => 'Campagne et toutes ses données supprimées avec succès !'
        ]);
    }
}
