<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Ajouter le champ status s'il n'existe pas
            if (!Schema::hasColumn('campaigns', 'status')) {
                $table->string('status')->default('upcoming');
            }
            // Supprimer le champ description s'il existe
            if (Schema::hasColumn('campaigns', 'description')) {
                $table->dropColumn('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Restaurer description si nécessaire
            if (!Schema::hasColumn('campaigns', 'description')) {
                $table->text('description')->nullable();
            }
            // Supprimer status si nécessaire
            if (Schema::hasColumn('campaigns', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
