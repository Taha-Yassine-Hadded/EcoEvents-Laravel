<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'trigger_event',
        'subject',
        'content',
        'variables',
        'is_active'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Relation avec les notifications envoyées
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(SponsorNotification::class, 'template_id');
    }

    /**
     * Obtenir le contenu avec les variables remplacées
     */
    public function getContentWithVariables(array $data): string
    {
        $content = $this->content;
        
        foreach ($data as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        
        return $content;
    }

    /**
     * Obtenir le sujet avec les variables remplacées
     */
    public function getSubjectWithVariables(array $data): string
    {
        if (!$this->subject) {
            return '';
        }
        
        $subject = $this->subject;
        
        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
        }
        
        return $subject;
    }
}