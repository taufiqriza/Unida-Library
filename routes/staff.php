<?php

use App\Http\Controllers\Staff\StaffDashboardController;
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
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard.index');

        // Profile & Settings
        Route::get('/profile', fn() => view('staff.profile.index'))->name('profile');
    });
