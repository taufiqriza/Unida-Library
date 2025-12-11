<?php

use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\BiblioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Staff Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', \App\Http\Middleware\EnsureStaffAccess::class])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        
        // Dashboard
        Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');

        // Bibliography
        Route::prefix('biblio')->name('biblio.')->group(function () {
            Route::get('/', [BiblioController::class, 'index'])->name('index');
            Route::get('/create', [BiblioController::class, 'create'])->name('create');
            Route::post('/', [BiblioController::class, 'store'])->name('store');
            Route::get('/{book}', [BiblioController::class, 'show'])->name('show');
            Route::get('/{book}/edit', [BiblioController::class, 'edit'])->name('edit');
            Route::put('/{book}', [BiblioController::class, 'update'])->name('update');
            Route::post('/{book}/items', [BiblioController::class, 'addItems'])->name('add-items');
        });

        // Profile
        Route::get('/profile', fn() => view('staff.profile.index'))->name('profile');
    });
