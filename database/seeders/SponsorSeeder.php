<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs sponsors
        $sponsorUsers = User::factory(5)->create([
            'role' => 'sponsor'
        ]);

        // Créer les profils sponsors correspondants
        foreach ($sponsorUsers as $user) {
            Sponsor::create([
                'user_id' => $user->id,
                'company_name' => fake()->company(),
                'website' => fake()->url(),
                'phone' => fake()->phoneNumber(),
                'description' => fake()->paragraph(3),
                'status' => fake()->randomElement(['active', 'inactive', 'pending']),
            ]);
        }
    }
}