<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use App\Services\NotificationService;
use Carbon\Carbon;

class ScheduleSponsorReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsors:schedule-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Programmer les rappels automatiques pour les sponsors';

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
        $this->info('Programmation des rappels automatiques...');

        // 1. Rappels de paiement (7 jours après approbation)
        $this->schedulePaymentReminders();

        // 2. Rappels d'événements (24h avant)
        $this->scheduleEventReminders();

        // 3. Rappels de contrats (30 jours avant expiration)
        $this->scheduleContractReminders();

        // 4. Rapports mensuels (1er de chaque mois)
        $this->scheduleMonthlyReports();

        $this->info('Rappels programmés avec succès !');
    }

    /**
     * Programmer les rappels de paiement
     */
    private function schedulePaymentReminders()
    {
        $this->info('Programmation des rappels de paiement...');

        $sponsorships = SponsorshipTemp::where('status', 'approved')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->where('created_at', '>', Carbon::now()->subDays(8))
            ->with('user', 'event')
            ->get();

        foreach ($sponsorships as $sponsorship) {
            $this->notificationService->sendNotification(
                $sponsorship->user,
                'payment_due',
                [
                    'user_name' => $sponsorship->user->name,
                    'event_title' => $sponsorship->event_title,
                    'amount' => $sponsorship->amount,
                    'due_date' => Carbon::now()->addDays(3)->format('d/m/Y')
                ],
                ['email', 'sms']
            );

            $this->line("Rappel paiement programmé pour: {$sponsorship->user->name}");
        }

        $this->info("Rappels de paiement programmés: {$sponsorships->count()}");
    }

    /**
     * Programmer les rappels d'événements
     */
    private function scheduleEventReminders()
    {
        $this->info('Programmation des rappels d\'événements...');

        $events = Event::where('status', 'published')
            ->whereBetween('date', [
                Carbon::now()->addHours(20),
                Carbon::now()->addHours(28)
            ])
            ->with(['sponsorshipsTemp.user'])
            ->get();

        foreach ($events as $event) {
            foreach ($event->sponsorshipsTemp as $sponsorship) {
                if ($sponsorship->status === 'approved') {
                    $this->notificationService->sendNotification(
                        $sponsorship->user,
                        'event_starting_soon',
                        [
                            'user_name' => $sponsorship->user->name,
                            'event_title' => $event->title,
                            'time_remaining' => '24 heures',
                            'event_date' => $event->date->format('d/m/Y H:i'),
                            'event_location' => $event->location ?? 'Lieu à confirmer',
                            'package_name' => $sponsorship->package_name
                        ],
                        ['email', 'push', 'sms']
                    );

                    $this->line("Rappel événement programmé pour: {$sponsorship->user->name} - {$event->title}");
                }
            }
        }

        $this->info("Rappels d'événements programmés pour: {$events->count()} événements");
    }

    /**
     * Programmer les rappels de contrats
     */
    private function scheduleContractReminders()
    {
        $this->info('Programmation des rappels de contrats...');

        $sponsorships = SponsorshipTemp::where('status', 'approved')
            ->where('created_at', '<=', Carbon::now()->subDays(335)) // 365 - 30 jours
            ->where('created_at', '>', Carbon::now()->subDays(336))
            ->with('user', 'event')
            ->get();

        foreach ($sponsorships as $sponsorship) {
            $this->notificationService->sendNotification(
                $sponsorship->user,
                'contract_expiring',
                [
                    'user_name' => $sponsorship->user->name,
                    'event_title' => $sponsorship->event_title,
                    'days_remaining' => 30,
                    'expiry_date' => Carbon::now()->addDays(30)->format('d/m/Y'),
                    'package_name' => $sponsorship->package_name
                ],
                ['email', 'in_app']
            );

            $this->line("Rappel contrat programmé pour: {$sponsorship->user->name}");
        }

        $this->info("Rappels de contrats programmés: {$sponsorships->count()}");
    }

    /**
     * Programmer les rapports mensuels
     */
    private function scheduleMonthlyReports()
    {
        $this->info('Programmation des rapports mensuels...');

        // Envoyer les rapports mensuels le 1er de chaque mois
        if (Carbon::now()->day === 1) {
            $sponsors = \App\Models\User::where('role', 'sponsor')->get();

            foreach ($sponsors as $sponsor) {
                $stats = $this->getMonthlyStats($sponsor);

                $this->notificationService->sendNotification(
                    $sponsor,
                    'monthly_report',
                    [
                        'user_name' => $sponsor->name,
                        'month_year' => Carbon::now()->subMonth()->format('F Y'),
                        'active_sponsorships' => $stats['active_sponsorships'],
                        'events_count' => $stats['events_count'],
                        'total_invested' => $stats['total_invested'],
                        'impressions' => $stats['impressions'],
                        'clicks' => $stats['clicks'],
                        'ctr' => $stats['ctr'],
                        'roi' => $stats['roi']
                    ],
                    ['email']
                );

                $this->line("Rapport mensuel programmé pour: {$sponsor->name}");
            }

            $this->info("Rapports mensuels programmés: {$sponsors->count()}");
        } else {
            $this->info("Pas le moment pour les rapports mensuels (1er du mois requis)");
        }
    }

    /**
     * Obtenir les statistiques mensuelles d'un sponsor
     */
    private function getMonthlyStats($sponsor)
    {
        $lastMonth = Carbon::now()->subMonth();

        $activeSponsorships = SponsorshipTemp::where('user_id', $sponsor->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $lastMonth->startOfMonth())
            ->where('created_at', '<=', $lastMonth->endOfMonth())
            ->count();

        $eventsCount = SponsorshipTemp::where('user_id', $sponsor->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $lastMonth->startOfMonth())
            ->where('created_at', '<=', $lastMonth->endOfMonth())
            ->distinct('event_id')
            ->count();

        $totalInvested = SponsorshipTemp::where('user_id', $sponsor->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', $lastMonth->startOfMonth())
            ->where('created_at', '<=', $lastMonth->endOfMonth())
            ->sum('amount');

        // Simuler des données d'analytics
        $impressions = rand(1000, 10000);
        $clicks = rand(50, 500);
        $ctr = $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0;
        $roi = rand(120, 300); // ROI simulé entre 120% et 300%

        return [
            'active_sponsorships' => $activeSponsorships,
            'events_count' => $eventsCount,
            'total_invested' => $totalInvested,
            'impressions' => $impressions,
            'clicks' => $clicks,
            'ctr' => $ctr,
            'roi' => $roi
        ];
    }
}