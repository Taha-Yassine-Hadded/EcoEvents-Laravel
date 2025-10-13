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
        Schema::create('community_forum_posts', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('thread_id')->constrained('community_forum_threads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Content
            $table->longText('content');

            // Moderation flag
            $table->boolean('is_hidden')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['thread_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_forum_posts');
    }
};
