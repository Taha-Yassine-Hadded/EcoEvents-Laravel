<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les packages de sponsoring
        $this->call(PackageSeeder::class);
        
        // Créer quelques sponsors de test
        $this->call(SponsorSeeder::class);
        
        // Créer quelques sponsorships de test
        $this->call(SponsorshipSeeder::class);
    }
}
