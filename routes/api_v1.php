<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\EbookController;
use App\Http\Controllers\Api\V1\EthesisController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\PlagiarismController;
use App\Http\Controllers\Api\V1\SubmissionController;
use App\Http\Controllers\Api\V1\ClearanceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\GeneralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes - Mobile App
|--------------------------------------------------------------------------
*/

// Public routes
Route::middleware('throttle:api')->group(function () {
    // General
    Route::get('/', [GeneralController::class, 'index']);
    Route::get('/branches', [GeneralController::class, 'branches']);
    Route::get('/faculties', [GeneralController::class, 'faculties']);
    Route::get('/departments', [GeneralController::class, 'departments']);
    Route::get('/settings', [GeneralController::class, 'settings']);
    Route::get('/news', [GeneralController::class, 'news']);
    Route::get('/news/{slug}', [GeneralController::class, 'newsShow']);

    // Catalog
    Route::get('/catalog', [CatalogController::class, 'index']);
    Route::get('/catalog/filters', [CatalogController::class, 'filters']);
    Route::get('/catalog/popular', [CatalogController::class, 'popular']);
    Route::get('/catalog/new', [CatalogController::class, 'newArrivals']);
    Route::get('/catalog/isbn/{isbn}', [CatalogController::class, 'findByIsbn']);
    Route::get('/catalog/{id}', [CatalogController::class, 'show']);

    // E-Books (public)
    Route::get('/ebooks', [EbookController::class, 'index']);
    Route::get('/ebooks/{id}', [EbookController::class, 'show']);

    // E-Thesis (public)
    Route::get('/etheses', [EthesisController::class, 'index']);
    Route::get('/etheses/{id}', [EthesisController::class, 'show']);

    // Auth
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes (requires auth)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/profile/photo', [AuthController::class, 'updatePhoto']);
    Route::post('/auth/fcm-token', [AuthController::class, 'registerFcmToken']);
    Route::delete('/auth/fcm-token', [AuthController::class, 'removeFcmToken']);

    // Loans
    Route::get('/loans', [LoanController::class, 'active']);
    Route::get('/loans/history', [LoanController::class, 'history']);
    Route::get('/loans/{id}', [LoanController::class, 'show']);

    // Fines
    Route::get('/fines', [LoanController::class, 'fines']);
    Route::get('/fines/summary', [LoanController::class, 'finesSummary']);

    // Plagiarism
    Route::get('/plagiarism', [PlagiarismController::class, 'index']);
    Route::post('/plagiarism', [PlagiarismController::class, 'store']);
    Route::get('/plagiarism/{id}', [PlagiarismController::class, 'show']);
    Route::get('/plagiarism/{id}/certificate', [PlagiarismController::class, 'certificate']);
    Route::post('/plagiarism/external', [PlagiarismController::class, 'storeExternal']);

    // Thesis Submissions
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::get('/submissions/{id}', [SubmissionController::class, 'show']);
    Route::put('/submissions/{id}', [SubmissionController::class, 'update']);
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy']);

    // Clearance Letters
    Route::get('/clearance', [ClearanceController::class, 'index']);
    Route::get('/clearance/check', [ClearanceController::class, 'checkEligibility']);
    Route::get('/clearance/{id}', [ClearanceController::class, 'show']);
    Route::get('/clearance/{id}/download', [ClearanceController::class, 'download']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
