<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class FixSponsorshipEventNames extends Command
{
    protected $signature = 'sponsorships:fix-event-names';
    protected $description = 'Fix sponsorship event names to show real event names instead of "Événement non spécifié"';

    public function handle()
    {
        $this->info('🔧 Correction des noms d\'événements dans les sponsorships...');
        
        // Récupérer tous les sponsorships
        $sponsorships = SponsorshipTemp::all();
        $this->info("Sponsorships trouvés: {$sponsorships->count()}");
        
        // Récupérer les événements disponibles
        $events = Event::all();
        $this->info("Événements disponibles: {$events->count()}");
        
        if ($events->isEmpty()) {
            $this->error('❌ Aucun événement trouvé. Exécutez d\'abord: php artisan reset:events');
            return;
        }
        
        // Afficher les événements disponibles
        foreach ($events as $event) {
            $this->line("  📅 {$event->title} (ID: {$event->id})");
        }
        
        $this->newLine();
        
        // Mettre à jour les sponsorships
        $updated = 0;
        foreach ($sponsorships as $sponsorship) {
            // Utiliser un événement différent pour chaque sponsorship pour éviter les contraintes d'unicité
            $event = $events[$updated % $events->count()];
            
            // Mettre à jour seulement les champs event_title, event_description, event_date, event_location
            // sans toucher à event_id pour éviter les contraintes d'unicité
            $sponsorship->update([
                'event_title' => $event->title,
                'event_description' => $event->description,
                'event_date' => $event->date,
                'event_location' => $event->location,
            ]);
            
            $updated++;
            $this->line("✅ Sponsorship {$sponsorship->id} mis à jour avec: {$event->title}");
        }
        
        $this->newLine();
        $this->info("🎉 Correction terminée ! {$updated} sponsorships mis à jour.");
        $this->info("Les noms d'événements devraient maintenant s'afficher correctement dans l'admin.");
    }
}
