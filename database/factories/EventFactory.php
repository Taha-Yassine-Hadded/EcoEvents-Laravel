<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date'        => $this->faker->dateTimeBetween('+1 days', '+6 months'),
            'location'    => $this->faker->city(),
            'capacity'    => $this->faker->numberBetween(20, 200),
            'status'      => $this->faker->randomElement(['upcoming', 'ongoing', 'completed', 'cancelled']),
            'organizer_id'=> User::factory(), // create an organizer user
            'category_id' => Category::factory(),
        ];
    }
}
