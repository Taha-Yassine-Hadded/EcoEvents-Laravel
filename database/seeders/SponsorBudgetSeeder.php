<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SponsorBudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mettre à jour les sponsors existants avec budget et sector
        $sponsors = User::where('role', 'sponsor')->get();
        
        $budgetSectors = [
            ['budget' => 50000, 'sector' => 'technology'],
            ['budget' => 75000, 'sector' => 'healthcare'],
            ['budget' => 100000, 'sector' => 'finance'],
            ['budget' => 30000, 'sector' => 'education'],
            ['budget' => 60000, 'sector' => 'environment'],
            ['budget' => 40000, 'sector' => 'entertainment'],
            ['budget' => 80000, 'sector' => 'sports'],
            ['budget' => 35000, 'sector' => 'food'],
            ['budget' => 45000, 'sector' => 'fashion'],
            ['budget' => 90000, 'sector' => 'automotive'],
        ];
        
        foreach ($sponsors as $index => $sponsor) {
            $budgetSector = $budgetSectors[$index % count($budgetSectors)];
            
            $sponsor->update([
                'budget' => $budgetSector['budget'],
                'sector' => $budgetSector['sector']
            ]);
            
            $this->command->info("Sponsor {$sponsor->name} mis à jour avec budget: {$budgetSector['budget']}€ et secteur: {$budgetSector['sector']}");
        }
        
        // Créer quelques nouveaux sponsors avec budget et sector
        $newSponsors = [
            [
                'name' => 'TechCorp Solutions',
                'email' => 'contact@techcorp.com',
                'password' => Hash::make('password'),
                'company_name' => 'TechCorp Solutions',
                'phone' => '+33 1 23 45 67 89',
                'city' => 'Paris',
                'address' => '123 Avenue des Champs-Élysées',
                'bio' => 'Entreprise spécialisée dans les solutions technologiques innovantes.',
                'role' => 'sponsor',
                'budget' => 120000,
                'sector' => 'technology'
            ],
            [
                'name' => 'GreenEnergy Corp',
                'email' => 'info@greenenergy.com',
                'password' => Hash::make('password'),
                'company_name' => 'GreenEnergy Corp',
                'phone' => '+33 1 98 76 54 32',
                'city' => 'Lyon',
                'address' => '456 Rue de la République',
                'bio' => 'Leader dans les énergies renouvelables et le développement durable.',
                'role' => 'sponsor',
                'budget' => 85000,
                'sector' => 'environment'
            ],
            [
                'name' => 'SportMax Events',
                'email' => 'events@sportmax.com',
                'password' => Hash::make('password'),
                'company_name' => 'SportMax Events',
                'phone' => '+33 1 55 44 33 22',
                'city' => 'Marseille',
                'address' => '789 Boulevard de la Liberté',
                'bio' => 'Organisateur d\'événements sportifs et de compétitions.',
                'role' => 'sponsor',
                'budget' => 65000,
                'sector' => 'sports'
            ]
        ];
        
        foreach ($newSponsors as $sponsorData) {
            $sponsor = User::create($sponsorData);
            $this->command->info("Nouveau sponsor créé: {$sponsor->name} avec budget: {$sponsor->budget}€");
        }
        
        $this->command->info('Seeder SponsorBudget terminé avec succès !');
    }
}
