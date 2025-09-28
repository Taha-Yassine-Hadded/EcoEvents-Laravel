<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    /**
     * Display a listing of the organizer's communities.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un organisateur
        if (!$user->isOrganizer()) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $communities = Community::byOrganizer($user->id)
            ->with(['members' => function($query) {
                $query->active();
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('organizer.communities.index', compact('communities'));
    }

    /**
     * Show the form for creating a new community.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer()) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $categories = Community::getCategories();
        
        return view('organizer.communities.create', compact('categories'));
    }

    /**
     * Store a newly created community in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer()) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'required|string|min:10',
            'category' => ['required', Rule::in(array_keys(Community::getCategories()))],
            'location' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:5|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['organizer_id'] = $user->id;

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('communities', 'public');
        }

        $community = Community::create($validated);

        return redirect()->route('organizer.communities.index')
            ->with('success', 'Communauté créée avec succès !');
    }

    /**
     * Display the specified community.
     */
    public function show(Community $community)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est le créateur de la communauté
        if (!$user->isOrganizer() || $community->organizer_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $community->load(['members.user', 'organizer']);
        
        $stats = [
            'total_members' => $community->members()->active()->count(),
            'pending_requests' => $community->members()->pending()->count(),
            'capacity_percentage' => ($community->active_members_count / $community->max_members) * 100,
        ];

        return view('organizer.communities.show', compact('community', 'stats'));
    }

    /**
     * Show the form for editing the specified community.
     */
    public function edit(Community $community)
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer() || $community->organizer_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $categories = Community::getCategories();
        
        return view('organizer.communities.edit', compact('community', 'categories'));
    }

    /**
     * Update the specified community in storage.
     */
    public function update(Request $request, Community $community)
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer() || $community->organizer_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('communities')->ignore($community->id)],
            'description' => 'required|string|min:10',
            'category' => ['required', Rule::in(array_keys(Community::getCategories()))],
            'location' => 'nullable|string|max:255',
            'max_members' => 'required|integer|min:5|max:1000',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($community->image) {
                Storage::disk('public')->delete($community->image);
            }
            $validated['image'] = $request->file('image')->store('communities', 'public');
        }

        $community->update($validated);

        return redirect()->route('organizer.communities.show', $community)
            ->with('success', 'Communauté mise à jour avec succès !');
    }

    /**
     * Remove the specified community from storage.
     */
    public function destroy(Community $community)
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer() || $community->organizer_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Vérifier s'il y a des membres actifs
        if ($community->active_members_count > 0) {
            return redirect()->route('organizer.communities.show', $community)
                ->with('error', 'Impossible de supprimer une communauté avec des membres actifs.');
        }

        // Supprimer l'image si elle existe
        if ($community->image) {
            Storage::disk('public')->delete($community->image);
        }

        $community->delete();

        return redirect()->route('organizer.communities.index')
            ->with('success', 'Communauté supprimée avec succès !');
    }

    /**
     * Toggle community status (active/inactive).
     */
    public function toggleStatus(Community $community)
    {
        $user = Auth::user();
        
        if (!$user->isOrganizer() || $community->organizer_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        $community->update(['is_active' => !$community->is_active]);
        
        $status = $community->is_active ? 'activée' : 'désactivée';
        
        return redirect()->back()
            ->with('success', "Communauté {$status} avec succès !");
    }

    /**
     * Afficher toutes les demandes d'adhésion en attente pour les communautés de l'organisateur
     */
    public function membershipRequests()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un organisateur
        if (!$user || $user->role !== 'organizer') {
            abort(403, 'Accès réservé aux organisateurs.');
        }
        
        // Récupérer toutes les demandes en attente pour les communautés de cet organisateur
        $pendingRequests = CommunityMember::with(['user', 'community'])
            ->whereHas('community', function($query) use ($user) {
                $query->where('organizer_id', $user->id);
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('organizer.membership-requests.index', compact('pendingRequests'));
    }

    /**
     * Approuver une demande d'adhésion
     */
    public function approveMembership(CommunityMember $membership)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un organisateur
        if (!$user || $user->role !== 'organizer') {
            abort(403, 'Accès réservé aux organisateurs.');
        }
        
        // Vérifier que l'organisateur possède bien cette communauté
        if ($membership->community->organizer_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer cette demande.');
        }
        
        // Vérifier que la communauté n'est pas pleine
        if ($membership->community->isFull()) {
            return redirect()->back()
                ->with('error', 'La communauté a atteint sa capacité maximale.');
        }
        
        $membership->update(['status' => 'approved']);
        
        return redirect()->back()
            ->with('success', "Demande de {$membership->user->name} approuvée avec succès !");
    }

    /**
     * Rejeter une demande d'adhésion
     */
    public function rejectMembership(CommunityMember $membership)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est un organisateur
        if (!$user || $user->role !== 'organizer') {
            abort(403, 'Accès réservé aux organisateurs.');
        }
        
        // Vérifier que l'organisateur possède bien cette communauté
        if ($membership->community->organizer_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer cette demande.');
        }
        
        $userName = $membership->user->name;
        $membership->delete();
        
        return redirect()->back()
            ->with('success', "Demande de {$userName} rejetée.");
    }
}
