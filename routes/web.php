<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Front\FrontCampaignController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SponsorDashboardController;
use App\Http\Controllers\SponsorProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\SponsorManagementController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\AdminPackageController;
use App\Http\Middleware\VerifyJWT;
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

// ==================== SPONSOR MANAGEMENT ROUTES ====================

// Sponsor Dashboard
Route::get('/sponsor-dashboard', [SponsorDashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.dashboard');

// ==================== SPONSOR ANALYTICS ====================
Route::get('/sponsor/analytics', [\App\Http\Controllers\SponsorAnalyticsController::class, 'dashboard'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.analytics');

Route::get('/sponsor/analytics/chart-data', [\App\Http\Controllers\SponsorAnalyticsController::class, 'getChartData'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.analytics.chart');

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

// Admin Contracts Management
Route::prefix('admin/contracts')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminContractController::class, 'index'])->name('admin.contracts.index');
    Route::get('/{id}', [\App\Http\Controllers\AdminContractController::class, 'show'])->name('admin.contracts.show');
    Route::get('/{id}/download', [\App\Http\Controllers\AdminContractController::class, 'download'])->name('admin.contracts.download');
    Route::get('/{id}/view', [\App\Http\Controllers\AdminContractController::class, 'view'])->name('admin.contracts.view');
    Route::post('/{id}/regenerate', [\App\Http\Controllers\AdminContractController::class, 'regenerate'])->name('admin.contracts.regenerate');
    Route::delete('/{id}/delete', [\App\Http\Controllers\AdminContractController::class, 'delete'])->name('admin.contracts.delete');
    Route::get('/export/all', [\App\Http\Controllers\AdminContractController::class, 'exportAll'])->name('admin.contracts.export');
});

// Routes pour les contrats PDF
Route::prefix('contracts')->middleware([\App\Http\Middleware\VerifyJWT::class])->group(function () {
    Route::get('/sponsorship/{id}/download', [ContractController::class, 'downloadContract'])->name('contracts.sponsorship.download');
    Route::get('/sponsorship/{id}/view', [ContractController::class, 'viewContract'])->name('contracts.sponsorship.view');
});

// Admin Campaigns Management
Route::prefix('admin/campaigns')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminCampaignController::class, 'index'])->name('admin.campaigns.index');
    Route::get('/data', [\App\Http\Controllers\AdminCampaignController::class, 'getCampaignsData'])->name('admin.campaigns.data');
    Route::post('/', [\App\Http\Controllers\AdminCampaignController::class, 'store'])->name('admin.campaigns.store');
    Route::get('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'show'])->name('admin.campaigns.show');
    Route::put('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'update'])->name('admin.campaigns.update');
    Route::post('/{id}/toggle-status', [\App\Http\Controllers\AdminCampaignController::class, 'toggleStatus'])->name('admin.campaigns.toggle-status');
    Route::delete('/{id}', [\App\Http\Controllers\AdminCampaignController::class, 'destroy'])->name('admin.campaigns.destroy');
});

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
});

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
    ->name('api.campaigns.like');

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
Route::prefix('admin')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->name('admin.')->group(function () {

    // Events
    Route::get('/events', [EventController::class, 'backIndex'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'createAdmin'])->name('events.create');
    Route::get('/events/{event}/details', [EventController::class, 'showAdmin'])->name('admin.events.show');
    Route::post('/events', [EventController::class, 'storeAdmin'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'editAdmin'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'updateAdmin'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroyAdmin'])->name('events.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Packages
    Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}', [AdminPackageController::class, 'show'])->name('packages.show');
    Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');
    Route::post('/packages/{package}/toggle-status', [AdminPackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    Route::post('/packages/{package}/duplicate', [AdminPackageController::class, 'duplicate'])->name('packages.duplicate');
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

Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])->name('registrations.index');

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

// ==================== APIs IA pour Recommandations de Sponsoring ====================

// APIs de recommandations IA
Route::prefix('api/sponsor/ai')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    
    // Recommandations d'événements
    Route::get('/recommendations/events', [\App\Http\Controllers\AISponsorshipController::class, 'recommendEvents'])
        ->name('api.sponsor.ai.recommendations.events');
    
    // Recommandations de packages pour un événement
    Route::get('/recommendations/packages/{event_id}', [\App\Http\Controllers\AISponsorshipController::class, 'recommendPackages'])
        ->name('api.sponsor.ai.recommendations.packages');
    
    // Recommandations de budget pour un événement
    Route::get('/recommendations/budget/{event_id}', [\App\Http\Controllers\AISponsorshipController::class, 'recommendBudget'])
        ->name('api.sponsor.ai.recommendations.budget');
    
    // Recommandations de timing pour proposer un sponsorship
    Route::get('/recommendations/timing/{event_id}', [\App\Http\Controllers\AISponsorshipController::class, 'recommendTiming'])
        ->name('api.sponsor.ai.recommendations.timing');
    
    // Insights sur le profil du sponsor
    Route::get('/insights/profile', [\App\Http\Controllers\AISponsorshipController::class, 'getSponsorInsights'])
        ->name('api.sponsor.ai.insights.profile');
});

// ==================== APIs pour Système de Commentaires & Feedback ====================

// APIs de feedback et commentaires
Route::prefix('api/sponsor/feedback')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    
    // Routes spécifiques (doivent être avant les routes avec paramètres)
    Route::get('/types', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'getFeedbackTypes'])
        ->name('api.sponsor.feedback.types');
    
    Route::get('/most-helpful', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'getMostHelpful'])
        ->name('api.sponsor.feedback.most.helpful');
    
    Route::get('/search', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'search'])
        ->name('api.sponsor.feedback.search');
    
    Route::get('/events', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'getEvents'])
        ->name('api.sponsor.feedback.events');
    
    Route::get('/sponsorships', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'getUserSponsorships'])
        ->name('api.sponsor.feedback.sponsorships');
    
    Route::get('/event/{eventId}/stats', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'getEventStats'])
        ->name('api.sponsor.feedback.event.stats');
});

