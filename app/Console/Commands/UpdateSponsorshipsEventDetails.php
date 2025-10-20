<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SponsorshipTemp;
use App\Models\Event;

class UpdateSponsorshipsEventDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsorships:update-event-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing sponsorships with event details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mise à jour des détails d\'événements pour les sponsorships existants...');
        
        $sponsorships = SponsorshipTemp::whereNull('event_title')->get();
        
        if ($sponsorships->isEmpty()) {
            $this->info('Aucun sponsorship à mettre à jour.');
            return;
        }
        
        $bar = $this->output->createProgressBar($sponsorships->count());
        $bar->start();
        
        foreach ($sponsorships as $sponsorship) {
            $event = Event::find($sponsorship->event_id);
            
            if ($event) {
                $sponsorship->update([
                    'event_title' => $event->title,
                    'event_description' => $event->description,
                    'event_date' => $event->date,
                    'event_location' => $event->location,
                ]);
                $this->line("\nSponsorship {$sponsorship->id} mis à jour avec '{$event->title}'");
            } else {
                $sponsorship->update([
                    'event_title' => 'Événement supprimé',
                    'event_description' => 'Cet événement a été supprimé de la base de données',
                    'event_date' => null,
                    'event_location' => 'Lieu non spécifié',
                ]);
                $this->line("\nSponsorship {$sponsorship->id} marqué comme événement supprimé");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Mise à jour terminée !');
    }
}