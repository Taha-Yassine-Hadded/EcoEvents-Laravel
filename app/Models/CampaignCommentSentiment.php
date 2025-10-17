<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCommentSentiment extends Model
{
    protected $fillable = [
        'campaign_comment_id', 'campaign_id',
        'anger', 'anticipation', 'disgust', 'fear', 'joy', 'sadness', 'surprise', 'trust',
        'positive', 'negative', 'overall_sentiment_score', 'dominant_emotion', 'confidence',
        'detected_language', 'raw_scores', 'matched_words', 'comment_content'
    ];

    protected $casts = [
        'raw_scores' => 'array',
        'matched_words' => 'array',
        'overall_sentiment_score' => 'decimal:4',
        'confidence' => 'decimal:4',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(CampaignComment::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    // Helper pour obtenir l'émotion dominante
    public function getDominantEmotionAttribute(): ?string
    {
        $emotions = [
            'joy' => $this->joy,
            'anger' => $this->anger,
            'sadness' => $this->sadness,
            'fear' => $this->fear,
            'disgust' => $this->disgust,
            'surprise' => $this->surprise,
            'trust' => $this->trust,
            'anticipation' => $this->anticipation,
        ];

        return collect($emotions)->sortByDesc(fn($score, $emotion) => $score)->keys()->first();
    }
}
