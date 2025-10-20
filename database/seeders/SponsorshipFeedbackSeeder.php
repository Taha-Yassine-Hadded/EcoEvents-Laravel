<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SponsorshipFeedback;
use App\Models\User;
use App\Models\Event;
use App\Models\SponsorshipTemp;

class SponsorshipFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer un utilisateur sponsor
        $sponsor = User::where('role', 'sponsor')->first();
        
        if (!$sponsor) {
            $this->command->warn('Aucun utilisateur sponsor trouvé. Créez d\'abord un utilisateur sponsor.');
            return;
        }

        // Récupérer quelques événements
        $events = Event::take(3)->get();
        
        if ($events->isEmpty()) {
            $this->command->warn('Aucun événement trouvé. Créez d\'abord des événements.');
            return;
        }

        // Récupérer quelques sponsorships
        $sponsorships = SponsorshipTemp::take(2)->get();

        // Créer des feedbacks de test
        $feedbacks = [
            [
                'event_id' => $events[0]->id,
                'user_id' => $sponsor->id,
                'sponsorship_temp_id' => $sponsorships->isNotEmpty() ? $sponsorships[0]->id : null,
                'feedback_type' => 'post_event',
                'rating' => 5,
                'title' => 'Excellent événement !',
                'content' => 'Nous avons eu une excellente expérience lors de cet événement. L\'organisation était parfaite et nous avons pu établir de nombreux contacts professionnels.',
                'is_anonymous' => false,
                'status' => 'published',
                'tags' => ['excellent', 'organisation', 'réseautage'],
                'metadata' => [
                    'user_agent' => 'Mozilla/5.0 (Test Browser)',
                    'ip_address' => '127.0.0.1'
                ]
            ],
            [
                'event_id' => $events[0]->id,
                'user_id' => $sponsor->id,
                'sponsorship_temp_id' => $sponsorships->isNotEmpty() ? $sponsorships[0]->id : null,
                'feedback_type' => 'package_feedback',
                'rating' => 4,
                'title' => 'Bon package sponsor',
                'content' => 'Le package sponsor était bien conçu avec de bons avantages. Nous recommandons ce type de partenariat.',
                'is_anonymous' => false,
                'status' => 'published',
                'tags' => ['package', 'recommandation'],
                'metadata' => [
                    'user_agent' => 'Mozilla/5.0 (Test Browser)',
                    'ip_address' => '127.0.0.1'
                ]
            ],
            [
                'event_id' => $events->count() > 1 ? $events[1]->id : $events[0]->id,
                'user_id' => $sponsor->id,
                'sponsorship_temp_id' => $sponsorships->count() > 1 ? $sponsorships[1]->id : null,
                'feedback_type' => 'improvement_suggestion',
                'rating' => null,
                'title' => 'Suggestion d\'amélioration',
                'content' => 'Il serait bien d\'avoir plus d\'interactions avec les participants et peut-être organiser des sessions de networking plus structurées.',
                'is_anonymous' => true,
                'status' => 'published',
                'tags' => ['amélioration', 'networking'],
                'metadata' => [
                    'user_agent' => 'Mozilla/5.0 (Test Browser)',
                    'ip_address' => '127.0.0.1'
                ]
            ],
            [
                'event_id' => $events->count() > 2 ? $events[2]->id : $events[0]->id,
                'user_id' => $sponsor->id,
                'sponsorship_temp_id' => null,
                'feedback_type' => 'general_comment',
                'rating' => 3,
                'title' => 'Commentaire général',
                'content' => 'Événement correct dans l\'ensemble, mais il y a quelques points à améliorer au niveau de la logistique.',
                'is_anonymous' => false,
                'status' => 'published',
                'tags' => ['général', 'logistique'],
                'metadata' => [
                    'user_agent' => 'Mozilla/5.0 (Test Browser)',
                    'ip_address' => '127.0.0.1'
                ]
            ]
        ];

        foreach ($feedbacks as $feedbackData) {
            SponsorshipFeedback::create($feedbackData);
        }

        $this->command->info('Feedbacks de test créés avec succès !');
    }
}
