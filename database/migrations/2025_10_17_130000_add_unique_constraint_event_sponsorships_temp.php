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
            // Ajouter une nouvelle contrainte unique sur user_id et event_id
            // (sans supprimer l'ancienne pour éviter les conflits de clés étrangères)
            $table->unique(['user_id', 'event_id'], 'unique_user_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            $table->dropUnique('unique_user_event');
        });
    }
};
