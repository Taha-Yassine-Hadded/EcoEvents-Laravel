<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EchofyCampaign extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'echofy_campaigns';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'objectives',
        'actions',
        'contact_info',
        'media_urls',
        'category',
        'start_date',
        'end_date',
        'created_by',
        'views_count',
        'shares_count',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'media_urls' => 'array',
            'objectives' => 'array',
            'actions' => 'array',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'views_count' => 'integer',
            'shares_count' => 'integer',
            'status' => 'string',
        ];
    }

    /**
     * Get the user that created the campaign.join user->compain
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the comments for the campaign.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(CampaignComment::class);
    }

    /**
     * Get the likes for the campaign.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CampaignLike::class);
    }

    /**
     * Get the status of the campaign (upcoming, active, ended).
     */
    public function getStatusAttribute(): string
    {
        $now = now();
        if ($this->start_date > $now) {
            return 'upcoming';
        } elseif ($this->start_date <= $now && $this->end_date >= $now) {
            return 'active';
        } else {
            return 'ended';
        }
    }

    /**
     * Get the main image URL for the campaign.
     */
    public function getMainImageUrlAttribute(): ?string
    {
        $media = $this->media_urls ?? [];
        return !empty($media['images']) ? asset('storage/' . $media['images'][0]) : null;
    }

    /**
     * Get the count of comments.
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }

    /**
     * Get the count of likes.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Increment the views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment the shares count.
     */
    public function incrementShares(): void
    {
        $this->increment('shares_count');
    }

    /**
     * Relation avec Sponsorships
     */
    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    /**
     * Relation avec Sponsors à travers Sponsorships
     */
    public function sponsors()
    {
        return $this->hasManyThrough(Sponsor::class, Sponsorship::class);
    }

    /**
     * Boot the model to automatically set the created_by field.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (auth()->check()) {
                $campaign->created_by = auth()->id();
            }
        });
    }
}