// APIs pour les notifications sponsor
Route::prefix('api/sponsor/notifications')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    
    Route::get('/', [\App\Http\Controllers\SponsorNotificationController::class, 'index'])
        ->name('api.sponsor.notifications.index');
    
    Route::get('/unread', [\App\Http\Controllers\SponsorNotificationController::class, 'unread'])
        ->name('api.sponsor.notifications.unread');
    
    Route::post('/{id}/read', [\App\Http\Controllers\SponsorNotificationController::class, 'markAsRead'])
        ->name('api.sponsor.notifications.read');
    
    Route::post('/mark-all-read', [\App\Http\Controllers\SponsorNotificationController::class, 'markAllAsRead'])
        ->name('api.sponsor.notifications.mark-all-read');
    
    Route::get('/preferences', [\App\Http\Controllers\SponsorNotificationController::class, 'getPreferences'])
        ->name('api.sponsor.notifications.preferences');
    
    Route::put('/preferences', [\App\Http\Controllers\SponsorNotificationController::class, 'updatePreferences'])
        ->name('api.sponsor.notifications.update-preferences');
    
    Route::delete('/{id}', [\App\Http\Controllers\SponsorNotificationController::class, 'destroy'])
        ->name('api.sponsor.notifications.destroy');
});

// Retour aux routes feedback
Route::prefix('api/sponsor/feedback')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    
    // CRUD des feedbacks
    Route::get('/', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'index'])
        ->name('api.sponsor.feedback.index');
    
    Route::post('/', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'store'])
        ->name('api.sponsor.feedback.store');
    
    Route::get('/{id}', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'show'])
        ->name('api.sponsor.feedback.show');
    
    Route::put('/{id}', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'update'])
        ->name('api.sponsor.feedback.update');
    
    Route::delete('/{id}', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'destroy'])
        ->name('api.sponsor.feedback.destroy');
    
    // Actions sur les feedbacks
    Route::post('/{id}/like', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'toggleLike'])
        ->name('api.sponsor.feedback.like');
});

