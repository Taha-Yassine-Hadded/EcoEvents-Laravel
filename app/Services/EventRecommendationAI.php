<?php

namespace App\Services;

use App\Models\Event;
use App\Models\SponsorshipTemp;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class EventRecommendationAI
{
    /**
     * Recommander des événements pour un sponsor
     */
    public function recommendEvents(User $sponsor, int $limit = 10)
    {
        $events = Event::with(['category', 'packages'])
            ->where('status', 'upcoming')
            ->get();

        $recommendations = [];
        
        foreach ($events as $event) {
            $score = $this->calculateCompatibilityScore($sponsor, $event);
            
            if ($score > 0) {
                $recommendations[] = [
                    'event' => $event,
                    'score' => $score,
                    'reasons' => $this->getRecommendationReasons($sponsor, $event, $score),
                    'estimated_roi' => $this->estimateROI($sponsor, $event),
                    'risk_level' => $this->calculateRiskLevel($sponsor, $event)
                ];
            }
        }

        // Trier par score décroissant
        usort($recommendations, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_slice($recommendations, 0, $limit);
    }

    /**
     * Calculer le score de compatibilité sponsor-événement
     */
    private function calculateCompatibilityScore(User $sponsor, Event $event): float
    {
        $score = 0;
        
        // 1. Score basé sur le secteur d'activité (30% du score total)
        $score += $this->sectorCompatibilityScore($sponsor, $event) * 0.3;
        
        // 2. Score basé sur le budget designé (25% du score total)
        $score += $this->budgetCompatibilityScore($sponsor, $event) * 0.25;
        
        // 3. Score basé sur l'historique de sponsoring (20% du score total)
        $score += $this->historicalCompatibilityScore($sponsor, $event) * 0.2;
        
        // 4. Score basé sur la localisation (15% du score total)
        $score += $this->locationCompatibilityScore($sponsor, $event) * 0.15;
        
        // 5. Score basé sur la popularité de l'événement (10% du score total)
        $score += $this->popularityScore($event) * 0.1;
        
        return round($score, 2);
    }

    /**
     * Compatibilité secteur d'activité
     */
    private function sectorCompatibilityScore(User $sponsor, Event $event): float
    {
        // Si le sponsor a un secteur défini et l'événement a une catégorie
        if ($sponsor->sector && $event->category) {
            // Mappage des secteurs avec les catégories d'événements
            $sectorCategoryMapping = [
                'technology' => ['technology', 'innovation', 'startup'],
                'healthcare' => ['health', 'medical', 'wellness'],
                'finance' => ['finance', 'business', 'economy'],
                'education' => ['education', 'training', 'learning'],
                'environment' => ['environment', 'sustainability', 'green'],
                'entertainment' => ['entertainment', 'culture', 'arts'],
                'sports' => ['sports', 'fitness', 'recreation'],
                'food' => ['food', 'culinary', 'hospitality'],
                'fashion' => ['fashion', 'beauty', 'lifestyle'],
                'automotive' => ['automotive', 'transport', 'mobility']
            ];

            $sponsorSector = strtolower($sponsor->sector);
            $eventCategory = strtolower($event->category->name);

            if (isset($sectorCategoryMapping[$sponsorSector])) {
                if (in_array($eventCategory, $sectorCategoryMapping[$sponsorSector])) {
                    return 100; // Match parfait
                }
            }
        }

        // Score par défaut si pas de match spécifique
        return 50;
    }

    /**
     * Compatibilité budget
     */
    private function budgetCompatibilityScore(User $sponsor, Event $event): float
    {
        $sponsorBudget = $sponsor->budget ?? 0;
        $eventPackages = $event->packages;
        
        if ($eventPackages->isEmpty()) {
            return 30; // Score faible si pas de packages
        }

        $minPackagePrice = $eventPackages->min('price');
        $maxPackagePrice = $eventPackages->max('price');
        $avgPackagePrice = $eventPackages->avg('price');

        // Si le budget du sponsor correspond aux prix des packages
        if ($sponsorBudget >= $minPackagePrice && $sponsorBudget <= $maxPackagePrice * 1.5) {
            return 100; // Budget parfaitement adapté
        } elseif ($sponsorBudget >= $minPackagePrice) {
            return 75; // Budget suffisant
        } elseif ($sponsorBudget >= $minPackagePrice * 0.7) {
            return 50; // Budget limite
        } else {
            return 25; // Budget insuffisant
        }
    }

    /**
     * Compatibilité historique
     */
    private function historicalCompatibilityScore(User $sponsor, Event $event): float
    {
        // Analyser l'historique des sponsorships du sponsor
        $sponsorHistory = SponsorshipTemp::where('user_id', $sponsor->id)
            ->where('status', 'approved')
            ->with('event')
            ->get();

        if ($sponsorHistory->isEmpty()) {
            return 60; // Score neutre pour les nouveaux sponsors
        }

        $successfulCategories = $sponsorHistory->pluck('event.category_id')->unique();
        $totalInvested = $sponsorHistory->sum('amount');
        $avgInvestment = $sponsorHistory->avg('amount');

        $score = 60; // Score de base

        // Bonus si le sponsor a déjà sponsorisé des événements similaires
        if ($event->category_id && $successfulCategories->contains($event->category_id)) {
            $score += 30;
        }

        // Bonus si le budget habituel correspond aux packages de l'événement
        $eventAvgPrice = $event->packages->avg('price');
        if ($eventAvgPrice && abs($avgInvestment - $eventAvgPrice) / $eventAvgPrice < 0.3) {
            $score += 10;
        }

        return min($score, 100);
    }

    /**
     * Compatibilité localisation
     */
    private function locationCompatibilityScore(User $sponsor, Event $event): float
    {
        $sponsorLocation = strtolower($sponsor->city ?? '');
        $eventLocation = strtolower($event->location ?? '');

        if (empty($sponsorLocation) || empty($eventLocation)) {
            return 70; // Score neutre si pas d'info de localisation
        }

        // Si même ville
        if (strpos($eventLocation, $sponsorLocation) !== false || 
            strpos($sponsorLocation, $eventLocation) !== false) {
            return 100;
        }

        // Si même région/pays (Tunisie)
        if (strpos($eventLocation, 'tunis') !== false && strpos($sponsorLocation, 'tunis') !== false) {
            return 85;
        }

        return 70; // Score par défaut
    }

    /**
     * Score de popularité de l'événement
     */
    private function popularityScore(Event $event): float
    {
        // Calculer la popularité basée sur le nombre de sponsorships déjà reçus
        $sponsorshipCount = SponsorshipTemp::where('event_id', $event->id)->count();
        
        // Score basé sur la capacité et les sponsorships reçus
        $capacity = $event->capacity ?? 100;
        $popularityRatio = min($sponsorshipCount / max($capacity / 10, 1), 1);
        
        // Score inversé : plus l'événement est populaire, plus le score diminue (moins d'opportunité)
        return 100 - ($popularityRatio * 40);
    }

    /**
     * Obtenir les raisons de la recommandation
     */
    private function getRecommendationReasons(User $sponsor, Event $event, float $score): array
    {
        $reasons = [];

        if ($score >= 80) {
            $reasons[] = "Excellent match avec votre profil";
        } elseif ($score >= 60) {
            $reasons[] = "Bon match avec votre profil";
        }

        // Raisons spécifiques
        if ($sponsor->sector && $event->category) {
            $reasons[] = "Événement dans votre secteur d'activité";
        }

        if ($sponsor->budget) {
            $minPrice = $event->packages->min('price');
            if ($sponsor->budget >= $minPrice) {
                $reasons[] = "Budget adapté aux packages disponibles";
            }
        }

        $sponsorshipCount = SponsorshipTemp::where('event_id', $event->id)->count();
        if ($sponsorshipCount < 5) {
            $reasons[] = "Opportunité unique (peu de sponsors)";
        }

        return $reasons;
    }

    /**
     * Estimer le ROI potentiel
     */
    private function estimateROI(User $sponsor, Event $event): float
    {
        $baseROI = 15; // ROI de base 15%
        
        // Facteurs d'amélioration du ROI
        $eventFactors = $this->getEventROIFactors($event);
        $sponsorFactors = $this->getSponsorROIFactors($sponsor);
        
        return $baseROI + $eventFactors + $sponsorFactors;
    }

    /**
     * Facteurs ROI liés à l'événement
     */
    private function getEventROIFactors(Event $event): float
    {
        $factors = 0;
        
        // Plus l'événement est populaire, plus le ROI potentiel
        $sponsorshipCount = SponsorshipTemp::where('event_id', $event->id)->count();
        if ($sponsorshipCount > 10) {
            $factors += 5; // Événement très demandé
        } elseif ($sponsorshipCount > 5) {
            $factors += 3; // Événement populaire
        }
        
        // Bonus pour les événements tech/innovation
        if ($event->category && in_array(strtolower($event->category->name), ['technology', 'innovation'])) {
            $factors += 3;
        }
        
        return $factors;
    }

    /**
     * Facteurs ROI liés au sponsor
     */
    private function getSponsorROIFactors(User $sponsor): float
    {
        $factors = 0;
        
        // Bonus pour les sponsors expérimentés
        $sponsorshipCount = SponsorshipTemp::where('user_id', $sponsor->id)->count();
        if ($sponsorshipCount > 5) {
            $factors += 2; // Sponsor expérimenté
        }
        
        // Bonus pour les sponsors dans les secteurs à fort ROI
        if ($sponsor->sector && in_array(strtolower($sponsor->sector), ['technology', 'finance'])) {
            $factors += 2;
        }
        
        return $factors;
    }

    /**
     * Calculer le niveau de risque
     */
    private function calculateRiskLevel(User $sponsor, Event $event): string
    {
        $riskScore = 0;
        
        // Risque basé sur l'historique du sponsor
        $sponsorshipCount = SponsorshipTemp::where('user_id', $sponsor->id)->count();
        if ($sponsorshipCount < 2) {
            $riskScore += 2; // Sponsor peu expérimenté
        }
        
        // Risque basé sur la nouveauté de l'événement
        $sponsorshipCount = SponsorshipTemp::where('event_id', $event->id)->count();
        if ($sponsorshipCount < 3) {
            $riskScore += 1; // Événement peu testé
        }
        
        // Risque basé sur le budget
        if ($sponsor->budget) {
            $minPrice = $event->packages->min('price');
            if ($sponsor->budget < $minPrice * 1.2) {
                $riskScore += 1; // Budget serré
            }
        }
        
        if ($riskScore <= 1) {
            return 'low';
        } elseif ($riskScore <= 3) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Obtenir le score de confiance global
     */
    public function getConfidenceScore(): float
    {
        // Score de confiance basé sur la qualité des données
        $dataQuality = $this->assessDataQuality();
        return round($dataQuality, 2);
    }

    /**
     * Évaluer la qualité des données
     */
    private function assessDataQuality(): float
    {
        $totalEvents = Event::count();
        $eventsWithCategories = Event::whereNotNull('category_id')->count();
        $eventsWithPackages = Event::whereHas('packages')->count();
        
        $categoryQuality = $totalEvents > 0 ? ($eventsWithCategories / $totalEvents) * 100 : 0;
        $packageQuality = $totalEvents > 0 ? ($eventsWithPackages / $totalEvents) * 100 : 0;
        
        return ($categoryQuality + $packageQuality) / 2;
    }
}
