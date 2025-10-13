<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('last_message_id')->nullable()->after('is_private');
            $table->timestamp('last_activity_at')->nullable()->after('last_message_id');
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropIndex(['last_activity_at']);
            $table->dropColumn(['last_message_id', 'last_activity_at']);
        });
    }
};
