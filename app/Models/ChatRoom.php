<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'target_type', 'target_id', 'name', 'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function members()
    {
        return $this->hasMany(ChatRoomMember::class);
    }

    // Relation polymorphique pour les communautés
    public function target()
    {
        return $this->morphTo();
    }

    // Relation spécifique pour les communautés
    public function community()
    {
        return $this->belongsTo(\App\Models\Community::class, 'target_id')
            ->where('target_type', 'community');
    }

    // Scopes
    public function scopeForCommunity($query, $communityId)
    {
        return $query->where('target_type', 'community')
                    ->where('target_id', $communityId);
    }
}
