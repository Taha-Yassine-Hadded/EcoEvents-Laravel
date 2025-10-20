<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Package;
use App\Models\Event;

class TestPackageSync extends Command
{
    protected $signature = 'packages:test-sync';
    protected $description = 'Test package synchronization between admin and sponsor views';

    public function handle()
    {
        $this->info('🔄 Test de synchronisation des packages...');
        
        $events = Event::with('packages')->get();
        
        if ($events->isEmpty()) {
            $this->error('❌ Aucun événement trouvé.');
            return;
        }
        
        foreach ($events as $event) {
            $this->newLine();
            $this->info("📅 Événement: {$event->title}");
            $this->line("   ID: {$event->id}");
            $this->line("   Date: " . $event->date->format('d/m/Y'));
            
            // Packages actifs (ceux que voient les sponsors)
            $activePackages = Package::where('event_id', $event->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('price')
                ->get();
            
            if ($activePackages->count() > 0) {
                $this->info("   📦 Packages actifs ({$activePackages->count()}):");
                foreach ($activePackages as $package) {
                    $featured = $package->is_featured ? ' ⭐' : '';
                    $this->line("      • {$package->name} - {$package->price}€{$featured}");
                    if ($package->benefits && count($package->benefits) > 0) {
                        $this->line("        Bénéfices: " . count($package->benefits) . " éléments");
                    }
                }
            } else {
                $this->warn("   ⚠️ Aucun package actif trouvé");
            }
            
            // Packages inactifs (cachés des sponsors)
            $inactivePackages = Package::where('event_id', $event->id)
                ->where('is_active', false)
                ->get();
            
            if ($inactivePackages->count() > 0) {
                $this->info("   🚫 Packages inactifs ({$inactivePackages->count()}):");
                foreach ($inactivePackages as $package) {
                    $this->line("      • {$package->name} - {$package->price}€ (caché)");
                }
            }
        }
        
        $this->newLine();
        $this->info('✅ Test terminé !');
        $this->info('💡 Les packages "actifs" sont visibles par les sponsors');
        $this->info('💡 Les packages "inactifs" sont cachés des sponsors');
        $this->info('💡 Les packages sont triés par sort_order puis par prix');
    }
}
