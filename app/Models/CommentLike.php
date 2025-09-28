<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentLike extends Model
{
    protected $fillable = [
        'comment_id',
        'user_id',
    ];

    /**
     * Relation avec le commentaire
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(CampaignComment::class, 'comment_id');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
