<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SponsorshipTemp;
use App\Models\User;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->auth;
            if (!$user) {
                return response()->json(['error' => 'Veuillez vous connecter pour accéder au tableau de bord.'], 401);
            }

            // Si c'est un admin, utiliser le dashboard backoffice existant avec les statistiques
            if ($user->role === 'admin') {
                $stats = $this->getAdminStats();
                // Derniers contrats générés
                try {
                    $recentContracts = SponsorshipTemp::where('status', 'approved')
                        ->whereNotNull('contract_pdf')
                        ->with(['user', 'event'])
                        ->orderBy('updated_at', 'desc')
                        ->limit(6)
                        ->get();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('DashboardController: Erreur recentContracts', ['error' => $e->getMessage()]);
                    $recentContracts = collect();
                }
                return view('pages.backOffice.dashboard', ['user' => $user, 'stats' => $stats, 'recentContracts' => $recentContracts]);
            }

            // Si c'est un sponsor, rediriger vers le dashboard sponsor
            if ($user->role === 'sponsor') {
                return redirect()->route('sponsor.dashboard');
            }

            // Dashboard par défaut pour les autres rôles
            return view('pages.backOffice.dashboard', ['user' => $user]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController: Erreur', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur d\'authentification.'], 500);
        }
    }

    /**
     * Récupérer les statistiques pour l'admin
     */
    private function getAdminStats()
    {
        try {
            // Statistiques générales
            $totalUsers = User::count();
            $totalSponsors = User::where('role', 'sponsor')->count();
            $totalEvents = Event::count();
            
            // Statistiques des sponsoring avec gestion d'erreur
            $totalSponsorships = 0;
            $pendingSponsorships = 0;
            $approvedSponsorships = 0;
            $totalSponsorshipAmount = 0;
            $pendingSponsorshipAmount = 0;
            $approvedSponsorshipAmount = 0;
            $recentSponsorships = 0;
            $monthlySponsorships = collect();
            
            // Statistiques des contrats
            $totalContracts = 0;
            $contractsAmount = 0;
            
            try {
                $totalSponsorships = SponsorshipTemp::count();
                $pendingSponsorships = SponsorshipTemp::where('status', 'pending')->count();
                $approvedSponsorships = SponsorshipTemp::where('status', 'approved')->count();
                $totalSponsorshipAmount = SponsorshipTemp::sum('amount') ?? 0;
                $pendingSponsorshipAmount = SponsorshipTemp::where('status', 'pending')->sum('amount') ?? 0;
                $approvedSponsorshipAmount = SponsorshipTemp::where('status', 'approved')->sum('amount') ?? 0;
                
                // Sponsoring récents (7 derniers jours)
                $recentSponsorships = SponsorshipTemp::where('created_at', '>=', now()->subDays(7))->count();
                
                // Sponsoring par mois (cette année)
                $monthlySponsorships = SponsorshipTemp::whereYear('created_at', now()->year)
                    ->selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                
                // Statistiques des contrats
                $totalContracts = SponsorshipTemp::where('status', 'approved')
                    ->whereNotNull('contract_pdf')
                    ->count();
                $contractsAmount = SponsorshipTemp::where('status', 'approved')
                    ->whereNotNull('contract_pdf')
                    ->sum('amount') ?? 0;
            } catch (\Exception $sponsorshipError) {
                \Illuminate\Support\Facades\Log::error('DashboardController: Erreur stats sponsoring', ['error' => $sponsorshipError->getMessage()]);
            }

            return [
                'general' => [
                    'total_users' => $totalUsers,
                    'total_sponsors' => $totalSponsors,
                    'total_events' => $totalEvents,
                ],
                'sponsoring' => [
                    'total_sponsorships' => $totalSponsorships,
                    'pending_sponsorships' => $pendingSponsorships,
                    'approved_sponsorships' => $approvedSponsorships,
                    'total_amount' => $totalSponsorshipAmount,
                    'pending_amount' => $pendingSponsorshipAmount,
                    'approved_amount' => $approvedSponsorshipAmount,
                    'recent_sponsorships' => $recentSponsorships,
                ],
                'contracts' => [
                    'total_contracts' => $totalContracts,
                    'contracts_amount' => $contractsAmount,
                ],
                'monthly_data' => $monthlySponsorships,
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('DashboardController: Erreur stats', ['error' => $e->getMessage()]);
            return [
                'general' => [
                    'total_users' => 0,
                    'total_sponsors' => 0,
                    'total_events' => 0,
                ],
                'sponsoring' => [
                    'total_sponsorships' => 0,
                    'pending_sponsorships' => 0,
                    'approved_sponsorships' => 0,
                    'total_amount' => 0,
                    'pending_amount' => 0,
                    'approved_amount' => 0,
                    'recent_sponsorships' => 0,
                ],
                'contracts' => [
                    'total_contracts' => 0,
                    'contracts_amount' => 0,
                ],
                'monthly_data' => collect(),
            ];
        }
    }

}
