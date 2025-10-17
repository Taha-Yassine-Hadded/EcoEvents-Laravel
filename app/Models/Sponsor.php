<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'logo',
        'website',
        'phone',
        'description',
        'status'
    ];

    // Relation avec User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec Sponsorships
    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    // Relation avec Campaigns à travers Sponsorships
    public function campaigns()
    {
        return $this->hasManyThrough(Campaign::class, Sponsorship::class);
    }
}