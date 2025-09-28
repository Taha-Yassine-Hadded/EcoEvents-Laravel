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
        // Make sure we have organizers & categories first
        $organizers = User::where('role', 'organizer')->get();
        $categories = Category::all();

        // Create events
        foreach ($organizers as $organizer) {
            Event::factory()->count(3)->create([
                'organizer_id' => $organizer->id,
                'category_id'  => $categories->random()->id,
            ]);
        }
    }
}
