<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\EcoBotService;
use App\Models\User;

class CommunityChatController extends Controller
{
    /**
     * Afficher le chat d'une communauté
     */
    public function show(Community $community)
    {
        $user = Auth::user();

        // Mode test : permettre l'accès sans authentification pour la communauté de test
        if ($community->name === 'Communauté Test Chat' && !$user) {
            // Créer un utilisateur temporaire pour les tests
            $user = \App\Models\User::where('email', 'user1@test.com')->first();
            if ($user) {
                Auth::login($user);
            }
        }

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder au chat.');
        }

        // Vérifier si l'utilisateur est membre de la communauté
        if (!$community->members()->where('user_id', $user->id)->where('status', 'approved')->exists()) {
            return redirect()->route('communities.show', $community)
                ->with('error', 'Vous devez être membre de cette communauté pour accéder au chat.');
        }

        // Récupérer ou créer la salle de chat pour cette communauté
        $chatRoom = $this->getOrCreateCommunityChatRoom($community);

        // Récupérer les messages récents (50 derniers)
        $messages = ChatMessage::with('user:id,name')
            ->where('chat_room_id', $chatRoom->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse();

        // Récupérer les membres actifs du chat
        $activeMembers = $chatRoom->members()
            ->with('user:id,name')
            ->where('status', 'active')
            ->get();

        // Si aucun membre actif, utiliser les membres de la communauté
        if ($activeMembers->isEmpty()) {
            $activeMembers = $community->members()
                ->with('user:id,name')
                ->where('status', 'approved')
                ->get()
                ->map(function($member) use ($chatRoom) {
                    return (object) [
                        'user' => $member->user,
                        'status' => 'active',
                        'joined_at' => $member->created_at,
                        'last_read_at' => null, // Ajouter cette propriété
                        'chat_room_id' => $chatRoom->id
                    ];
                });
        }

        return view('chat.community', compact('community', 'chatRoom', 'messages', 'activeMembers'));
    }

