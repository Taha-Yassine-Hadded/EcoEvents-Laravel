<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

            // Pour l'instant, pas de campagnes (table n'existe pas encore)
            $campaigns = collect([]);

            // Statistiques du sponsor (pour l'instant vides, à développer)
            $stats = [
                'total_campaigns_viewed' => 0,
                'sponsorships_proposed' => 0,
                'sponsorships_approved' => 0,
                'total_invested' => 0
            ];

            return view('pages.backOffice.sponsor-dashboard', [
                'user' => $user,
                'campaigns' => $campaigns,
                'stats' => $stats
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

            // Pour l'instant, pas de campagnes (table n'existe pas encore)
            $campaigns = collect([]);

            return view('pages.backOffice.sponsor-campaigns', [
                'user' => $user,
                'campaigns' => $campaigns
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorDashboardController: Erreur campaigns', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du chargement des campagnes.'], 500);
        }
    }
}
