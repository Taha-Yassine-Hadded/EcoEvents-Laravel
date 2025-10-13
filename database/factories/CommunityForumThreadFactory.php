<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityForumThread>
 */
class CommunityForumThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker;
        return [
            'title' => rtrim($faker->sentence(mt_rand(3, 8)), '.'),
            'content' => $faker->paragraphs(mt_rand(2, 5), true),
            'is_pinned' => false,
            'is_locked' => false,
            'is_hidden' => false,
            'tags' => $faker->boolean(40) ? $faker->words(mt_rand(1, 3)) : null,
        ];
    }
}
