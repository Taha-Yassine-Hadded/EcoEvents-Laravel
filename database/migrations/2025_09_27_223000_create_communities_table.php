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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('category'); // Recyclage, Jardinage, Énergie, Transport, etc.
            $table->string('location')->nullable(); // ville/région
            $table->integer('max_members')->default(100);
            $table->unsignedBigInteger('organizer_id');
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable(); // image de la communauté
            $table->timestamps();

            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['category', 'location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};
