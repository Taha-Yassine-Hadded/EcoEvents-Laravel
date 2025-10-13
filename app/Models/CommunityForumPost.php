<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommunityForumPost extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'content',
        'is_hidden',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function thread()
    {
        return $this->belongsTo(CommunityForumThread::class, 'thread_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
