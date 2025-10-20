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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du template
            $table->string('type'); // email, sms, push, in_app
            $table->string('trigger_event'); // sponsorship_created, payment_due, event_reminder, etc.
            $table->string('subject')->nullable(); // Sujet pour les emails
            $table->text('content'); // Contenu du template avec variables {{variable}}
            $table->json('variables')->nullable(); // Variables disponibles
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};