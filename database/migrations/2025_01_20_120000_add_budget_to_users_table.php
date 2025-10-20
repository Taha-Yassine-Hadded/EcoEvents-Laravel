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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter le champ budget pour les sponsors
            if (!Schema::hasColumn('users', 'budget')) {
                $table->decimal('budget', 10, 2)->nullable()->after('role')->comment('Budget annuel du sponsor en euros');
            }
            
            // Ajouter le champ sector pour les sponsors
            if (!Schema::hasColumn('users', 'sector')) {
                $table->string('sector', 50)->nullable()->after('budget')->comment('Secteur d\'activité du sponsor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['budget', 'sector']);
        });
    }
};
