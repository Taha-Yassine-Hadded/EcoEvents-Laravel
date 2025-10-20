<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Event;

class CreateTestPackages extends Command
{
    protected $signature = 'packages:create-test';
    protected $description = 'Create test packages for existing events';

    public function handle()
    {
        $this->info('🎁 Création de packages de test pour les événements...');
        
        $events = Event::take(3)->get();
        
        if ($events->isEmpty()) {
            $this->error('❌ Aucun événement trouvé. Exécutez d\'abord: php artisan reset:events');
            return;
        }
        
        $this->info("Événements trouvés: {$events->count()}");
        
        foreach ($events as $event) {
            $this->line("📅 Création de packages pour: {$event->title}");
            
            // Package Bronze
            Package::create([
                'name' => 'Bronze',
                'description' => 'Package Bronze - Parfait pour les petites entreprises qui souhaitent s\'impliquer dans l\'événement.',
                'price' => 500,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 2m²',
                    'Distribution de flyers'
                ],
                'event_id' => $event->id,
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => false
            ]);
            
            // Package Silver
            Package::create([
                'name' => 'Silver',
                'description' => 'Package Silver - Idéal pour les entreprises moyennes qui veulent une visibilité accrue.',
                'price' => 1000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 4m²',
                    'Intervention de 5 minutes',
                    'Distribution de flyers',
                    'Bannières publicitaires'
                ],
                'event_id' => $event->id,
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => false
            ]);
            
            // Package Gold
            Package::create([
                'name' => 'Gold',
                'description' => 'Package Gold - Pour les grandes entreprises qui souhaitent une visibilité maximale.',
                'price' => 2000,
                'benefits' => [
                    'Logo sur les supports de communication',
                    'Mention dans les réseaux sociaux',
                    'Stand de 6m²',
                    'Intervention de 10 minutes',
                    'Distribution de flyers',
                    'Bannières publicitaires',
                    'Interview média',
                    'Accès VIP',
                    'Cocktail de networking'
                ],
                'event_id' => $event->id,
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true
            ]);
            
            $this->line("   ✅ 3 packages créés pour {$event->title}");
        }
        
        $this->newLine();
        $this->info('🎉 Packages de test créés avec succès !');
        $this->info('Vous pouvez maintenant accéder à la gestion des packages dans l\'admin.');
        $this->newLine();
        $this->info('📋 Packages créés par événement :');
        $this->line('   • Bronze - 500€ (Stand 2m², logo, réseaux sociaux)');
        $this->line('   • Silver - 1000€ (Stand 4m², intervention 5min, bannières)');
        $this->line('   • Gold - 2000€ (Stand 6m², intervention 10min, VIP) ⭐');
    }
}
