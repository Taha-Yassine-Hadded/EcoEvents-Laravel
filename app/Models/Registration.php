<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'registered_at',
    ];

    /**
     * Registration belongs to an event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Registration belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a user is registered for an event
     */
    public static function isUserRegistered($eventId, $userId)
    {
        return self::where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get registration count for an event
     */
    public static function getEventRegistrationCount($eventId)
    {
        return self::where('event_id', $eventId)->count();
    }
}
