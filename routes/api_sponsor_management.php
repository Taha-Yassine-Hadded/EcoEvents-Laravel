<?php

// API Routes pour la gestion des sponsors - Version métier avancée
// Fichier: routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SponsorApiController;
use App\Http\Controllers\Api\SponsorshipApiController;
use App\Http\Controllers\Api\SponsorAnalyticsApiController;
use App\Http\Controllers\Api\SponsorNotificationApiController;
use App\Http\Controllers\Api\SponsorContractApiController;
use App\Http\Controllers\Api\SponsorRecommendationApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Sponsor Management
|--------------------------------------------------------------------------
|
| Routes API dédiées à la gestion des sponsors avec fonctionnalités métier avancées
|
*/

// ==================== AUTHENTIFICATION API ====================
Route::prefix('auth')->group(function () {
    Route::post('/sponsor/register', [SponsorApiController::class, 'register']);
    Route::post('/sponsor/login', [SponsorApiController::class, 'login']);
    Route::post('/sponsor/logout', [SponsorApiController::class, 'logout'])->middleware('auth:api');
    Route::post('/sponsor/refresh', [SponsorApiController::class, 'refresh'])->middleware('auth:api');
    Route::post('/sponsor/forgot-password', [SponsorApiController::class, 'forgotPassword']);
    Route::post('/sponsor/reset-password', [SponsorApiController::class, 'resetPassword']);
});

// ==================== SPONSOR PROFILE API ====================
Route::prefix('sponsors')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // Profile Management
    Route::get('/profile', [SponsorApiController::class, 'getProfile']);
    Route::put('/profile', [SponsorApiController::class, 'updateProfile']);
    Route::post('/profile/avatar', [SponsorApiController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [SponsorApiController::class, 'deleteAvatar']);
    Route::put('/profile/password', [SponsorApiController::class, 'updatePassword']);
    
    // Company Management
    Route::get('/company', [SponsorApiController::class, 'getCompany']);
    Route::put('/company', [SponsorApiController::class, 'updateCompany']);
    Route::post('/company/logo', [SponsorApiController::class, 'uploadCompanyLogo']);
    Route::delete('/company/logo', [SponsorApiController::class, 'deleteCompanyLogo']);
    
    // Preferences & Settings
    Route::get('/preferences', [SponsorApiController::class, 'getPreferences']);
    Route::put('/preferences', [SponsorApiController::class, 'updatePreferences']);
    Route::get('/notification-settings', [SponsorApiController::class, 'getNotificationSettings']);
    Route::put('/notification-settings', [SponsorApiController::class, 'updateNotificationSettings']);
});

// ==================== SPONSORSHIP MANAGEMENT API ====================
Route::prefix('sponsorships')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // CRUD Operations
    Route::get('/', [SponsorshipApiController::class, 'index']);
    Route::get('/{id}', [SponsorshipApiController::class, 'show']);
    Route::post('/', [SponsorshipApiController::class, 'store']);
    Route::put('/{id}', [SponsorshipApiController::class, 'update']);
    Route::delete('/{id}', [SponsorshipApiController::class, 'destroy']);
    
    // Status Management
    Route::post('/{id}/cancel', [SponsorshipApiController::class, 'cancel']);
    Route::post('/{id}/reactivate', [SponsorshipApiController::class, 'reactivate']);
    
    // Bulk Operations
    Route::post('/bulk-cancel', [SponsorshipApiController::class, 'bulkCancel']);
    Route::post('/bulk-update', [SponsorshipApiController::class, 'bulkUpdate']);
    
    // Filtering & Search
    Route::get('/search/{query}', [SponsorshipApiController::class, 'search']);
    Route::get('/filter/by-status/{status}', [SponsorshipApiController::class, 'filterByStatus']);
    Route::get('/filter/by-event/{eventId}', [SponsorshipApiController::class, 'filterByEvent']);
    Route::get('/filter/by-date-range', [SponsorshipApiController::class, 'filterByDateRange']);
    
    // Export & Reporting
    Route::get('/export/csv', [SponsorshipApiController::class, 'exportCsv']);
    Route::get('/export/pdf', [SponsorshipApiController::class, 'exportPdf']);
    Route::get('/report/summary', [SponsorshipApiController::class, 'getSummaryReport']);
});