// Route pour la page des recommandations IA
Route::get('/sponsor/ai-recommendations', [\App\Http\Controllers\AISponsorshipController::class, 'showRecommendationsPage'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.ai.recommendations');

// ==================== ROUTES SPONSOR STORIES ====================

// Routes pour la gestion des stories des sponsors
Route::prefix('sponsor/stories')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    
    // Pages principales
    Route::get('/', [\App\Http\Controllers\SponsorStoryController::class, 'index'])
        ->name('sponsor.stories.index');
    
    Route::get('/my-stories', [\App\Http\Controllers\SponsorStoryController::class, 'myStories'])
        ->name('sponsor.stories.my-stories');
    
    Route::get('/create', [\App\Http\Controllers\SponsorStoryController::class, 'create'])
        ->name('sponsor.stories.create');
    
    Route::get('/{id}', [\App\Http\Controllers\SponsorStoryController::class, 'show'])
        ->name('sponsor.stories.show');
    
    Route::get('/{id}/edit', [\App\Http\Controllers\SponsorStoryController::class, 'edit'])
        ->name('sponsor.stories.edit');
    
    // Actions CRUD
    Route::post('/', [\App\Http\Controllers\SponsorStoryController::class, 'store'])
        ->name('sponsor.stories.store');
    
    Route::put('/{id}', [\App\Http\Controllers\SponsorStoryController::class, 'update'])
        ->name('sponsor.stories.update');
    
    Route::delete('/{id}', [\App\Http\Controllers\SponsorStoryController::class, 'destroy'])
        ->name('sponsor.stories.destroy');
    
    // Actions spéciales
    Route::post('/{id}/feature', [\App\Http\Controllers\SponsorStoryController::class, 'markAsFeatured'])
        ->name('sponsor.stories.feature');
    
    Route::post('/{id}/unfeature', [\App\Http\Controllers\SponsorStoryController::class, 'unmarkAsFeatured'])
        ->name('sponsor.stories.unfeature');
    
    Route::post('/{id}/extend', [\App\Http\Controllers\SponsorStoryController::class, 'extend'])
        ->name('sponsor.stories.extend');
    
    Route::post('/{id}/like', [\App\Http\Controllers\SponsorStoryController::class, 'like'])
        ->name('sponsor.stories.like');
    
    // API endpoints
    Route::prefix('api')->group(function () {
        Route::get('/', [\App\Http\Controllers\SponsorStoryController::class, 'apiIndex'])
            ->name('api.sponsor.stories.index');
        
        Route::get('/stats', [\App\Http\Controllers\SponsorStoryController::class, 'apiStats'])
            ->name('api.sponsor.stories.stats');
    });
});

// Routes publiques pour voir les stories (sans authentification)
Route::prefix('stories')->group(function () {
    Route::get('/', [\App\Http\Controllers\SponsorStoryController::class, 'index'])
        ->name('stories.public.index');
    
    Route::get('/{id}', [\App\Http\Controllers\SponsorStoryController::class, 'show'])
        ->name('stories.public.show');
    
    Route::post('/{id}/like', [\App\Http\Controllers\SponsorStoryController::class, 'like'])
        ->name('stories.public.like');
});

// Routes admin pour la gestion des stories
Route::prefix('admin/stories')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    
    Route::get('/', [\App\Http\Controllers\SponsorStoryController::class, 'adminIndex'])
        ->name('admin.stories.index');
    
    Route::post('/cleanup-expired', [\App\Http\Controllers\SponsorStoryController::class, 'adminCleanupExpired'])
        ->name('admin.stories.cleanup-expired');
});

// Route pour la page de feedback
Route::get('/sponsor/feedback', [\App\Http\Controllers\SponsorshipFeedbackController::class, 'showFeedbackPage'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.feedback');

// Routes pour le profil sponsor
Route::get('/sponsor/profile', [\App\Http\Controllers\SponsorProfileController::class, 'show'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.profile');

// API Routes pour le profil sponsor
Route::prefix('api/sponsor')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])->group(function () {
    Route::post('/profile', [\App\Http\Controllers\SponsorProfileController::class, 'update']);
    Route::post('/profile/password', [\App\Http\Controllers\SponsorProfileController::class, 'updatePassword']);
});

Route::get('/sponsor/notifications', [\App\Http\Controllers\SponsorNotificationController::class, 'showNotificationsPage'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.notifications');