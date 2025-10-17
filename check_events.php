<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;

try {
    echo "=== VÉRIFICATION DES ÉVÉNEMENTS ===\n";
    echo "Total d'événements: " . Event::count() . "\n\n";
    
    echo "Événements par statut:\n";
    $statusCounts = Event::selectRaw('status, count(*) as count')->groupBy('status')->get();
    foreach($statusCounts as $stat) {
        echo "- {$stat->status}: {$stat->count}\n";
    }
    
    echo "\n=== ÉVÉNEMENTS DISPONIBLES POUR SPONSORING ===\n";
    $availableEvents = Event::where('status', '!=', 'cancelled')->get();
    echo "Nombre d'événements disponibles: " . $availableEvents->count() . "\n\n";
    
    foreach($availableEvents as $event) {
        echo "- ID: {$event->id} | Titre: {$event->title} | Statut: {$event->status} | Date: {$event->date}\n";
    }
    
    echo "\n=== ÉVÉNEMENTS EXCLUS (annulés) ===\n";
    $cancelledEvents = Event::where('status', 'cancelled')->get();
    echo "Nombre d'événements annulés: " . $cancelledEvents->count() . "\n";
    
    foreach($cancelledEvents as $event) {
        echo "- ID: {$event->id} | Titre: {$event->title} | Statut: {$event->status}\n";
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
