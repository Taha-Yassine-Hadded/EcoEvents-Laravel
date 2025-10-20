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
        Schema::create('feedback_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sponsorship_feedback_id');
            $table->boolean('is_like')->default(true); // true = like, false = dislike
            $table->timestamps();

            // Index unique pour éviter les doublons
            $table->unique(['user_id', 'sponsorship_feedback_id']);

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sponsorship_feedback_id')->references('id')->on('sponsorship_feedbacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_likes');
    }
};
