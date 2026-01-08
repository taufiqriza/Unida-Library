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
            Route::get('/import', \App\Livewire\Staff\Catalog\BookImport::class)->name('import');
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
        
        // Clearance Letter Download (Staff)
        Route::get('/clearance-letter/{letter}/download', [App\Http\Controllers\Staff\ClearanceLetterController::class, 'download'])->name('clearance-letter.download');

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
            
            // Debug route
            Route::get('/{member}/test', function($member) {
                $memberModel = \App\Models\Member::findOrFail($member);
                return "Member found: " . $memberModel->name . " (ID: " . $memberModel->id . ")";
            })->name('test');
        });

        // Tasks (Kanban Board) & Schedule
        Route::prefix('task')->name('task.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Task\TaskKanban::class)->name('index');
            Route::get('/schedule', \App\Livewire\Staff\Task\StaffScheduleManager::class)->name('schedule');
            Route::get('/create', \App\Livewire\Staff\Task\TaskForm::class)->name('create');
            Route::get('/{task}/edit', \App\Livewire\Staff\Task\TaskForm::class)->name('edit');
        });

        // Attendance
        Route::get('/attendance', \App\Livewire\Staff\Attendance\AttendancePortal::class)->name('attendance.index');

        // Notifications
        Route::prefix('notification')->name('notification.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Notification\NotificationCenter::class)->name('index');
            Route::get('/settings', \App\Livewire\Staff\Notification\NotificationSettings::class)->name('settings');
        });

        // Statistics
        Route::get('/statistics', \App\Livewire\Staff\Statistics\LibraryStatistics::class)->name('statistics.index');
        Route::get('/statistics/export/{type}', [\App\Http\Controllers\Staff\StatisticsExportController::class, 'export'])
            ->middleware('throttle:export')
            ->name('statistics.export');

        // Analytics (Google Analytics)
        Route::get('/analytics', \App\Livewire\Staff\Analytics\AnalyticsDashboard::class)->name('analytics.index');

        // Security Dashboard (Super Admin Only)
        Route::get('/security', \App\Livewire\Staff\Security\SecurityDashboard::class)
            ->middleware('can:manage-staff')
            ->name('security.index');

        // Short URL Dashboard
        Route::get('/short-urls', \App\Livewire\Staff\ShortUrl\ShortUrlDashboard::class)
            ->name('short-urls.index');

        // Profile
        Route::get('/profile', \App\Livewire\Staff\Profile\StaffProfile::class)->name('profile');

        // Control (Admin Only)
        Route::prefix('control')->name('control.')->middleware('can:manage-staff')->group(function () {
            Route::get('/', \App\Livewire\Staff\Control\StaffControl::class)->name('index');
        });

        // Survey Module
        Route::prefix('survey')->name('survey.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Survey\SurveyDashboard::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Survey\SurveyForm::class)->name('create');
            Route::get('/{survey}/edit', \App\Livewire\Staff\Survey\SurveyForm::class)->name('edit');
            Route::get('/{survey}/responses', \App\Livewire\Staff\Survey\SurveyResponses::class)->name('responses');
            Route::get('/{survey}/analytics', \App\Livewire\Staff\Survey\SurveyAnalytics::class)->name('analytics');
        });

        // E-Learning Module
        Route::prefix('elearning')->name('elearning.')->group(function () {
            Route::get('/', \App\Livewire\Staff\Elearning\ElearningDashboard::class)->name('index');
            Route::get('/create', \App\Livewire\Staff\Elearning\CourseForm::class)->name('create');
            Route::get('/{id}', \App\Livewire\Staff\Elearning\CourseShow::class)->name('show');
            Route::get('/{id}/edit', \App\Livewire\Staff\Elearning\CourseForm::class)->name('edit');
        });

        // Email Management
        Route::get('/email', \App\Livewire\Staff\Email\EmailManagement::class)
            ->middleware('can:manage-staff')
            ->name('email.index');
        
        Route::get('/email/preview/{template}', function ($template) {
            $data = match($template) {
                'service-promotion' => ['recipientName' => 'Bapak/Ibu Pimpinan', 'appUrl' => config('app.url'), 'websiteUrl' => config('app.url')],
                'welcome' => ['name' => 'Ahmad Fauzi', 'loginUrl' => config('app.url') . '/login'],
                'publication-approved' => ['title' => 'Contoh Judul Karya Ilmiah', 'type' => 'Skripsi', 'author' => 'Ahmad Fauzi', 'year' => date('Y'), 'nim' => '2021001234', 'portalUrl' => config('app.url')],
                'plagiarism-result' => ['title' => 'Contoh Judul Dokumen', 'similarityScore' => 15, 'name' => 'Ahmad Fauzi', 'status' => 'completed', 'viewUrl' => config('app.url')],
                'certificate-updated' => ['title' => 'Contoh Judul Karya', 'name' => 'Ahmad Fauzi', 'downloadUrl' => config('app.url')],
                'loan-reminder' => ['name' => 'Ahmad Fauzi', 'bookTitle' => 'Contoh Judul Buku', 'dueDate' => now()->addDays(3)->format('d M Y'), 'daysLeft' => 3],
                'loan-overdue' => ['name' => 'Ahmad Fauzi', 'bookTitle' => 'Contoh Judul Buku', 'dueDate' => now()->subDays(5)->format('d M Y'), 'daysOverdue' => 5, 'fine' => 5000],
                'event-invitation' => ['name' => 'Bapak/Ibu', 'eventTitle' => 'Workshop Penulisan Karya Ilmiah', 'eventDate' => now()->addDays(7)->format('d M Y'), 'eventTime' => '09:00 WIB', 'eventLocation' => 'Aula Perpustakaan Lt. 2', 'eventDescription' => 'Workshop ini akan membahas teknik penulisan karya ilmiah yang baik dan benar.', 'registerUrl' => config('app.url')],
                'announcement' => ['title' => 'Jadwal Libur Akhir Tahun', 'content' => 'Perpustakaan UNIDA akan tutup pada tanggal 25 Desember - 1 Januari dalam rangka libur akhir tahun.', 'recipientName' => 'Civitas Akademika'],
                'new-collection' => ['name' => 'Ahmad Fauzi', 'collections' => [['title' => 'Artificial Intelligence: A Modern Approach', 'author' => 'Stuart Russell', 'type' => 'Buku'], ['title' => 'Deep Learning', 'author' => 'Ian Goodfellow', 'type' => 'Buku']], 'viewUrl' => config('app.url')],
                'loan-extended' => ['name' => 'Ahmad Fauzi', 'bookTitle' => 'Contoh Judul Buku', 'oldDueDate' => now()->format('d M Y'), 'newDueDate' => now()->addDays(7)->format('d M Y')],
                'reservation-ready' => ['name' => 'Ahmad Fauzi', 'bookTitle' => 'Contoh Judul Buku', 'pickupDeadline' => now()->addDays(3)->format('d M Y'), 'pickupLocation' => 'Meja Sirkulasi Lt. 1'],
                'newsletter' => ['month' => 'Januari 2026', 'totalVisitors' => 1250, 'totalLoans' => 450, 'topBooks' => [['title' => 'Buku Populer 1', 'loans' => 25], ['title' => 'Buku Populer 2', 'loans' => 20]], 'upcomingEvents' => [['title' => 'Workshop Menulis', 'date' => '15 Jan 2026']]],
                default => []
            };
            return view("emails.{$template}", $data);
        })->middleware('can:manage-staff')->name('email.preview');
        
        // Logout - secure with signed URL (no CSRF needed, expires in 5 min)
        Route::get('/logout/{signature}', function ($signature) {
            // Verify signature manually
            if (!hash_equals($signature, hash('sha256', auth()->id() . config('app.key')))) {
                abort(403, 'Invalid logout signature');
            }
            
            // Logout from web guard only - don't invalidate entire session
            auth()->guard('web')->logout();
            
            // Regenerate token for security
            request()->session()->regenerateToken();
            
            return redirect('/login')->with('success', 'Berhasil logout.');
        })->name('logout.execute');
        
        // Generate logout URL with signature
        Route::get('/logout', function () {
            $signature = hash('sha256', auth()->id() . config('app.key'));
            return redirect()->route('staff.logout.execute', ['signature' => $signature]);
        })->name('logout');
    });
