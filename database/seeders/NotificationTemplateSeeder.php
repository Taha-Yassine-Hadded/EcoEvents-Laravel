<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Sponsorship créé
            [
                'name' => 'Sponsorship Créé - Email',
                'type' => 'email',
                'trigger_event' => 'sponsorship_created',
                'subject' => 'Votre sponsorship pour {{event_title}} a été créé',
                'content' => 'Bonjour {{user_name}},\n\nVotre demande de sponsorship pour l\'événement "{{event_title}}" a été créée avec succès.\n\nDétails du sponsorship :\n- Package : {{package_name}}\n- Montant : {{amount}}€\n- Statut : En attente d\'approbation\n\nNous vous tiendrons informé de l\'évolution de votre demande.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'event_title', 'package_name', 'amount'],
                'is_active' => true
            ],
            [
                'name' => 'Sponsorship Créé - In-App',
                'type' => 'in_app',
                'trigger_event' => 'sponsorship_created',
                'subject' => null,
                'content' => 'Votre sponsorship pour {{event_title}} a été créé avec succès.',
                'variables' => ['event_title'],
                'is_active' => true
            ],

            // Sponsorship approuvé
            [
                'name' => 'Sponsorship Approuvé - Email',
                'type' => 'email',
                'trigger_event' => 'sponsorship_approved',
                'subject' => '🎉 Votre sponsorship pour {{event_title}} a été approuvé !',
                'content' => 'Félicitations {{user_name}} !\n\nVotre demande de sponsorship pour l\'événement "{{event_title}}" a été approuvée.\n\nDétails :\n- Package : {{package_name}}\n- Montant : {{amount}}€\n- Date de l\'événement : {{event_date}}\n\nVous pouvez maintenant accéder à votre espace sponsor pour suivre votre sponsorship.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'event_title', 'package_name', 'amount', 'event_date'],
                'is_active' => true
            ],

            // Paiement dû
            [
                'name' => 'Paiement Dû - Email',
                'type' => 'email',
                'trigger_event' => 'payment_due',
                'subject' => '⏰ Rappel : Paiement dû pour {{event_title}}',
                'content' => 'Bonjour {{user_name}},\n\nNous vous rappelons que le paiement pour votre sponsorship de l\'événement "{{event_title}}" est dû.\n\nDétails :\n- Montant : {{amount}}€\n- Date limite : {{due_date}}\n\nVeuillez effectuer le paiement dès que possible pour éviter tout retard.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'event_title', 'amount', 'due_date'],
                'is_active' => true
            ],
            [
                'name' => 'Paiement Dû - SMS',
                'type' => 'sms',
                'trigger_event' => 'payment_due',
                'subject' => null,
                'content' => 'Rappel Echofy : Paiement de {{amount}}€ dû pour {{event_title}}. Date limite : {{due_date}}',
                'variables' => ['amount', 'event_title', 'due_date'],
                'is_active' => true
            ],

            // Événement bientôt
            [
                'name' => 'Événement Bientôt - Email',
                'type' => 'email',
                'trigger_event' => 'event_starting_soon',
                'subject' => '🚀 {{event_title}} commence bientôt !',
                'content' => 'Bonjour {{user_name}},\n\nL\'événement "{{event_title}}" que vous sponsorisez commence dans {{time_remaining}}.\n\nDétails :\n- Date : {{event_date}}\n- Lieu : {{event_location}}\n- Package : {{package_name}}\n\nAssurez-vous d\'être prêt pour maximiser votre visibilité lors de cet événement.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'event_title', 'time_remaining', 'event_date', 'event_location', 'package_name'],
                'is_active' => true
            ],
            [
                'name' => 'Événement Bientôt - Push',
                'type' => 'push',
                'trigger_event' => 'event_starting_soon',
                'subject' => null,
                'content' => '🚀 {{event_title}} commence dans {{time_remaining}} !',
                'variables' => ['event_title', 'time_remaining'],
                'is_active' => true
            ],

            // Rapport mensuel
            [
                'name' => 'Rapport Mensuel - Email',
                'type' => 'email',
                'trigger_event' => 'monthly_report',
                'subject' => '📊 Votre rapport mensuel Echofy - {{month_year}}',
                'content' => 'Bonjour {{user_name}},\n\nVoici votre rapport mensuel pour {{month_year}} :\n\n📈 Statistiques :\n- Sponsorships actifs : {{active_sponsorships}}\n- Événements sponsorisés : {{events_count}}\n- Montant total investi : {{total_invested}}€\n- Impressions générées : {{impressions}}\n- Clics reçus : {{clicks}}\n\n📊 Performance :\n- Taux de clic : {{ctr}}%\n- ROI estimé : {{roi}}%\n\nConsultez votre dashboard pour plus de détails.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'month_year', 'active_sponsorships', 'events_count', 'total_invested', 'impressions', 'clicks', 'ctr', 'roi'],
                'is_active' => true
            ],

            // Contrat expirant
            [
                'name' => 'Contrat Expirant - Email',
                'type' => 'email',
                'trigger_event' => 'contract_expiring',
                'subject' => '⚠️ Votre contrat pour {{event_title}} expire bientôt',
                'content' => 'Bonjour {{user_name}},\n\nVotre contrat de sponsorship pour l\'événement "{{event_title}}" expire dans {{days_remaining}} jours.\n\nDétails :\n- Date d\'expiration : {{expiry_date}}\n- Package : {{package_name}}\n\nSouhaitez-vous renouveler votre sponsorship ? Contactez-nous rapidement.\n\nCordialement,\nL\'équipe Echofy',
                'variables' => ['user_name', 'event_title', 'days_remaining', 'expiry_date', 'package_name'],
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::create($template);
        }

        $this->command->info('Templates de notifications créés avec succès !');
    }
}