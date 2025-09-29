<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Bronze',
                'price' => 500,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 2m²',
                ],
                'description' => 'Package Bronze - Parfait pour les petites entreprises qui souhaitent s\'engager dans l\'écologie.',
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'price' => 1000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 4m²',
                    'Intervention de 5 minutes',
                    'Distribution de flyers',
                ],
                'description' => 'Package Silver - Idéal pour les entreprises moyennes qui veulent une visibilité accrue.',
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'price' => 2000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 6m²',
                    'Intervention de 10 minutes',
                    'Distribution de flyers',
                    'Bannières publicitaires',
                    'Interview média',
                ],
                'description' => 'Package Gold - Pour les grandes entreprises qui souhaitent un sponsoring premium.',
                'is_active' => true,
            ],
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
    }
}