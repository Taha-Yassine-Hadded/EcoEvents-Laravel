<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $attributes = [
        'campaign_id' => 0, // Valeur par défaut pour la compatibilité
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Boot method pour ajouter des validations
     */
    // Validation supprimée du modèle - elle est gérée dans le contrôleur

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
}