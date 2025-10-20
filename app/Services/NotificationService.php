<?php

namespace App\Services;

use App\Models\SponsorNotification;
use App\Models\NotificationTemplate;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer une notification à un sponsor
     */
    public function sendNotification(
        User $user,
        string $triggerEvent,
        array $data = [],
        array $channels = ['email', 'in_app']
    ): void {
        // Vérifier les préférences de l'utilisateur
        $preferences = $this->getUserPreferences($user, $triggerEvent);
        
        foreach ($channels as $channel) {
            if (!$preferences->isEnabled($channel)) {
                continue;
            }

            // Obtenir le template
            $template = NotificationTemplate::where('trigger_event', $triggerEvent)
                ->where('type', $channel)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                Log::warning("Template not found for trigger: {$triggerEvent}, channel: {$channel}");
                continue;
            }

            // Créer la notification
            $notification = SponsorNotification::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'type' => $channel,
                'trigger_event' => $triggerEvent,
                'subject' => $template->getSubjectWithVariables($data),
                'content' => $template->getContentWithVariables($data),
                'data' => $data,
                'status' => 'pending'
            ]);

            // Envoyer selon le canal
            $this->sendByChannel($notification, $channel);
        }
    }

    /**
     * Envoyer une notification à plusieurs sponsors
     */
    public function sendBulkNotification(
        array $userIds,
        string $triggerEvent,
        array $data = [],
        array $channels = ['email', 'in_app']
    ): void {
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->sendNotification($user, $triggerEvent, $data, $channels);
            }
        }
    }

    /**
     * Envoyer selon le canal spécifique
     */
    private function sendByChannel(SponsorNotification $notification, string $channel): void
    {
        try {
            switch ($channel) {
                case 'email':
                    $this->sendEmail($notification);
                    break;
                case 'sms':
                    $this->sendSMS($notification);
                    break;
                case 'push':
                    $this->sendPush($notification);
                    break;
                case 'in_app':
                    $this->sendInApp($notification);
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
            $notification->markAsFailed();
        }
    }

    /**
     * Envoyer par email
     */
    private function sendEmail(SponsorNotification $notification): void
    {
        // Utiliser Laravel Mail avec un template personnalisé
        Mail::send('emails.sponsor-notification', [
            'notification' => $notification,
            'user' => $notification->user
        ], function ($message) use ($notification) {
            $message->to($notification->user->email)
                   ->subject($notification->subject);
        });

        $notification->markAsSent();
    }

    /**
     * Envoyer par SMS (intégration avec service SMS)
     */
    private function sendSMS(SponsorNotification $notification): void
    {
        // Intégration avec un service SMS comme Twilio
        // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        // $message = $twilio->messages->create(
        //     $notification->user->phone,
        //     ['from' => config('services.twilio.from'), 'body' => $notification->content]
        // );
        
        // Pour l'instant, on simule l'envoi
        Log::info("SMS sent to {$notification->user->phone}: {$notification->content}");
        $notification->markAsSent();
    }

    /**
     * Envoyer notification push
     */
    private function sendPush(SponsorNotification $notification): void
    {
        // Intégration avec Firebase Cloud Messaging ou Pusher
        // Pour l'instant, on simule l'envoi
        Log::info("Push notification sent to user {$notification->user_id}: {$notification->content}");
        $notification->markAsSent();
    }

    /**
     * Envoyer notification in-app
     */
    private function sendInApp(SponsorNotification $notification): void
    {
        // Les notifications in-app sont déjà stockées en base
        // Elles seront récupérées via l'API
        $notification->markAsSent();
    }

    /**
     * Obtenir les préférences de l'utilisateur
     */
    private function getUserPreferences(User $user, string $triggerEvent): NotificationPreference
    {
        $notificationType = $this->getNotificationTypeFromTrigger($triggerEvent);
        
        return NotificationPreference::firstOrCreate(
            [
                'user_id' => $user->id,
                'notification_type' => $notificationType
            ],
            [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'in_app_enabled' => true
            ]
        );
    }

    /**
     * Mapper le trigger vers le type de notification
     */
    private function getNotificationTypeFromTrigger(string $triggerEvent): string
    {
        return match($triggerEvent) {
            'sponsorship_created', 'sponsorship_approved', 'sponsorship_rejected' => 'sponsorship_updates',
            'payment_due', 'payment_received', 'payment_failed' => 'payment_reminders',
            'event_starting_soon', 'event_started', 'event_ended' => 'event_alerts',
            'contract_expiring', 'contract_signed' => 'contract_deadlines',
            'monthly_report', 'performance_update' => 'performance_reports',
            default => 'sponsorship_updates'
        };
    }

    /**
     * Obtenir les notifications non lues d'un utilisateur
     */
    public function getUnreadNotifications(User $user, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return SponsorNotification::where('user_id', $user->id)
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(User $user): void
    {
        SponsorNotification::where('user_id', $user->id)
            ->where('status', 'sent')
            ->update([
                'status' => 'read',
                'read_at' => now()
            ]);
    }
}
