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
<<<<<<< HEAD
=======
        'latitude',
        'longitude',
>>>>>>> main
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
<<<<<<< HEAD
=======
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
>>>>>>> main
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
<<<<<<< HEAD
=======

    /**
     * Check if event has coordinates for map display.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get coordinates as array.
     */
    public function getCoordinates(): ?array
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude
        ];
    }
>>>>>>> main
}