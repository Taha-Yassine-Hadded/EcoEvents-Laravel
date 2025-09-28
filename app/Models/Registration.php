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
}
