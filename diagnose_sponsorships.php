<?php

// Script de diagnostic et correction pour les sponsorships
// Bootstrap Laravel pour accéder aux modèles et à la base de données

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SponsorshipTemp;
use App\Models\Event;

echo "=== DIAGNOSTIC DES SPONSORSHIPS ===\n\n";

try {
    // 1. Vérifier la structure de la table
    echo "1. Vérification de la structure de la table:\n";
    $columns = \DB::select("SHOW COLUMNS FROM sponsorships_temp");
    $hasEventId = false;
    $hasEventTitle = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'event_id') {
            $hasEventId = true;
            echo "   ✓ Colonne event_id trouvée\n";
        }
        if ($column->Field === 'event_title') {
            $hasEventTitle = true;
            echo "   ✓ Colonne event_title trouvée\n";
        }
    }
    
    if (!$hasEventId) {
        echo "   ❌ Colonne event_id manquante!\n";
    }
    if (!$hasEventTitle) {
        echo "   ❌ Colonne event_title manquante!\n";
    }
    
    echo "\n2. Vérification des données:\n";
    
    // 2. Compter les sponsorships
    $totalSponsorships = SponsorshipTemp::count();
    echo "   Total sponsorships: {$totalSponsorships}\n";
    
    // 3. Compter ceux avec event_id
    $withEventId = SponsorshipTemp::whereNotNull('event_id')->count();
    echo "   Avec event_id: {$withEventId}\n";
    
    // 4. Compter ceux avec event_title
    $withEventTitle = SponsorshipTemp::whereNotNull('event_title')->where('event_title', '!=', '')->count();
    echo "   Avec event_title: {$withEventTitle}\n";
    
    // 5. Compter ceux sans event_id ni event_title
    $withoutData = SponsorshipTemp::where(function($q) {
        $q->whereNull('event_id')->orWhere('event_id', 0);
    })->where(function($q) {
        $q->whereNull('event_title')->orWhere('event_title', '');
    })->count();
    echo "   Sans données d'événement: {$withoutData}\n";
    
    echo "\n3. Exemples de données problématiques:\n";
    $problematicSponsorships = SponsorshipTemp::where(function($q) {
        $q->whereNull('event_id')->orWhere('event_id', 0);
    })->where(function($q) {
        $q->whereNull('event_title')->orWhere('event_title', '');
    })->take(3)->get();
    
    foreach ($problematicSponsorships as $sponsorship) {
        echo "   ID: {$sponsorship->id} | Event ID: " . ($sponsorship->event_id ?? 'NULL') . " | Event Title: " . ($sponsorship->event_title ?? 'NULL') . " | Campaign ID: " . ($sponsorship->campaign_id ?? 'NULL') . "\n";
    }
    
    echo "\n4. Tentative de correction:\n";
    
    if ($withoutData > 0) {
        echo "   Correction des sponsorships sans données d'événement...\n";
        
        $corrected = 0;
        $errors = 0;
        
        foreach ($problematicSponsorships as $sponsorship) {
            try {
                // Essayer de trouver un événement correspondant
                $event = null;
                
                // Si on a un campaign_id, essayer de le mapper à un event_id
                if ($sponsorship->campaign_id) {
                    // Logique de mapping campaign_id -> event_id
                    // Pour l'instant, on va essayer de trouver un événement par défaut
                    $event = Event::first();
                }
                
                if ($event) {
                    $sponsorship->update([
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'event_description' => $event->description ?? 'Aucune description disponible',
                        'event_date' => $event->date ?? null,
                        'event_location' => $event->location ?? 'Lieu non spécifié',
                    ]);
                    
                    $corrected++;
                    echo "   ✓ Sponsorship {$sponsorship->id} corrigé avec l'événement: {$event->title}\n";
                } else {
                    echo "   ⚠ Sponsorship {$sponsorship->id}: Aucun événement trouvé pour la correction\n";
                }
            } catch (Exception $e) {
                echo "   ❌ Erreur pour le sponsorship {$sponsorship->id}: " . $e->getMessage() . "\n";
                $errors++;
            }
        }
        
        echo "\n   Résultat de la correction:\n";
        echo "   ✓ Corrigés: {$corrected}\n";
        echo "   ❌ Erreurs: {$errors}\n";
    } else {
        echo "   Aucune correction nécessaire.\n";
    }
    
    echo "\n=== DIAGNOSTIC TERMINÉ ===\n";
    
} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
}
