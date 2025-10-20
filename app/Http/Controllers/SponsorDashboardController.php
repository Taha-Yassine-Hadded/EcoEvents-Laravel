<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Models\Package;
use App\Models\SponsorshipTemp;

class SponsorDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder au tableau de bord sponsor.'], 401);
            }

            if ($user->role !== 'sponsor') {
                return redirect()->route('home')->with('error', 'Accès non autorisé : réservé aux sponsors.');
            }

            // Récupérer les événements de la table events au lieu des campagnes statiques
            $events = Event::with(['category', 'organizer'])
                ->where('status', '!=', 'cancelled')
                ->orderBy('date', 'asc')
                ->limit(10)
                ->get();

            // Statistiques du sponsor basées sur les événements
            $stats = [
                'total_events_available' => Event::where('status', '!=', 'cancelled')->count(),
                'upcoming_events' => Event::where('status', 'upcoming')->count(),
                'ongoing_events' => Event::where('status', 'ongoing')->count(),
                'total_categories' => Category::count()
            ];

            // Récupérer les sponsoring du sponsor
            $sponsorships = SponsorshipTemp::where('user_id', $user->id)
                ->with(['event'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Statistiques des sponsoring
            $sponsorshipStats = [
                'total_proposals' => $sponsorships->count(),
                'pending_proposals' => $sponsorships->where('status', 'pending')->count(),
                'approved_proposals' => $sponsorships->where('status', 'approved')->count(),
                'rejected_proposals' => $sponsorships->where('status', 'rejected')->count(),
                'total_invested' => $sponsorships->where('status', 'approved')->sum('amount'),
            ];

            return view('pages.backOffice.sponsor-dashboard', [
                'user' => $user,
                'events' => $events,
                'stats' => $stats,
                'sponsorships' => $sponsorships,
                'sponsorshipStats' => $sponsorshipStats
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorDashboardController: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur d\'authentification.'], 500);
        }
    }

    public function campaigns(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            // Récupérer tous les événements disponibles pour le sponsoring
            $events = Event::with(['category', 'organizer'])
                ->where('status', '!=', 'cancelled')
                ->orderBy('date', 'asc')
                ->paginate(12); // Pagination restaurée

            // Récupérer les packages disponibles pour le sponsoring (éviter les doublons)
            $packages = Package::where('is_active', true)
                ->distinct()
                ->orderBy('price', 'asc')
                ->get();


            return view('pages.backOffice.sponsor-campaigns', [
                'user' => $user,
                'events' => $events,
                'packages' => $packages
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorDashboardController: Erreur campaigns', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du chargement des campagnes.'], 500);
        }
    }

    public function createSponsorship(Request $request)
    {
        try {
            $user = $request->auth;

            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $request->validate([
                'event_id' => 'required|exists:events,id',
                'package_id' => 'required|exists:packages,id',
                'amount' => 'required|numeric|min:0',
                'message' => 'nullable|string|max:1000'
            ]);

            // Récupérer le package et l'événement pour obtenir les détails
            $package = Package::find($request->package_id);
            $event = Event::find($request->event_id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Événement non trouvé.'
                ], 404);
            }

            // Créer la proposition de sponsoring avec SponsorshipTemp
            $sponsorship = \App\Models\SponsorshipTemp::create([
                'user_id' => $user->id,
                'event_id' => $request->event_id, // Utiliser event_id correctement
                'campaign_id' => $request->event_id, // Garder pour compatibilité
                'package_id' => $request->package_id,
                'package_name' => $package->name,
                'amount' => $request->amount,
                'status' => 'pending',
                'notes' => $request->message,
                'event_title' => $event->title,
                'event_description' => $event->description ?? 'Aucune description disponible',
                'event_date' => $event->date ?? null,
                'event_location' => $event->location ?? 'Lieu non spécifié',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proposition de sponsoring envoyée avec succès !',
                'sponsorship' => $sponsorship
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorDashboardController: Erreur createSponsorship', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la création de la proposition de sponsoring: ' . $e->getMessage()
            ], 500);
        }
    }
}
