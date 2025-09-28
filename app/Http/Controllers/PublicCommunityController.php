<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class PublicCommunityController extends Controller
{
    /**
     * Récupérer l'utilisateur authentifié via JWT ou Laravel Auth
     */
    private function getAuthenticatedUser(Request $request)
    {
        // Essayer d'abord l'utilisateur injecté par le middleware JWT
        if (isset($request->auth)) {
            return $request->auth;
        }
        
        // Ensuite essayer Laravel Auth
        if (Auth::check()) {
            return Auth::user();
        }
        
        return null;
    }

    /**
     * Display a listing of active communities.
     */
    public function index(Request $request)
    {
        $query = Community::active()->with(['organizer', 'members']);

        // Filtrage par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filtrage par localisation
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Recherche par nom
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $communities = $query->orderBy('created_at', 'desc')->paginate(12);
        $categories = Community::getCategories();

        // Statistiques générales
        $stats = [
            'total_communities' => Community::active()->count(),
            'total_members' => CommunityMember::where('status', 'approved')->count(),
            'categories_count' => Community::active()->distinct('category')->count(),
        ];

        // Récupérer l'utilisateur authentifié (JWT ou Laravel)
        $currentUser = $this->getAuthenticatedUser($request);

        return view('pages.frontOffice.communities.index', compact('communities', 'categories', 'stats', 'currentUser'));
    }

    /**
     * Display the specified community.
     */
    public function show(Request $request, Community $community)
    {
        if (!$community->is_active) {
            abort(404, 'Cette communauté n\'est pas disponible.');
        }

        $community->load(['organizer', 'members.user']);
        
        // Récupérer l'utilisateur authentifié (JWT ou Laravel)
        $currentUser = $this->getAuthenticatedUser($request);
        
        $userIsMember = false;
        $membershipStatus = null;
        
        if ($currentUser) {
            $membership = CommunityMember::where('community_id', $community->id)
                                       ->where('user_id', $currentUser->id)
                                       ->first();
            if ($membership) {
                $userIsMember = true;
                $membershipStatus = $membership->status;
            }
        }

        return view('pages.frontOffice.communities.show', compact('community', 'userIsMember', 'membershipStatus', 'currentUser'));
    }

    /**
     * Join a community.
     */
    public function join(Community $community)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour rejoindre une communauté.');
        }

        $user = Auth::user();

        // Vérifier si la communauté est active
        if (!$community->is_active) {
            return redirect()->back()->with('error', 'Cette communauté n\'accepte plus de nouveaux membres.');
        }

        // Vérifier si la communauté est pleine
        if ($community->isFull()) {
            return redirect()->back()->with('error', 'Cette communauté a atteint sa capacité maximale.');
        }

        // Vérifier si l'utilisateur est déjà membre
        $existingMembership = CommunityMember::where('community_id', $community->id)
                                           ->where('user_id', $user->id)
                                           ->first();

        if ($existingMembership) {
            if ($existingMembership->status === 'approved') {
                return redirect()->back()->with('info', 'Vous êtes déjà membre de cette communauté.');
            } elseif ($existingMembership->status === 'pending') {
                return redirect()->back()->with('info', 'Votre demande d\'adhésion est en cours de traitement.');
            }
        }

        // Créer l'adhésion
        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $user->id,
            'status' => 'approved', // Auto-approuvé pour simplifier
            'is_active' => true,
            'joined_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Félicitations ! Vous avez rejoint la communauté "' . $community->name . '".');
    }

    /**
     * Leave a community.
     */
    public function leave(Community $community)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $membership = CommunityMember::where('community_id', $community->id)
                                   ->where('user_id', $user->id)
                                   ->first();

        if (!$membership) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas membre de cette communauté.');
        }

        $membership->delete();

        return redirect()->back()->with('success', 'Vous avez quitté la communauté "' . $community->name . '".');
    }

    /**
     * Get communities by category (AJAX).
     */
    public function byCategory($category)
    {
        $communities = Community::active()
                               ->where('category', $category)
                               ->with(['organizer', 'members'])
                               ->get();

        return response()->json($communities);
    }
}
