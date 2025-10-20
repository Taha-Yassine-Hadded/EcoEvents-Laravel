<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SponsorStory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanupExpiredStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:cleanup 
                            {--dry-run : Afficher ce qui serait supprimé sans effectuer la suppression}
                            {--force : Forcer la suppression même des stories en vedette}
                            {--older-than=24 : Supprimer les stories plus anciennes que X heures (défaut: 24)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprime automatiquement les stories expirées et leurs fichiers associés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $olderThan = (int) $this->option('older-than');

        $this->info("🧹 Nettoyage des stories expirées...");
        $this->info("📅 Stories plus anciennes que {$olderThan} heures");

        if ($dryRun) {
            $this->warn("🔍 Mode DRY-RUN activé - Aucune suppression ne sera effectuée");
        }

        // Construire la requête pour les stories à supprimer
        $query = SponsorStory::where('expires_at', '<', now());

        if (!$force) {
            // Exclure les stories en vedette sauf si --force est utilisé
            $query->where('is_featured', false);
        }

        $storiesToDelete = $query->get();

        if ($storiesToDelete->isEmpty()) {
            $this->info("✅ Aucune story à supprimer trouvée");
            return 0;
        }

        $this->info("📊 {$storiesToDelete->count()} stories trouvées pour suppression");

        // Afficher les détails des stories à supprimer
        $headers = ['ID', 'Titre', 'Sponsor', 'Type', 'Vues', 'Likes', 'Expirée le', 'En Vedette'];
        $rows = [];

        foreach ($storiesToDelete as $story) {
            $rows[] = [
                $story->id,
                $story->title ?: 'Sans titre',
                $story->sponsor->name ?? 'N/A',
                $story->media_type,
                $story->views_count,
                $story->likes_count,
                $story->expires_at->format('d/m/Y H:i'),
                $story->is_featured ? 'Oui' : 'Non'
            ];
        }

        $this->table($headers, $rows);

        if ($dryRun) {
            $this->info("🔍 DRY-RUN terminé - {$storiesToDelete->count()} stories seraient supprimées");
            return 0;
        }

        // Confirmer la suppression
        if (!$this->confirm("Voulez-vous vraiment supprimer ces {$storiesToDelete->count()} stories ?")) {
            $this->info("❌ Suppression annulée");
            return 0;
        }

        $deletedCount = 0;
        $filesDeleted = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($storiesToDelete->count());
        $progressBar->start();

        foreach ($storiesToDelete as $story) {
            try {
                // Supprimer le fichier média s'il existe
                if ($story->media_path && Storage::disk('public')->exists($story->media_path)) {
                    Storage::disk('public')->delete($story->media_path);
                    $filesDeleted++;
                }

                // Supprimer la story de la base de données
                $story->delete();
                $deletedCount++;

                Log::info('Story supprimée automatiquement', [
                    'story_id' => $story->id,
                    'sponsor_id' => $story->sponsor_id,
                    'expires_at' => $story->expires_at,
                    'is_featured' => $story->is_featured
                ]);

            } catch (\Exception $e) {
                $errors++;
                Log::error('Erreur lors de la suppression de la story', [
                    'story_id' => $story->id,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Afficher le résumé
        $this->info("✅ Nettoyage terminé !");
        $this->info("📊 Stories supprimées: {$deletedCount}");
        $this->info("📁 Fichiers supprimés: {$filesDeleted}");
        
        if ($errors > 0) {
            $this->warn("⚠️  Erreurs rencontrées: {$errors}");
        }

        // Statistiques finales
        $remainingStories = SponsorStory::count();
        $activeStories = SponsorStory::available()->count();
        $featuredStories = SponsorStory::featured()->count();

        $this->info("📈 Statistiques finales:");
        $this->info("   • Stories totales: {$remainingStories}");
        $this->info("   • Stories actives: {$activeStories}");
        $this->info("   • Stories en vedette: {$featuredStories}");

        return 0;
    }
}