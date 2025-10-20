<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SponsorAnalyticsApiController extends Controller
{
    // ==================== DASHBOARD ANALYTICS ====================

    /**
     * Données du dashboard sponsor
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)->get();

            $dashboardData = [
                'overview' => [
                    'total_sponsorships' => $sponsorships->count(),
                    'pending_sponsorships' => $sponsorships->where('status', 'pending')->count(),
                    'approved_sponsorships' => $sponsorships->where('status', 'approved')->count(),
                    'total_invested' => $sponsorships->where('status', 'approved')->sum('amount'),
                    'success_rate' => $this->calculateSuccessRate($sponsorships),
                ],
                'recent_activity' => $this->getRecentActivity($user),
                'upcoming_events' => $this->getUpcomingEvents($user),
                'performance_metrics' => $this->getPerformanceMetrics($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboardData
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getDashboardData', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données du dashboard'
            ], 500);
        }
    }

    /**
     * Vue d'ensemble des analytics
     */
    public function getOverview(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $overview = [
                'financial_summary' => $this->getFinancialSummary($user),
                'sponsorship_trends' => $this->getSponsorshipTrends($user),
                'category_analysis' => $this->getCategoryAnalysis($user),
                'monthly_performance' => $this->getMonthlyPerformance($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $overview
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getOverview', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la vue d\'ensemble'
            ], 500);
        }
    }

    // ==================== PERFORMANCE METRICS ====================

    /**
     * Métriques de performance
     */
    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $metrics = [
                'roi_analysis' => $this->getROIAnalysis($user),
                'success_rate' => $this->getSuccessRate($user),
                'average_investment' => $this->getAverageInvestment($user),
                'investment_frequency' => $this->getInvestmentFrequency($user),
                'event_performance' => $this->getEventPerformance($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getPerformanceMetrics', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des métriques'
            ], 500);
        }
    }

    /**
     * Analyse ROI
     */
    public function getROIAnalysis(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $roiData = [
                'total_investment' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount'),
                'estimated_return' => $this->calculateEstimatedReturn($user),
                'roi_percentage' => $this->calculateROIPercentage($user),
                'best_performing_events' => $this->getBestPerformingEvents($user),
                'roi_by_category' => $this->getROIByCategory($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $roiData
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getROIAnalysis', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse ROI'
            ], 500);
        }
    }

    /**
     * Taux de succès
     */
    public function getSuccessRate(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $sponsorships = SponsorshipTemp::where('user_id', $user->id)->get();
            $totalSponsorships = $sponsorships->count();
            $approvedSponsorships = $sponsorships->where('status', 'approved')->count();

            $successRate = $totalSponsorships > 0 
                ? round(($approvedSponsorships / $totalSponsorships) * 100, 2)
                : 0;

            $successRateData = [
                'overall_success_rate' => $successRate,
                'success_rate_by_month' => $this->getSuccessRateByMonth($user),
                'success_rate_by_category' => $this->getSuccessRateByCategory($user),
                'success_rate_trend' => $this->getSuccessRateTrend($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $successRateData
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getSuccessRate', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du taux de succès'
            ], 500);
        }
    }

    // ==================== FINANCIAL ANALYTICS ====================

    /**
     * Résumé financier
     */
    public function getFinancialSummary(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $financialSummary = [
                'total_invested' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount'),
                'pending_investments' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->sum('amount'),
                'average_investment' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->avg('amount'),
                'investment_distribution' => $this->getInvestmentDistribution($user),
                'monthly_investment_trend' => $this->getMonthlyInvestmentTrend($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $financialSummary
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getFinancialSummary', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du résumé financier'
            ], 500);
        }
    }

    /**
     * Utilisation du budget
     */
    public function getBudgetUtilization(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $budgetUtilization = [
                'total_budget' => $user->budget ?? 0,
                'used_budget' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount'),
                'pending_budget' => SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->sum('amount'),
                'remaining_budget' => ($user->budget ?? 0) - SponsorshipTemp::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->sum('amount'),
                'utilization_percentage' => $this->calculateBudgetUtilization($user),
                'budget_by_category' => $this->getBudgetByCategory($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $budgetUtilization
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getBudgetUtilization', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse du budget'
            ], 500);
        }
    }

    /**
     * Tendances d'investissement
     */
    public function getInvestmentTrends(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $investmentTrends = [
                'monthly_trends' => $this->getMonthlyInvestmentTrend($user),
                'yearly_trends' => $this->getYearlyInvestmentTrend($user),
                'category_trends' => $this->getCategoryInvestmentTrend($user),
                'amount_trends' => $this->getAmountTrends($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $investmentTrends
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getInvestmentTrends', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse des tendances'
            ], 500);
        }
    }

    // ==================== EVENT ANALYTICS ====================

    /**
     * Performance des événements
     */
    public function getEventPerformance(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $eventPerformance = [
                'top_performing_events' => $this->getTopPerformingEvents($user),
                'event_success_rate' => $this->getEventSuccessRate($user),
                'event_investment_distribution' => $this->getEventInvestmentDistribution($user),
                'upcoming_opportunities' => $this->getUpcomingOpportunities($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $eventPerformance
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getEventPerformance', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse des événements'
            ], 500);
        }
    }

    /**
     * Analyse par catégorie
     */
    public function getCategoryAnalysis(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $categoryAnalysis = [
                'investment_by_category' => $this->getInvestmentByCategory($user),
                'success_rate_by_category' => $this->getSuccessRateByCategory($user),
                'preferred_categories' => $this->getPreferredCategories($user),
                'category_performance' => $this->getCategoryPerformance($user),
            ];

            return response()->json([
                'success' => true,
                'data' => $categoryAnalysis
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getCategoryAnalysis', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'analyse par catégorie'
            ], 500);
        }
    }

    // ==================== TIME-BASED ANALYTICS ====================

    /**
     * Rapport mensuel
     */
    public function getMonthlyReport(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;
            $month = $request->get('month', date('Y-m'));

            $monthlyReport = [
                'month' => $month,
                'sponsorships_count' => $this->getMonthlySponsorshipsCount($user, $month),
                'total_investment' => $this->getMonthlyInvestment($user, $month),
                'success_rate' => $this->getMonthlySuccessRate($user, $month),
                'top_events' => $this->getMonthlyTopEvents($user, $month),
                'category_breakdown' => $this->getMonthlyCategoryBreakdown($user, $month),
            ];

            return response()->json([
                'success' => true,
                'data' => $monthlyReport
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getMonthlyReport', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport mensuel'
            ], 500);
        }
    }

    /**
     * Rapport annuel
     */
    public function getYearlyReport(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;
            $year = $request->get('year', date('Y'));

            $yearlyReport = [
                'year' => $year,
                'annual_summary' => $this->getAnnualSummary($user, $year),
                'monthly_breakdown' => $this->getMonthlyBreakdown($user, $year),
                'year_over_year_growth' => $this->getYearOverYearGrowth($user, $year),
                'annual_goals' => $this->getAnnualGoals($user, $year),
            ];

            return response()->json([
                'success' => true,
                'data' => $yearlyReport
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getYearlyReport', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du rapport annuel'
            ], 500);
        }
    }

    // ==================== CHART DATA ====================

    /**
     * Données pour le graphique des tendances de sponsoring
     */
    public function getSponsorshipTrendsChart(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;
            $period = $request->get('period', '12months');

            $chartData = $this->generateSponsorshipTrendsChartData($user, $period);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getSponsorshipTrendsChart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des données du graphique'
            ], 500);
        }
    }

    /**
     * Données pour le graphique de distribution ROI
     */
    public function getROIDistributionChart(Request $request): JsonResponse
    {
        try {
            $user = $request->auth;

            $chartData = $this->generateROIDistributionChartData($user);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur getROIDistributionChart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération des données ROI'
            ], 500);
        }
    }

    // ==================== ADMIN ANALYTICS ====================

    /**
     * Vue d'ensemble pour l'admin
     */
    public function adminGetOverview(Request $request): JsonResponse
    {
        try {
            $overview = [
                'total_sponsors' => User::where('role', 'sponsor')->count(),
                'active_sponsors' => User::where('role', 'sponsor')->where('status', 'approved')->count(),
                'total_sponsorships' => SponsorshipTemp::count(),
                'total_revenue' => SponsorshipTemp::where('status', 'approved')->sum('amount'),
                'pending_sponsorships' => SponsorshipTemp::where('status', 'pending')->count(),
                'sponsor_growth' => $this->getSponsorGrowth(),
                'revenue_trends' => $this->getRevenueTrends(),
            ];

            return response()->json([
                'success' => true,
                'data' => $overview
            ]);

        } catch (\Exception $e) {
            Log::error('SponsorAnalyticsApiController: Erreur adminGetOverview', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la vue d\'ensemble admin'
            ], 500);
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    private function calculateSuccessRate($sponsorships)
    {
        $total = $sponsorships->count();
        $approved = $sponsorships->where('status', 'approved')->count();
        
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    private function getRecentActivity($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->with(['event'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getUpcomingEvents($user)
    {
        return Event::where('status', 'upcoming')
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(5)
            ->get();
    }

    private function getPerformanceMetrics($user)
    {
        $sponsorships = SponsorshipTemp::where('user_id', $user->id)->get();
        
        return [
            'total_investments' => $sponsorships->where('status', 'approved')->sum('amount'),
            'average_investment' => $sponsorships->where('status', 'approved')->avg('amount'),
            'success_rate' => $this->calculateSuccessRate($sponsorships),
            'investment_frequency' => $sponsorships->count(),
        ];
    }

    private function calculateEstimatedReturn($user)
    {
        // Logique simplifiée pour l'estimation du retour
        $totalInvestment = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
        
        // Estimation basée sur une moyenne de 15% de retour
        return $totalInvestment * 1.15;
    }

    private function calculateROIPercentage($user)
    {
        $totalInvestment = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
        
        if ($totalInvestment == 0) return 0;
        
        $estimatedReturn = $this->calculateEstimatedReturn($user);
        return round((($estimatedReturn - $totalInvestment) / $totalInvestment) * 100, 2);
    }

    private function getBestPerformingEvents($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['event'])
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();
    }

    private function getROIByCategory($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sponsorships_temp.amount) as total_amount'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    private function getInvestmentDistribution($user)
    {
        return [
            'approved' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'approved')->sum('amount'),
            'pending' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
            'rejected' => SponsorshipTemp::where('user_id', $user->id)->where('status', 'rejected')->sum('amount'),
        ];
    }

    private function getMonthlyInvestmentTrend($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function calculateBudgetUtilization($user)
    {
        $totalBudget = $user->budget ?? 0;
        $usedBudget = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
        
        return $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 2) : 0;
    }

    private function getBudgetByCategory($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sponsorships_temp.amount) as amount'))
            ->groupBy('categories.id', 'categories.name')
            ->get();
    }

    private function getTopPerformingEvents($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with(['event'])
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get();
    }

    private function getEventSuccessRate($user)
    {
        $events = Event::whereHas('sponsorships', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['sponsorships' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->get();

        $eventSuccessRates = [];
        foreach ($events as $event) {
            $totalSponsorships = $event->sponsorships->count();
            $approvedSponsorships = $event->sponsorships->where('status', 'approved')->count();
            
            $eventSuccessRates[] = [
                'event' => $event,
                'success_rate' => $totalSponsorships > 0 ? round(($approvedSponsorships / $totalSponsorships) * 100, 2) : 0
            ];
        }

        return collect($eventSuccessRates)->sortByDesc('success_rate')->take(5)->values();
    }

    private function getInvestmentByCategory($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(sponsorships_temp.amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_amount', 'desc')
            ->get();
    }

    private function getSuccessRateByCategory($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'))
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($item) {
                $item->success_rate = $item->total > 0 ? round(($item->approved / $item->total) * 100, 2) : 0;
                return $item;
            });
    }

    private function getPreferredCategories($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as frequency'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('frequency', 'desc')
            ->limit(5)
            ->get();
    }

    private function getCategoryPerformance($user)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name',
                DB::raw('COUNT(*) as total_sponsorships'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_sponsorships'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as total_investment'),
                DB::raw('AVG(CASE WHEN status = "approved" THEN amount ELSE NULL END) as avg_investment'))
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($item) {
                $item->success_rate = $item->total_sponsorships > 0 
                    ? round(($item->approved_sponsorships / $item->total_sponsorships) * 100, 2) 
                    : 0;
                return $item;
            });
    }

    private function getMonthlySponsorshipsCount($user, $month)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->count();
    }

    private function getMonthlyInvestment($user, $month)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->sum('amount');
    }

    private function getMonthlySuccessRate($user, $month)
    {
        $total = SponsorshipTemp::where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->count();
        
        $approved = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->count();
        
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    private function getMonthlyTopEvents($user, $month)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->with(['event'])
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();
    }

    private function getMonthlyCategoryBreakdown($user, $month)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->join('events', 'sponsorships_temp.event_id', '=', 'events.id')
            ->join('categories', 'events.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->get();
    }

    private function getAnnualSummary($user, $year)
    {
        return [
            'total_sponsorships' => SponsorshipTemp::where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->count(),
            'approved_sponsorships' => SponsorshipTemp::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $year)
                ->count(),
            'total_investment' => SponsorshipTemp::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $year)
                ->sum('amount'),
            'average_investment' => SponsorshipTemp::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $year)
                ->avg('amount'),
        ];
    }

    private function getMonthlyBreakdown($user, $year)
    {
        return SponsorshipTemp::where('user_id', $user->id)
            ->whereYear('created_at', $year)
            ->select(DB::raw('MONTH(created_at) as month'), 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN amount ELSE 0 END) as investment'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getYearOverYearGrowth($user, $year)
    {
        $currentYear = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('created_at', $year)
            ->sum('amount');
        
        $previousYear = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('created_at', $year - 1)
            ->sum('amount');
        
        if ($previousYear == 0) return 0;
        
        return round((($currentYear - $previousYear) / $previousYear) * 100, 2);
    }

    private function getAnnualGoals($user, $year)
    {
        // Logique simplifiée pour les objectifs annuels
        $totalInvestment = SponsorshipTemp::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('created_at', $year)
            ->sum('amount');
        
        $budget = $user->budget ?? 0;
        
        return [
            'budget_goal' => $budget,
            'achieved' => $totalInvestment,
            'completion_percentage' => $budget > 0 ? round(($totalInvestment / $budget) * 100, 2) : 0,
            'remaining' => max(0, $budget - $totalInvestment),
        ];
    }

    private function generateSponsorshipTrendsChartData($user, $period)
    {
        // Logique pour générer les données du graphique
        $months = [];
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $count = SponsorshipTemp::where('user_id', $user->id)
                ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
                ->count();
            
            $months[] = $date->format('M Y');
            $data[] = $count;
        }
        
        return [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Sponsoring par mois',
                    'data' => $data,
                    'borderColor' => '#3498db',
                    'backgroundColor' => 'rgba(52, 152, 219, 0.1)',
                ]
            ]
        ];
    }

    private function generateROIDistributionChartData($user)
    {
        $categories = $this->getROIByCategory($user);
        
        return [
            'labels' => $categories->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Investissement par catégorie',
                    'data' => $categories->pluck('total_amount')->toArray(),
                    'backgroundColor' => [
                        '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                        '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#16a085'
                    ]
                ]
            ]
        ];
    }

    private function getSponsorGrowth()
    {
        return User::where('role', 'sponsor')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getRevenueTrends()
    {
        return SponsorshipTemp::where('status', 'approved')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