    /**
     * Envoyer un message dans le chat de la communauté
     */
    public function sendMessage(Request $request, Community $community)
    {
        $user = Auth::user();

        // Debug: Afficher les données reçues
        Log::info('Message reçu:', [
            'content' => $request->content,
            'message_type' => $request->message_type,
            'has_voice_file' => $request->hasFile('voice_file'),
            'voice_file_name' => $request->hasFile('voice_file') ? $request->file('voice_file')->getClientOriginalName() : null,
            'all_data' => $request->all()
        ]);

        // Vérifier si l'utilisateur est membre de la communauté
        if (!$community->members()->where('user_id', $user->id)->where('status', 'approved')->exists()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $request->validate([
            'content' => 'nullable|string|max:1000',
            'message_type' => 'nullable|in:text,image,voice',
            'voice_file' => 'nullable|file|mimes:wav,mp3,ogg|max:10240',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240'
        ], [
            'voice_file.mimes' => 'Le fichier vocal doit être au format WAV, MP3 ou OGG.',
            'voice_file.max' => 'Le fichier vocal ne doit pas dépasser 10MB.',
            'content.max' => 'Le message ne doit pas dépasser 1000 caractères.'
        ]);

        // Validation personnalisée : au moins content, voice_file ou attachments doit être présent
        if (empty($request->content) && !$request->hasFile('voice_file') && !$request->hasFile('attachments')) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'content' => ['Le message doit contenir du texte, un fichier vocal ou des pièces jointes.']
                ]
            ], 422);
        }

        // Récupérer ou créer la salle de chat
        $chatRoom = $this->getOrCreateCommunityChatRoom($community);

        // Gérer le message vocal
        $voiceUrl = null;
        if ($request->hasFile('voice_file')) {
            $voiceFile = $request->file('voice_file');
            $filename = time() . '_voice_' . uniqid() . '.' . $voiceFile->getClientOriginalExtension();
            $path = $voiceFile->storeAs('chat/voice', $filename, 'public');
            $voiceUrl = '/storage/' . $path;
        }

        // Déterminer le type de message
        $messageType = $request->message_type ?? 'text';
        if ($voiceUrl) {
            $messageType = 'voice';
        } elseif ($request->hasFile('attachments')) {
            // Vérifier si au moins un fichier est une image
            $hasImage = false;
            foreach ($request->file('attachments') as $file) {
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $hasImage = true;
                    break;
                }
            }
            if ($hasImage) {
                $messageType = 'image';
            }
        }

        // Contenu par défaut pour les messages vocaux et emojis
        $content = $request->content;
        if ($messageType === 'voice' && empty($content)) {
            $content = '🎤 Message vocal';
        } elseif (empty($content) && $request->hasFile('attachments')) {
            // Contenu par défaut pour les fichiers seuls
            $content = '📎 Fichier envoyé';
        } elseif (empty($content)) {
            // Contenu par défaut pour les emojis seuls
            $content = '💬 Message';
        }

        // Créer le message
        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $user->id,
            'content' => $content,
            'message_type' => $messageType,
            'voice_url' => $voiceUrl,
            'attachments' => $this->handleAttachments($request)
        ]);

        // Diffuser le message en temps réel
        event(new MessageSent($message));

        // EcoChatBot: réponse automatique (mention ou mots-clés) avec cooldown 5s/room
        try {
            /** @var EcoBotService $ecoBot */
            Log::info('ECOBOT_DEBUG_START', [ 'type' => $messageType, 'content' => $content ]);
            $ecoBot = app(EcoBotService::class);
            if ($messageType === 'text' && $ecoBot->shouldRespond($content)) {
                $cooldownKey = 'ecobot_cooldown_' . $chatRoom->id;
                if (true) { // Temporairement désactivé pour test
                    $bot = User::firstOrCreate(
                        ['email' => 'ecochatbot@system.local'],
                        [
                            'name' => 'EcoChatBot',
                            'password' => bcrypt(str()->random(32)),
                            'role' => 'bot',
                        ]
                    );

                    \App\Models\ChatRoomMember::firstOrCreate([
                        'chat_room_id' => $chatRoom->id,
                        'user_id' => $bot->id,
                    ], [ 'status' => 'active', 'role' => 'member', 'joined_at' => now() ]);

                    $reply = $ecoBot->generateReply($content);
                    $botMessage = ChatMessage::create([
                        'chat_room_id' => $chatRoom->id,
                        'user_id' => $bot->id,
                        'content' => $reply,
                        'message_type' => 'text',
                    ]);
                    event(new MessageSent($botMessage->load('user:id,name')));
                    Log::info('ECOBOT_SENT', [ 'reply_preview' => mb_substr($reply, 0, 120) ]);
                } else {
                    Log::info('ECOBOT_COOLDOWN_SKIP', [ 'room_id' => $chatRoom->id ]);
                }
            } else {
                Log::info('ECOBOT_SKIP', [ 'reason' => 'no_match_or_non_text' ]);
            }
        } catch (\Throwable $e) {
            Log::warning('EcoChatBot error: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('user:id,name')
        ]);
    }

    /**
     * Récupérer l'historique des messages
     */
    public function getMessages(Request $request, Community $community)
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est membre de la communauté
        if (!$community->members()->where('user_id', $user->id)->where('status', 'approved')->exists()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $chatRoom = $this->getOrCreateCommunityChatRoom($community);
        $lastMessageId = $request->get('last_message_id', 0);

        $messages = ChatMessage::with('user:id,name')
            ->where('chat_room_id', $chatRoom->id)
            ->when($lastMessageId, function($query, $lastMessageId) {
                return $query->where('id', '<', $lastMessageId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse();

        return response()->json([
            'messages' => $messages,
            'has_more' => $messages->count() == 20
        ]);
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead(Request $request, Community $community)
    {
        $user = Auth::user();
        $chatRoom = $this->getOrCreateCommunityChatRoom($community);

        // Mettre à jour le timestamp de dernière lecture
        $chatRoom->members()
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Récupérer ou créer une salle de chat pour une communauté
     */
    private function getOrCreateCommunityChatRoom(Community $community)
    {
        $chatRoom = ChatRoom::where('target_type', 'community')
            ->where('target_id', $community->id)
            ->first();

        if (!$chatRoom) {
            $chatRoom = ChatRoom::create([
                'owner_id' => $community->organizer_id,
                'target_type' => 'community',
                'target_id' => $community->id,
                'name' => "Chat - {$community->name}",
                'is_private' => false
            ]);

            // Ajouter tous les membres approuvés de la communauté au chat
            $communityMembers = $community->members()
                ->where('status', 'approved')
                ->get();

            foreach ($communityMembers as $member) {
                ChatRoomMember::create([
                    'chat_room_id' => $chatRoom->id,
                    'user_id' => $member->user_id,
                    'status' => 'active',
                    'joined_at' => now()
                ]);
            }
        }

        return $chatRoom;
    }

    /**
     * Gérer les pièces jointes
     */
    private function handleAttachments(Request $request)
    {
        if (!$request->hasFile('attachments')) {
            return null;
        }

        $attachments = [];
        foreach ($request->file('attachments') as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chat/attachments', $filename, 'public');

            $attachments[] = [
                'filename' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];
        }

        return $attachments;
    }
}
