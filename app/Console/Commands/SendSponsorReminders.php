<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SponsorshipTemp;
use App\Models\Event;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendSponsorReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sponsors:send-reminders {--type=all : Type de rappel (all, payment, event, contract)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer des rappels automatiques aux sponsors';

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
        $type = $this->option('type');
        
        $this->info("Envoi des rappels sponsors (type: {$type})...");

        switch ($type) {
            case 'payment':
                $this->sendPaymentReminders();
                break;
            case 'event':
                $this->sendEventReminders();
                break;
            case 'contract':
                $this->sendContractReminders();
                break;
            case 'all':
            default:
                $this->sendPaymentReminders();
                $this->sendEventReminders();
                $this->sendContractReminders();
                break;
        }

        $this->info('Rappels envoyés avec succès !');
    }

    /**
     * Envoyer des rappels de paiement
     */
    private function sendPaymentReminders()
    {
        $this->info('Envoi des rappels de paiement...');

        // Sponsorships approuvés mais non payés depuis plus de 7 jours
        $sponsorships = SponsorshipTemp::where('status', 'approved')
            ->where('created_at', '<=', Carbon::now()->subDays(7))
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

            $this->line("Rappel paiement envoyé à: {$sponsorship->user->name}");
        }

        $this->info("Rappels de paiement envoyés: {$sponsorships->count()}");
    }

    /**
     * Envoyer des rappels d'événements
     */
    private function sendEventReminders()
    {
        $this->info('Envoi des rappels d\'événements...');

        // Événements qui commencent dans 24h
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

                    $this->line("Rappel événement envoyé à: {$sponsorship->user->name} pour {$event->title}");
                }
            }
        }

        $this->info("Rappels d'événements envoyés pour: {$events->count()} événements");
    }

    /**
     * Envoyer des rappels de contrats
     */
    private function sendContractReminders()
    {
        $this->info('Envoi des rappels de contrats...');

        // Sponsorships qui expirent dans 30 jours
        $sponsorships = SponsorshipTemp::where('status', 'approved')
            ->where('created_at', '<=', Carbon::now()->subDays(335)) // 365 - 30 jours
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

            $this->line("Rappel contrat envoyé à: {$sponsorship->user->name}");
        }

        $this->info("Rappels de contrats envoyés: {$sponsorships->count()}");
    }
}