<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sponsorship_feedback_id',
        'is_like'
    ];

    protected $casts = [
        'is_like' => 'boolean'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le feedback
     */
    public function sponsorshipFeedback(): BelongsTo
    {
        return $this->belongsTo(SponsorshipFeedback::class);
    }
}
