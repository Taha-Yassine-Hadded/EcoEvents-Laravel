<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SponsorshipTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminSponsorController extends Controller
{
    /**
     * Afficher la liste des sponsors
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'sponsor');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsors = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.sponsors.index', compact('sponsors'));
    }

    /**
     * Retourner les sponsors en JSON pour l'API
     */
    public function getSponsorsData(Request $request)
    {
        $query = User::where('role', 'sponsor');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sponsors = $query->orderBy('created_at', 'desc')->get();

        return response()->json($sponsors);
    }

    /**
     * Afficher les détails d'un sponsor
     */
    public function show(Request $request, $id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        // Si c'est une requête AJAX (pour la modal), retourner JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($sponsor);
        }
        
        // Sinon, retourner la vue normale
        $sponsorships = SponsorshipTemp::where('user_id', $sponsor->id)
            ->with('campaign')
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

        return view('admin.sponsors.show', compact('sponsor', 'sponsorships', 'stats'));
    }

    /**
     * Approuver un sponsor
     */
    public function approve($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $sponsor->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Sponsor approuvé avec succès !'
        ]);
    }

    /**
     * Rejeter un sponsor
     */
    public function reject($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $sponsor->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Sponsor rejeté avec succès !'
        ]);
    }

    /**
     * Activer/Désactiver un sponsor
     */
    public function toggleStatus($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        $newStatus = $sponsor->status === 'active' ? 'inactive' : 'active';
        $sponsor->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Sponsor {$newStatus} avec succès !",
            'new_status' => $newStatus
        ]);
    }

    /**
     * Supprimer un sponsor
     */
    public function destroy($id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);
        
        // Supprimer les sponsorships
        SponsorshipTemp::where('user_id', $sponsor->id)->delete();
        
        // Supprimer l'image de profil
        if ($sponsor->profile_image) {
            Storage::disk('public')->delete($sponsor->profile_image);
        }
        
        // Supprimer le logo
        if ($sponsor->logo) {
            Storage::disk('public')->delete($sponsor->logo);
        }
        
        // Supprimer le compte
        $sponsor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sponsor supprimé avec succès !'
        ]);
    }

    /**
     * Mettre à jour les informations d'un sponsor
     */
    public function update(Request $request, $id)
    {
        $sponsor = User::where('role', 'sponsor')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected,active,inactive',
        ]);

        $sponsor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Informations du sponsor mises à jour avec succès !'
        ]);
    }
}