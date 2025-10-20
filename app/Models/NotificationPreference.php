<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'in_app_enabled',
        'timing_preferences'
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'timing_preferences' => 'array'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifier si un type de notification est activé
     */
    public function isEnabled(string $type): bool
    {
        return match($type) {
            'email' => $this->email_enabled,
            'sms' => $this->sms_enabled,
            'push' => $this->push_enabled,
            'in_app' => $this->in_app_enabled,
            default => false
        };
    }

    /**
     * Types de notifications disponibles
     */
    public static function getAvailableTypes(): array
    {
        return [
            'sponsorship_updates' => 'Mises à jour des sponsorships',
            'payment_reminders' => 'Rappels de paiement',
            'event_alerts' => 'Alertes d\'événements',
            'contract_deadlines' => 'Échéances de contrats',
            'performance_reports' => 'Rapports de performance',
            'system_maintenance' => 'Maintenance système',
            'marketing_offers' => 'Offres marketing'
        ];
    }
}