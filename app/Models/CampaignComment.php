<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignComment extends Model
{
    protected $fillable = [
        'campaign_id',
        'user_id',
        'content',
    ];

    /**
     * Relation avec l'utilisateur qui a posté le commentaire
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la campagne associée
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Relation avec les likes du commentaire
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }

    /**
     * Accesseur pour le nombre de likes
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }
}
