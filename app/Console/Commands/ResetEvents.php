<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Category;
use App\Models\SponsorshipTemp;

class ResetEvents extends Command
{
    protected $signature = 'reset:events';
    protected $description = 'Delete all events and create new test events';

    public function handle()
    {
        $this->info('=== RÉINITIALISATION DES ÉVÉNEMENTS ===');
        
        // Supprimer tous les sponsorships d'abord (pour éviter les contraintes)
        $this->info('1. Suppression des sponsorships...');
        $sponsorshipsCount = SponsorshipTemp::count();
        SponsorshipTemp::truncate();
        $this->info("   ✅ {$sponsorshipsCount} sponsorships supprimés");
        
        // Supprimer les registrations qui référencent les événements
        $this->info('2. Suppression des registrations...');
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('registrations')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info("   ✅ Registrations supprimés");
        
        // Supprimer tous les événements
        $this->info('3. Suppression des événements...');
        $eventsCount = Event::count();
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Event::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info("   ✅ {$eventsCount} événements supprimés");
        
        // Supprimer et recréer les catégories
        $this->info('4. Recréation des catégories...');
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $categories = [
            ['name' => 'Environnement', 'description' => 'Événements liés à la protection de l\'environnement'],
            ['name' => 'Technologie', 'description' => 'Événements technologiques et innovation'],
            ['name' => 'Social', 'description' => 'Événements sociaux et communautaires'],
            ['name' => 'Éducation', 'description' => 'Événements éducatifs et formation'],
            ['name' => 'Santé', 'description' => 'Événements liés à la santé et bien-être'],
        ];
        
        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
        $this->info("   ✅ " . count($categories) . " catégories créées");
        
        // Créer de nouveaux événements de test
        $this->info('5. Création de nouveaux événements...');
        
        $events = [
            [
                'title' => 'CleanTech Summit 2025',
                'description' => 'Sommet international sur les technologies propres et l\'innovation verte. Rassemblement des leaders mondiaux pour discuter des solutions durables.',
                'date' => now()->addDays(30),
                'location' => 'Tunis, Palais des Congrès',
                'capacity' => 500,
                'status' => 'upcoming',
                'category_name' => 'Technologie'
            ],
            [
                'title' => 'Journée Mondiale de l\'Environnement',
                'description' => 'Célébration de la journée mondiale de l\'environnement avec des ateliers, conférences et activités de sensibilisation écologique.',
                'date' => now()->addDays(45),
                'location' => 'Sidi Bou Said, Centre Culturel',
                'capacity' => 200,
                'status' => 'upcoming',
                'category_name' => 'Environnement'
            ],
            [
                'title' => 'Forum de l\'Innovation Sociale',
                'description' => 'Forum dédié aux innovations sociales et aux projets communautaires. Présentation des meilleures pratiques et solutions sociales.',
                'date' => now()->addDays(60),
                'location' => 'Carthage, Institut Supérieur',
                'capacity' => 300,
                'status' => 'upcoming',
                'category_name' => 'Social'
            ],
            [
                'title' => 'Conférence Éducation 4.0',
                'description' => 'Conférence sur l\'avenir de l\'éducation avec les nouvelles technologies et méthodes d\'apprentissage innovantes.',
                'date' => now()->addDays(75),
                'location' => 'Tunis, Université de Tunis',
                'capacity' => 400,
                'status' => 'upcoming',
                'category_name' => 'Éducation'
            ],
            [
                'title' => 'Semaine de la Santé Digitale',
                'description' => 'Événement dédié à la santé digitale, télémédecine et innovations médicales. Exposition des dernières technologies de santé.',
                'date' => now()->addDays(90),
                'location' => 'Monastir, Centre Médical',
                'capacity' => 250,
                'status' => 'upcoming',
                'category_name' => 'Santé'
            ],
            [
                'title' => 'Green Business Conference',
                'description' => 'Conférence sur les entreprises vertes et les modèles économiques durables. Networking et partenariats écologiques.',
                'date' => now()->addDays(15),
                'location' => 'Hammamet, Hôtel Resort',
                'capacity' => 150,
                'status' => 'upcoming',
                'category_name' => 'Environnement'
            ],
            [
                'title' => 'Tech Startup Pitch Day',
                'description' => 'Journée de pitch pour les startups technologiques. Présentation devant des investisseurs et experts du secteur.',
                'date' => now()->addDays(20),
                'location' => 'Tunis, Technopole El Ghazala',
                'capacity' => 100,
                'status' => 'upcoming',
                'category_name' => 'Technologie'
            ],
            [
                'title' => 'Festival de l\'Innovation Sociale',
                'description' => 'Festival célébrant les innovations sociales et les projets communautaires. Ateliers, expositions et conférences.',
                'date' => now()->addDays(35),
                'location' => 'Sousse, Centre Culturel',
                'capacity' => 350,
                'status' => 'upcoming',
                'category_name' => 'Social'
            ]
        ];
        
        foreach ($events as $eventData) {
            $category = Category::where('name', $eventData['category_name'])->first();
            unset($eventData['category_name']);
            
            $event = Event::create(array_merge($eventData, [
                'organizer_id' => 1,
                'category_id' => $category->id,
            ]));
            
            $this->line("   ✅ {$event->title} (ID: {$event->id})");
        }
        
        $this->newLine();
        $this->info('=== RÉSUMÉ ===');
        $this->info("✅ Sponsorships supprimés: {$sponsorshipsCount}");
        $this->info("✅ Événements supprimés: {$eventsCount}");
        $this->info("✅ Catégories créées: " . count($categories));
        $this->info("✅ Nouveaux événements créés: " . count($events));
        
        $this->newLine();
        $this->info('🎯 Instructions pour tester:');
        $this->info('1. Connectez-vous en tant que sponsor');
        $this->info('2. Allez dans "Campagnes" pour voir les nouveaux événements');
        $this->info('3. Créez des sponsorships pour tester le système');
        $this->info('4. Vérifiez que les noms d\'événements s\'affichent correctement dans l\'admin');
        
        $this->newLine();
        $this->info('📋 Nouveaux événements disponibles:');
        foreach ($events as $event) {
            $this->line("   • {$event['title']} - {$event['location']}");
        }
    }
}