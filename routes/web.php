<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\FrontCampaignController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CommunityForumThreadController;
use App\Http\Controllers\CommunityForumPostController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use Tymon\JWTAuth\Facades\JWTAuth;

// Routes de broadcasting pour Echo/Reverb
Broadcast::routes(['middleware' => ['web']]);

// Canal de chat pour les communautés
Broadcast::channel('chat.room.{roomId}', function ($user, int $roomId) {
    $room = ChatRoom::find($roomId);
    if (!$room) {
        return false;
    }
    // Owner can access
    if ($room->owner_id === $user->id) {
        return true;
    }
    // Members can access
    return ChatRoomMember::where('chat_room_id', $room->id)
        ->where('user_id', $user->id)
        ->exists();
});

Route::get('/', function () {
    return view('pages.frontOffice.home');
})->name('home');



// About page
Route::get('/about', function () {
    return view('pages.frontOffice.about');
});

// Blog 2 Column page
Route::get('/blog-2column', function () {
    return view('pages.frontOffice.blog-2column');
});

// Blog Details page
Route::get('/blog-details', function () {
    return view('pages.frontOffice.blog-details');
});

// Blog Grid page
Route::get('/blog-grid', function () {
    return view('pages.frontOffice.blog-grid');
});

// Blog List page
Route::get('/blog-list', function () {
    return view('pages.frontOffice.blog-list');
});

// Contact page
Route::get('/contact', function () {
    return view('pages.frontOffice.contact');
});

// Service page
Route::get('/service', function () {
    return view('pages.frontOffice.service');
});

// Service details
Route::get('/service-details', function () {
    return view('pages.frontOffice.service-details');
});

// Project
Route::get('/project', function () {
    return view('pages.frontOffice.project');
});

// Project details
Route::get('/project-details', function () {
    return view('pages.frontOffice.project-details');
});

// Donation
Route::get('/donation', function () {
    return view('pages.frontOffice.donation');
});

// Donation details
Route::get('/donation-details', function () {
    return view('pages.frontOffice.donation-details');
});

// Team
Route::get('/team', function () {
    return view('pages.frontOffice.team');
});

// FAQ
Route::get('/faqs', function () {
    return view('pages.frontOffice.faqs');
});

// Testimonial
Route::get('/testimonial', function () {
    return view('pages.frontOffice.testimonial');
});

// Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])
    ->name('admin.dashboard');

/*Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('\App\Http\Middleware\VerifyJWT::class')
    ->name('admin.dashboard');*/

// Test route
// Test route avec un seul middleware
Route::get('/test', function () {
    return 'Test route works!';
})->middleware(\App\Http\Middleware\VerifyJWT::class);


// Simple register test
Route::get('/register-test', function () {
    return 'Register route works!';
});

// Test controller
Route::get('/register-controller-test', [AuthController::class, 'showRegisterForm']);

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware(\App\Http\Middleware\VerifyJWT::class)->name('logout');

