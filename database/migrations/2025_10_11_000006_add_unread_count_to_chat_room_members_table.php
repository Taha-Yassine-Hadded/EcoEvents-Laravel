<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->unsignedInteger('unread_count')->default(0)->after('role');
            $table->index('unread_count');
        });
    }

    public function down(): void
    {
        Schema::table('chat_room_members', function (Blueprint $table) {
            $table->dropIndex(['unread_count']);
            $table->dropColumn('unread_count');
        });
    }
};
