<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'website',
        'logo',
        'phone',
        'address',
        'city',
        'interests',
        'role',
        'bio',
        'profile_image',
        'budget',
        'sector',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'interests' => 'array',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    /**
     * Check if user is organizer
     */
    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    /**
     * Get user's full address
     */
    public function getFullAddressAttribute(): string
    {
        return $this->address . ', ' . $this->city;
    }

    /**
     * Get profile image URL - retourne null si pas d'image
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : null;
    }

    /**
     * Get user initials from name
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }

        return $initials ?: 'NN'; // Par défaut si aucun nom
    }

    /**
     * Check if user has profile image
     */
    public function hasProfileImage(): bool
    {
        return !empty($this->profile_image) &&
            file_exists(storage_path('app/public/' . $this->profile_image));
    }

    /**
     * Check if user is sponsor
     */
    public function isSponsor(): bool
    {
        return $this->role === 'sponsor';
    }

    /**
     * Relation avec Sponsor
     */
    public function sponsor()
    {
        return $this->hasOne(Sponsor::class);
    }

    /**
     * Get the identifier that will be stored in the JWT subject claim.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get communities created by this user (if organizer).
     */
    public function createdCommunities()
    {
        return $this->hasMany(Community::class, 'organizer_id');
    }

    /**
     * Get communities this user is member of.
     */
    public function memberCommunities()
    {
        return $this->belongsToMany(Community::class, 'community_members', 'user_id', 'community_id')
                    ->withPivot(['status', 'is_active', 'joined_at'])
                    ->withTimestamps();
    }
    public function likes(): HasMany
    {
        return $this->hasMany(CampaignLike::class, 'user_id');
    }

    /**
     * Relation avec les likes des commentaires
     */
    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class, 'user_id');

    }

    public function likedCampaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_likes', 'user_id', 'campaign_id')
                    ->withTimestamps();
    }

    /**
     * Relation avec les sponsorships temporaires
     */
    public function sponsorshipsTemp(): HasMany
    {
        return $this->hasMany(SponsorshipTemp::class, 'user_id');
    }

    /**
     * Relation avec les stories du sponsor
     */
    public function sponsorStories(): HasMany
    {
        return $this->hasMany(SponsorStory::class, 'sponsor_id');
    }
}