// ==================== CONTRACT MANAGEMENT API ====================
Route::prefix('contracts')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // Contract Operations
    Route::get('/', [SponsorContractApiController::class, 'index']);
    Route::get('/{id}', [SponsorContractApiController::class, 'show']);
    Route::get('/{id}/download', [SponsorContractApiController::class, 'download']);
    Route::get('/{id}/view', [SponsorContractApiController::class, 'view']);
    
    // Contract Actions
    Route::post('/{id}/sign', [SponsorContractApiController::class, 'sign']);
    Route::post('/{id}/request-changes', [SponsorContractApiController::class, 'requestChanges']);
    Route::post('/{id}/approve', [SponsorContractApiController::class, 'approve']);
    
    // Bulk Contract Operations
    Route::post('/bulk-download', [SponsorContractApiController::class, 'bulkDownload']);
    Route::get('/export/all', [SponsorContractApiController::class, 'exportAll']);
});

// ==================== ANALYTICS & REPORTING API ====================
Route::prefix('analytics')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // Dashboard Analytics
    Route::get('/dashboard', [SponsorAnalyticsApiController::class, 'getDashboardData']);
    Route::get('/overview', [SponsorAnalyticsApiController::class, 'getOverview']);
    
    // Performance Metrics
    Route::get('/performance', [SponsorAnalyticsApiController::class, 'getPerformanceMetrics']);
    Route::get('/roi-analysis', [SponsorAnalyticsApiController::class, 'getROIAnalysis']);
    Route::get('/success-rate', [SponsorAnalyticsApiController::class, 'getSuccessRate']);
    
    // Financial Analytics
    Route::get('/financial-summary', [SponsorAnalyticsApiController::class, 'getFinancialSummary']);
    Route::get('/budget-utilization', [SponsorAnalyticsApiController::class, 'getBudgetUtilization']);
    Route::get('/investment-trends', [SponsorAnalyticsApiController::class, 'getInvestmentTrends']);
    
    // Event Analytics
    Route::get('/event-performance', [SponsorAnalyticsApiController::class, 'getEventPerformance']);
    Route::get('/category-analysis', [SponsorAnalyticsApiController::class, 'getCategoryAnalysis']);
    Route::get('/geographic-analysis', [SponsorAnalyticsApiController::class, 'getGeographicAnalysis']);
    
    // Time-based Analytics
    Route::get('/monthly-report', [SponsorAnalyticsApiController::class, 'getMonthlyReport']);
    Route::get('/yearly-report', [SponsorAnalyticsApiController::class, 'getYearlyReport']);
    Route::get('/custom-date-range', [SponsorAnalyticsApiController::class, 'getCustomDateRangeReport']);
    
    // Chart Data
    Route::get('/charts/sponsorship-trends', [SponsorAnalyticsApiController::class, 'getSponsorshipTrendsChart']);
    Route::get('/charts/roi-distribution', [SponsorAnalyticsApiController::class, 'getROIDistributionChart']);
    Route::get('/charts/event-categories', [SponsorAnalyticsApiController::class, 'getEventCategoriesChart']);
    Route::get('/charts/budget-allocation', [SponsorAnalyticsApiController::class, 'getBudgetAllocationChart']);
});