// User Routes
Route::get('/user', [UserController::class, 'getUser'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('user.get');


// Routes pour les différents rôles
Route::get('/organizer-home', function () {
    return view('pages.frontOffice.home')->with('role_message', 'Bienvenue Organisateur ! Vous pouvez maintenant gérer vos événements.');
})->middleware('jwt.optional')->name('organizer.home');

Route::get('/participant-home', function () {
    return view('pages.frontOffice.home')->with('role_message', 'Bienvenue Participant ! Découvrez les événements écologiques près de chez vous.');
})->middleware('jwt.optional')->name('participant.home');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode'])->name('password.email');
Route::get('/verify-reset-code', [PasswordResetController::class, 'showVerifyCodeForm'])->name('password.reset.verify');
Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode'])->name('password.verify');
Route::get('/reset-password', [PasswordResetController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Route temporaire pour vérifier l'utilisateur connecté
Route::get('/check-user', function () {
    $user = Auth::user();
    if ($user) {
        return response()->json([
            'connected' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    } else {
        return response()->json(['connected' => false, 'message' => 'Aucun utilisateur connecté']);
    }
})->middleware(\App\Http\Middleware\VerifyJWT::class);

// ========================================
// ROUTES COMMUNAUTÉS - INTERFACE ORGANISATEUR
// ========================================
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PublicCommunityController;

Route::prefix('organizer')->name('organizer.')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])->group(function () {
    // CRUD Communautés
    Route::resource('communities', CommunityController::class);

    // Actions spéciales
    Route::patch('communities/{community}/toggle-status', [CommunityController::class, 'toggleStatus'])->name('communities.toggle-status');

    // Gestion des demandes d'adhésion
    Route::post('communities/{community}/approve/{user}', [\App\Http\Controllers\CommunityMembershipController::class, 'approve'])->name('communities.approve');
    Route::post('communities/{community}/reject/{user}', [\App\Http\Controllers\CommunityMembershipController::class, 'reject'])->name('communities.reject');
});

// Route de test pour vérifier le rôle utilisateur
Route::get('/test-user', function() {
    $token = request()->bearerToken();
    $headerToken = request()->header('Authorization');
    $sessionToken = session('jwt_token');

    return response()->json([
        'bearer_token' => $token,
        'header_auth' => $headerToken,
        'session_token' => $sessionToken,
        'localStorage_info' => 'Check browser localStorage for jwt_token',
        'headers' => request()->headers->all()
    ]);
});

// Route de test pour le chat
Route::get('/test-chat', function() {
    $communities = \App\Models\Community::with('members')->where('is_active', true)->take(3)->get();

    if ($communities->isEmpty()) {
        return response()->json(['error' => 'Aucune communauté active trouvée']);
    }

    $community = $communities->first();

    return response()->json([
        'community' => $community,
        'chat_url' => route('communities.chat', $community),
        'members_count' => $community->members->count(),
        'reverb_config' => [
            'host' => env('REVERB_HOST'),
            'port' => env('REVERB_PORT'),
            'scheme' => env('REVERB_SCHEME'),
        ]
    ]);
});

// Page de test pour le token JWT
Route::get('/test-token', function() {
    return view('test-token');
});

// Page de test pour le chat
Route::get('/test-chat-debug', function() {
    return view('chat-test');
});

// Route de test directe pour le chat (sans authentification)
Route::get('/test-chat-direct', function() {
    $community = \App\Models\Community::where('name', 'Communauté Test Chat')->first();
    if (!$community) {
        return 'Communauté de test non trouvée. Exécutez: php artisan db:seed --class=ChatTestSeeder';
    }

    return redirect()->route('communities.chat', $community);
});

// Route de test simple (sans authentification)
Route::get('/test-chat-simple', function() {
    return view('chat-test-simple');
});

// Route de test directe pour le chat communautaire
Route::get('/test-chat-direct/{community}', function(\App\Models\Community $community) {
    $user = Auth::user();

    // Si pas d'utilisateur, utiliser un utilisateur de test
    if (!$user) {
        $user = \App\Models\User::where('email', 'user1@test.com')->first();
        if ($user) {
            Auth::login($user);
        }
    }

    // Ajouter l'utilisateur à la communauté s'il n'y est pas
    if ($user) {
        \App\Models\CommunityMember::updateOrCreate([
            'community_id' => $community->id,
            'user_id' => $user->id
        ], [
            'status' => 'approved',
            'joined_at' => now()
        ]);
    }

    // Récupérer ou créer la salle de chat
    $chatRoom = \App\Models\ChatRoom::where('target_type', 'community')
        ->where('target_id', $community->id)
        ->first();

    if (!$chatRoom) {
        $chatRoom = \App\Models\ChatRoom::create([
            'owner_id' => $community->organizer_id,
            'target_type' => 'community',
            'target_id' => $community->id,
            'name' => "Chat - {$community->name}",
            'is_private' => false
        ]);
    }

    // Récupérer les messages
    $messages = \App\Models\ChatMessage::with('user:id,name')
        ->where('chat_room_id', $chatRoom->id)
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get()
        ->reverse();

    // Récupérer les membres actifs
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

    return view('chat.community', compact('community', 'chatRoom', 'messages', 'activeMembers'));
});

// Route de connexion automatique pour les tests
Route::get('/auto-login', function() {
    return view('auto-login');
});

// Page de test pour les communautés
Route::get('/test-communities', function() {
    return view('test-communities');
});

// Route pour activer toutes les communautés
Route::get('/activate-communities', function() {
    $count = \App\Models\Community::query()->update(['is_active' => 1]);
    return "✅ {$count} communautés activées ! <a href='/communities'>Voir les communautés</a>";
});

// Route pour vérifier votre rôle utilisateur
Route::get('/check-role', function() {
    if (Auth::check()) {
        $user = Auth::user();
        return "
        <h3>👤 Informations utilisateur :</h3>
        <p><strong>Nom :</strong> {$user->name}</p>
        <p><strong>Email :</strong> {$user->email}</p>
        <p><strong>Rôle :</strong> <span style='color: " . ($user->role === 'organizer' ? 'green' : 'blue') . "'>{$user->role}</span></p>
        <hr>
        <a href='/communities'>Voir les communautés</a> |
        " . ($user->role === 'organizer' ? "<a href='/organizer/communities'>Interface Organisateur</a>" : "<em>Pas d'accès organisateur</em>") . "
        <hr>
        <h4>🔧 Actions de test :</h4>
        <a href='/switch-to-participant' style='background: blue; color: white; padding: 5px 10px; text-decoration: none;'>Devenir Participant</a> |
        <a href='/switch-to-organizer' style='background: green; color: white; padding: 5px 10px; text-decoration: none;'>Devenir Organisateur</a>
        ";
    } else {
        return "❌ Vous n'êtes pas connecté. <a href='/login'>Se connecter</a>";
    }
})->middleware('web');

// Routes pour changer de rôle (pour test)
Route::get('/switch-to-participant', function() {
    if (Auth::check()) {
        Auth::user()->update(['role' => 'participant']);
        return "✅ Vous êtes maintenant PARTICIPANT. <a href='/communities'>Voir les communautés</a>";
    }
    return redirect('/login');
})->middleware('web');

Route::get('/switch-to-organizer', function() {
    if (Auth::check()) {
        Auth::user()->update(['role' => 'organizer']);
        return "✅ Vous êtes maintenant ORGANISATEUR. <a href='/communities'>Voir les communautés</a>";
    }
    return redirect('/login');
})->middleware('web');

// Route pour forcer la déconnexion complète
Route::get('/force-logout', function() {
    // Déconnexion Laravel
    Auth::logout();

    // Vider toutes les sessions
    session()->flush();
    session()->regenerate();

    // Supprimer le token JWT du localStorage (via JavaScript)
    return "
    <h3>🚪 Déconnexion forcée</h3>
    <p>Toutes les sessions ont été supprimées.</p>
    <script>
        // Supprimer le token JWT
        localStorage.removeItem('jwt_token');
        sessionStorage.clear();

        // Redirection après nettoyage
        setTimeout(function() {
            window.location.href = '/login';
        }, 2000);
    </script>
    <p>Redirection vers la page de connexion...</p>
    ";
})->middleware('web');

// ========================================
// ROUTES COMMUNAUTÉS - INTERFACE PUBLIQUE
// ========================================
Route::prefix('communities')->name('communities.')->group(function () {
    // Pages publiques (avec middleware JWT optionnel)
    Route::get('/', [PublicCommunityController::class, 'index'])->name('index')->middleware('jwt.optional');
    Route::get('/{community}', [PublicCommunityController::class, 'show'])->name('show')->middleware('jwt.optional');
    Route::get('/category/{category}', [PublicCommunityController::class, 'byCategory'])->name('by-category');

    // Actions nécessitant une connexion
    Route::middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
        Route::post('/{community}/join', [\App\Http\Controllers\CommunityMembershipController::class, 'join'])->name('join');
        Route::delete('/{community}/leave', [\App\Http\Controllers\CommunityMembershipController::class, 'leave'])->name('leave');

        // Routes du chat communautaire
        Route::get('/{community}/chat', [\App\Http\Controllers\CommunityChatController::class, 'show'])->name('chat');
        Route::post('/{community}/chat/message', [\App\Http\Controllers\CommunityChatController::class, 'sendMessage'])->name('chat.send');
        Route::get('/{community}/chat/messages', [\App\Http\Controllers\CommunityChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/{community}/chat/read', [\App\Http\Controllers\CommunityChatController::class, 'markAsRead'])->name('chat.read');
    });
});
// UI Forum (pages Blade)
Route::prefix('ui/communities/{community}/forum')->group(function () {
    Route::get('/', function (\App\Models\Community $community) {
        // Vue liste threads
        return view('forum.index');
    })->name('ui.forum.index');

    Route::get('/{thread}', function (\App\Models\Community $community, \App\Models\CommunityForumThread $thread) {
        // Vue détail thread
        return view('forum.show');
    })->name('ui.forum.show');
});

// Lecture publique (jwt.optional)
Route::middleware('jwt.optional')->group(function () {
    Route::get('/communities/{community}/forum', [CommunityForumThreadController::class, 'index']);
    Route::get('/communities/{community}/forum/{thread}', [CommunityForumThreadController::class, 'show']);
    Route::get('/communities/{community}/forum/{thread}/posts', [CommunityForumPostController::class, 'index']);
});

// Actions (connexion requise)
Route::middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
    Route::post('/communities/{community}/forum', [CommunityForumThreadController::class, 'store']);
    Route::patch('/communities/{community}/forum/{thread}', [CommunityForumThreadController::class, 'update']);
    Route::delete('/communities/{community}/forum/{thread}', [CommunityForumThreadController::class, 'destroy']);

    Route::post('/communities/{community}/forum/{thread}/posts', [CommunityForumPostController::class, 'store']);
    Route::patch('/communities/{community}/forum/{thread}/posts/{post}', [CommunityForumPostController::class, 'update']);
    Route::delete('/communities/{community}/forum/{thread}/posts/{post}', [CommunityForumPostController::class, 'destroy']);
});

// Modération (organisateur)
Route::prefix('organizer')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])->group(function () {
    Route::patch('/communities/{community}/forum/{thread}/pin', [CommunityForumThreadController::class, 'pin']);
    Route::patch('/communities/{community}/forum/{thread}/unpin', [CommunityForumThreadController::class, 'unpin']);
    Route::patch('/communities/{community}/forum/{thread}/lock', [CommunityForumThreadController::class, 'lock']);
    Route::patch('/communities/{community}/forum/{thread}/unlock', [CommunityForumThreadController::class, 'unlock']);
    Route::patch('/communities/{community}/forum/{thread}/hide', [CommunityForumThreadController::class, 'hide']);
    Route::patch('/communities/{community}/forum/{thread}/unhide', [CommunityForumThreadController::class, 'unhide']);
});
// ========================================
// ROUTES GESTION DES DEMANDES D'ADHÉSION
// ========================================
Route::prefix('organizer')->name('organizer.')->middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
    // Gestion des demandes d'adhésion
    Route::get('/membership-requests', [\App\Http\Controllers\CommunityController::class, 'membershipRequests'])->name('membership-requests');
    Route::post('/membership-requests/{membership}/approve', [\App\Http\Controllers\CommunityController::class, 'approveMembership'])->name('membership.approve');
    Route::post('/membership-requests/{membership}/reject', [\App\Http\Controllers\CommunityController::class, 'rejectMembership'])->name('membership.reject');
});


