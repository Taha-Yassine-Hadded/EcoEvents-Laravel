<?php

namespace Database\Seeders;

use App\Models\Sponsorship;
use App\Models\Sponsor;
use App\Models\Campaign;
use App\Models\Package;
use Illuminate\Database\Seeder;

class SponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques sponsorships de test
        $sponsors = Sponsor::all();
        $campaigns = Campaign::all();
        $packages = Package::all();

        if ($sponsors->count() > 0 && $campaigns->count() > 0 && $packages->count() > 0) {
            for ($i = 0; $i < 10; $i++) {
                Sponsorship::create([
                    'sponsor_id' => $sponsors->random()->id,
                    'campaign_id' => $campaigns->random()->id,
                    'package_id' => $packages->random()->id,
                    'amount' => fake()->randomFloat(2, 100, 5000),
                    'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'cancelled', 'completed']),
                    'notes' => fake()->optional(0.7)->paragraph(),
                ]);
            }
        }
    }
}