// ==================== RECOMMENDATION ENGINE API ====================
Route::prefix('recommendations')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // AI Recommendations
    Route::get('/events', [SponsorRecommendationApiController::class, 'getEventRecommendations']);
    Route::get('/events/personalized', [SponsorRecommendationApiController::class, 'getPersonalizedRecommendations']);
    Route::get('/events/similar-sponsors', [SponsorRecommendationApiController::class, 'getSimilarSponsorsRecommendations']);
    
    // Recommendation Preferences
    Route::get('/preferences', [SponsorRecommendationApiController::class, 'getRecommendationPreferences']);
    Route::put('/preferences', [SponsorRecommendationApiController::class, 'updateRecommendationPreferences']);
    
    // Recommendation Feedback
    Route::post('/feedback', [SponsorRecommendationApiController::class, 'submitFeedback']);
    Route::get('/feedback/history', [SponsorRecommendationApiController::class, 'getFeedbackHistory']);
    
    // Smart Matching
    Route::get('/smart-match', [SponsorRecommendationApiController::class, 'getSmartMatches']);
    Route::post('/smart-match/refresh', [SponsorRecommendationApiController::class, 'refreshSmartMatches']);
});

// ==================== NOTIFICATION MANAGEMENT API ====================
Route::prefix('notifications')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // Notification CRUD
    Route::get('/', [SponsorNotificationApiController::class, 'index']);
    Route::get('/unread', [SponsorNotificationApiController::class, 'getUnread']);
    Route::get('/{id}', [SponsorNotificationApiController::class, 'show']);
    Route::put('/{id}/mark-read', [SponsorNotificationApiController::class, 'markAsRead']);
    Route::put('/{id}/mark-unread', [SponsorNotificationApiController::class, 'markAsUnread']);
    Route::delete('/{id}', [SponsorNotificationApiController::class, 'delete']);
    
    // Bulk Notification Operations
    Route::post('/mark-all-read', [SponsorNotificationApiController::class, 'markAllAsRead']);
    Route::post('/mark-all-unread', [SponsorNotificationApiController::class, 'markAllAsUnread']);
    Route::delete('/bulk-delete', [SponsorNotificationApiController::class, 'bulkDelete']);
    
    // Notification Preferences
    Route::get('/preferences', [SponsorNotificationApiController::class, 'getPreferences']);
    Route::put('/preferences', [SponsorNotificationApiController::class, 'updatePreferences']);
    
    // Notification Types
    Route::get('/types', [SponsorNotificationApiController::class, 'getNotificationTypes']);
    Route::get('/templates', [SponsorNotificationApiController::class, 'getTemplates']);
});

// ==================== EVENT DISCOVERY API ====================
Route::prefix('events')->middleware(['auth:api', 'role:sponsor'])->group(function () {
    
    // Event Discovery
    Route::get('/discover', [SponsorApiController::class, 'discoverEvents']);
    Route::get('/featured', [SponsorApiController::class, 'getFeaturedEvents']);
    Route::get('/upcoming', [SponsorApiController::class, 'getUpcomingEvents']);
    Route::get('/recommended', [SponsorApiController::class, 'getRecommendedEvents']);
    
    // Event Details
    Route::get('/{id}', [SponsorApiController::class, 'getEventDetails']);
    Route::get('/{id}/packages', [SponsorApiController::class, 'getEventPackages']);
    Route::get('/{id}/sponsors', [SponsorApiController::class, 'getEventSponsors']);
    
    // Event Filtering
    Route::get('/filter/by-category/{categoryId}', [SponsorApiController::class, 'filterByCategory']);
    Route::get('/filter/by-location', [SponsorApiController::class, 'filterByLocation']);
    Route::get('/filter/by-budget', [SponsorApiController::class, 'filterByBudget']);
    Route::get('/filter/by-date', [SponsorApiController::class, 'filterByDate']);
    
    // Event Search
    Route::get('/search/{query}', [SponsorApiController::class, 'searchEvents']);
    Route::get('/search/advanced', [SponsorApiController::class, 'advancedSearch']);
});

