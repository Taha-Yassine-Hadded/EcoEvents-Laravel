<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SponsorStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_id',
        'event_id',
        'sponsorship_id',
        'title',
        'content',
        'media_type',
        'media_path',
        'media_url',
        'background_color',
        'text_color',
        'font_size',
        'style_options',
        'views_count',
        'likes_count',
        'is_active',
        'is_featured',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'style_options' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ==================== RELATIONS ====================

    /**
     * Relation avec le sponsor (User)
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Relation avec l'événement
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec le sponsoring
     */
    public function sponsorship(): BelongsTo
    {
        return $this->belongsTo(SponsorshipTemp::class);
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les stories actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les stories non expirées
     */
    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope pour les stories disponibles (actives et non expirées)
     */
    public function scopeAvailable($query)
    {
        return $query->active()->notExpired();
    }

    /**
     * Scope pour les stories en vedette
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope pour les stories d'un sponsor spécifique
     */
    public function scopeBySponsor($query, $sponsorId)
    {
        return $query->where('sponsor_id', $sponsorId);
    }

    /**
     * Scope pour les stories récentes
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // ==================== ACCESSORS ====================

    /**
     * Vérifier si la story est expirée
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Obtenir le temps restant avant expiration
     */
    public function getTimeRemainingAttribute(): string
    {
        if ($this->is_expired) {
            return 'Expirée';
        }

        $remaining = now()->diffInMinutes($this->expires_at);
        
        if ($remaining < 60) {
            return $remaining . ' min';
        } elseif ($remaining < 1440) { // 24 heures
            return floor($remaining / 60) . 'h ' . ($remaining % 60) . 'min';
        } else {
            return floor($remaining / 1440) . ' jour(s)';
        }
    }

    /**
     * Obtenir l'URL complète du média
     */
    public function getMediaUrlAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }

        if ($this->media_path) {
            return asset('storage/' . $this->media_path);
        }

        return null;
    }

    /**
     * Obtenir le pourcentage de progression (temps écoulé)
     */
    public function getProgressPercentageAttribute(): float
    {
        $totalDuration = 24 * 60; // 24 heures en minutes
        $elapsed = now()->diffInMinutes($this->created_at);
        
        return min(100, ($elapsed / $totalDuration) * 100);
    }

    // ==================== MUTATORS ====================

    /**
     * Définir automatiquement la date d'expiration lors de la création
     */
    public function setExpiresAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['expires_at'] = now()->addHours(24);
        } else {
            $this->attributes['expires_at'] = $value;
        }
    }

    // ==================== MÉTHODES ====================

    /**
     * Incrémenter le nombre de vues
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Incrémenter le nombre de likes
     */
    public function incrementLikes(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Marquer comme en vedette
     */
    public function markAsFeatured(): void
    {
        $this->update(['is_featured' => true]);
    }

    /**
     * Retirer de la vedette
     */
    public function unmarkAsFeatured(): void
    {
        $this->update(['is_featured' => false]);
    }

    /**
     * Désactiver la story
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activer la story
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Prolonger la story (pour les stories en vedette)
     */
    public function extend(int $hours = 24): void
    {
        $this->update(['expires_at' => $this->expires_at->addHours($hours)]);
    }

    /**
     * Vérifier si la story peut être prolongée
     */
    public function canBeExtended(): bool
    {
        return $this->is_featured && !$this->is_expired;
    }

    /**
     * Obtenir les statistiques de la story
     */
    public function getStats(): array
    {
        return [
            'views' => $this->views_count,
            'likes' => $this->likes_count,
            'engagement_rate' => $this->views_count > 0 ? round(($this->likes_count / $this->views_count) * 100, 2) : 0,
            'time_remaining' => $this->time_remaining,
            'progress_percentage' => $this->progress_percentage,
            'is_expired' => $this->is_expired,
            'is_featured' => $this->is_featured,
        ];
    }

    /**
     * Obtenir le résumé de la story pour l'affichage
     */
    public function getSummary(int $length = 100): string
    {
        if (!$this->content) {
            return $this->title ?? 'Story sans contenu';
        }

        return strlen($this->content) > $length 
            ? substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    // ==================== MÉTHODES STATIQUES ====================

    /**
     * Supprimer automatiquement les stories expirées
     */
    public static function deleteExpiredStories(): int
    {
        return self::where('expires_at', '<', now())->delete();
    }

    /**
     * Obtenir les stories populaires
     */
    public static function getPopularStories(int $limit = 10)
    {
        return self::available()
            ->orderBy('views_count', 'desc')
            ->orderBy('likes_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les stories en vedette
     */
    public static function getFeaturedStories(int $limit = 5)
    {
        return self::available()
            ->featured()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les stories récentes d'un sponsor
     */
    public static function getRecentStoriesBySponsor(int $sponsorId, int $limit = 5)
    {
        return self::available()
            ->bySponsor($sponsorId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les statistiques globales des stories
     */
    public static function getGlobalStats(): array
    {
        $totalStories = self::count();
        $activeStories = self::available()->count();
        $expiredStories = self::where('expires_at', '<', now())->count();
        $featuredStories = self::available()->featured()->count();
        $totalViews = self::sum('views_count');
        $totalLikes = self::sum('likes_count');

        return [
            'total_stories' => $totalStories,
            'active_stories' => $activeStories,
            'expired_stories' => $expiredStories,
            'featured_stories' => $featuredStories,
            'total_views' => $totalViews,
            'total_likes' => $totalLikes,
            'average_engagement' => $totalViews > 0 ? round(($totalLikes / $totalViews) * 100, 2) : 0,
        ];
    }
}