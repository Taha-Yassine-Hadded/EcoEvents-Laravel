<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'content',
        'media_urls',
        'category',
        'start_date',
        'end_date',
        'created_by',
        'views_count',
        'shares_count',
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
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'views_count' => 'integer',
            'shares_count' => 'integer',
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
     * Check if the campaign is active.
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
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
}
