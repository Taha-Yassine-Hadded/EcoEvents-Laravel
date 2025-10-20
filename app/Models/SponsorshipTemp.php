<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\NotificationService;

class SponsorshipTemp extends Model
{
    use HasFactory;

    protected $table = 'sponsorships_temp';

    protected $fillable = [
        'user_id',
        'event_id',
        'campaign_id', // Garder pour compatibilité
        'package_id',
        'package_name',
        'amount',
        'status',
        'notes',
        'event_title', // Sauvegarder le titre de l'événement
        'event_description', // Sauvegarder la description
        'event_date', // Sauvegarder la date
        'event_location', // Sauvegarder le lieu
    ];

    protected $attributes = [
        'campaign_id' => 0, // Valeur par défaut pour la compatibilité
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Boot method pour ajouter des événements
     */
    protected static function boot()
    {
        parent::boot();

        // Envoyer une notification quand un sponsorship est créé
        static::created(function ($sponsorship) {
            $notificationService = app(NotificationService::class);
            $notificationService->sendNotification(
                $sponsorship->user,
                'sponsorship_created',
                [
                    'user_name' => $sponsorship->user->name,
                    'event_title' => $sponsorship->event_title,
                    'package_name' => $sponsorship->package_name,
                    'amount' => $sponsorship->amount
                ],
                ['email', 'in_app']
            );
        });

        // Envoyer une notification quand un sponsorship est approuvé
        static::updated(function ($sponsorship) {
            if ($sponsorship->wasChanged('status') && $sponsorship->status === 'approved') {
                $notificationService = app(NotificationService::class);
                $notificationService->sendNotification(
                    $sponsorship->user,
                    'sponsorship_approved',
                    [
                        'user_name' => $sponsorship->user->name,
                        'event_title' => $sponsorship->event_title,
                        'package_name' => $sponsorship->package_name,
                        'amount' => $sponsorship->amount,
                        'event_date' => $sponsorship->event_date
                    ],
                    ['email', 'in_app', 'push']
                );
            }
        });
    }

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'événement (utilise la table events de votre collègue)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relation avec le package
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}