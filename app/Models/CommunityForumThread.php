<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunityForumThread extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'community_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_locked',
        'is_hidden',
        'tags',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_hidden' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Relationships
     */
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(CommunityForumPost::class, 'thread_id');
    }
}
