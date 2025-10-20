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
        Schema::create('sponsor_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Sponsor
            $table->unsignedBigInteger('template_id')->nullable(); // Template utilisé
            $table->string('type'); // email, sms, push, in_app
            $table->string('trigger_event'); // Événement déclencheur
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('data')->nullable(); // Données supplémentaires
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'read'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('external_id')->nullable(); // ID du service externe (SMS, email)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('notification_templates')->onDelete('set null');
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsor_notifications');
    }
};