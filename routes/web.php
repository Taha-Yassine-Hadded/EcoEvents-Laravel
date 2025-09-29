<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SponsorDashboardController;
use App\Http\Controllers\SponsorProfileController;
use App\Http\Controllers\SponsorManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\VerifyJWT; // <-- importe ton middleware



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
Route::get('/sponsor/campaigns', [SponsorManagementController::class, 'showCampaigns'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.campaigns');

Route::get('/sponsor/campaigns/{id}', [SponsorManagementController::class, 'showCampaignDetails'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.campaign.details');

// ==================== SPONSORSHIPS MANAGEMENT ====================
Route::get('/sponsor/sponsorships', [SponsorManagementController::class, 'showSponsorships'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':sponsor'])
    ->name('sponsor.sponsorships');

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

/*Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('\App\Http\Middleware\VerifyJWT::class')
    ->name('admin.dashboard');*/

// Test route
// Test route avec un seul middleware
Route::get('/test', function () {
    return 'Test route works!';
})->middleware(\App\Http\Middleware\RoleGuard::class);


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






// Page Blade publique (le JS enverra Authorization: Bearer <token>)
Route::view('/profile', 'pages.frontOffice.profile-edit')->name('profile.edit');

// Actions protégées par JWT (appels fetch depuis la page)
Route::middleware([VerifyJWT::class])->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ==================== ADMIN ROUTES ====================

// Admin Dashboard
Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])
    ->name('admin.dashboard');

// Admin Sponsors Management
Route::prefix('admin/sponsors')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminSponsorController::class, 'index'])->name('admin.sponsors.index');
    Route::get('/data', [\App\Http\Controllers\AdminSponsorController::class, 'getSponsorsData'])->name('admin.sponsors.data');
    Route::get('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'show'])->name('admin.sponsors.show');
    Route::put('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'update'])->name('admin.sponsors.update');
    Route::post('/{id}/approve', [\App\Http\Controllers\AdminSponsorController::class, 'approve'])->name('admin.sponsors.approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\AdminSponsorController::class, 'reject'])->name('admin.sponsors.reject');
    Route::post('/{id}/toggle-status', [\App\Http\Controllers\AdminSponsorController::class, 'toggleStatus'])->name('admin.sponsors.toggle-status');
    Route::delete('/{id}', [\App\Http\Controllers\AdminSponsorController::class, 'destroy'])->name('admin.sponsors.destroy');
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

