<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SponsorAnalyticsController extends Controller
{
    /**
     * Dashboard analytique principal
     */
    public function dashboard(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }

        // Période par défaut (6 derniers mois)
        $period = $request->get('period', '6months');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        // Métriques principales
        $metrics = $this->getMainMetrics($user->id, $startDate, $endDate);
        
        // Données pour les graphiques
        $chartData = $this->getChartDataForDashboard($user->id, $startDate, $endDate);
        
        // Top événements sponsorisés
        $topEvents = $this->getTopEvents($user->id, $startDate, $endDate);
        
        // Performance par mois
        $monthlyPerformance = $this->getMonthlyPerformance($user->id, $startDate, $endDate);
        
        // Comparaison avec la période précédente
        $comparison = $this->getPeriodComparison($user->id, $period);

        return view('pages.backOffice.sponsor-analytics', compact(
            'user',
            'metrics', 
            'chartData', 
            'topEvents', 
            'monthlyPerformance', 
            'comparison',
            'period'
        ));
    }

    /**
     * API pour les données de graphiques (AJAX)
     */
    public function getChartData(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $period = $request->get('period', '6months');
        $chartType = $request->get('chart_type', 'investment_trend');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        $data = [];

        switch ($chartType) {
            case 'investment_trend':
                $data = $this->getInvestmentTrendData($user->id, $startDate, $endDate);
                break;
            case 'roi_performance':
                $data = $this->getROIPerformanceData($user->id, $startDate, $endDate);
                break;
            case 'category_distribution':
                $data = $this->getCategoryDistributionData($user->id, $startDate, $endDate);
                break;
            case 'status_distribution':
                $data = $this->getStatusDistributionData($user->id, $startDate, $endDate);
                break;
        }

        return response()->json($data);
    }

    /**
     * Calculer les métriques principales
     */
    private function getMainMetrics($userId, $startDate, $endDate)
    {
        $sponsorships = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalInvested = $sponsorships->sum('amount');
        $totalSponsorships = $sponsorships->count();
        $approvedSponsorships = $sponsorships->where('status', 'approved')->count();
        $pendingSponsorships = $sponsorships->where('status', 'pending')->count();
        $completedSponsorships = $sponsorships->where('status', 'completed')->count();

        // Calcul du ROI moyen (simulation basée sur le statut)
        $roiRate = $this->calculateROI($sponsorships);
        
        // Calcul de l'engagement (basé sur la fréquence des sponsorships)
        $engagementScore = $this->calculateEngagementScore($userId, $startDate, $endDate);

        return [
            'total_invested' => $totalInvested,
            'total_sponsorships' => $totalSponsorships,
            'approved_sponsorships' => $approvedSponsorships,
            'pending_sponsorships' => $pendingSponsorships,
            'completed_sponsorships' => $completedSponsorships,
            'average_roi' => $roiRate,
            'engagement_score' => $engagementScore,
            'success_rate' => $totalSponsorships > 0 ? round(($approvedSponsorships / $totalSponsorships) * 100, 2) : 0,
        ];
    }

    /**
     * Données pour les graphiques
     */
    private function getChartDataForDashboard($userId, $startDate, $endDate)
    {
        return [
            'investment_trend' => $this->getInvestmentTrendData($userId, $startDate, $endDate),
            'roi_performance' => $this->getROIPerformanceData($userId, $startDate, $endDate),
            'category_distribution' => $this->getCategoryDistributionData($userId, $startDate, $endDate),
            'status_distribution' => $this->getStatusDistributionData($userId, $startDate, $endDate),
        ];
    }

    /**
     * Données de tendance d'investissement
     */
    private function getInvestmentTrendData($userId, $startDate, $endDate)
    {
        $data = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as sponsorship_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $data->pluck('month')->toArray(),
            'amounts' => $data->pluck('total_amount')->toArray(),
            'counts' => $data->pluck('sponsorship_count')->toArray(),
        ];
    }

    /**
     * Données de performance ROI
     */
    private function getROIPerformanceData($userId, $startDate, $endDate)
    {
        $sponsorships = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('event')
            ->get();

        $roiData = [];
        foreach ($sponsorships as $sponsorship) {
            $roiData[] = [
                'event_name' => $sponsorship->event_title ?? 'Événement supprimé',
                'investment' => $sponsorship->amount,
                'estimated_roi' => $this->calculateEventROI($sponsorship),
                'status' => $sponsorship->status,
            ];
        }

        return $roiData;
    }

    /**
     * Distribution par catégorie
     */
    private function getCategoryDistributionData($userId, $startDate, $endDate)
    {
        $data = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('sponsorships_temp.created_at', [$startDate, $endDate])
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sponsorships_temp.amount) as total_amount'),
                DB::raw('COUNT(*) as sponsorship_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_amount', 'desc')
            ->get();

        return [
            'labels' => $data->pluck('category_name')->toArray(),
            'amounts' => $data->pluck('total_amount')->toArray(),
            'counts' => $data->pluck('sponsorship_count')->toArray(),
        ];
    }

    /**
     * Distribution par statut
     */
    private function getStatusDistributionData($userId, $startDate, $endDate)
    {
        $data = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'status',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        return [
            'labels' => $data->pluck('status')->map(function($status) {
                return ucfirst($status);
            })->toArray(),
            'amounts' => $data->pluck('total_amount')->toArray(),
            'counts' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Top événements sponsorisés
     */
    private function getTopEvents($userId, $startDate, $endDate)
    {
        return SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                'event_title',
                'amount',
                'status',
                'created_at'
            )
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Performance mensuelle
     */
    private function getMonthlyPerformance($userId, $startDate, $endDate)
    {
        return SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total_invested'),
                DB::raw('COUNT(*) as total_sponsorships'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as approved_amount'),
                DB::raw('COUNT(CASE WHEN status = "approved" THEN 1 END) as approved_count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * Comparaison avec la période précédente
     */
    private function getPeriodComparison($userId, $period)
    {
        $currentStart = $this->getStartDate($period);
        $currentEnd = Carbon::now();
        
        $previousStart = $this->getStartDate($period, true);
        $previousEnd = $currentStart;

        $currentData = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->select(
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as total_count')
            )
            ->first();

        $previousData = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->select(
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as total_count')
            )
            ->first();

        $currentAmount = $currentData->total_amount ?? 0;
        $previousAmount = $previousData->total_amount ?? 0;
        $currentCount = $currentData->total_count ?? 0;
        $previousCount = $previousData->total_count ?? 0;

        return [
            'amount_change' => $previousAmount > 0 ? round((($currentAmount - $previousAmount) / $previousAmount) * 100, 2) : 0,
            'count_change' => $previousCount > 0 ? round((($currentCount - $previousCount) / $previousCount) * 100, 2) : 0,
            'current_amount' => $currentAmount,
            'previous_amount' => $previousAmount,
            'current_count' => $currentCount,
            'previous_count' => $previousCount,
        ];
    }

    /**
     * Calculer le ROI d'un sponsorship
     */
    private function calculateROI($sponsorships)
    {
        if ($sponsorships->isEmpty()) return 0;

        $totalROI = 0;
        $count = 0;

        foreach ($sponsorships as $sponsorship) {
            $roi = $this->calculateEventROI($sponsorship);
            $totalROI += $roi;
            $count++;
        }

        return $count > 0 ? round($totalROI / $count, 2) : 0;
    }

    /**
     * Calculer le ROI d'un événement spécifique
     */
    private function calculateEventROI($sponsorship)
    {
        // Simulation du ROI basé sur le statut et le montant
        $baseROI = 15; // ROI de base de 15%
        
        switch ($sponsorship->status) {
            case 'completed':
                return $baseROI + 10; // 25%
            case 'approved':
                return $baseROI + 5;  // 20%
            case 'pending':
                return $baseROI;     // 15%
            default:
                return $baseROI - 5; // 10%
        }
    }

    /**
     * Calculer le score d'engagement
     */
    private function calculateEngagementScore($userId, $startDate, $endDate)
    {
        $sponsorships = SponsorshipTemp::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $daysDiff = $startDate->diffInDays($endDate);
        $frequency = $daysDiff > 0 ? $sponsorships / $daysDiff : 0;

        // Score basé sur la fréquence (0-100)
        return min(100, round($frequency * 30, 2));
    }

    /**
     * Obtenir la date de début selon la période
     */
    private function getStartDate($period, $previous = false)
    {
        $multiplier = $previous ? 2 : 1;
        
        switch ($period) {
            case '1month':
                return Carbon::now()->subMonths(1 * $multiplier);
            case '3months':
                return Carbon::now()->subMonths(3 * $multiplier);
            case '6months':
                return Carbon::now()->subMonths(6 * $multiplier);
            case '1year':
                return Carbon::now()->subYear(1 * $multiplier);
            default:
                return Carbon::now()->subMonths(6 * $multiplier);
        }
    }
}
