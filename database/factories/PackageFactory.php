<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $packages = [
            'Bronze' => [
                'price' => 500,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 2m²',
                ]
            ],
            'Silver' => [
                'price' => 1000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 4m²',
                    'Intervention de 5 minutes',
                    'Distribution de flyers',
                ]
            ],
            'Gold' => [
                'price' => 2000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 6m²',
                    'Intervention de 10 minutes',
                    'Distribution de flyers',
                    'Bannières publicitaires',
                    'Interview média',
                ]
            ],
        ];

        $packageName = $this->faker->randomElement(array_keys($packages));
        $packageData = $packages[$packageName];

        return [
            'name' => $packageName,
            'price' => $packageData['price'],
            'benefits' => $packageData['benefits'],
            'description' => "Package {$packageName} - Parfait pour les entreprises qui souhaitent s'engager dans l'écologie.",
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}