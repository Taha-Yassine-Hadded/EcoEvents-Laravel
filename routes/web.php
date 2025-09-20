<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

// About page
Route::get('/about', function () {
    return view('about'); // resources/views/about.blade.php
});

// Blog 2 Column page
Route::get('/blog-2column', function () {
    return view('blog-2column'); // resources/views/blog-2column.blade.php
});

// Blog Details page
Route::get('/blog-details', function () {
    return view('blog-details'); // resources/views/blog-details.blade.php
});

// Blog Grid page
Route::get('/blog-grid', function () {
    return view('blog-grid'); // resources/views/blog-grid.blade.php
});

// Blog List page
Route::get('/blog-list', function () {
    return view('blog-list'); // resources/views/blog-list.blade.php
});

// Contact page
Route::get('/contact', function () {
    return view('contact'); // resources/views/contact.blade.php
});

// Service page
Route::get('/service', function () {
    return view('service'); // resources/views/service.blade.php
});

// Service details
Route::get('/service-details', function () {
    return view('service-details'); // resources/views/service-details.blade.php
});

// Project
Route::get('/project', function () {
    return view('project'); // resources/views/project.blade.php
});

// Project details
Route::get('/project-details', function () {
    return view('project-details'); // resources/views/project-details.blade.php
});

// Donation
Route::get('/donation', function () {
    return view('donation'); // resources/views/donation.blade.php
});

// Donation details
Route::get('/donation-details', function () {
    return view('donation-details'); // resources/views/donation-details.blade.php
});

// Team
Route::get('/team', function () {
    return view('team'); // resources/views/team.blade.php
});

// FAQ
Route::get('/faqs', function () {
    return view('faqs'); // resources/views/faqs.blade.php
});

// Testimonial
Route::get('/testimonial', function () {
    return view('testimonial'); // resources/views/testimonial.blade.php
});

