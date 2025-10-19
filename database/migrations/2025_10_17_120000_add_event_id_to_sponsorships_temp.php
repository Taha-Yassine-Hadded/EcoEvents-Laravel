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
        if (!Schema::hasColumn('sponsorships_temp', 'event_id')) {
            Schema::table('sponsorships_temp', function (Blueprint $table) {
                // Ajouter la colonne event_id pour lier aux événements du collègue
                $table->unsignedBigInteger('event_id')->nullable()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sponsorships_temp', 'event_id')) {
            Schema::table('sponsorships_temp', function (Blueprint $table) {
                $table->dropColumn('event_id');
            });
        }
    }
};
