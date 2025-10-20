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
        Schema::create('sponsorship_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sponsorship_temp_id')->nullable();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('feedback_type', [
                'pre_event',
                'post_event', 
                'package_feedback',
                'organizer_feedback',
                'general_comment',
                'improvement_suggestion',
                'experience_sharing'
            ]);
            $table->integer('rating')->nullable(); // 1-5 étoiles
            $table->string('title')->nullable();
            $table->text('content');
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['draft', 'published', 'moderated', 'hidden'])->default('published');
            $table->unsignedBigInteger('parent_feedback_id')->nullable(); // Pour les réponses
            $table->json('tags')->nullable(); // Tags pour catégoriser
            $table->json('attachments')->nullable(); // URLs des fichiers joints
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();

            // Index
            $table->index(['event_id', 'feedback_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('parent_feedback_id');

            // Foreign keys
            $table->foreign('sponsorship_temp_id')->references('id')->on('sponsorships_temp')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_feedback_id')->references('id')->on('sponsorship_feedbacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorship_feedbacks');
    }
};
