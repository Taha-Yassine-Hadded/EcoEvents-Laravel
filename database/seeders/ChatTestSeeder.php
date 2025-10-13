<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Hash;

class ChatTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test
        $organizer = User::updateOrCreate(
            ['email' => 'organizer@test.com'],
            [
                'name' => 'Organisateur Test',
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'phone' => '0123456789',
                'city' => 'Tunis',
                'interests' => json_encode(['recyclage', 'jardinage']),
                'email_verified_at' => now(),
            ]
        );

        $user1 = User::updateOrCreate(
            ['email' => 'user1@test.com'],
            [
                'name' => 'Utilisateur 1',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'phone' => '0123456790',
                'city' => 'Tunis',
                'interests' => json_encode(['recyclage', 'energie']),
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@test.com'],
            [
                'name' => 'Utilisateur 2',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'phone' => '0123456791',
                'city' => 'Sfax',
                'interests' => json_encode(['jardinage', 'transport']),
                'email_verified_at' => now(),
            ]
        );

        // Créer une communauté de test
        $community = Community::create([
            'name' => 'Communauté Test Chat',
            'description' => 'Une communauté pour tester le système de chat en temps réel',
            'category' => 'recyclage',
            'location' => 'Tunis, Tunisie',
            'max_members' => 50,
            'organizer_id' => $organizer->id,
            'is_active' => true,
        ]);

        // Ajouter les utilisateurs comme membres de la communauté
        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $organizer->id,
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $user1->id,
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        CommunityMember::create([
            'community_id' => $community->id,
            'user_id' => $user2->id,
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        // Créer une salle de chat pour la communauté
        $chatRoom = ChatRoom::create([
            'owner_id' => $organizer->id,
            'target_type' => 'community',
            'target_id' => $community->id,
            'name' => "Chat - {$community->name}",
            'is_private' => false,
        ]);

        // Ajouter les membres au chat
        ChatRoomMember::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $organizer->id,
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        ChatRoomMember::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user1->id,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        ChatRoomMember::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user2->id,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // Créer quelques messages de test
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $organizer->id,
            'content' => 'Bienvenue dans le chat de notre communauté ! 👋',
        ]);

        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user1->id,
            'content' => 'Merci ! Je suis ravi de rejoindre cette communauté écologique 🌱',
        ]);

        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user2->id,
            'content' => 'Salut tout le monde ! J\'ai hâte de partager nos idées vertes 💚',
        ]);

        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->info("📧 Comptes de test créés :");
        $this->command->info("   - Organisateur: organizer@test.com / password");
        $this->command->info("   - Utilisateur 1: user1@test.com / password");
        $this->command->info("   - Utilisateur 2: user2@test.com / password");
        $this->command->info("🏘️  Communauté: {$community->name}");
        $this->command->info("💬 Chat URL: /communities/{$community->id}/chat");
    }
}