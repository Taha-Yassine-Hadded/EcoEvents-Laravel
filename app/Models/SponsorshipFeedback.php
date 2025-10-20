<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SponsorshipFeedback extends Model
{
    use HasFactory;

    protected $table = 'sponsorship_feedbacks';

    protected $fillable = [
        'sponsorship_temp_id',
        'event_id',
        'user_id',
        'feedback_type',
        'rating',
        'title',
        'content',
        'is_anonymous',
        'status',
        'parent_feedback_id',
        'tags',
        'attachments',
        'metadata'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'rating' => 'integer',
        'tags' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope pour les feedbacks publiés
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope pour un événement spécifique
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Scope pour un type de feedback spécifique
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }

    /**
     * Scope pour un utilisateur spécifique
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Types de feedback
    const TYPE_PRE_EVENT = 'pre_event';
    const TYPE_POST_EVENT = 'post_event';
    const TYPE_PACKAGE_FEEDBACK = 'package_feedback';
    const TYPE_ORGANIZER_FEEDBACK = 'organizer_feedback';
    const TYPE_GENERAL_COMMENT = 'general_comment';
    const TYPE_IMPROVEMENT_SUGGESTION = 'improvement_suggestion';
    const TYPE_EXPERIENCE_SHARING = 'experience_sharing';

    // Statuts
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_MODERATED = 'moderated';
    const STATUS_HIDDEN = 'hidden';

    /**
     * Relation avec l'utilisateur (sponsor)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'événement
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation avec le sponsorship
     */
    public function sponsorshipTemp(): BelongsTo
    {
        return $this->belongsTo(SponsorshipTemp::class);
    }

    /**
     * Relation avec le feedback parent (pour les réponses)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SponsorshipFeedback::class, 'parent_feedback_id');
    }

    /**
     * Relation avec les réponses (feedback enfants)
     */
    public function children(): HasMany
    {
        return $this->hasMany(SponsorshipFeedback::class, 'parent_feedback_id');
    }

    /**
     * Relation avec les likes
     */
    public function likes(): HasMany
    {
        return $this->hasMany(FeedbackLike::class);
    }

    /**
     * Obtenir le nom d'affichage de l'utilisateur
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Sponsor Anonyme';
        }
        
        return $this->user ? $this->user->name : 'Utilisateur Supprimé';
    }

    /**
     * Obtenir l'avatar d'affichage
     */
    public function getDisplayAvatarAttribute(): string
    {
        if ($this->is_anonymous) {
            return asset('assets/images/default-avatar.png');
        }
        
        if ($this->user && $this->user->profile_image) {
            return asset('storage/' . $this->user->profile_image);
        }
        
        return asset('assets/images/default-avatar.png');
    }

    /**
     * Obtenir le type de feedback formaté
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            self::TYPE_PRE_EVENT => 'Avant l\'événement',
            self::TYPE_POST_EVENT => 'Après l\'événement',
            self::TYPE_PACKAGE_FEEDBACK => 'Feedback sur le package',
            self::TYPE_ORGANIZER_FEEDBACK => 'Feedback sur l\'organisateur',
            self::TYPE_GENERAL_COMMENT => 'Commentaire général',
            self::TYPE_IMPROVEMENT_SUGGESTION => 'Suggestion d\'amélioration',
            self::TYPE_EXPERIENCE_SHARING => 'Partage d\'expérience'
        ];

        return $types[$this->feedback_type] ?? 'Autre';
    }

    /**
     * Obtenir l'icône du type de feedback
     */
    public function getTypeIconAttribute(): string
    {
        $icons = [
            self::TYPE_PRE_EVENT => 'fas fa-calendar-check',
            self::TYPE_POST_EVENT => 'fas fa-calendar-times',
            self::TYPE_PACKAGE_FEEDBACK => 'fas fa-box',
            self::TYPE_ORGANIZER_FEEDBACK => 'fas fa-user-tie',
            self::TYPE_GENERAL_COMMENT => 'fas fa-comment',
            self::TYPE_IMPROVEMENT_SUGGESTION => 'fas fa-lightbulb',
            self::TYPE_EXPERIENCE_SHARING => 'fas fa-share-alt'
        ];

        return $icons[$this->feedback_type] ?? 'fas fa-comment';
    }

    /**
     * Obtenir les étoiles de notation
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        $rating = $this->rating ?? 0;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $stars .= '<i class="fas fa-star text-warning"></i>';
            } else {
                $stars .= '<i class="far fa-star text-muted"></i>';
            }
        }
        
        return $stars;
    }

    /**
     * Obtenir le temps écoulé depuis la création
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Vérifier si le feedback a des réponses
     */
    public function hasReplies(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Obtenir le nombre de likes
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Vérifier si un utilisateur a liké ce feedback
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope pour les feedbacks avec notation
     */
    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }

    /**
     * Scope pour les feedbacks récents
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Obtenir la note moyenne pour un événement
     */
    public static function getAverageRatingForEvent(int $eventId): float
    {
        return self::forEvent($eventId)
            ->withRating()
            ->avg('rating') ?? 0;
    }

    /**
     * Obtenir les statistiques de feedback pour un événement
     */
    public static function getEventFeedbackStats(int $eventId): array
    {
        $feedbacks = self::forEvent($eventId)->published();
        
        return [
            'total_feedbacks' => $feedbacks->count(),
            'average_rating' => $feedbacks->withRating()->avg('rating') ?? 0,
            'rating_distribution' => [
                '5_stars' => $feedbacks->where('rating', 5)->count(),
                '4_stars' => $feedbacks->where('rating', 4)->count(),
                '3_stars' => $feedbacks->where('rating', 3)->count(),
                '2_stars' => $feedbacks->where('rating', 2)->count(),
                '1_star' => $feedbacks->where('rating', 1)->count(),
            ],
            'feedback_types' => $feedbacks->selectRaw('feedback_type, COUNT(*) as count')
                ->groupBy('feedback_type')
                ->pluck('count', 'feedback_type')
                ->toArray()
        ];
    }

    /**
     * Obtenir les feedbacks les plus utiles
     */
    public static function getMostHelpful(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return self::published()
            ->with(['user', 'event', 'likes'])
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->limit($limit)
            ->get();
    }
}