<?php

namespace App\Services;

use App\Models\Community;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CommunityRecommendationService
{
    private array $ecoInterests = [
        'recyclage', 'compostage', 'énergie renouvelable', 'biodiversité', 'climat',
        'pollution', 'eau', 'forêt', 'agriculture biologique', 'transport durable',
        'zéro déchet', 'économie circulaire', 'énergies vertes', 'protection animale',
        'permaculture', 'énergies solaires', 'éolien', 'géothermie', 'hydroélectricité',
        'vélo', 'transport public', 'covoiturage', 'alimentation locale', 'circuits courts',
        'consommation responsable', 'upcycling', 'réparation', 'partage', 'location',
        'énergies propres', 'efficacité énergétique', 'isolation', 'rénovation thermique'
    ];

    /**
     * Génère des recommandations de communautés pour un utilisateur
     */
    public function getRecommendations(User $user, int $limit = 5): array
    {
        $cacheKey = "recommendations_user_{$user->id}";

        return Cache::remember($cacheKey, 3600, function () use ($user, $limit) {
            return $this->generateRecommendations($user, $limit);
        });
    }

    /**
     * Génère les recommandations basées sur l'IA
     */
    private function generateRecommendations(User $user, int $limit): array
    {
        // 1. Analyser les centres d'intérêt de l'utilisateur
        $userInterests = $this->analyzeUserInterests($user);

        // 2. Obtenir toutes les communautés actives
        $communities = Community::where('is_active', true)
            ->with(['organizer:id,name'])
            ->get();

        // 3. Calculer les scores de recommandation
        $recommendations = [];

        foreach ($communities as $community) {
            $score = $this->calculateRecommendationScore($user, $userInterests, $community);

            if ($score > 0.1) { // Seuil minimum
                $recommendations[] = [
                    'community' => $community,
                    'score' => $score,
                    'reasons' => $this->getRecommendationReasons($userInterests, $community),
                    'match_percentage' => round($score * 100, 1)
                ];
            }
        }

        // 4. Trier par score et limiter
        usort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($recommendations, 0, $limit);
    }

    /**
     * Analyse les centres d'intérêt de l'utilisateur
     */
    private function analyzeUserInterests(User $user): array
    {
        $interests = [];

        // Analyser les messages de l'utilisateur
        $userMessages = ChatMessage::where('user_id', $user->id)
            ->where('message_type', 'text')
            ->pluck('content')
            ->toArray();

        // Analyser le contenu des messages pour détecter les centres d'intérêt
        foreach ($userMessages as $message) {
            $messageInterests = $this->extractInterestsFromText($message);
            $interests = array_merge($interests, $messageInterests);
        }

        // Ajouter des centres d'intérêt par défaut basés sur l'âge et la localisation
        $defaultInterests = $this->getDefaultInterestsForUser($user);
        $interests = array_merge($interests, $defaultInterests);

        // Compter les occurrences et retourner les plus fréquents
        $interestCounts = array_count_values($interests);
        arsort($interestCounts);

        return array_keys(array_slice($interestCounts, 0, 10, true));
    }

    /**
     * Extrait les centres d'intérêt d'un texte
     */
    private function extractInterestsFromText(string $text): array
    {
        $text = strtolower($text);
        $foundInterests = [];

        foreach ($this->ecoInterests as $interest) {
            if (strpos($text, strtolower($interest)) !== false) {
                $foundInterests[] = $interest;
            }
        }

        return $foundInterests;
    }

    /**
     * Obtient les centres d'intérêt par défaut pour un utilisateur
     */
    private function getDefaultInterestsForUser(User $user): array
    {
        $defaultInterests = [];

        // Basé sur l'âge
        if ($user->age < 25) {
            $defaultInterests = ['transport durable', 'vélo', 'consommation responsable'];
        } elseif ($user->age < 40) {
            $defaultInterests = ['énergie renouvelable', 'biodiversité', 'zéro déchet'];
        } else {
            $defaultInterests = ['agriculture biologique', 'permaculture', 'protection animale'];
        }

        return $defaultInterests;
    }

    /**
     * Calcule le score de recommandation pour une communauté
     */
    private function calculateRecommendationScore(User $user, array $userInterests, Community $community): float
    {
        $score = 0.0;

        // 1. Similarité des mots-clés (40% du score)
        $keywordSimilarity = $this->calculateKeywordSimilarity($userInterests, $community->keywords ?? []);
        $score += $keywordSimilarity * 0.4;

        // 2. Activité de la communauté (20% du score)
        $activityScore = $this->calculateActivityScore($community);
        $score += $activityScore * 0.2;

        // 3. Localisation (15% du score)
        $locationScore = $this->calculateLocationScore($user, $community);
        $score += $locationScore * 0.15;

        // 4. Taille de la communauté (10% du score)
        $sizeScore = $this->calculateSizeScore($community);
        $score += $sizeScore * 0.1;

        // 5. Catégorie (15% du score)
        $categoryScore = $this->calculateCategoryScore($userInterests, $community->category);
        $score += $categoryScore * 0.15;

        return min(1.0, $score);
    }

    /**
     * Calcule la similarité entre les intérêts utilisateur et les mots-clés communauté
     */
    private function calculateKeywordSimilarity(array $userInterests, array $communityKeywords): float
    {
        if (empty($userInterests) || empty($communityKeywords)) {
            return 0.0;
        }

        $userSet = array_flip($userInterests);
        $communitySet = array_flip($communityKeywords);

        $intersection = count(array_intersect_key($userSet, $communitySet));
        $union = count(array_unique(array_merge($userInterests, $communityKeywords)));

        return $union > 0 ? $intersection / $union : 0.0;
    }

    /**
     * Calcule le score d'activité d'une communauté
     */
    private function calculateActivityScore(Community $community): float
    {
        // Basé sur le nombre de membres et l'activité récente
        $memberCount = $community->members()->count();
        $maxMembers = $community->max_members ?? 100;

        $activityRatio = min(1.0, $memberCount / $maxMembers);

        return $activityRatio;
    }

    /**
     * Calcule le score de localisation
     */
    private function calculateLocationScore(User $user, Community $community): float
    {
        // Si l'utilisateur et la communauté sont dans la même ville
        if ($user->location && $community->location &&
            strtolower($user->location) === strtolower($community->location)) {
            return 1.0;
        }

        // Si pas de localisation spécifiée, score neutre
        return 0.5;
    }

    /**
     * Calcule le score de taille de communauté
     */
    private function calculateSizeScore(Community $community): float
    {
        $memberCount = $community->members()->count();

        // Score optimal entre 20 et 200 membres
        if ($memberCount >= 20 && $memberCount <= 200) {
            return 1.0;
        } elseif ($memberCount < 20) {
            return $memberCount / 20;
        } else {
            return max(0.5, 200 / $memberCount);
        }
    }

    /**
     * Calcule le score de catégorie
     */
    private function calculateCategoryScore(array $userInterests, string $category): float
    {
        $categoryKeywords = $this->getCategoryKeywords($category);
        return $this->calculateKeywordSimilarity($userInterests, $categoryKeywords);
    }

    /**
     * Obtient les mots-clés associés à une catégorie
     */
    private function getCategoryKeywords(string $category): array
    {
        $categoryMap = [
            'Énergies Renouvelables' => ['énergie', 'solaire', 'éolien', 'renouvelable', 'vert'],
            'Recyclage & Zéro Déchet' => ['recyclage', 'zéro déchet', 'compostage', 'réduction'],
            'Biodiversité & Nature' => ['biodiversité', 'nature', 'protection', 'faune', 'flore'],
            'Transport Durable' => ['transport', 'vélo', 'durable', 'mobilité', 'électrique'],
            'Agriculture Écologique' => ['agriculture', 'biologique', 'permaculture', 'local'],
            'Climat & Environnement' => ['climat', 'environnement', 'pollution', 'carbone'],
        ];

        return $categoryMap[$category] ?? [];
    }

    /**
     * Génère les raisons de recommandation
     */
    private function getRecommendationReasons(array $userInterests, Community $community): array
    {
        $reasons = [];

        // Vérifier les mots-clés en commun
        $commonKeywords = array_intersect($userInterests, $community->keywords ?? []);
        if (!empty($commonKeywords)) {
            $reasons[] = "Intérêts communs: " . implode(', ', $commonKeywords);
        }

        // Vérifier la catégorie
        if ($community->category) {
            $reasons[] = "Catégorie: {$community->category}";
        }

        // Vérifier la localisation
        if ($community->location) {
            $reasons[] = "Localisation: {$community->location}";
        }

        // Vérifier la taille
        $memberCount = $community->members()->count();
        if ($memberCount > 0) {
            $reasons[] = "{$memberCount} membres actifs";
        }

        return $reasons;
    }

    /**
     * Met à jour les mots-clés d'une communauté basés sur son contenu
     */
    public function updateCommunityKeywords(Community $community): void
    {
        $keywords = $this->extractKeywordsFromCommunity($community);
        $community->update(['keywords' => $keywords]);
    }

    /**
     * Extrait les mots-clés d'une communauté
     */
    private function extractKeywordsFromCommunity(Community $community): array
    {
        $text = strtolower($community->name . ' ' . $community->description);
        $foundKeywords = [];

        foreach ($this->ecoInterests as $interest) {
            if (strpos($text, strtolower($interest)) !== false) {
                $foundKeywords[] = $interest;
            }
        }

        // Ajouter des mots-clés basés sur la catégorie
        $categoryKeywords = $this->getCategoryKeywords($community->category);
        $foundKeywords = array_merge($foundKeywords, $categoryKeywords);

        return array_unique($foundKeywords);
    }

    /**
     * Obtient les communautés populaires
     */
    public function getPopularCommunities(int $limit = 5): array
    {
        return Community::where('is_active', true)
            ->withCount('members')
            ->orderBy('members_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Obtient les communautés récentes
     */
    public function getRecentCommunities(int $limit = 5): array
    {
        return Community::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
