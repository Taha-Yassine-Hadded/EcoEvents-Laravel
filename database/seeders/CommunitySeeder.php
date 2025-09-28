<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs organisateurs si ils n'existent pas
        $organizers = [];
        
        for ($i = 1; $i <= 3; $i++) {
            $organizer = User::firstOrCreate(
                ['email' => "organisateur{$i}@ecoevents.com"],
                [
                    'name' => "Organisateur {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'organizer',
                    'phone' => "0612345{$i}78",
                    'city' => ['Tunis', 'Sfax', 'Sousse'][$i-1],
                    'interests' => ['recyclage', 'jardinage', 'energie'],
                ]
            );
            $organizers[] = $organizer;
        }

        // Créer quelques participants
        $participants = [];
        for ($i = 1; $i <= 10; $i++) {
            $participant = User::firstOrCreate(
                ['email' => "participant{$i}@ecoevents.com"],
                [
                    'name' => "Participant {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'participant',
                    'phone' => "0698765{$i}32",
                    'city' => ['Tunis', 'Sfax', 'Sousse', 'Ariana', 'Monastir'][($i-1) % 5],
                    'interests' => [
                        ['recyclage', 'jardinage'],
                        ['energie', 'transport'],
                        ['biodiversite', 'eau'],
                        ['sensibilisation'],
                        ['nettoyage', 'recyclage']
                    ][($i-1) % 5],
                ]
            );
            $participants[] = $participant;
        }

        // Données des communautés
        $communitiesData = [
            [
                'name' => 'Zéro Déchet Tunis',
                'description' => 'Communauté dédiée à la réduction des déchets dans la capitale. Nous organisons des ateliers DIY, des défis zéro déchet et des échanges de bonnes pratiques pour un mode de vie plus durable.',
                'category' => 'recyclage',
                'location' => 'Tunis',
                'max_members' => 50,
                'organizer_id' => $organizers[0]->id,
            ],
            [
                'name' => 'Jardins Urbains Sfax',
                'description' => 'Créons ensemble des espaces verts en ville ! Notre communauté se consacre au développement de jardins urbains, potagers partagés et à la sensibilisation au jardinage écologique.',
                'category' => 'jardinage',
                'location' => 'Sfax',
                'max_members' => 30,
                'organizer_id' => $organizers[1]->id,
            ],
            [
                'name' => 'Énergie Verte Sousse',
                'description' => 'Promotion des énergies renouvelables et de l\'efficacité énergétique. Nous organisons des conférences, des visites de sites et des projets collaboratifs pour un avenir énergétique durable.',
                'category' => 'energie',
                'location' => 'Sousse',
                'max_members' => 40,
                'organizer_id' => $organizers[2]->id,
            ],
            [
                'name' => 'Cyclistes Écolos Ariana',
                'description' => 'Communauté de cyclistes passionnés par la mobilité douce. Nous organisons des sorties vélo, des actions de sensibilisation et promouvons le vélo comme alternative écologique.',
                'category' => 'transport',
                'location' => 'Ariana',
                'max_members' => 60,
                'organizer_id' => $organizers[0]->id,
            ],
            [
                'name' => 'Protecteurs de la Biodiversité',
                'description' => 'Ensemble pour protéger la faune et la flore locales. Nous organisons des sorties nature, des actions de nettoyage et des projets de conservation de la biodiversité.',
                'category' => 'biodiversite',
                'location' => 'Monastir',
                'max_members' => 35,
                'organizer_id' => $organizers[1]->id,
            ],
            [
                'name' => 'Éco-Sensibilisation Campus',
                'description' => 'Communauté étudiante dédiée à la sensibilisation environnementale. Nous organisons des événements, des conférences et des projets éco-responsables dans les universités.',
                'category' => 'sensibilisation',
                'location' => 'Tunis',
                'max_members' => 80,
                'organizer_id' => $organizers[2]->id,
            ],
        ];

        // Créer les communautés
        foreach ($communitiesData as $communityData) {
            $community = Community::create($communityData);

            // Ajouter des membres aléatoires à chaque communauté
            $memberCount = rand(5, min(15, $community->max_members));
            $selectedParticipants = collect($participants)->random($memberCount);

            foreach ($selectedParticipants as $participant) {
                CommunityMember::create([
                    'community_id' => $community->id,
                    'user_id' => $participant->id,
                    'status' => 'approved',
                    'is_active' => true,
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('✅ Communautés créées avec succès !');
        $this->command->info('📊 Statistiques :');
        $this->command->info('   - ' . count($communitiesData) . ' communautés créées');
        $this->command->info('   - ' . count($organizers) . ' organisateurs');
        $this->command->info('   - ' . count($participants) . ' participants');
        $this->command->info('');
        $this->command->info('🔑 Comptes de test :');
        $this->command->info('   Organisateurs : organisateur1@ecoevents.com (password: password)');
        $this->command->info('   Participants : participant1@ecoevents.com (password: password)');
    }
}
