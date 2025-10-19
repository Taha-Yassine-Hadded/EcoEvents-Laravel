<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\FrontCampaignController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Event Registration Route
Route::post('/events/{event}/register', [RegistrationController::class, 'register'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('events.register');

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

// Admin Dashboard (accessible by admin and organizer)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class])
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
})->middleware(\App\Http\Middleware\VerifyJWT::class)->name('organizer.home');

Route::get('/participant-home', function () {
    return view('pages.frontOffice.home')->with('role_message', 'Bienvenue Participant ! Découvrez les événements écologiques près de chez vous.');
})->middleware(\App\Http\Middleware\VerifyJWT::class)->name('participant.home');

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

// Page de test pour le token JWT
Route::get('/test-token', function() {
    return view('test-token');
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
    });
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


// --------------------
// FrontOffice (Public events, visible to all users)
// --------------------
Route::get('/events', [EventController::class, 'index'])->name('front.events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('front.events.show');


// --------------------
// FrontOffice Organizer (Own events management)
// --------------------
Route::prefix('organizer/events')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])->group(function () {
    Route::get('/create', [EventController::class, 'create'])->name('front.events.create');
    Route::post('/', [EventController::class, 'store'])->name('front.events.store');
    Route::get('/{event}/edit', [EventController::class, 'edit'])->name('front.events.edit');
    Route::put('/{event}', [EventController::class, 'update'])->name('front.events.update');
    Route::delete('/{event}', [EventController::class, 'destroy'])->name('front.events.destroy');
});

// --------------------
// BackOffice (Admin full CRUD for events and categories)
// --------------------
Route::prefix('admin')->middleware([\App\Http\Middleware\VerifyJWT::class])->name('admin.')->group(function () {

    // Events (accessible by admin and organizer)
    Route::get('/events', [EventController::class, 'backIndex'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'createAdmin'])->name('events.create');
    Route::get('/events/{event}/details', [EventController::class, 'showAdmin'])->name('admin.events.show');
    Route::post('/events', [EventController::class, 'storeAdmin'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'editAdmin'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'updateAdmin'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroyAdmin'])->name('events.destroy');
    
    // Event Subscribers (for organizers to view their event subscribers)
    Route::get('/events/{event}/subscribers', [EventController::class, 'eventSubscribers'])->name('events.subscribers');

});

// Admin and Organizer routes (categories management)
Route::prefix('admin')->middleware([\App\Http\Middleware\VerifyJWT::class])->name('admin.')->group(function () {
    // Categories (admin and organizer)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});


Route::get('/api/categories', function() {
    $categories = \App\Models\Category::all();
    return response()->json(['categories' => $categories]);
});

Route::prefix('organizer/events')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])->group(function () {
    Route::post('/{event}', [EventController::class, 'updateOrganizer'])->name('organizer.events.update');
});

// --------------------
// Event Subscription (AJAX endpoint for all authenticated users)
// --------------------
Route::post('/events/{event}/subscribe', [RegistrationController::class, 'subscribe'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('events.subscribe');

Route::delete('/events/{event}/unsubscribe', [RegistrationController::class, 'unsubscribe'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('events.unsubscribe');

Route::get('/events/{event}/registration-status', [RegistrationController::class, 'checkRegistration'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('events.registration.status');

Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])
    ->middleware(\App\Http\Middleware\VerifyJWT::class)
    ->name('registrations.index');

// Organizer Dashboard Routes
Route::get('/organizer/dashboard', [EventController::class, 'organizerDashboard'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])
    ->name('organizer.dashboard');

Route::get('/organizer/events/{event}/subscribers', [EventController::class, 'eventSubscribers'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])
    ->name('organizer.event.subscribers');

Route::prefix('api')->group(function () {
    Route::get('/events/{event}/registrations', [RegistrationController::class, 'getEventRegistrations'])->name('api.events.registrations');
});

Route::post('/generate-description', [EventController::class, 'generateDescription'])
    ->name('admin.events.generate-description');
