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
            // Livewire Components for List, Create, Edit
            Route::get('/', \App\Livewire\Staff\Biblio\BiblioList::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Biblio\BiblioForm::class)->name('create');
            Route::get('/{book}/edit', \App\Livewire\Staff\Biblio\BiblioForm::class)->name('edit');
            
            // Keep Show for read-only view if needed, or replace later
            Route::get('/{book}', [BiblioController::class, 'show'])->name('show');
            Route::post('/{book}/items', [BiblioController::class, 'addItems'])->name('add-items');
        });

        // Circulation
        Route::prefix('circulation')->name('circulation.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Circulation\CirculationTransaction::class)->name('index');
        });

        // Members
        Route::prefix('member')->name('member.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Member\MemberList::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Member\MemberForm::class)->name('create');
            Route::get('/{member}', \App\Livewire\Staff\Member\MemberShow::class)->name('show');
            Route::get('/{member}/edit', \App\Livewire\Staff\Member\MemberForm::class)->name('edit');
        });

        // Tasks (Kanban Board)
        Route::prefix('task')->name('task.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Task\TaskKanban::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Task\TaskForm::class)->name('create');
            Route::get('/{task}/edit', \App\Livewire\Staff\Task\TaskForm::class)->name('edit');
        });

        // Profile
        Route::get('/profile', fn() => view('staff.profile.index'))->name('profile');
    });
