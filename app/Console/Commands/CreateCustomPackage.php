<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Event;

class CreateCustomPackage extends Command
{
    protected $signature = 'packages:create-custom';
    protected $description = 'Create a custom package to test synchronization';

    public function handle()
    {
        $this->info('🎁 Création d\'un package personnalisé pour tester la synchronisation...');
        
        $event = Event::first();
        
        if (!$event) {
            $this->error('❌ Aucun événement trouvé. Exécutez d\'abord: php artisan reset:events');
            return;
        }
        
        $this->info("Événement sélectionné: {$event->title}");
        
        // Créer un package personnalisé
        $package = Package::create([
            'name' => 'Platinum VIP',
            'description' => 'Package Platinum VIP - Le package premium ultime pour les entreprises leaders.',
            'price' => 5000,
            'benefits' => [
                'Logo principal sur tous les supports',
                'Stand VIP de 10m² en position premium',
                'Intervention keynote de 20 minutes',
                'Cocktail privé avec networking exclusif',
                'Interview média complète',
                'Bannières géantes',
                'Distribution de goodies premium',
                'Accès backstage',
                'Déjeuner avec les speakers'
            ],
            'event_id' => $event->id,
            'sort_order' => 0, // Premier dans la liste
            'is_active' => true,
            'is_featured' => true
        ]);
        
        $this->newLine();
        $this->info('✅ Package personnalisé créé avec succès !');
        $this->info("📦 Nom: {$package->name}");
        $this->info("💰 Prix: {$package->price}€");
        $this->info("🎯 Événement: {$event->title}");
        $this->info("⭐ Mis en avant: " . ($package->is_featured ? 'Oui' : 'Non'));
        $this->newLine();
        $this->info('🔄 Ce package devrait maintenant apparaître chez les sponsors !');
        $this->info('💡 Testez en vous connectant comme sponsor et en consultant les détails de l\'événement.');
    }
}
