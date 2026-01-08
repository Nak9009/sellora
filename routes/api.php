// routes/api.php
<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController, UserController, CategoryController,
    AdController, FavoriteController, ChatController,
    PaymentController, ReportController
};

Route::middleware('api')->group(function () {
    // Public Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Public Ads
    Route::get('/ads', [AdController::class, 'index']);
    Route::get('/ads/{ad:slug}', [AdController::class, 'show']);
    Route::get('/ads/search', [AdController::class, 'search']);

    // Public Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);

    // Public CMS
    Route::get('/pages/{page:slug}', [\App\Http\Controllers\Api\CmsController::class, 'show']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // User Profile
        Route::get('/user/profile', [UserController::class, 'profile']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);
        Route::put('/user/password', [UserController::class, 'changePassword']);
        Route::get('/user/ads', [UserController::class, 'myAds']);
        Route::get('/user/stats', [UserController::class, 'stats']);

        // Ads Management
        Route::post('/ads', [AdController::class, 'store']);
        Route::put('/ads/{ad}', [AdController::class, 'update']);
        Route::delete('/ads/{ad}', [AdController::class, 'destroy']);
        Route::post('/ads/{ad}/images', [AdController::class, 'uploadImages']);
        Route::delete('/ads/{ad}/images/{image}', [AdController::class, 'deleteImage']);

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{ad}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{ad}', [FavoriteController::class, 'destroy']);
        Route::get('/favorites/check/{ad}', [FavoriteController::class, 'check']);

        // Chats
        Route::get('/chats', [ChatController::class, 'index']);
        Route::get('/chats/{chat}', [ChatController::class, 'show']);
        Route::post('/chats/{ad}/start', [ChatController::class, 'startChat']);
        Route::post('/chats/{chat}/messages', [ChatController::class, 'sendMessage']);
        Route::get('/chats/{chat}/messages', [ChatController::class, 'messages']);
        Route::put('/chats/{chat}/messages/{message}/read', [ChatController::class, 'markAsRead']);
        Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

        // Reports
        Route::post('/reports', [ReportController::class, 'store']);
        Route::get('/reports/my', [ReportController::class, 'myReports']);

        // Payments & Subscriptions
        Route::post('/payments', [PaymentController::class, 'initiatePayment']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);
        Route::get('/subscriptions', [PaymentController::class, 'subscriptions']);
        Route::post('/subscriptions/{plan}/subscribe', [PaymentController::class, 'subscribe']);
        Route::post('/subscriptions/{subscription}/cancel', [PaymentController::class, 'cancelSubscription']);

        // Plans (public but listed here)
        Route::get('/plans', [PaymentController::class, 'plans']);
    });
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index']);

    // Users Management
    Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::put('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block']);
    Route::put('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock']);

    // Ads Management
    Route::apiResource('ads', \App\Http\Controllers\Admin\AdController::class)->only(['index', 'show']);
    Route::put('/ads/{ad}/approve', [\App\Http\Controllers\Admin\AdController::class, 'approve']);
    Route::put('/ads/{ad}/reject', [\App\Http\Controllers\Admin\AdController::class, 'reject']);
    Route::delete('/ads/{ad}', [\App\Http\Controllers\Admin\AdController::class, 'destroy']);

    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class);

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index']);
    Route::put('/reports/{report}/resolve', [\App\Http\Controllers\Admin\ReportController::class, 'resolve']);

    // Plans
    Route::apiResource('plans', \App\Http\Controllers\Admin\PlanController::class);

    // Payments
    Route::get('/payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show']);

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index']);
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update']);

    // CMS Pages
    Route::apiResource('pages', \App\Http\Controllers\Admin\CmsPageController::class);
});
