<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - {{ $community->name }} - EcoEvents</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fallback pour Echo si Vite ne fonctionne pas -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.7.4/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@2.2.4/dist/echo.iife.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #075e54 0%, #128c7e 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        .navbar { display: none; } /* Masquer la navbar pour le chat */

        /* Style WhatsApp pour le conteneur principal */
        .container-fluid {
            background: #e5ddd5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0f0f0' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            height: 100vh;
            padding: 0;
        }

        /* Styles pour les emojis */
        .emoji-panel {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
        }

        .emoji-item {
            font-size: 20px;
            padding: 5px;
            cursor: pointer;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.2s;
        }

        .emoji-item:hover {
            background-color: #f0f0f0;
        }

        /* Styles pour l'enregistrement vocal */
        .voice-panel {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .voice-recorder {
            text-align: center;
        }

        #recording-status {
            font-size: 14px;
            color: #666;
        }

        .recording {
            color: #dc3545;
            font-weight: bold;
        }

        /* Styles pour les images de profil */
        .profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            margin-right: 8px;
            flex-shrink: 0;
        }

        /* Avatar pour les messages des autres utilisateurs */
        .other-message .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 8px;
            flex-shrink: 0;
        }

        /* Structure des messages */
        .other-message {
            justify-content: flex-start;
        }

        .own-message {
            justify-content: flex-end;
        }

        .profile-image-large {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        /* Styles pour les messages d'image */
        .message-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            cursor: pointer;
        }

        .message-image:hover {
            opacity: 0.8;
        }

        /* Styles pour les fichiers */
        .message-file {
            display: flex;
            align-items: center;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 4px 0;
        }

        .message-file i {
            margin-right: 8px;
            color: #6c757d;
        }

        .message-file a {
            color: #007bff;
            text-decoration: none;
            flex: 1;
        }

        .message-file a:hover {
            text-decoration: underline;
        }

        /* Styles pour les messages vocaux */
        .voice-message {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            max-width: 250px;
        }

        .voice-message audio {
            flex: 1;
        }

        /* Styles pour les statuts */
        .online-status {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            margin-left: 5px;
        }

        .offline-status {
            display: inline-block;
            width: 8px;
            height: 8px;
            background-color: #6c757d;
            border-radius: 50%;
            margin-left: 5px;
        }

        /* Modal pour les images */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            cursor: pointer;
        }

        .modal-content {
            position: relative;
            margin: auto;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            text-align: center;
            cursor: default;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #ccc;
        }

        /* Indicateur en ligne */
        .online-indicator {
            width: 8px;
            height: 8px;
            background-color: #28a745;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        /* Améliorations pour la sidebar */
        .members-list::-webkit-scrollbar {
            width: 4px;
        }

        .members-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .members-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .members-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Styles pour les stats */
        .stats-item {
            padding: 15px 10px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin: 8px 0;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            text-align: center;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .stats-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Amélioration de l'organisation générale */
        .sidebar-section {
            background-color: white;
            margin-bottom: 1px;
            border-radius: 0;
        }

        .sidebar-section:first-child {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .sidebar-section:last-child {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Animation pour les boutons */
        .btn {
            transition: all 0.2s ease;
        }

        /* Styles pour les boutons d'action */
        .action-btn {
            transition: all 0.3s ease;
            border-width: 2px;
            font-weight: 500;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .action-btn:active {
            transform: translateY(0);
        }

        /* Styles pour les cartes de membres */
        .member-card:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%) !important;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar gauche style WhatsApp -->
        <div class="col-md-3 p-0" style="background-color: #f0f0f0; border-right: 1px solid #ddd;">
            <div class="d-flex flex-column h-100">
                <!-- Header de la communauté style WhatsApp -->
                <div class="p-3" style="background-color: #075e54; color: white;">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('communities.show', $community) }}" class="text-white me-3" style="text-decoration: none;">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-white">{{ $community->name }}</h6>
                            <small class="text-light">{{ $activeMembers->count() }} membres en ligne</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown" style="border-radius: 50%; width: 40px; height: 40px;">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showMembers()">
                                    <i class="fas fa-users me-2"></i>Membres
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="showInfo()">
                                    <i class="fas fa-info-circle me-2"></i>Informations
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Informations de la communauté style WhatsApp -->
                <div class="sidebar-section p-4">
                    <div class="text-center">
                        <img src="{{ $community->image_url ?: '/assets/images/default-community.png' }}"
                             alt="{{ $community->name }}"
                             class="rounded-circle mb-3 shadow-sm"
                             style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #f0f0f0;">
                        <h5 class="mb-2 text-dark">{{ $community->name }}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9em; line-height: 1.4;">
                            @if($community->description && strlen($community->description) > 3 && !str_repeat('r', strlen($community->description)) == $community->description)
                                {{ $community->description }}
                            @else
                                <em class="text-muted">Aucune description disponible</em>
                            @endif
                        </p>

                        <!-- Stats de la communauté améliorées -->
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-users text-primary mb-2" style="font-size: 1.2em;"></i>
                                    <h6 class="mb-0 text-primary">{{ $community->members()->where('status', 'approved')->count() }}</h6>
                                    <small class="text-muted">Membres</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-comments text-success mb-2" style="font-size: 1.2em;"></i>
                                    <h6 class="mb-0 text-success">{{ $messages->count() }}</h6>
                                    <small class="text-muted">Messages</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-item">
                                    <i class="fas fa-calendar text-info mb-2" style="font-size: 1.2em;"></i>
                                    <h6 class="mb-0 text-info">{{ $community->created_at->diffInDays(now()) }}</h6>
                                    <small class="text-muted">Jours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="sidebar-section p-3">
                    <h6 class="text-dark mb-3 fw-bold">
                        <i class="fas fa-bolt text-warning me-2"></i>Actions rapides
                    </h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="btn btn-outline-primary btn-sm w-100 action-btn" onclick="showMembers()" style="border-radius: 12px; padding: 10px; font-size: 0.85em;">
                                <i class="fas fa-users mb-1 d-block" style="font-size: 1.2em;"></i>Membres
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-success btn-sm w-100 action-btn" onclick="inviteMembers()" style="border-radius: 12px; padding: 10px; font-size: 0.85em;">
                                <i class="fas fa-user-plus mb-1 d-block" style="font-size: 1.2em;"></i>Inviter
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info btn-sm w-100 action-btn" onclick="showSettings()" style="border-radius: 12px; padding: 10px; font-size: 0.85em;">
                                <i class="fas fa-cog mb-1 d-block" style="font-size: 1.2em;"></i>Paramètres
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-secondary btn-sm w-100 action-btn" onclick="exportChat()" style="border-radius: 12px; padding: 10px; font-size: 0.85em;">
                                <i class="fas fa-download mb-1 d-block" style="font-size: 1.2em;"></i>Exporter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Membres en ligne style WhatsApp -->
                <div class="sidebar-section flex-grow-1 p-3">
                    <h6 class="text-dark mb-3 fw-bold">
                        <i class="fas fa-circle text-success me-2" style="font-size: 0.7em;"></i>Membres actifs
                    </h6>
                    <div class="members-list" style="max-height: 300px; overflow-y: auto;">
                        @foreach($activeMembers as $member)
                            <div class="member-card d-flex align-items-center p-3 mb-2 rounded" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 1px solid #e9ecef; transition: all 0.2s ease;">
                                <div class="position-relative">
                                    <img src="{{ $member->user->profile_image ? Storage::url($member->user->profile_image) : '/storage/profiles/default.jpg' }}"
                                         class="rounded-circle me-3 shadow-sm"
                                         style="width: 45px; height: 45px; object-fit: cover; border: 3px solid #fff;"
                                         onerror="this.src='/storage/profiles/default.jpg'">
                                    <span class="position-absolute bottom-0 end-0 online-indicator" style="width: 12px; height: 12px; border: 2px solid white;"></span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1">{{ $member->user->name }}</div>
                                    <small class="text-success d-flex align-items-center">
                                        @if($member->last_read_at)
                                            Dernière activité: {{ $member->last_read_at->diffForHumans() }}
                                        @else
                                            En ligne maintenant
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Zone principale du chat -->
        <div class="col-md-9 d-flex flex-column h-100 p-0">
            <!-- Header du chat style WhatsApp -->
            <div class="bg-white border-bottom p-3" style="background-color: #075e54; color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ $community->image_url ?: '/assets/images/default-community.png' }}"
                             class="rounded-circle me-3"
                             style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="mb-0 text-white">{{ $community->name }}</h6>
                            <small class="text-light">{{ $activeMembers->count() }} membres en ligne</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="scrollToBottom()">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleNotifications()">
                            <i class="fas fa-bell" id="notification-icon"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Zone des messages style WhatsApp -->
            <div class="flex-grow-1" id="messages-container" style="overflow-y: auto; background-color: #e5ddd5; background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23f0f0f0\" fill-opacity=\"0.1\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"2\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); padding: 20px 20px 10px 20px;">
                <div class="messages-list" id="messages-list">
                    @forelse($messages as $message)
                        <div class="message-item {{ $message->user_id === auth()->id() ? 'own-message' : 'other-message' }}"
                             data-message-id="{{ $message->id }}">
                            @if($message->user_id !== auth()->id())
                                @php $isBotAvatar = strtolower($message->user->name ?? '') === 'ecochatbot'; @endphp
                                <img src="{{ $isBotAvatar ? asset('assets/images/bot-avatar.png') : ($message->user->profile_image ? Storage::url($message->user->profile_image) : '/storage/profiles/default.jpg') }}"
                                     class="message-avatar"
                                     onerror="this.src='{{ $isBotAvatar ? asset('assets/images/bot-avatar.png') : '/storage/profiles/default.jpg' }}'">
                            @endif
                            <div class="message-bubble">
                                @if($message->user_id !== auth()->id())
                                    <div class="message-sender">
                                        {{ $message->user->name }}
                                        @php $isBot = strtolower($message->user->name ?? '') === 'ecochatbot'; @endphp
                                        @if($isBot)
                                            <span class="badge bg-success ms-2">Bot</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="message-content">{{ $message->content }}</div>
                                @if($message->attachments)
                                    <div class="message-attachments">
                                        @foreach($message->attachments as $attachment)
                                            <div class="attachment-item">
                                                @if(str_starts_with($attachment['mime_type'], 'image/'))
                                                    <img src="{{ Storage::url($attachment['path']) }}"
                                                         alt="{{ $attachment['filename'] }}"
                                                         class="img-thumbnail"
                                                         style="max-width: 200px; max-height: 200px;">
                                                @else
                                                    <a href="{{ Storage::url($attachment['path']) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-paperclip me-1"></i>{{ $attachment['filename'] }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="message-time">
                                    {{ $message->created_at->format('H:i') }}
                                    @if($message->user_id === auth()->id())
                                        <i class="fas fa-check-double text-primary ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>Aucun message pour le moment. Soyez le premier à écrire !</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Zone de saisie -->
            <div class="bg-white border-top p-3" style="background-color: #f0f0f0;">
                <form id="message-form" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-end gap-2">
                        <!-- Bouton pièces jointes -->
                        <div class="position-relative">
                            <input type="file"
                                   id="attachments-input"
                                   name="attachments[]"
                                   multiple
                                   accept="image/*,.pdf,.doc,.docx"
                                   style="display: none;">
                            <button type="button"
                                    class="btn btn-outline-secondary btn-sm"
                                    onclick="document.getElementById('attachments-input').click()"
                                    style="border-radius: 50%; width: 40px; height: 40px; border-color: #ddd;">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm ms-1"
                                    onclick="document.getElementById('camera-input').click()"
                                    style="border-radius: 50%; width: 40px; height: 40px; border-color: #ddd;">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" id="camera-input" class="d-none" accept="image/*" capture="environment">
                        </div>

                        <!-- Zone de texte style WhatsApp -->
                        <div class="flex-grow-1">
                            <div style="background-color: white; border-radius: 21px; padding: 8px 16px; border: 1px solid #ddd;">
                                <textarea class="form-control"
                                          id="message-input"
                                          name="content"
                                          placeholder="Tapez votre message..."
                                          rows="1"
                                          maxlength="1000"
                                          style="resize: none; min-height: 24px; max-height: 120px; border: none; outline: none; background: transparent;"></textarea>
                            </div>
                        </div>

                        <!-- Bouton d'envoi style WhatsApp -->
                        <button type="submit"
                                class="btn btn-primary btn-sm"
                                id="send-button"
                                disabled
                                style="border-radius: 50%; width: 40px; height: 40px; background-color: #25d366; border-color: #25d366;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <button type="button"
                                class="btn btn-warning btn-sm ms-2"
                                id="test-button"
                                title="Test sans JWT">
                            <i class="fas fa-flask"></i>
                        </button>
                        <button type="button"
                                class="btn btn-info btn-sm ms-1"
                                id="token-button"
                                title="Obtenir Token JWT">
                            <i class="fas fa-key"></i>
                        </button>
                        <button type="button"
                                class="btn btn-success btn-sm ms-1"
                                id="emoji-button"
                                title="Emojis">
                            <i class="far fa-smile"></i>
                        </button>
    <button type="button"
            class="btn btn-danger btn-sm ms-1"
            id="voice-button"
            title="Message vocal">
        <i class="fas fa-microphone"></i>
    </button>
    <button type="button"
            class="btn btn-secondary btn-sm ms-1"
            id="test-voice-button"
            title="Test Message Vocal">
        <i class="fas fa-flask"></i> 🎤
    </button>
                    </div>

                    <!-- Prévisualisation des pièces jointes -->
                    <div id="attachments-preview" class="mt-2" style="display: none;">
                        <small class="text-muted">Pièces jointes :</small>
                        <div id="attachments-list"></div>
                    </div>

                    <!-- Panneau d'emojis -->
                    <div id="emoji-panel" class="emoji-panel mt-2" style="display: none;">
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="😀">😀</span>
                            <span class="emoji-item" data-emoji="😃">😃</span>
                            <span class="emoji-item" data-emoji="😄">😄</span>
                            <span class="emoji-item" data-emoji="😁">😁</span>
                            <span class="emoji-item" data-emoji="😆">😆</span>
                            <span class="emoji-item" data-emoji="😅">😅</span>
                            <span class="emoji-item" data-emoji="😂">😂</span>
                            <span class="emoji-item" data-emoji="🤣">🤣</span>
                            <span class="emoji-item" data-emoji="😊">😊</span>
                            <span class="emoji-item" data-emoji="😇">😇</span>
                            <span class="emoji-item" data-emoji="🙂">🙂</span>
                            <span class="emoji-item" data-emoji="🙃">🙃</span>
                            <span class="emoji-item" data-emoji="😉">😉</span>
                            <span class="emoji-item" data-emoji="😌">😌</span>
                            <span class="emoji-item" data-emoji="😍">😍</span>
                            <span class="emoji-item" data-emoji="🥰">🥰</span>
                            <span class="emoji-item" data-emoji="😘">😘</span>
                            <span class="emoji-item" data-emoji="😗">😗</span>
                            <span class="emoji-item" data-emoji="😙">😙</span>
                            <span class="emoji-item" data-emoji="😚">😚</span>
                            <span class="emoji-item" data-emoji="😋">😋</span>
                            <span class="emoji-item" data-emoji="😛">😛</span>
                            <span class="emoji-item" data-emoji="😝">😝</span>
                            <span class="emoji-item" data-emoji="😜">😜</span>
                            <span class="emoji-item" data-emoji="🤪">🤪</span>
                            <span class="emoji-item" data-emoji="🤨">🤨</span>
                            <span class="emoji-item" data-emoji="🧐">🧐</span>
                            <span class="emoji-item" data-emoji="🤓">🤓</span>
                            <span class="emoji-item" data-emoji="😎">😎</span>
                            <span class="emoji-item" data-emoji="🤩">🤩</span>
                            <span class="emoji-item" data-emoji="🥳">🥳</span>
                            <span class="emoji-item" data-emoji="👍">👍</span>
                            <span class="emoji-item" data-emoji="👎">👎</span>
                            <span class="emoji-item" data-emoji="👌">👌</span>
                            <span class="emoji-item" data-emoji="✌️">✌️</span>
                            <span class="emoji-item" data-emoji="🤞">🤞</span>
                            <span class="emoji-item" data-emoji="🤟">🤟</span>
                            <span class="emoji-item" data-emoji="🤘">🤘</span>
                            <span class="emoji-item" data-emoji="🤙">🤙</span>
                            <span class="emoji-item" data-emoji="👈">👈</span>
                            <span class="emoji-item" data-emoji="👉">👉</span>
                            <span class="emoji-item" data-emoji="👆">👆</span>
                            <span class="emoji-item" data-emoji="👇">👇</span>
                            <span class="emoji-item" data-emoji="☝️">☝️</span>
                            <span class="emoji-item" data-emoji="✋">✋</span>
                            <span class="emoji-item" data-emoji="🤚">🤚</span>
                            <span class="emoji-item" data-emoji="🖐️">🖐️</span>
                            <span class="emoji-item" data-emoji="🖖">🖖</span>
                            <span class="emoji-item" data-emoji="👋">👋</span>
                            <span class="emoji-item" data-emoji="🤝">🤝</span>
                            <span class="emoji-item" data-emoji="👏">👏</span>
                            <span class="emoji-item" data-emoji="🙌">🙌</span>
                            <span class="emoji-item" data-emoji="👐">👐</span>
                            <span class="emoji-item" data-emoji="🤲">🤲</span>
                            <span class="emoji-item" data-emoji="🤜">🤜</span>
                            <span class="emoji-item" data-emoji="🤛">🤛</span>
                            <span class="emoji-item" data-emoji="✊">✊</span>
                            <span class="emoji-item" data-emoji="👊">👊</span>
                            <span class="emoji-item" data-emoji="💪">💪</span>
                            <span class="emoji-item" data-emoji="🙏">🙏</span>
                            <span class="emoji-item" data-emoji="❤️">❤️</span>
                            <span class="emoji-item" data-emoji="🧡">🧡</span>
                            <span class="emoji-item" data-emoji="💛">💛</span>
                            <span class="emoji-item" data-emoji="💚">💚</span>
                            <span class="emoji-item" data-emoji="💙">💙</span>
                            <span class="emoji-item" data-emoji="💜">💜</span>
                            <span class="emoji-item" data-emoji="🖤">🖤</span>
                            <span class="emoji-item" data-emoji="🤍">🤍</span>
                            <span class="emoji-item" data-emoji="🤎">🤎</span>
                            <span class="emoji-item" data-emoji="💔">💔</span>
                            <span class="emoji-item" data-emoji="❣️">❣️</span>
                            <span class="emoji-item" data-emoji="💕">💕</span>
                            <span class="emoji-item" data-emoji="💞">💞</span>
                            <span class="emoji-item" data-emoji="💓">💓</span>
                            <span class="emoji-item" data-emoji="💗">💗</span>
                            <span class="emoji-item" data-emoji="💖">💖</span>
                            <span class="emoji-item" data-emoji="💘">💘</span>
                            <span class="emoji-item" data-emoji="💝">💝</span>
                            <span class="emoji-item" data-emoji="💟">💟</span>
                        </div>
                    </div>

                    <!-- Panneau de message vocal -->
                    <div id="voice-panel" class="voice-panel mt-2" style="display: none;">
                        <div class="voice-recorder">
                            <button type="button" id="start-recording" class="btn btn-danger btn-sm">
                                <i class="fas fa-microphone"></i> Commencer l'enregistrement
                            </button>
                            <button type="button" id="stop-recording" class="btn btn-secondary btn-sm ms-2" style="display: none;">
                                <i class="fas fa-stop"></i> Arrêter
                            </button>
                            <div id="recording-status" class="mt-2"></div>
                            <audio id="audio-preview" controls style="display: none;" class="mt-2"></audio>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal des membres -->
<div class="modal fade" id="membersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Membres de la communauté</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @foreach($activeMembers as $member)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-placeholder me-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $member->user->name }}</div>
                            <small class="text-muted">
                                Membre depuis {{ $member->joined_at->format('d/m/Y') }}
                            </small>
                        </div>
                        <span class="badge bg-success">Actif</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.h-100 { height: 100vh !important; }
.messages-list { padding-bottom: 20px; }
.message-item {
    margin-bottom: 8px;
    display: flex;
    align-items: flex-end;
    padding: 2px 8px;
}
.message-bubble {
    max-width: 65%;
    padding: 8px 12px 8px 12px;
    border-radius: 18px;
    position: relative;
    box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    margin-bottom: 2px;
}
.own-message .message-bubble {
    background-color: #dcf8c6;
    color: #303030;
    margin-left: auto;
    border-bottom-right-radius: 4px;
    margin-right: 8px;
}
.other-message .message-bubble {
    background-color: #ffffff;
    color: #303030;
    border-bottom-left-radius: 4px;
    margin-left: 8px;
}
.message-sender {
    font-weight: 600;
    font-size: 0.85em;
    margin-bottom: 2px;
    opacity: 0.8;
}
.message-content {
    word-wrap: break-word;
    line-height: 1.4;
}
.message-time {
    font-size: 0.75em;
    opacity: 0.7;
    margin-top: 5px;
    text-align: right;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
}
.other-message .message-time {
    text-align: left;
}
.avatar-placeholder {
    width: 40px;
    height: 40px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}
.message-attachments {
    margin-top: 8px;
}
.attachment-item {
    margin-bottom: 5px;
}
#messages-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    background-attachment: fixed;
}
.messages-list {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 20px;
    margin: 10px;
}
</style>

<script>
// Utiliser l'instance Echo configurée dans bootstrap.js
// window.Echo est déjà configuré globalement

// Variables globales
const communityId = {{ $community->id }};
const chatRoomId = {{ $chatRoom->id }};
const currentUserId = {{ auth()->id() }};
let isLoadingMessages = false;
let notificationsEnabled = true;

// Fonction pour envoyer un message de test (sans JWT)
async function sendTestMessage() {
    try {
        console.log('🧪 Envoi d\'un message de test...');

        const response = await fetch('/test/chat/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                community_id: communityId,
                content: 'Message de test envoyé à ' + new Date().toLocaleTimeString()
            })
        });

        if (response.ok) {
            const data = await response.json();
            console.log('✅ Message de test envoyé avec succès');
            addMessageToChat(data.message);
        } else {
            console.error('❌ Erreur lors de l\'envoi du message de test:', response.status);
        }
    } catch (error) {
        console.error('❌ Erreur lors de l\'envoi du message de test:', error);
    }
}

// Fonction pour obtenir un token JWT
async function getJWTToken() {
    try {
        console.log('🔑 Tentative d\'obtention d\'un token JWT...');

        // Essayer de se connecter avec un utilisateur de test
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                email: 'user1@test.com',
                password: 'password'
            })
        });

        if (response.ok) {
            const data = await response.json();
            if (data.token) {
                localStorage.setItem('jwt_token', data.token);
                console.log('✅ Token JWT obtenu avec succès');
                return true;
            }
        }

        // Si l'API ne fonctionne pas, essayer la route web
        const webResponse = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                email: 'user1@test.com',
                password: 'password'
            })
        });

        if (webResponse.ok) {
            const webData = await webResponse.json();
            if (webData.token) {
                localStorage.setItem('jwt_token', webData.token);
                console.log('✅ Token JWT obtenu via route web');
                return true;
            }
        }

        console.log('❌ Impossible d\'obtenir un token JWT');
        return false;
    } catch (error) {
        console.error('❌ Erreur lors de l\'obtention du token:', error);
        return false;
    }
}

// Fonction pour renouveler le token JWT (alias)
async function refreshJWTToken() {
    return await getJWTToken();
}

// Variables pour l'enregistrement vocal
let mediaRecorder = null;
let audioChunks = [];
let isRecording = false;

// Fonction pour gérer la sélection de fichiers
function handleFileSelection(files) {
    if (files.length === 0) return;

    const attachmentsPreview = document.getElementById('attachments-preview');
    const attachmentsList = document.getElementById('attachments-list');

    attachmentsPreview.style.display = 'block';
    attachmentsList.innerHTML = '';

    Array.from(files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'd-flex align-items-center mb-2';

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '50px';
            img.style.height = '50px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '4px';
            img.style.marginRight = '10px';

            fileItem.innerHTML = `
                <img src="${img.src}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                <div>
                    <small class="text-muted">${file.name}</small><br>
                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                </div>
            `;
        } else {
            fileItem.innerHTML = `
                <i class="fas fa-file me-2"></i>
                <div>
                    <small class="text-muted">${file.name}</small><br>
                    <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                </div>
            `;
        }

        attachmentsList.appendChild(fileItem);
    });
}

// Fonction pour démarrer l'enregistrement vocal
async function startVoiceRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            const audioUrl = URL.createObjectURL(audioBlob);

            document.getElementById('audio-preview').src = audioUrl;
            document.getElementById('audio-preview').style.display = 'block';

            // Ajouter un bouton pour envoyer l'audio
            const sendAudioBtn = document.createElement('button');
            sendAudioBtn.className = 'btn btn-primary btn-sm mt-2';
            sendAudioBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer le message vocal';
            sendAudioBtn.onclick = () => sendVoiceMessage(audioBlob);

            document.getElementById('recording-status').appendChild(sendAudioBtn);
        };

        mediaRecorder.start();
        isRecording = true;

        document.getElementById('start-recording').style.display = 'none';
        document.getElementById('stop-recording').style.display = 'inline-block';
        document.getElementById('recording-status').innerHTML = '<span class="recording">🔴 Enregistrement en cours...</span>';

    } catch (error) {
        console.error('Erreur lors de l\'enregistrement:', error);
        alert('Erreur lors de l\'accès au microphone');
    }
}

// Fonction pour arrêter l'enregistrement vocal
function stopVoiceRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;

        document.getElementById('start-recording').style.display = 'inline-block';
        document.getElementById('stop-recording').style.display = 'none';
        document.getElementById('recording-status').innerHTML = '<span class="text-success">✅ Enregistrement terminé</span>';

        // Arrêter le stream
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
}

// Fonction pour envoyer un message vocal
async function sendVoiceMessage(audioBlob) {
    try {
        const formData = new FormData();
        formData.append('content', 'Message vocal');
        formData.append('message_type', 'voice');
        formData.append('voice_file', audioBlob, 'voice_message.wav');

        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        console.log('🔑 Token JWT pour message vocal:', token ? 'Présent' : 'Absent');
        console.log('🛡️ Token CSRF:', csrfToken ? 'Présent' : 'Absent');

        if (!token) {
            console.log('⚠️ Token JWT manquant, utilisation de la route de test...');
            // Ajouter community_id au FormData
            formData.append('community_id', communityId);

            // Utiliser la route de test qui ne nécessite pas de JWT
            const testResponse = await fetch('/test/chat/voice-message', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (testResponse.ok) {
                const testData = await testResponse.json();
                console.log('✅ Message vocal envoyé via route de test');
                addMessageToChat(testData.message);
                document.getElementById('audio-preview').style.display = 'none';
                document.getElementById('recording-status').innerHTML = '';
                document.getElementById('voice-panel').style.display = 'none';
                return;
            } else {
                throw new Error('Impossible d\'envoyer le message vocal. Token JWT manquant.');
            }
        }

        if (!csrfToken) {
            throw new Error('Token CSRF manquant. Veuillez recharger la page.');
        }

        const response = await fetch(`/communities/${communityId}/chat/message`, {
            method: 'POST',
            body: formData,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        if (response.ok) {
            const data = await response.json();
            console.log('✅ Message vocal envoyé avec succès');

            // Ajouter le message vocal au chat
            addMessageToChat(data.message);

            // Nettoyer l'interface
            document.getElementById('audio-preview').style.display = 'none';
            document.getElementById('recording-status').innerHTML = '';
            document.getElementById('voice-panel').style.display = 'none';
        } else {
            // Obtenir les détails de l'erreur
            const errorData = await response.json().catch(() => ({}));
            console.log('❌ Erreur détaillée:', errorData);

            if (response.status === 419) {
                console.log('❌ Token CSRF expiré, tentative de renouvellement...');
                // Essayer de renouveler le token CSRF
                const csrfResponse = await fetch('/csrf-token');
                if (csrfResponse.ok) {
                    const csrfData = await csrfResponse.json();
                    document.querySelector('meta[name="csrf-token"]').content = csrfData.csrf_token;
                    console.log('🔄 Token CSRF renouvelé, nouvelle tentative...');

                    // Nouvelle tentative avec le nouveau token CSRF
                    const newCsrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const retryResponse = await fetch(`/communities/${communityId}/chat/message`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': newCsrfToken
                        }
                    });

                    if (retryResponse.ok) {
                        const retryData = await retryResponse.json();
                        console.log('✅ Message vocal envoyé après renouvellement CSRF');
                        addMessageToChat(retryData.message);
                        document.getElementById('audio-preview').style.display = 'none';
                        document.getElementById('recording-status').innerHTML = '';
                        document.getElementById('voice-panel').style.display = 'none';
                        return;
                    }
                }
                throw new Error('Token CSRF expiré. Veuillez recharger la page.');
            } else if (response.status === 422) {
                console.log('❌ Erreur de validation:', errorData);
                const errorMessage = errorData.message || errorData.errors ? JSON.stringify(errorData.errors || errorData.message) : 'Erreur de validation';
                throw new Error(`Erreur de validation: ${errorMessage}`);
            }
            throw new Error(`HTTP error! status: ${response.status} - ${JSON.stringify(errorData)}`);
        }
    } catch (error) {
        console.error('❌ Erreur lors de l\'envoi du message vocal:', error);
        alert('Erreur lors de l\'envoi du message vocal');
    }
}


// Initialisation Echo de secours si pas disponible
function initializeEchoFallback() {
    if (typeof window.Echo === 'undefined' && typeof Echo !== 'undefined') {
        console.log('🔄 Initialisation Echo de secours...');

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '5r9xfmwnydi4m6tg9xqw',
            wsHost: '127.0.0.1',
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback) => {
                        const token = localStorage.getItem('jwt_token');

                        fetch('/broadcasting/auth', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': token ? `Bearer ${token}` : '',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                socket_id: socketId,
                                channel_name: channel.name
                            })
                        })
                        .then(response => response.json())
                        .then(data => callback(false, data))
                        .catch(error => {
                            console.error('Erreur d\'autorisation Echo:', error);
                            callback(true, error);
                        });
                    }
                };
            },
        });

        console.log('✅ Echo de secours initialisé');
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', async function() {
    console.log('🚀 Initialisation du chat...');
    console.log('Community ID:', communityId);
    console.log('Chat Room ID:', chatRoomId);
    console.log('Current User ID:', currentUserId);

    // Vérifier et obtenir un token JWT si nécessaire
    let token = localStorage.getItem('jwt_token');
    if (!token) {
        console.log('🔑 Aucun token JWT trouvé, tentative d\'obtention...');
        const tokenObtained = await getJWTToken();
        if (!tokenObtained) {
            console.log('⚠️ Impossible d\'obtenir un token JWT, le chat fonctionnera en mode test uniquement');
        }
    } else {
        console.log('✅ Token JWT déjà présent');
    }

    // Initialiser Echo de secours si nécessaire
    setTimeout(() => {
        initializeEchoFallback();

        console.log('Echo disponible:', typeof window.Echo !== 'undefined');
        console.log('Token JWT final:', localStorage.getItem('jwt_token') ? 'Présent' : 'Absent');

        initializeChat();
        setupEventListeners();
        scrollToBottom();
    }, 1000); // Attendre 1 seconde pour que les scripts se chargent
});

function initializeChat() {
    console.log('🔧 Configuration du chat...');

    if (typeof window.Echo === 'undefined') {
        console.error('❌ Echo n\'est pas disponible !');
        return;
    }

    try {
        // Écouter les nouveaux messages
        console.log('📡 Connexion au canal:', `chat.room.${chatRoomId}`);

        Echo.private(`chat.room.${chatRoomId}`)
            .listen('MessageSent', (e) => {
                console.log('📨 Nouveau message reçu:', e);
                // Les données du message sont directement dans e, pas dans e.message
                addMessageToChat(e);
                scrollToBottom();

                // Notification sonore si activée
                if (notificationsEnabled && e.user.id !== currentUserId) {
                    playNotificationSound();
                    showBrowserNotification(e.user.name, e.content);
                }
            })
            .error((error) => {
                console.error('❌ Erreur de connexion Echo:', error);
            });

        console.log('✅ Chat configuré avec succès');
    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation du chat:', error);
    }

    // Auto-resize textarea
    const messageInput = document.getElementById('message-input');
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';

        // Activer/désactiver le bouton d'envoi
        const sendButton = document.getElementById('send-button');
        sendButton.disabled = !this.value.trim();
    });

    // Gestion des pièces jointes
    document.getElementById('attachments-input').addEventListener('change', handleAttachmentsPreview);
}

function setupEventListeners() {
    // Envoi de message
    document.getElementById('message-form').addEventListener('submit', sendMessage);

    // Bouton de test
    document.getElementById('test-button').addEventListener('click', sendTestMessage);

    // Bouton token JWT
    document.getElementById('token-button').addEventListener('click', async function() {
        console.log('🔑 Demande manuelle de token JWT...');
        const success = await getJWTToken();
        if (success) {
            alert('✅ Token JWT obtenu avec succès ! Vous pouvez maintenant envoyer des messages.');
        } else {
            alert('❌ Impossible d\'obtenir un token JWT. Vérifiez les logs de la console.');
        }
    });

    // Bouton emojis
    document.getElementById('emoji-button').addEventListener('click', function() {
        const emojiPanel = document.getElementById('emoji-panel');
        const voicePanel = document.getElementById('voice-panel');

        if (emojiPanel.style.display === 'none') {
            emojiPanel.style.display = 'block';
            voicePanel.style.display = 'none';
        } else {
            emojiPanel.style.display = 'none';
        }
    });

    // Bouton message vocal
    document.getElementById('voice-button').addEventListener('click', function() {
        const voicePanel = document.getElementById('voice-panel');
        const emojiPanel = document.getElementById('emoji-panel');

        if (voicePanel.style.display === 'none') {
            voicePanel.style.display = 'block';
            emojiPanel.style.display = 'none';
        } else {
            voicePanel.style.display = 'none';
        }
    });

    // Fonction pour convertir AudioBuffer en WAV
    function audioBufferToWav(buffer) {
        const length = buffer.length;
        const sampleRate = buffer.sampleRate;
        const arrayBuffer = new ArrayBuffer(44 + length * 2);
        const view = new DataView(arrayBuffer);

        // WAV header
        const writeString = (offset, string) => {
            for (let i = 0; i < string.length; i++) {
                view.setUint8(offset + i, string.charCodeAt(i));
            }
        };

        writeString(0, 'RIFF');
        view.setUint32(4, 36 + length * 2, true);
        writeString(8, 'WAVE');
        writeString(12, 'fmt ');
        view.setUint32(16, 16, true);
        view.setUint16(20, 1, true);
        view.setUint16(22, 1, true);
        view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * 2, true);
        view.setUint16(32, 2, true);
        view.setUint16(34, 16, true);
        writeString(36, 'data');
        view.setUint32(40, length * 2, true);

        // Convertir les données audio
        const channelData = buffer.getChannelData(0);
        let offset = 44;
        for (let i = 0; i < length; i++) {
            const sample = Math.max(-1, Math.min(1, channelData[i]));
            view.setInt16(offset, sample < 0 ? sample * 0x8000 : sample * 0x7FFF, true);
            offset += 2;
        }

        return new Blob([arrayBuffer], { type: 'audio/wav' });
    }

    // Bouton de test message vocal
    document.getElementById('test-voice-button').addEventListener('click', async function() {
        try {
            console.log('🧪 Test de message vocal...');

            // Créer un blob audio de test (silence de 2 secondes)
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const sampleRate = audioContext.sampleRate;
            const duration = 2; // 2 secondes
            const length = sampleRate * duration;
            const buffer = audioContext.createBuffer(1, length, sampleRate);
            const channelData = buffer.getChannelData(0);

            // Remplir avec du silence
            for (let i = 0; i < length; i++) {
                channelData[i] = 0;
            }

            // Convertir en WAV
            const wavBlob = audioBufferToWav(buffer);

            const formData = new FormData();
            formData.append('content', '🧪 Test de message vocal automatique');
            formData.append('message_type', 'voice');
            formData.append('voice_file', wavBlob, 'test_voice.wav');
            formData.append('community_id', communityId);

            const response = await fetch('/test/chat/voice-message', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const data = await response.json();
                console.log('✅ Test vocal réussi:', data);
                addMessageToChat(data.message);
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ Erreur test vocal:', errorData);
                alert('Erreur lors du test vocal: ' + JSON.stringify(errorData));
            }
        } catch (error) {
            console.error('❌ Erreur lors du test vocal:', error);
            alert('Erreur lors du test vocal: ' + error.message);
        }
    });

    // Gestion des emojis
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('emoji-item')) {
            const emoji = e.target.getAttribute('data-emoji');
            const messageInput = document.getElementById('message-input');
            messageInput.value += emoji;
            messageInput.focus();
            document.getElementById('emoji-panel').style.display = 'none';
        }
    });

    // Gestion de l'appareil photo
    document.getElementById('camera-input').addEventListener('change', function(e) {
        handleFileSelection(e.target.files);
    });

    // Gestion de l'enregistrement vocal
    document.getElementById('start-recording').addEventListener('click', startVoiceRecording);
    document.getElementById('stop-recording').addEventListener('click', stopVoiceRecording);

    // Touche Entrée pour envoyer (Shift+Entrée pour nouvelle ligne)
    document.getElementById('message-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(e);
        }
    });

    // Marquer comme lu lors du scroll
    document.getElementById('messages-container').addEventListener('scroll', markMessagesAsRead);
}

async function sendMessage(e) {
    e.preventDefault();

    const formData = new FormData(document.getElementById('message-form'));
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');

    if (!messageInput.value.trim()) return;

    // Désactiver le formulaire
    sendButton.disabled = true;
    messageInput.disabled = true;

    try {
        // Ajouter le token JWT aux headers
        const token = localStorage.getItem('jwt_token');
        console.log('🔑 Token JWT pour envoi message:', token ? 'Présent' : 'Absent');

        if (!token) {
            throw new Error('Token JWT manquant. Veuillez vous reconnecter.');
        }

        const response = await fetch(`/communities/${communityId}/chat/message`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                console.log('❌ Token JWT expiré ou invalide');
                // Essayer de renouveler le token
                const refreshResult = await refreshJWTToken();
                if (refreshResult) {
                    console.log('🔄 Token renouvelé, nouvelle tentative...');
                    // Nouvelle tentative avec le nouveau token
                    const newToken = localStorage.getItem('jwt_token');
                    const retryResponse = await fetch(`/communities/${communityId}/chat/message`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Authorization': `Bearer ${newToken}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!retryResponse.ok) {
                        throw new Error(`HTTP error! status: ${retryResponse.status}`);
                    }

                    const retryData = await retryResponse.json();
                    console.log('✅ Message envoyé après renouvellement du token');
                    addMessageToChat(retryData.message);
                    messageInput.value = '';
                    return;
                } else {
                    throw new Error('Token JWT expiré. Veuillez vous reconnecter.');
                }
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.success) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
            clearAttachmentsPreview();
            scrollToBottom();
        } else {
            showAlert('Erreur lors de l\'envoi du message: ' + (result.message || 'Erreur inconnue'), 'danger');
        }
    } catch (error) {
        console.error('Erreur détaillée:', error);
        showAlert('Erreur de connexion: ' + error.message, 'danger');
    } finally {
        sendButton.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    }
}

function addMessageToChat(messageData) {
    const messagesList = document.getElementById('messages-list');
    const isEmpty = messagesList.querySelector('.text-center');

    if (isEmpty) {
        isEmpty.remove();
    }

    const messageElement = createMessageElement(messageData);
    messagesList.appendChild(messageElement);
}

function createMessageElement(messageData) {
    const isOwnMessage = messageData.user.id === currentUserId;
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-item ${isOwnMessage ? 'own-message' : 'other-message'}`;
    messageDiv.setAttribute('data-message-id', messageData.id);

    const time = new Date(messageData.created_at).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit'
    });

    // Récupérer l'image de profil de l'utilisateur
    const profileImage = messageData.user.profile_image || '/storage/profiles/default.jpg';

    // Gérer différents types de messages
    let messageContent = '';
    if (messageData.message_type === 'voice' && messageData.voice_url) {
        messageContent = `
            <div class="voice-message">
                <i class="fas fa-microphone text-primary"></i>
                <audio controls>
                    <source src="${messageData.voice_url}" type="audio/wav">
                    Votre navigateur ne supporte pas la lecture audio.
                </audio>
            </div>
        `;
    } else if (messageData.message_type === 'image' && messageData.attachments && messageData.attachments.length > 0) {
        // Gérer les images
        const images = messageData.attachments.filter(att => att.mime_type && att.mime_type.startsWith('image/'));
        let imageContent = '';
        images.forEach(img => {
            imageContent += `
                <div class="message-image">
                    <img src="/storage/${img.path}" alt="${img.filename}" style="max-width: 300px; max-height: 300px; border-radius: 8px; cursor: pointer;" onclick="openImageModal('/storage/${img.path}')">
                </div>
            `;
        });
        messageContent = `
            ${imageContent}
            ${messageData.content ? `<div class="message-content">${messageData.content}</div>` : ''}
        `;
    } else if (messageData.attachments && messageData.attachments.length > 0) {
        // Gérer les fichiers non-images
        const nonImages = messageData.attachments.filter(att => !att.mime_type || !att.mime_type.startsWith('image/'));
        let fileContent = '';
        nonImages.forEach(file => {
            fileContent += `
                <div class="message-file">
                    <i class="fas fa-file"></i>
                    <a href="/storage/${file.path}" target="_blank" download="${file.filename}">
                        ${file.filename}
                    </a>
                    <small class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</small>
                </div>
            `;
        });
        messageContent = `
            ${fileContent}
            ${messageData.content ? `<div class="message-content">${messageData.content}</div>` : ''}
        `;
    } else {
        messageContent = `<div class="message-content">${messageData.content}</div>`;
    }

    if (isOwnMessage) {
        // Message de l'utilisateur actuel (à droite)
        messageDiv.innerHTML = `
            <div class="message-bubble">
                ${messageContent}
                <div class="message-time">
                    ${time}
                    <i class="fas fa-check-double text-primary ms-1"></i>
                </div>
            </div>
        `;
    } else {
        // Message des autres utilisateurs (à gauche avec avatar)
        messageDiv.innerHTML = `
            <img src="${profileImage}" class="message-avatar" onerror="this.src='/storage/profiles/default.jpg'">
            <div class="message-bubble">
                <div class="message-sender">${messageData.user.name}</div>
                ${messageContent}
                <div class="message-time">
                    ${time}
                </div>
            </div>
        `;
    }

    return messageDiv;
}

function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Fonction pour ouvrir une image en modal
function openImageModal(imageSrc) {
    // Créer le modal s'il n'existe pas
    let modal = document.getElementById('image-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'image-modal';
        modal.className = 'modal';
        modal.style.display = 'none';
        modal.innerHTML = `
            <div class="modal-content">
                <span class="close">&times;</span>
                <img id="modal-image" src="" style="max-width: 90%; max-height: 90%;">
            </div>
        `;
        document.body.appendChild(modal);

        // Fermer le modal en cliquant sur X ou en dehors
        modal.querySelector('.close').onclick = () => modal.style.display = 'none';
        modal.onclick = (e) => {
            if (e.target === modal) modal.style.display = 'none';
        };
    }

    // Afficher l'image
    document.getElementById('modal-image').src = imageSrc;
    modal.style.display = 'block';
}

function markMessagesAsRead() {
    // Marquer les messages comme lus
    const token = localStorage.getItem('jwt_token');

    fetch(`/communities/${communityId}/chat/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Authorization': token ? `Bearer ${token}` : '',
            'Accept': 'application/json'
        }
    }).catch(error => {
        console.error('Erreur lors du marquage des messages comme lus:', error);
    });
}

// Nouvelles fonctions pour les actions rapides
function showMembers() {
    alert('Fonctionnalité "Membres" - À implémenter');
}

function inviteMembers() {
    alert('Fonctionnalité "Inviter des membres" - À implémenter');
}

function showSettings() {
    alert('Fonctionnalité "Paramètres" - À implémenter');
}

function exportChat() {
    alert('Fonctionnalité "Exporter le chat" - À implémenter');
}

function handleAttachmentsPreview(e) {
    const files = Array.from(e.target.files);
    const preview = document.getElementById('attachments-preview');
    const list = document.getElementById('attachments-list');

    if (files.length === 0) {
        preview.style.display = 'none';
        return;
    }

    preview.style.display = 'block';
    list.innerHTML = '';

    files.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'd-flex align-items-center justify-content-between mb-1';

        const icon = file.type.startsWith('image/') ? 'fas fa-image' : 'fas fa-file';
        const size = (file.size / 1024).toFixed(1) + ' KB';

        item.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="${icon} me-2"></i>
                <span>${file.name}</span>
                <small class="text-muted ms-2">(${size})</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment(${index})">
                <i class="fas fa-times"></i>
            </button>
        `;

        list.appendChild(item);
    });
}

function removeAttachment(index) {
    const input = document.getElementById('attachments-input');
    const dt = new DataTransfer();

    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });

    input.files = dt.files;
    handleAttachmentsPreview({ target: input });
}

function clearAttachmentsPreview() {
    document.getElementById('attachments-input').value = '';
    document.getElementById('attachments-preview').style.display = 'none';
}

function playNotificationSound() {
    // Créer un son de notification simple
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUarm7blmGgU7k9n1unEiBC13yO/eizEIHWq+8+OWT');
    audio.play().catch(() => {}); // Ignorer les erreurs de lecture
}

function showBrowserNotification(senderName, content) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(`${senderName} - ${document.title}`, {
            body: content,
            icon: '/favicon.ico'
        });
    }
}

function toggleNotifications() {
    notificationsEnabled = !notificationsEnabled;
    const icon = document.getElementById('notification-icon');

    if (notificationsEnabled) {
        icon.className = 'fas fa-bell';
        icon.parentElement.classList.remove('btn-secondary');
        icon.parentElement.classList.add('btn-outline-secondary');
    } else {
        icon.className = 'fas fa-bell-slash';
        icon.parentElement.classList.remove('btn-outline-secondary');
        icon.parentElement.classList.add('btn-secondary');
    }
}

function showMembers() {
    new bootstrap.Modal(document.getElementById('membersModal')).show();
}

function showInfo() {
    alert('Informations de la communauté:\n\n' +
          `Nom: ${document.querySelector('h5').textContent}\n` +
          `Description: {{ $community->description }}\n` +
          `Membres: {{ $activeMembers->count() }}`);
}

function showAlert(message, type = 'info') {
    // Créer une alerte temporaire
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Demander la permission pour les notifications
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
</script>

</body>
</html>
