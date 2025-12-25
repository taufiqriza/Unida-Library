<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ElibraryController;
use App\Http\Controllers\Api\EprintsController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MemberLoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes (Rate Limited: 60 req/min)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:api')->group(function () {
    // Home / Landing
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/branches', [HomeController::class, 'branches']);

    // Departments (for complete profile form)
    Route::get('/departments', function (\Illuminate\Http\Request $request) {
        return \App\Models\Department::where('faculty_id', $request->faculty_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    });

    // Catalog
    Route::get('/catalog', [CatalogController::class, 'index']);
    Route::get('/catalog/filters', [CatalogController::class, 'filters']);
    Route::get('/catalog/{id}', [CatalogController::class, 'show']);

    // E-Library
    Route::get('/ebooks', [ElibraryController::class, 'ebooks']);
    Route::get('/ebooks/{id}', [ElibraryController::class, 'ebookShow']);
    Route::get('/etheses', [ElibraryController::class, 'etheses']);
    Route::get('/etheses/{id}', [ElibraryController::class, 'ethesisShow']);

    // News
    Route::get('/news', [ElibraryController::class, 'news']);
    Route::get('/news/{slug}', [ElibraryController::class, 'newsShow']);

    // Auth (with stricter rate limit for login)
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    // EPrints Integration
    Route::post('/eprints/verify', [EprintsController::class, 'verify']);
    Route::post('/eprints/verify-token', [EprintsController::class, 'verifyLoginToken']);
});

/*
|--------------------------------------------------------------------------
| Protected API Routes (Member Auth)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Member Loans
    Route::get('/my/loans', [MemberLoanController::class, 'active']);
    Route::get('/my/loans/history', [MemberLoanController::class, 'history']);
    Route::get('/my/fines', [MemberLoanController::class, 'fines']);
});

// Member Auth (session-based for web)
Route::middleware(['web', 'auth:member'])->group(function () {
    Route::get('/eprints/login-token', [EprintsController::class, 'generateLoginToken']);
});
