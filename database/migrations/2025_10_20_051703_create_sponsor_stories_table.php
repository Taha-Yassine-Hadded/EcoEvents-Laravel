<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sponsor_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->foreignId('sponsorship_id')->nullable()->constrained('sponsorships_temp')->onDelete('set null');
            
            // Contenu de la story
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('media_type')->default('image'); // image, video, text
            $table->string('media_path')->nullable(); // Chemin vers le fichier média
            $table->string('media_url')->nullable(); // URL du média
            
            // Style et apparence
            $table->string('background_color')->default('#3498db');
            $table->string('text_color')->default('#ffffff');
            $table->string('font_size')->default('medium'); // small, medium, large
            $table->json('style_options')->nullable(); // Options de style supplémentaires
            
            // Métadonnées
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Dates importantes
            $table->timestamp('expires_at'); // Date d'expiration (24h après création)
            $table->timestamps();
            
            // Index pour les performances
            $table->index(['sponsor_id', 'expires_at']);
            $table->index(['is_active', 'expires_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsor_stories');
    }
};