<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Basic registration info
            $table->enum('status', ['registered', 'attended', 'cancelled', 'no-show'])->default('registered');
            $table->timestamp('registered_at')->useCurrent();
            
            // New fields for eco-events
            $table->string('role')->nullable(); // Volunteer role (dropdown)
            $table->string('skills')->nullable(); // Skills they can contribute (dropdown)
            $table->boolean('has_transportation')->default(false); // If they have transportation
            $table->boolean('has_participated_before')->default(false); // Previous participation
            $table->string('emergency_contact')->nullable(); // Emergency contact info

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
