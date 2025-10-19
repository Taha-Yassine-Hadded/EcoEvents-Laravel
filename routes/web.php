<?php

use App\Http\Controllers\Api\SentimentAnalysisController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignAnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\FrontCampaignController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SponsorDashboardController;
use App\Http\Controllers\SponsorProfileController;
use App\Http\Controllers\SponsorManagementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PublicCommunityController;
use App\Http\Controllers\CommunityMembershipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContractController;
use App\Http\Middleware\VerifyJWT;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

// ==================== SPONSOR MANAGEMENT ROUTES ====================

// Sponsor Dashboard
Route::get('/sponsor-dashboard', [SponsorDashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.dashboard');

// ==================== PROFILE MANAGEMENT ====================
Route::get('/sponsor/profile', [SponsorManagementController::class, 'showProfile'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.profile');

Route::put('/sponsor/profile', [SponsorManagementController::class, 'updateProfile'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.profile.update');

Route::put('/sponsor/profile/password', [SponsorManagementController::class, 'updatePassword'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.profile.password');

Route::delete('/sponsor/profile', [SponsorManagementController::class, 'deleteProfile'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.profile.delete');

// ==================== COMPANY MANAGEMENT ====================
Route::get('/sponsor/company', [SponsorManagementController::class, 'showCompany'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.company');

Route::put('/sponsor/company', [SponsorManagementController::class, 'updateCompany'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.company.update');

// ==================== CAMPAIGNS MANAGEMENT ====================
Route::get('/sponsor/campaigns', [SponsorDashboardController::class, 'campaigns'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.campaigns');

Route::get('/sponsor/campaigns/{id}', [SponsorManagementController::class, 'showCampaignDetails'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.campaign.details');

// ==================== SPONSORSHIPS MANAGEMENT ====================
Route::get('/sponsor/sponsorships', [SponsorManagementController::class, 'showSponsorships'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.sponsorships');

Route::get('/sponsor/all-sponsorships', [SponsorManagementController::class, 'showAllSponsorships'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.all-sponsorships');

Route::post('/sponsor/sponsorships', [SponsorManagementController::class, 'createSponsorship'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.sponsorships.create');

Route::put('/sponsor/sponsorships/{id}/cancel', [SponsorManagementController::class, 'cancelSponsorship'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.sponsorships.cancel');

// ==================== STATISTICS ====================
Route::get('/sponsor/statistics', [SponsorManagementController::class, 'showStatistics'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.statistics');

// Test route
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

// Page Blade publique (le JS enverra Authorization: Bearer <token>)
Route::view('/profile', 'pages.frontOffice.profile-edit')->name('profile.edit');

// Actions protégées par JWT (appels fetch depuis la page)
Route::middleware([VerifyJWT::class])->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

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

// ==================== ADMIN ROUTES ====================

// Admin Dashboard
Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])
    ->name('admin.dashboard');

// Admin Sponsors Management
Route::prefix('admin/sponsors')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminSponsorController::class, 'index'])->name('admin.sponsors.index');
    Route::get('/data', [\App\Http\Controllers\AdminSponsorController::class, 'getSponsorsData'])->name('admin.sponsors.data');
    Route::get('/pending-sponsorships', [\App\Http\Controllers\AdminSponsorController::class, 'pendingSponsorships'])->name('admin.sponsors.pending-sponsorships');
    Route::get('/approved-sponsorships', [\App\Http\Controllers\AdminSponsorController::class, 'approvedSponsorships'])->name('admin.sponsors.approved-sponsorships');
    Route::post('/sponsorships/{id}/approve', [\App\Http\Controllers\AdminSponsorController::class, 'approveSponsorship'])->name('admin.sponsors.sponsorships.approve');
    Route::post('/sponsorships/{id}/reject', [\App\Http\Controllers\AdminSponsorController::class, 'rejectSponsorship'])->name('admin.sponsors.sponsorships.reject');
    Route::post('/sponsorships/{id}/complete', [\App\Http\Controllers\AdminSponsorController::class, 'completeSponsorship'])->name('admin.sponsors.sponsorships.complete');
    Route::get('/sponsorships/{id}/contract', [\App\Http\Controllers\AdminSponsorController::class, 'viewContract'])->name('admin.sponsors.contract.view');
    Route::get('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'show'])->name('admin.sponsors.show');
    Route::put('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'update'])->name('admin.sponsors.update');
    Route::post('/{id}/approve', [\App\Http\Controllers\AdminSponsorController::class, 'approve'])->name('admin.sponsors.approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\AdminSponsorController::class, 'reject'])->name('admin.sponsors.reject');
    Route::post('/{id}/toggle-status', [\App\Http\Controllers\AdminSponsorController::class, 'toggleStatus'])->name('admin.sponsors.toggle-status');
    Route::delete('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'destroy'])->name('admin.sponsors.destroy');
});

// Admin Contracts shortcut (read-only list of approved sponsorships with contracts)
Route::get('/admin/contracts', [\App\Http\Controllers\AdminSponsorController::class, 'approvedSponsorships'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])
    ->name('admin.contracts.index');

// Routes pour les contrats PDF
Route::prefix('contracts')->middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
    Route::get('/sponsorship/{id}/download', [ContractController::class, 'downloadContract'])->name('contracts.sponsorship.download');
    Route::get('/sponsorship/{id}/view', [ContractController::class, 'viewContract'])->name('contracts.sponsorship.view');
});

/*
 * // Admin Campaigns Management
Route::prefix('admin/campaigns')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminCampaignController::class, 'index'])->name('admin.campaigns.index');
    Route::get('/data', [\App\Http\Controllers\AdminCampaignController::class, 'getCampaignsData'])->name('admin.campaigns.data');
    Route::post('/', [\App\Http\Controllers\AdminCampaignController::class, 'store'])->name('admin.campaigns.store');
    Route::get('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'show'])->name('admin.campaigns.show');
    Route::put('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'update'])->name('admin.campaigns.update');
    Route::post('/{id}/toggle-status', [\App\Http\Controllers\AdminCampaignController::class, 'toggleStatus'])->name('admin.campaigns.toggle-status');
    Route::delete('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
});*/

// ========================================
// ROUTES COMMUNAUTÉS - INTERFACE ORGANISATEUR
// ========================================
Route::prefix('organizer')->name('organizer.')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':organizer'])->group(function () {
    // CRUD Communautés
    Route::resource('communities', CommunityController::class);

    // Actions spéciales
    Route::patch('communities/{community}/toggle-status', [CommunityController::class, 'toggleStatus'])->name('communities.toggle-status');

    // Gestion des demandes d'adhésion
    Route::post('communities/{community}/approve/{user}', [CommunityMembershipController::class, 'approve'])->name('communities.approve');
    Route::post('communities/{community}/reject/{user}', [CommunityMembershipController::class, 'reject'])->name('communities.reject');
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
    Route::get('/', [PublicCommunityController::class, 'index'])->name('index')->middleware('jwt.optional');
    Route::get('/{community}', [PublicCommunityController::class, 'show'])->name('show')->middleware('jwt.optional');
    Route::get('/category/{category}', [PublicCommunityController::class, 'byCategory'])->name('by-category');

    Route::middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
        Route::post('/{community}/join', [CommunityMembershipController::class, 'join'])->name('join');
        Route::delete('/{community}/leave', [CommunityMembershipController::class, 'leave'])->name('leave');
    });
});

// ========================================
// ROUTES GESTION DES DEMANDES D'ADHÉSION
// ========================================
Route::prefix('organizer')->name('organizer.')->middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
    Route::get('/membership-requests', [CommunityController::class, 'membershipRequests'])->name('membership-requests');
    Route::post('/membership-requests/{membership}/approve', [CommunityController::class, 'approveMembership'])->name('membership.approve');
    Route::post('/membership-requests/{membership}/reject', [CommunityController::class, 'rejectMembership'])->name('membership.reject');
});

// Routes pour la gestion des campagnes
Route::prefix('admin/campaigns')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/create', [CampaignController::class, 'create'])->name('admin.campaigns.create');
    Route::post('/store', [CampaignController::class, 'store'])->name('admin.campaigns.store');
    Route::get('/dashboard', [CampaignAnalyticsController::class, 'dashboard'])->name('admin.campaigns.dashboard');
    Route::get('/stats/api', [CampaignAnalyticsController::class, 'apiStats'])->name('admin.campaigns.stats.api');
    Route::get('/', [CampaignController::class, 'index'])->name('admin.campaigns.index');
    Route::delete('/{id}', [CampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
    Route::post('/{id}', [CampaignController::class, 'update'])->name('admin.campaigns.update');
    Route::post('/{id}/duplicate', [CampaignController::class, 'duplicate'])->name('admin.campaigns.duplicate');
    Route::get('/{id}/export', [CampaignController::class, 'export'])->name('admin.campaigns.export');
    Route::post('/{id}/notify', [CampaignController::class, 'notify'])->name('admin.campaigns.notify');
    Route::get('/{id}/comments', [CampaignController::class, 'comments'])->name('admin.campaigns.comments');
    Route::delete('/{id}/comments/{comment}', [CampaignController::class, 'deleteComment'])->name('admin.campaigns.comments.delete');
    Route::get('/{id}', [CampaignController::class, 'show'])->name('admin.campaigns.show');



});

Route::prefix('campaigns')->group(function () {
    Route::get('/{campaign}/sentiments', [FrontCampaignController::class, 'getCampaignSentiments'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.sentiments');

    Route::get('/', [FrontCampaignController::class, 'index'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.index');
    Route::get('/{campaign}', [FrontCampaignController::class, 'show'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.show');

    // Recommandations
    Route::get('/recommendations', [FrontCampaignController::class, 'recommendations'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.recommendations');

    // Invalidation cache
    Route::post('/invalidate-recommendations-cache', [FrontCampaignController::class, 'invalidateRecommendationsCache'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.invalidate-cache');

    // Likes
    Route::post('/{campaign}/like', [FrontCampaignController::class, 'like'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('api.campaigns.like');

    // Commentaires
    Route::post('/{campaign}/comments', [FrontCampaignController::class, 'storeComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.store');

    Route::put('/{campaign}/comments/{comment}', [FrontCampaignController::class, 'updateComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.update');

    Route::delete('/{campaign}/comments/{comment}', [FrontCampaignController::class, 'deleteComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('front.campaigns.comments.delete');

    Route::post('/{campaign}/comments/{comment}/like', [FrontCampaignController::class, 'likeComment'])
        ->middleware(\App\Http\Middleware\VerifyJWT::class)
        ->name('api.comments.like');
});

// Route de filtrage
Route::post('/campaigns/filter', [FrontCampaignController::class, 'filter'])->name('api.campaigns.filter');

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
    Route::post('/{event}', [EventController::class, 'updateOrganizer'])->name('organizer.events.update');
});

// --------------------
// BackOffice (Admin full CRUD for events and categories)
// --------------------
Route::prefix('admin')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->name('admin.')->group(function () {
    // Events
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

// Routes API Sentiment
Route::prefix('api')->middleware(\App\Http\Middleware\VerifyJWT::class)->name('api.')->group(function () {
    Route::post('/sentiment/analyze', [SentimentAnalysisController::class, 'analyzeComment'])->name('sentiment.analyze');
    Route::get('/sentiment/test', [SentimentAnalysisController::class, 'testConnection'])->name('sentiment.test');
});

// Routes admin pour gestion sentiments
Route::prefix('admin')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->name('admin.')->group(function () {
    Route::get('/campaigns/{campaign}/sentiments', [CampaignController::class, 'sentiments'])->name('campaigns.sentiments');
    Route::delete('/sentiments/{sentiment}', [CampaignController::class, 'deleteSentiment'])->name('sentiments.destroy');
    Route::get('/sentiments/stats', function() {
        return \App\Models\CampaignCommentSentiment::selectRaw('
            detected_language,
            AVG(overall_sentiment_score) as avg_score,
            COUNT(*) as total_comments,
            SUM(CASE WHEN overall_sentiment_score > 0 THEN 1 ELSE 0 END) as positive_count
        ')
            ->groupBy('detected_language')
            ->get();
    })->name('sentiments.stats');
});

// ✅ ROUTE DEBUG PYTHON DIRECT
Route::get('/debug/python-test', function() {
    $testData = [
        'campaign_id' => 21,
        'comment_id' => 50,
        'content' => 'Ana farhèn barcha! 🇹🇳 yallah khouya zwin 😊',
        'user_id' => 4
    ];

    $pythonUrl = env('PYTHON_API_URL', 'http://localhost:5000/analyze-comment');

    Log::info('🧪 TEST PYTHON DIRECT', [
        'url' => $pythonUrl,
        'data' => $testData,
        'env_check' => [
            'PYTHON_API_URL' => $pythonUrl,
            'env_exists' => env('PYTHON_API_URL') !== null
        ]
    ]);

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->withOptions(['verify' => false])
            ->post($pythonUrl, $testData);

        $result = [
            'url_tested' => $pythonUrl,
            'status' => $response->status(),
            'successful' => $response->successful(),
            'response_time' => $response->header('X-Response-Time') ?? 'N/A',
            'body' => $response->body(),
            'json' => $response->json(),
            'headers' => $response->headers->all()
        ];

        Log::info('📡 RÉSULTAT PYTHON', $result);

        return response()->json($result, $response->status());

    } catch (\Exception $e) {
        $error = [
            'error' => $e->getMessage(),
            'url' => $pythonUrl,
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        Log::error('💥 ERREUR PYTHON TEST', $error);
        return response()->json($error, 500);
    }
})->name('debug.python.test');

// ✅ ROUTE TEST API LARA VEL SENTIMENT
Route::post('/debug/sentiment-laravel', function(Request $request) {
    $data = $request->validate([
        'campaign_id' => 'required|integer',
        'comment_id' => 'required|integer',
        'content' => 'required|string'
    ]);

    try {
        $response = app(\App\Http\Controllers\Api\SentimentAnalysisController::class)
            ->analyzeComment(new \Illuminate\Http\Request($data));

        return $response;
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware(\App\Http\Middleware\VerifyJWT::class);

// API route pour récupérer les détails d'un événement (pour les modales)
Route::get('/api/events/{event}', function($eventId) {
    try {
        $event = \App\Models\Event::with(['category', 'organizer'])->find($eventId);

        if (!$event) {
            return response()->json(['success' => false, 'error' => 'Événement non trouvé'], 404);
        }

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => 'Erreur lors du chargement de l\'événement'], 500);
    }
})->name('api.events.show');

// Route pour créer une proposition de sponsoring
Route::post('/sponsor/sponsorships', [SponsorDashboardController::class, 'createSponsorship'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.sponsorships.create');
