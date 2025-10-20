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
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            $table->string('event_title')->nullable()->after('notes');
            $table->text('event_description')->nullable()->after('event_title');
            $table->datetime('event_date')->nullable()->after('event_description');
            $table->string('event_location')->nullable()->after('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsorships_temp', function (Blueprint $table) {
            $table->dropColumn(['event_title', 'event_description', 'event_date', 'event_location']);
        });
    }
};