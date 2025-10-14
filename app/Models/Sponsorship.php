<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_id',
        'campaign_id',
        'package_id',
        'amount',
        'status',
        'notes',
        'contract_pdf',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Relation avec Sponsor
    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    // Relation avec Campaign
    public function campaign()
    {
        return $this->belongsTo(EchofyCampaign::class, 'campaign_id');
    }

    // Relation avec Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}