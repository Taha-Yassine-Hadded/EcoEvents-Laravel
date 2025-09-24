<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
Route::get('/dashboard', function () {
    return view('pages.backOffice.dashboard');
});

// Test route
Route::get('/test', function () {
    return 'Test route works!';
});

// Simple register test
Route::get('/register-test', function () {
    return 'Register route works!';
});

// Test controller
Route::get('/register-controller-test', [AuthController::class, 'showRegisterForm']);

// Registration Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
