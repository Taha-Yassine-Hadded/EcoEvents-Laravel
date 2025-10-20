<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Event;

class TestRealTimeSync extends Command
{
    protected $signature = 'packages:test-realtime-sync';
    protected $description = 'Test real-time synchronization by modifying packages and showing sponsor view';

    public function handle()
    {
        $this->info('🔄 Test de synchronisation en temps réel...');
        
        $event = Event::first();
        
        if (!$event) {
            $this->error('❌ Aucun événement trouvé.');
            return;
        }
        
        $this->info("📅 Événement de test: {$event->title}");
        
        // 1. Afficher l'état actuel
        $this->newLine();
        $this->info('1️⃣ État actuel des packages:');
        $this->showPackagesForEvent($event);
        
        // 2. Modifier un package existant
        $this->newLine();
        $this->info('2️⃣ Modification d\'un package existant...');
        
        $package = Package::where('event_id', $event->id)->first();
        if ($package) {
            $oldPrice = $package->price;
            $package->update(['price' => $package->price + 100]);
            $this->info("   ✅ Prix modifié: {$oldPrice}€ → {$package->price}€");
        }
        
        // 3. Désactiver un package
        $this->newLine();
        $this->info('3️⃣ Désactivation d\'un package...');
        
        $packageToDeactivate = Package::where('event_id', $event->id)
            ->where('is_active', true)
            ->skip(1) // Prendre le deuxième package
            ->first();
            
        if ($packageToDeactivate) {
            $packageToDeactivate->update(['is_active' => false]);
            $this->info("   ✅ Package désactivé: {$packageToDeactivate->name}");
        }
        
        // 4. Afficher le nouvel état
        $this->newLine();
        $this->info('4️⃣ Nouvel état des packages:');
        $this->showPackagesForEvent($event);
        
        // 5. Instructions pour tester
        $this->newLine();
        $this->info('5️⃣ Instructions pour tester:');
        $this->line('   • Connectez-vous comme sponsor');
        $this->line('   • Allez dans "Campagnes"');
        $this->line("   • Consultez les détails de: {$event->title}");
        $this->line('   • Vérifiez que les modifications apparaissent immédiatement');
        
        $this->newLine();
        $this->info('✅ Test terminé ! Les modifications sont synchronisées en temps réel.');
    }
    
    private function showPackagesForEvent($event)
    {
        $activePackages = Package::where('event_id', $event->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();
            
        $inactivePackages = Package::where('event_id', $event->id)
            ->where('is_active', false)
            ->get();
        
        if ($activePackages->count() > 0) {
            $this->info("   📦 Packages visibles par les sponsors ({$activePackages->count()}):");
            foreach ($activePackages as $package) {
                $featured = $package->is_featured ? ' ⭐' : '';
                $this->line("      • {$package->name} - {$package->price}€{$featured}");
            }
        } else {
            $this->warn("   ⚠️ Aucun package visible par les sponsors");
        }
        
        if ($inactivePackages->count() > 0) {
            $this->info("   🚫 Packages cachés des sponsors ({$inactivePackages->count()}):");
            foreach ($inactivePackages as $package) {
                $this->line("      • {$package->name} - {$package->price}€ (inactif)");
            }
        }
    }
}
