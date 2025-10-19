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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->json('interests')->nullable()->after('city');
            $table->enum('role', ['user', 'organizer', 'sponsor', 'admin'])->default('user')->after('interests');
            $table->text('bio')->nullable()->after('role');
            $table->string('profile_image')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address', 
                'city',
                'interests',
                'role',
                'bio',
                'profile_image'
            ]);
        });
    }
};
