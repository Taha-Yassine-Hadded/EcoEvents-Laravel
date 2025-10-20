<?php

// Script pour mettre à jour les sponsorships existants avec les données d'événement
// À exécuter via php artisan tinker ou directement

use App\Models\SponsorshipTemp;
use App\Models\Event;

echo "Mise à jour des sponsorships existants...\n";

try {
    // Récupérer tous les sponsorships qui n'ont pas event_title
    $sponsorships = SponsorshipTemp::whereNull('event_title')
        ->orWhere('event_title', '')
        ->get();
    
    echo "Trouvé " . $sponsorships->count() . " sponsorships à mettre à jour.\n";
    
    $updated = 0;
    $errors = 0;
    
    foreach ($sponsorships as $sponsorship) {
        try {
            // Récupérer l'événement
            $event = Event::find($sponsorship->event_id);
            
            if ($event) {
                // Mettre à jour avec les données de l'événement
                $sponsorship->update([
                    'event_title' => $event->title,
                    'event_description' => $event->description ?? 'Aucune description disponible',
                    'event_date' => $event->date ?? null,
                    'event_location' => $event->location ?? 'Lieu non spécifié',
                ]);
                
                $updated++;
                echo "✓ Sponsorship {$sponsorship->id} mis à jour avec l'événement: {$event->title}\n";
            } else {
                echo "⚠ Sponsorship {$sponsorship->id}: Événement {$sponsorship->event_id} non trouvé\n";
                $errors++;
            }
        } catch (Exception $e) {
            echo "❌ Erreur pour le sponsorship {$sponsorship->id}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    echo "\n🎉 Mise à jour terminée !\n";
    echo "✓ Sponsorships mis à jour: {$updated}\n";
    echo "⚠ Erreurs: {$errors}\n";
    
} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
}
