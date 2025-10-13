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
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->string('status')->default('active')->after('role'); // active|inactive|banned
            $table->timestamp('joined_at')->nullable()->after('status');
            $table->timestamp('last_read_at')->nullable()->after('joined_at');
            
            $table->index('status');
            $table->index('last_read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['last_read_at']);
            $table->dropColumn(['status', 'joined_at', 'last_read_at']);
        });
    }
};