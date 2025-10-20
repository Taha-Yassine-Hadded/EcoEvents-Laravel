<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SponsorshipTemp;
use App\Services\NotificationService;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {--user-id= : ID de l\'utilisateur spécifique}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester le système de notifications';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Utilisateur avec l'ID {$userId} non trouvé.");
                return;
            }
            $this->testNotificationsForUser($user);
        } else {
            $this->testNotificationsForAllSponsors();
        }
    }

    /**
     * Tester les notifications pour un utilisateur spécifique
     */
    private function testNotificationsForUser(User $user)
    {
        $this->info("Test des notifications pour: {$user->name} ({$user->email})");

        // Test notification de sponsorship créé
        $this->notificationService->sendNotification(
            $user,
            'sponsorship_created',
            [
                'user_name' => $user->name,
                'event_title' => 'Test Event 2024',
                'package_name' => 'Package Premium',
                'amount' => 1500
            ],
            ['email', 'in_app']
        );
        $this->line("✓ Notification 'sponsorship_created' envoyée");

        // Test notification de paiement dû
        $this->notificationService->sendNotification(
            $user,
            'payment_due',
            [
                'user_name' => $user->name,
                'event_title' => 'Test Event 2024',
                'amount' => 1500,
                'due_date' => '15/12/2024'
            ],
            ['email', 'sms']
        );
        $this->line("✓ Notification 'payment_due' envoyée");

        // Test notification d'événement bientôt
        $this->notificationService->sendNotification(
            $user,
            'event_starting_soon',
            [
                'user_name' => $user->name,
                'event_title' => 'Test Event 2024',
                'time_remaining' => '24 heures',
                'event_date' => '20/12/2024 14:00',
                'event_location' => 'Paris, France',
                'package_name' => 'Package Premium'
            ],
            ['email', 'push', 'sms']
        );
        $this->line("✓ Notification 'event_starting_soon' envoyée");

        $this->info("Test terminé pour {$user->name}");
    }

    /**
     * Tester les notifications pour tous les sponsors
     */
    private function testNotificationsForAllSponsors()
    {
        $sponsors = User::where('role', 'sponsor')->get();
        
        if ($sponsors->isEmpty()) {
            $this->warn("Aucun sponsor trouvé dans la base de données.");
            return;
        }

        $this->info("Test des notifications pour {$sponsors->count()} sponsors...");

        foreach ($sponsors as $sponsor) {
            $this->testNotificationsForUser($sponsor);
        }

        $this->info("Test terminé pour tous les sponsors !");
    }
}