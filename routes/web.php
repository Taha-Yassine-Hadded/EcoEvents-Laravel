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
use Illuminate\Support\Facades\Route;

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
});

// --------------------
// Registrations Routes (users only)
// --------------------
Route::prefix('user')->middleware([\App\Http\Middleware\VerifyJWT::class, \App\Http\Middleware\RoleGuard::class . ':user'])->group(function () {
    Route::post('/events/{event}/register', [RegistrationController::class, 'register'])->name('events.register');
    Route::get('/my-registrations', [RegistrationController::class, 'myRegistrations'])->name('registrations.index');
});
