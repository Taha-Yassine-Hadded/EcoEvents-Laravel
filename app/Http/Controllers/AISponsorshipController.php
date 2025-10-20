<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EventRecommendationAI;
use App\Models\User;
use App\Models\Event;

class AISponsorshipController extends Controller
{
    protected $recommendationAI;

    public function __construct(EventRecommendationAI $recommendationAI)
    {
        $this->recommendationAI = $recommendationAI;
    }

    /**
     * Afficher la page des recommandations IA
     */
    public function showRecommendationsPage(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }
        
        return view('pages.backOffice.sponsor-ai-recommendations', compact('user'));
    }

    /**
     * Recommander des événements pour un sponsor
     */
    public function recommendEvents(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $limit = $request->get('limit', 10);
            $recommendations = $this->recommendationAI->recommendEvents($user, $limit);
            $confidenceScore = $this->recommendationAI->getConfidenceScore();

            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
                'confidence_score' => $confidenceScore,
                'total_recommendations' => count($recommendations),
                'user_profile' => $this->getUserProfile($user)
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Sponsorship Controller: Erreur recommendEvents', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la génération des recommandations.'
            ], 500);
        }
    }

    /**
     * Recommander des packages pour un événement spécifique
     */
    public function recommendPackages(Request $request, $eventId)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $event = Event::with('packages')->findOrFail($eventId);
            $recommendations = $this->recommendPackagesForEvent($user, $event);

            return response()->json([
                'success' => true,
                'event' => $event,
                'package_recommendations' => $recommendations,
                'budget_analysis' => $this->analyzeBudget($user, $event)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Événement non trouvé.'
            ], 404);
        }
    }

    /**
     * Recommander un budget optimal pour un événement
     */
    public function recommendBudget(Request $request, $eventId)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $event = Event::with('packages')->findOrFail($eventId);
            $budgetRecommendation = $this->calculateOptimalBudget($user, $event);

            return response()->json([
                'success' => true,
                'event' => $event,
                'budget_recommendation' => $budgetRecommendation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du calcul du budget optimal.'
            ], 500);
        }
    }

    /**
     * Recommander le timing optimal pour proposer un sponsorship
     */
    public function recommendTiming(Request $request, $eventId)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $event = Event::findOrFail($eventId);
            $timingRecommendation = $this->calculateOptimalTiming($user, $event);

            return response()->json([
                'success' => true,
                'event' => $event,
                'timing_recommendation' => $timingRecommendation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du calcul du timing optimal.'
            ], 500);
        }
    }

    /**
     * Obtenir des insights sur le profil du sponsor
     */
    public function getSponsorInsights(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        try {
            $insights = $this->generateSponsorInsights($user);

            return response()->json([
                'success' => true,
                'insights' => $insights
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la génération des insights.'
            ], 500);
        }
    }

    /**
     * Recommander des packages pour un événement
     */
    private function recommendPackagesForEvent(User $sponsor, Event $event)
    {
        $packages = $event->packages->sortBy('price');
        $recommendations = [];

        foreach ($packages as $package) {
            $score = $this->calculatePackageScore($sponsor, $package);
            
            if ($score > 0) {
                $recommendations[] = [
                    'package' => $package,
                    'score' => $score,
                    'value_rating' => $this->calculateValueRating($package),
                    'roi_estimate' => $this->estimatePackageROI($sponsor, $package),
                    'recommendation_reason' => $this->getPackageRecommendationReason($sponsor, $package, $score)
                ];
            }
        }

        // Trier par score décroissant
        usort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $recommendations;
    }

    /**
     * Calculer le score d'un package
     */
    private function calculatePackageScore(User $sponsor, $package): float
    {
        $score = 0;
        $sponsorBudget = $sponsor->budget ?? 0;
        
        // Score basé sur l'adéquation budget/prix
        if ($sponsorBudget >= $package->price) {
            $score += 60;
            
            // Bonus si le budget permet d'autres packages aussi
            if ($sponsorBudget >= $package->price * 1.5) {
                $score += 20;
            }
        } else {
            // Score dégressif si budget insuffisant
            $score = max(0, 60 - (($package->price - $sponsorBudget) / $package->price) * 60);
        }

        // Bonus pour les packages populaires
        $packagePopularity = $this->getPackagePopularity($package);
        $score += $packagePopularity * 20;

        return min($score, 100);
    }

    /**
     * Obtenir la popularité d'un package
     */
    private function getPackagePopularity($package): float
    {
        // Calculer combien de fois ce package a été choisi
        $usageCount = \App\Models\SponsorshipTemp::where('package_id', $package->id)->count();
        
        // Normaliser sur une échelle de 0 à 1
        return min($usageCount / 10, 1);
    }

    /**
     * Calculer le rating de valeur d'un package
     */
    private function calculateValueRating($package): string
    {
        $price = $package->price;
        $features = $this->countPackageFeatures($package);
        
        $valueRatio = $features / max($price / 100, 1);
        
        if ($valueRatio > 0.8) {
            return 'excellent';
        } elseif ($valueRatio > 0.6) {
            return 'good';
        } elseif ($valueRatio > 0.4) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * Compter les fonctionnalités d'un package
     */
    private function countPackageFeatures($package): int
    {
        $features = 0;
        
        // Analyser la description pour compter les fonctionnalités
        $description = strtolower($package->description ?? '');
        
        $featureKeywords = [
            'logo', 'banner', 'stand', 'presentation', 'brochure',
            'social media', 'website', 'email', 'newsletter',
            'networking', 'speaking', 'exhibition', 'demo'
        ];
        
        foreach ($featureKeywords as $keyword) {
            if (strpos($description, $keyword) !== false) {
                $features++;
            }
        }
        
        return max($features, 1); // Minimum 1 pour éviter division par zéro
    }

    /**
     * Estimer le ROI d'un package
     */
    private function estimatePackageROI(User $sponsor, $package): float
    {
        $baseROI = 15;
        
        // ROI basé sur le prix du package
        if ($package->price < 500) {
            $baseROI += 5; // Packages bon marché = ROI plus élevé
        } elseif ($package->price > 2000) {
            $baseROI -= 3; // Packages chers = ROI plus risqué
        }
        
        // ROI basé sur les fonctionnalités
        $features = $this->countPackageFeatures($package);
        $baseROI += min($features * 2, 10);
        
        return $baseROI;
    }

    /**
     * Obtenir la raison de recommandation d'un package
     */
    private function getPackageRecommendationReason(User $sponsor, $package, float $score): string
    {
        if ($score >= 80) {
            return "Package parfaitement adapté à votre budget et profil";
        } elseif ($score >= 60) {
            return "Bon rapport qualité-prix pour votre profil";
        } elseif ($score >= 40) {
            return "Package intéressant mais nécessite un budget ajusté";
        } else {
            return "Package moins recommandé pour votre profil actuel";
        }
    }

    /**
     * Analyser le budget du sponsor pour un événement
     */
    private function analyzeBudget(User $sponsor, Event $event)
    {
        $sponsorBudget = $sponsor->budget ?? 0;
        $packages = $event->packages->sortBy('price');
        
        $analysis = [
            'sponsor_budget' => $sponsorBudget,
            'affordable_packages' => 0,
            'stretch_packages' => 0,
            'unaffordable_packages' => 0,
            'recommended_budget_range' => [
                'min' => $packages->min('price'),
                'max' => $packages->max('price') * 1.2
            ]
        ];
        
        foreach ($packages as $package) {
            if ($sponsorBudget >= $package->price) {
                $analysis['affordable_packages']++;
            } elseif ($sponsorBudget >= $package->price * 0.8) {
                $analysis['stretch_packages']++;
            } else {
                $analysis['unaffordable_packages']++;
            }
        }
        
        return $analysis;
    }

    /**
     * Calculer le budget optimal pour un événement
     */
    private function calculateOptimalBudget(User $sponsor, Event $event)
    {
        $packages = $event->packages->sortBy('price');
        $sponsorBudget = $sponsor->budget ?? 0;
        
        $recommendation = [
            'optimal_budget' => $packages->avg('price'),
            'minimum_budget' => $packages->min('price'),
            'maximum_budget' => $packages->max('price'),
            'budget_analysis' => $this->analyzeBudget($sponsor, $event),
            'recommendation_reason' => ''
        ];
        
        if ($sponsorBudget >= $recommendation['maximum_budget']) {
            $recommendation['recommendation_reason'] = "Votre budget vous permet d'accéder à tous les packages";
        } elseif ($sponsorBudget >= $recommendation['optimal_budget']) {
            $recommendation['recommendation_reason'] = "Votre budget est optimal pour la plupart des packages";
        } elseif ($sponsorBudget >= $recommendation['minimum_budget']) {
            $recommendation['recommendation_reason'] = "Votre budget vous permet d'accéder aux packages de base";
        } else {
            $recommendation['recommendation_reason'] = "Considérez augmenter votre budget pour accéder aux packages";
        }
        
        return $recommendation;
    }

    /**
     * Calculer le timing optimal
     */
    private function calculateOptimalTiming(User $sponsor, Event $event)
    {
        $eventDate = $event->date;
        $now = now();
        $daysUntilEvent = $now->diffInDays($eventDate);
        
        $recommendation = [
            'event_date' => $eventDate,
            'days_until_event' => $daysUntilEvent,
            'optimal_submission_time' => $this->calculateOptimalSubmissionTime($eventDate),
            'urgency_level' => $this->calculateUrgencyLevel($daysUntilEvent),
            'timing_recommendation' => $this->getTimingRecommendation($daysUntilEvent)
        ];
        
        return $recommendation;
    }

    /**
     * Calculer le temps optimal de soumission
     */
    private function calculateOptimalSubmissionTime($eventDate)
    {
        $eventDateTime = \Carbon\Carbon::parse($eventDate);
        
        // Recommander de soumettre 30-60 jours avant l'événement
        $optimalSubmissionDate = $eventDateTime->subDays(45);
        
        return [
            'date' => $optimalSubmissionDate->format('Y-m-d'),
            'days_before_event' => 45
        ];
    }

    /**
     * Calculer le niveau d'urgence
     */
    private function calculateUrgencyLevel(int $daysUntilEvent): string
    {
        if ($daysUntilEvent <= 7) {
            return 'critical';
        } elseif ($daysUntilEvent <= 30) {
            return 'high';
        } elseif ($daysUntilEvent <= 60) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Obtenir la recommandation de timing
     */
    private function getTimingRecommendation(int $daysUntilEvent): string
    {
        if ($daysUntilEvent <= 7) {
            return "Urgent ! Soumettez votre proposition immédiatement";
        } elseif ($daysUntilEvent <= 30) {
            return "Soumettez votre proposition cette semaine";
        } elseif ($daysUntilEvent <= 60) {
            return "Bon timing pour soumettre votre proposition";
        } else {
            return "Vous avez du temps, mais ne tardez pas trop";
        }
    }

    /**
     * Générer des insights sur le sponsor
     */
    private function generateSponsorInsights(User $sponsor)
    {
        $insights = [
            'profile_completeness' => $this->calculateProfileCompleteness($sponsor),
            'sponsorship_history' => $this->analyzeSponsorshipHistory($sponsor),
            'recommendations' => $this->getProfileRecommendations($sponsor),
            'market_opportunities' => $this->getMarketOpportunities($sponsor)
        ];
        
        return $insights;
    }

    /**
     * Calculer la complétude du profil
     */
    private function calculateProfileCompleteness(User $sponsor): array
    {
        $fields = ['name', 'email', 'company_name', 'sector', 'budget', 'city', 'phone'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($sponsor->$field)) {
                $completed++;
            }
        }
        
        $percentage = round(($completed / count($fields)) * 100, 2);
        
        return [
            'percentage' => $percentage,
            'completed_fields' => $completed,
            'total_fields' => count($fields),
            'missing_fields' => array_diff($fields, array_filter($fields, function($field) use ($sponsor) {
                return !empty($sponsor->$field);
            }))
        ];
    }

    /**
     * Analyser l'historique de sponsoring
     */
    private function analyzeSponsorshipHistory(User $sponsor): array
    {
        $sponsorships = \App\Models\SponsorshipTemp::where('user_id', $sponsor->id)->get();
        
        return [
            'total_sponsorships' => $sponsorships->count(),
            'approved_sponsorships' => $sponsorships->where('status', 'approved')->count(),
            'pending_sponsorships' => $sponsorships->where('status', 'pending')->count(),
            'total_invested' => $sponsorships->sum('amount'),
            'average_investment' => $sponsorships->avg('amount'),
            'success_rate' => $sponsorships->count() > 0 ? 
                round(($sponsorships->where('status', 'approved')->count() / $sponsorships->count()) * 100, 2) : 0
        ];
    }

    /**
     * Obtenir les recommandations de profil
     */
    private function getProfileRecommendations(User $sponsor): array
    {
        $recommendations = [];
        
        if (empty($sponsor->sector)) {
            $recommendations[] = "Ajoutez votre secteur d'activité pour des recommandations plus précises";
        }
        
        if (empty($sponsor->budget)) {
            $recommendations[] = "Définissez votre budget pour voir les packages adaptés";
        }
        
        if (empty($sponsor->company_name)) {
            $recommendations[] = "Complétez les informations de votre entreprise";
        }
        
        return $recommendations;
    }

    /**
     * Obtenir les opportunités du marché
     */
    private function getMarketOpportunities(User $sponsor): array
    {
        // Analyser les événements récents dans le secteur du sponsor
        $opportunities = [];
        
        if ($sponsor->sector) {
            $recentEvents = Event::where('status', 'upcoming')
                ->whereHas('category', function($query) use ($sponsor) {
                    $query->where('name', 'like', '%' . $sponsor->sector . '%');
                })
                ->limit(5)
                ->get();
                
            foreach ($recentEvents as $event) {
                $opportunities[] = [
                    'event' => $event,
                    'opportunity_score' => $this->calculateOpportunityScore($sponsor, $event)
                ];
            }
        }
        
        return $opportunities;
    }

    /**
     * Calculer le score d'opportunité
     */
    private function calculateOpportunityScore(User $sponsor, Event $event): float
    {
        // Score basé sur la nouveauté de l'événement et la compatibilité
        $sponsorshipCount = \App\Models\SponsorshipTemp::where('event_id', $event->id)->count();
        $noveltyScore = max(0, 100 - ($sponsorshipCount * 10));
        
        $compatibilityScore = $this->recommendationAI->calculateCompatibilityScore($sponsor, $event);
        
        return ($noveltyScore + $compatibilityScore) / 2;
    }

    /**
     * Obtenir le profil utilisateur pour les recommandations
     */
    private function getUserProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'company_name' => $user->company_name,
            'sector' => $user->sector,
            'budget' => $user->budget,
            'city' => $user->city,
            'profile_completeness' => $this->calculateProfileCompleteness($user)['percentage']
        ];
    }
}
