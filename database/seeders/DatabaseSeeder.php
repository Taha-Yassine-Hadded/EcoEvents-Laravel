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
        // Call your specific seeders in correct order
        $this->call([
            CategorySeeder::class,
            EventSeeder::class,
            UserSeeder::class,
<<<<<<< HEAD
=======
            PackageSeeder::class,
            SponsorSeeder::class,
            SponsorshipSeeder::class,
>>>>>>> 5d3dbd2521bb6bbf65406afa9a464a6a1650fa02
        ]);
    }
}