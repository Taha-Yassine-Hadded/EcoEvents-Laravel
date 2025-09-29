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
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            // Ajouter une contrainte unique pour empêcher les doublons
            $table->unique(['user_id', 'campaign_id'], 'unique_user_campaign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            $table->dropUnique('unique_user_campaign');
        });
    }
};