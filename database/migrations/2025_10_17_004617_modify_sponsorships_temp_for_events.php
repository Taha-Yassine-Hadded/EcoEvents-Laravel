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
            // Supprimer l'ancienne contrainte de clé étrangère
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
            
            // Ajouter une nouvelle colonne event_id sans contrainte de clé étrangère
            $table->unsignedBigInteger('event_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            // Supprimer event_id
            $table->dropColumn('event_id');
            
            // Restaurer campaign_id avec sa contrainte
            $table->foreignId('campaign_id')->constrained('echofy_campaigns')->onDelete('cascade');
        });
    }
};