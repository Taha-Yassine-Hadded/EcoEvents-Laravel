<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'capacity',
        'status',
        'organizer_id',
        'category_id',
        'img'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Event belongs to an organizer (User).
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Event belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Event has many registrations (participants).
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Event has many packages.
     */
    public function packages()
    {
        return $this->hasMany(Package::class)->ordered();
    }

    /**
     * Event has many active packages.
     */
    public function activePackages()
    {
        return $this->hasMany(Package::class)->active()->ordered();
    }

    /**
     * Event has many sponsorships temp.
     */
    public function sponsorshipsTemp()
    {
        return $this->hasMany(SponsorshipTemp::class);
    }
}