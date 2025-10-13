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
        Schema::create('community_forum_threads', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('community_id')->constrained('communities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Core content
            $table->string('title', 255);
            $table->longText('content');

            // Moderation/state flags
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_hidden')->default(false);

            // Optional metadata
            $table->json('tags')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['community_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_forum_threads');
    }
};
