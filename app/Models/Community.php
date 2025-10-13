<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Community extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category',
        'location',
        'max_members',
        'organizer_id',
        'is_active',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'max_members' => 'integer',
    ];

    /**
     * Get the organizer that created the community.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the members of the community.
     */
    public function members(): HasMany
    {
        return $this->hasMany(CommunityMember::class);
    }

    /**
     * Get active members count.
     */
    public function getActiveMembersCountAttribute(): int
    {
        return $this->members()->where('is_active', true)->count();
    }

    /**
     * Check if community is full.
     */
    public function isFull(): bool
    {
        return $this->active_members_count >= $this->max_members;
    }

    /**
     * Get community image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : asset('assets/images/default-community.jpg');
    }

    /**
     * Get available categories.
     */
    public static function getCategories(): array
    {
        return [
            'recyclage' => 'Recyclage & Zéro Déchet',
            'jardinage' => 'Jardinage Urbain',
            'energie' => 'Énergies Renouvelables',
            'transport' => 'Transport Écologique',
            'sensibilisation' => 'Sensibilisation',
            'nettoyage' => 'Nettoyage Environnemental',
            'biodiversite' => 'Protection Biodiversité',
            'eau' => 'Préservation de l\'Eau',
        ];
    }

    /**
     * Get category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Scope for active communities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for communities by organizer.
     */
    public function scopeByOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }

    /**
     * Get the chat room for this community.
     */
    public function chatRoom()
    {
        return $this->morphOne(\App\Models\ChatRoom::class, 'target')
            ->where('target_type', 'community');
    }
}
