<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaign_comment_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_comment_id')->constrained('campaign_comments')->onDelete('cascade');
            // Reference the renamed campaigns table to match earlier rename migration
            $table->foreignId('campaign_id')->constrained('echofy_campaigns')->onDelete('cascade');

            // Émotions NRC-EmoLex (0-1)
            $table->integer('anger')->default(0);
            $table->integer('anticipation')->default(0);
            $table->integer('disgust')->default(0);
            $table->integer('fear')->default(0);
            $table->integer('joy')->default(0);
            $table->integer('sadness')->default(0);
            $table->integer('surprise')->default(0);
            $table->integer('trust')->default(0);

            // Sentiments globaux
            $table->integer('positive')->default(0);
            $table->integer('negative')->default(0);

            // Scores normalisés (0.0-1.0)
            $table->decimal('overall_sentiment_score', 5, 4)->default(0.0); // -1 (négatif) à +1 (positif)
            $table->string('dominant_emotion')->nullable(); // 'joy', 'anger', etc.
            $table->decimal('confidence', 5, 4)->default(0.0);

            // Métadonnées
            $table->string('detected_language')->nullable(); // 'ar', 'fr', 'tunisian'
            $table->json('raw_scores')->nullable(); // Scores détaillés
            $table->json('matched_words')->nullable(); // Mots du lexique matchés
            $table->text('comment_content')->nullable(); // Cache du commentaire
            $table->timestamps();

            $table->index(['campaign_id', 'created_at']);
            $table->index('dominant_emotion');
            $table->index('overall_sentiment_score');
            $table->index('campaign_comment_id');
            $table->unique('campaign_comment_id'); // 1 analyse par commentaire
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_comment_sentiments');
    }
};
