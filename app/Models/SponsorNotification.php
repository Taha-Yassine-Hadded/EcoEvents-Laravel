<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'type',
        'trigger_event',
        'subject',
        'content',
        'data',
        'status',
        'sent_at',
        'read_at',
        'external_id'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime'
    ];

    /**
     * Relation avec l'utilisateur sponsor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    /**
     * Marquer comme envoyé
     */
    public function markAsSent(string $externalId = null): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'external_id' => $externalId
        ]);
    }

    /**
     * Marquer comme lu
     */
    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    /**
     * Marquer comme échoué
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}