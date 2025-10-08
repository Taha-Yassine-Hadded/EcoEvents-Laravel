<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we have categories first
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get organizers or create some if none exist
        $organizers = User::where('role', 'organizer')->get();
        
        if ($organizers->isEmpty()) {
            // Create some organizer users if none exist
            $organizers = User::factory()->count(2)->create(['role' => 'organizer']);
            $this->command->info('Created 2 organizer users for events.');
        }

        // Create events
        foreach ($organizers as $organizer) {
            Event::factory()->count(3)->create([
                'organizer_id' => $organizer->id,
                'category_id'  => $categories->random()->id,
            ]);
        }
        
        $this->command->info('Events created successfully!');
    }
}