// Routes pour la gestion des campagnes
Route::prefix('admin/campaigns')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/create', [CampaignController::class, 'create'])->name('admin.campaigns.create');
    Route::post('/store', [CampaignController::class, 'store'])->name('admin.campaigns.store');
    Route::get('/', [CampaignController::class, 'index'])->name('admin.campaigns.index');
    Route::delete('/{id}', [CampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
    Route::get('/{id}', [CampaignController::class, 'show'])->name('admin.campaigns.show');
    Route::post('/{id}', [CampaignController::class, 'update'])->name('admin.campaigns.update');
    Route::post('/{id}/duplicate', [CampaignController::class, 'duplicate'])->name('admin.campaigns.duplicate');
    Route::get('/{id}/export', [CampaignController::class, 'export'])->name('admin.campaigns.export');
    Route::post('/{id}/notify', [CampaignController::class, 'notify'])->name('admin.campaigns.notify');
    Route::get('/{id}/comments', [CampaignController::class, 'comments'])->name('admin.campaigns.comments');
    Route::delete('/{id}/comments/{comment}', [CampaignController::class, 'deleteComment'])->name('admin.campaigns.comments.delete');

}
);

Route::prefix('campaigns')->group(function () {
    Route::get('/', [FrontCampaignController::class, 'index'])->name('front.campaigns.index');
    Route::get('/{campaign}', [FrontCampaignController::class, 'show'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.show');


    Route::put('/{campaign}/comments/{comment}', [FrontCampaignController::class, 'updateComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.update');

    Route::delete('/{campaign}/comments/{comment}', [FrontCampaignController::class, 'deleteComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.delete');


    Route::post('/{campaign}/comments', [FrontCampaignController::class, 'storeComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.store');

    Route::post('/{campaign}/comments/{comment}/like', [FrontCampaignController::class, 'likeComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('api.comments.like');
});

Route::post('/campaigns/filter', [FrontCampaignController::class, 'filter'])->name('api.campaigns.filter');
// API routes
Route::post('/campaigns/{campaign}/like', [FrontCampaignController::class, 'like'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('api.campaigns.like');// Dans web.php, dans le groupe admin/campaigns
//Route::delete('/{id}', [CampaignController::class, 'destroy'])->name('admin.campaigns.destroy');


// ========================================
// CHAT TEMPS RÉEL (Reverb) - ROUTES ISOLÉES
// ========================================
Route::prefix('chat')
    ->middleware([\App\Http\Middleware\VerifyJWT::class])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->group(function () {
    // Créer un groupe privé custom
    Route::post('/rooms', [ChatController::class, 'createRoom'])->name('chat.rooms.create');

    // Mes salons (JSON)
    Route::get('/my-rooms', [ChatController::class, 'myRooms'])->name('chat.rooms.mine');

    // Démarrer/retourner une conversation 1–1
    Route::post('/one-to-one', [ChatController::class, 'oneToOne'])->name('chat.rooms.onetoone');

    // Contacts (scope community|all + search)
    Route::get('/contacts', [ChatController::class, 'contacts'])->name('chat.contacts');

    // Historique d'un salon
    Route::get('/rooms/{room}/messages', [ChatController::class, 'history'])->name('chat.rooms.history');

    // Envoyer un message
    Route::post('/rooms/{room}/messages', [ChatController::class, 'send'])->name('chat.rooms.send');

    // Marquer comme lu
    Route::post('/rooms/{room}/read', [ChatController::class, 'markRead'])->name('chat.rooms.read');

    // Indicateur de frappe (stub)
    Route::post('/rooms/{room}/typing', [ChatController::class, 'typing'])->name('chat.rooms.typing');
});

// Route pour obtenir un token JWT (pour le chat)
Route::post('/api/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    return response()->json([
        'success' => false,
        'error' => 'Identifiants invalides'
    ], 401);
})->name('api.login');

// Route pour obtenir un nouveau token CSRF
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');

// Route pour uploader des fichiers (images, audio)
Route::post('/upload/chat-file', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'file' => 'required|file|max:10240', // 10MB max
        'type' => 'required|in:image,voice,document'
    ]);

    $file = $request->file('file');
    $type = $request->type;

    // Définir le dossier selon le type
    $folder = match($type) {
        'image' => 'chat/images',
        'voice' => 'chat/voice',
        'document' => 'chat/documents',
        default => 'chat/files'
    };

    // Générer un nom unique
    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    // Stocker le fichier
    $path = $file->storeAs($folder, $filename, 'public');

    return response()->json([
        'success' => true,
        'file_url' => '/storage/' . $path,
        'filename' => $filename,
        'size' => $file->getSize(),
        'type' => $type
    ]);
})->name('upload.chat.file');

// Route de test pour envoyer des messages vocaux sans JWT (pour debug)
Route::post('/test/chat/voice-message', function (\Illuminate\Http\Request $request) {
    // Debug: Afficher toutes les données reçues
    \Log::info('Test voice message reçu:', [
        'all_data' => $request->all(),
        'files' => $request->allFiles(),
        'has_voice_file' => $request->hasFile('voice_file'),
        'community_id' => $request->community_id
    ]);

    $communityId = $request->community_id;
    if (!$communityId) {
        return response()->json(['error' => 'community_id manquant'], 422);
    }

    $community = \App\Models\Community::find($communityId);
    if (!$community) {
        return response()->json(['error' => 'Communauté non trouvée'], 404);
    }

    $chatRoom = \App\Models\ChatRoom::where('target_type', 'community')
        ->where('target_id', $community->id)
        ->first();

    if (!$chatRoom) {
        return response()->json(['error' => 'ChatRoom non trouvée'], 404);
    }

    // Gérer le message vocal
    $voiceUrl = null;
    if ($request->hasFile('voice_file')) {
        $voiceFile = $request->file('voice_file');
        $filename = time() . '_voice_test_' . uniqid() . '.' . $voiceFile->getClientOriginalExtension();
        $path = $voiceFile->storeAs('chat/voice', $filename, 'public');
        $voiceUrl = '/storage/' . $path;
        \Log::info('Fichier vocal stocké:', ['path' => $voiceUrl]);
    }

    // Créer un message vocal de test
    $message = \App\Models\ChatMessage::create([
        'chat_room_id' => $chatRoom->id,
        'user_id' => 3, // dozz
        'content' => '🎤 Message vocal de test',
        'message_type' => 'voice',
        'voice_url' => $voiceUrl
    ]);

    $message->load('user:id,name,profile_image');

    // Déclencher l'événement
    event(new \App\Events\MessageSent($message));

    return response()->json([
        'success' => true,
        'message' => [
            'id' => $message->id,
            'content' => $message->content,
            'message_type' => $message->message_type,
            'voice_url' => $message->voice_url,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'profile_image' => $message->user->profile_image,
            ],
            'created_at' => $message->created_at->toIso8601String(),
            'room_id' => $message->chat_room_id,
        ]
    ]);
})->name('test.chat.voice.message');

// Route de test pour envoyer des messages sans JWT (pour debug)
Route::post('/test/chat/message', function (\Illuminate\Http\Request $request) {
    $community = \App\Models\Community::find($request->community_id);
    if (!$community) {
        return response()->json(['error' => 'Communauté non trouvée'], 404);
    }

    $chatRoom = \App\Models\ChatRoom::where('target_type', 'community')
        ->where('target_id', $community->id)
        ->first();

    if (!$chatRoom) {
        return response()->json(['error' => 'ChatRoom non trouvée'], 404);
    }

    // Créer un message de test
    $message = \App\Models\ChatMessage::create([
        'chat_room_id' => $chatRoom->id,
        'user_id' => 3, // dozz
        'content' => $request->content ?: 'Message de test',
        'message_type' => 'text'
    ]);

    $message->load('user:id,name');

    // Déclencher l'événement
    broadcast(new \App\Events\MessageSent($message));

    return response()->json([
        'success' => true,
        'message' => [
            'id' => $message->id,
            'content' => $message->content,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
            ],
            'created_at' => $message->created_at->toIso8601String(),
            'room_id' => $message->chat_room_id,
        ]
    ]);
})->name('test.chat.message');

// Route spécifique pour accéder directement au chat d'une communauté
Route::get('/ui/chat/community/{community}', function (\App\Models\Community $community) {
    $user = Auth::user();
    if (!$user) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder au chat.');
    }

    // Vérifier si l'utilisateur est membre de la communauté
    $isMember = $community->members()
        ->where('user_id', $user->id)
        ->where('status', 'approved')
        ->exists();

    if (!$isMember) {
        // Ajouter l'utilisateur à la communauté pour les tests
        \App\Models\CommunityMember::updateOrCreate([
            'community_id' => $community->id,
            'user_id' => $user->id
        ], [
            'status' => 'approved',
            'joined_at' => now()
        ]);
    }

    // Trouver la ChatRoom de la communauté
    $chatRoom = \App\Models\ChatRoom::where('target_type', 'community')
        ->where('target_id', $community->id)
        ->first();

    if (!$chatRoom) {
        return "ChatRoom non trouvée pour la communauté {$community->name}";
    }

    return redirect("/ui/chat/rooms/{$chatRoom->id}");
})->middleware(['jwt.optional'])->name('ui.chat.community');

