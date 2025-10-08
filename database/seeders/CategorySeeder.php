<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Predefined categories
        $categories = [
            ['name' => 'Tree Planting', 'description' => 'Events for reforestation and greenery.'],
            ['name' => 'Recycling Drive', 'description' => 'Collect and recycle materials.'],
            ['name' => 'Beach Cleanup', 'description' => 'Cleaning oceans, beaches, and rivers.'],
            ['name' => 'Awareness Workshop', 'description' => 'Workshops to educate on eco-friendly living.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Add a few random ones if we don't have enough categories
        $currentCategoryCount = Category::count();
        if ($currentCategoryCount < 7) {
            Category::factory()->count(7 - $currentCategoryCount)->create();
        }
    }
}