// ==================== ADMIN SPONSOR MANAGEMENT API ====================
Route::prefix('admin/sponsors')->middleware(['auth:api', 'role:admin'])->group(function () {
    
    // Sponsor Management
    Route::get('/', [SponsorApiController::class, 'adminIndex']);
    Route::get('/{id}', [SponsorApiController::class, 'adminShow']);
    Route::put('/{id}', [SponsorApiController::class, 'adminUpdate']);
    Route::delete('/{id}', [SponsorApiController::class, 'adminDestroy']);
    
    // Sponsor Status Management
    Route::post('/{id}/approve', [SponsorApiController::class, 'adminApprove']);
    Route::post('/{id}/reject', [SponsorApiController::class, 'adminReject']);
    Route::post('/{id}/suspend', [SponsorApiController::class, 'adminSuspend']);
    Route::post('/{id}/activate', [SponsorApiController::class, 'adminActivate']);
    
    // Bulk Operations
    Route::post('/bulk-approve', [SponsorApiController::class, 'adminBulkApprove']);
    Route::post('/bulk-reject', [SponsorApiController::class, 'adminBulkReject']);
    Route::post('/bulk-suspend', [SponsorApiController::class, 'adminBulkSuspend']);
    
    // Admin Analytics
    Route::get('/analytics/overview', [SponsorAnalyticsApiController::class, 'adminGetOverview']);
    Route::get('/analytics/sponsor-performance', [SponsorAnalyticsApiController::class, 'adminGetSponsorPerformance']);
    Route::get('/analytics/financial-summary', [SponsorAnalyticsApiController::class, 'adminGetFinancialSummary']);
    
    // Admin Reports
    Route::get('/reports/sponsor-list', [SponsorApiController::class, 'adminGetSponsorList']);
    Route::get('/reports/sponsorship-summary', [SponsorApiController::class, 'adminGetSponsorshipSummary']);
    Route::get('/reports/export/sponsors', [SponsorApiController::class, 'adminExportSponsors']);
});

// ==================== PUBLIC SPONSOR API (Limited Access) ====================
Route::prefix('public/sponsors')->group(function () {
    
    // Public Sponsor Information
    Route::get('/featured', [SponsorApiController::class, 'getFeaturedSponsors']);
    Route::get('/{id}/public-profile', [SponsorApiController::class, 'getPublicProfile']);
    Route::get('/search', [SponsorApiController::class, 'publicSearch']);
    
    // Public Event Information
    Route::get('/events/upcoming', [SponsorApiController::class, 'getPublicUpcomingEvents']);
    Route::get('/events/{id}/public', [SponsorApiController::class, 'getPublicEventDetails']);
});

// ==================== WEBHOOKS & INTEGRATIONS ====================
Route::prefix('webhooks')->group(function () {
    
    // Payment Webhooks
    Route::post('/payment/success', [SponsorshipApiController::class, 'handlePaymentSuccess']);
    Route::post('/payment/failed', [SponsorshipApiController::class, 'handlePaymentFailed']);
    
    // External Service Webhooks
    Route::post('/external/event-update', [SponsorApiController::class, 'handleExternalEventUpdate']);
    Route::post('/external/sponsor-update', [SponsorApiController::class, 'handleExternalSponsorUpdate']);
});

// ==================== API DOCUMENTATION & HEALTH ====================
Route::prefix('api')->group(function () {
    
    // API Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'version' => '1.0.0'
        ]);
    });
    
    // API Documentation
    Route::get('/docs', function () {
        return response()->json([
            'title' => 'Echofy Sponsor Management API',
            'version' => '1.0.0',
            'description' => 'API complète pour la gestion des sponsors',
            'endpoints' => [
                'auth' => 'Authentification des sponsors',
                'sponsors' => 'Gestion des profils sponsors',
                'sponsorships' => 'Gestion des sponsoring',
                'contracts' => 'Gestion des contrats',
                'analytics' => 'Analytics et rapports',
                'recommendations' => 'Recommandations IA',
                'notifications' => 'Gestion des notifications',
                'events' => 'Découverte d\'événements',
                'admin' => 'Gestion administrative'
            ]
        ]);
    });
});