// Vue minimale pour tester un salon (n'affecte pas les pages existantes)
Route::get('/ui/chat/rooms/{room}', function (\App\Models\ChatRoom $room) {
    // Debug: afficher les informations de la room
    $debug = "DEBUG - ChatRoom ID: {$room->id}<br>";
    $debug .= "Target Type: {$room->target_type}<br>";
    $debug .= "Target ID: {$room->target_id}<br>";

    // Récupérer la communauté associée au chat
    $community = null;
    if ($room->target_type === 'community') {
        $community = \App\Models\Community::find($room->target_id);
        $debug .= "Community found: " . ($community ? $community->name : 'NULL') . "<br>";
    } else {
        $debug .= "Room is not for community<br>";

        // Si c'est une room custom, essayer de trouver une room communautaire pour cet utilisateur
        $user = Auth::user();
        if ($user) {
            $userCommunityRooms = \App\Models\ChatRoom::where('target_type', 'community')
                ->whereHas('members', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->get();

            if ($userCommunityRooms->count() > 0) {
                $communityRoom = $userCommunityRooms->first();
                $debug .= "Found community room: {$communityRoom->id} for community {$communityRoom->target_id}<br>";
                return redirect("/ui/chat/rooms/{$communityRoom->id}")->with('debug', $debug . 'Redirected to community room');
            }
        }
    }

    if (!$community) {
        return $debug . '<br>Communauté non trouvée pour cette salle de chat.';
    }

    // Mode test : ajouter l'utilisateur à la communauté s'il n'y est pas
    $user = Auth::user();
    if ($user) {
        $isMember = $community->members()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();

        if (!$isMember) {
            // Ajouter l'utilisateur à la communauté pour les tests
            \App\Models\CommunityMember::updateOrCreate([
                'community_id' => $community->id,
                'user_id' => $user->id
            ], [
                'status' => 'approved',
                'joined_at' => now()
            ]);
            $debug .= "User added to community<br>";
        } else {
            $debug .= "User already member<br>";
        }
    }

    $debug .= "Redirecting to communities.chat with community ID: {$community->id}<br>";

    // Rediriger vers la route du chat communautaire
    return redirect()->route('communities.chat', $community)->with('debug', $debug);
})->middleware(['jwt.optional'])->name('ui.chat.room');

// Page "Mes salons" (UI isolée)
Route::get('/ui/chat/my-rooms', function () {
    return view('chat.my-rooms');
})->middleware(['jwt.optional'])->name('ui.chat.myrooms');

// UI WhatsApp-like
Route::get('/ui/chat/whatsapp', function () {
    return view('chat.whatsapp');
})->middleware(['jwt.optional'])->name('ui.chat.whatsapp');
