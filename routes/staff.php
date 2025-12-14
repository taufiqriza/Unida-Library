<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Staff Portal Routes (100% Livewire)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', \App\Http\Middleware\EnsureStaffAccess::class])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        
        // Dashboard (Livewire)
        Route::get('/', \App\Livewire\Staff\Dashboard\StaffDashboard::class)->name('dashboard');

        // Bibliography (Livewire)
        Route::prefix('biblio')->name('biblio.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Biblio\BiblioList::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Biblio\BiblioForm::class)->name('create');
            Route::get('/{book}', \App\Livewire\Staff\Biblio\BiblioShow::class)->name('show');
            Route::get('/{id}/edit', \App\Livewire\Staff\Biblio\BiblioForm::class)->name('edit');
        });

        // Stock Opname
        Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
            Route::get('/', \App\Livewire\Staff\StockOpname\StockOpnameList::class)->name('index');
        });

        // News
        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', \App\Livewire\Staff\News\NewsList::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\News\NewsForm::class)->name('create');
            Route::get('/{id}/edit', \App\Livewire\Staff\News\NewsForm::class)->name('edit');
        });

        // E-Library
        Route::prefix('elibrary')->name('elibrary.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Elibrary\ElibraryDashboard::class)->name('index');
            Route::get('/ebook/create', \App\Livewire\Staff\Elibrary\EbookForm::class)->name('ebook.create');
            Route::get('/ebook/{id}/edit', \App\Livewire\Staff\Elibrary\EbookForm::class)->name('ebook.edit');
            Route::get('/ethesis/create', \App\Livewire\Staff\Elibrary\EthesisForm::class)->name('ethesis.create');
            Route::get('/ethesis/{id}/edit', \App\Livewire\Staff\Elibrary\EthesisForm::class)->name('ethesis.edit');
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
        Route::get('/profile', \App\Livewire\Staff\Profile\StaffProfile::class)->name('profile');

        // Control (Admin Only)
        Route::prefix('control')->name('control.')->middleware('can:manage-staff')->group(function () {
            Route::get('/', \App\Livewire\Staff\Control\StaffControl::class)->name('index');
        });
        
        // Logout
        Route::post('/logout', function () {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/login');
        })->name('logout');
    });
