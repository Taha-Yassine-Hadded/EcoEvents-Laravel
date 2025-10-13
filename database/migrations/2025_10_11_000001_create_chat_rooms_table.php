<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('target_type')->nullable(); // 'community' | 'campaign' | 'custom'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
