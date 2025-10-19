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
        // Vérifier si la table campaigns existe avant de la renommer
        if (Schema::hasTable('campaigns')) {
            Schema::rename('campaigns', 'echofy_campaigns');
        } else {
            // Si la table campaigns n'existe pas, créer directement echofy_campaigns
            Schema::create('echofy_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->json('media_urls')->nullable();
                $table->string('category')->nullable();
                $table->timestamp('start_date');
                $table->timestamp('end_date');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->integer('views_count')->default(0);
                $table->integer('shares_count')->default(0);
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Renommer de echofy_campaigns vers campaigns
        if (Schema::hasTable('echofy_campaigns')) {
            Schema::rename('echofy_campaigns', 'campaigns');
        }
    }
};
