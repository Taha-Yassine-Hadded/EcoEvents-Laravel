<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'community_id',
        'user_id',
        'status',
        'is_active',
        'joined_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the community.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active members.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'approved');
    }

    /**
     * Scope for pending members.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
