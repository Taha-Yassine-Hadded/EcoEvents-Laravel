<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecoevents.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'city' => 'EcoCity',
        ]);

        // Create some organizers & participants
        User::factory()->count(5)->create(['role' => 'organizer']);
        User::factory()->count(20)->create(['role' => 'user']);
    }
}
