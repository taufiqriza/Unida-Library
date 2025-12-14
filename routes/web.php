<?php

use App\Http\Controllers\Api\DdcController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\OpacController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ThesisFileController;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// OPAC Routes (Livewire)
Route::get('/', \App\Livewire\Opac\OpacHome::class)->name('opac.home');
Route::get('/search', fn() => view('opac.search'))->name('opac.search');
Route::get('/catalog/{id}', \App\Livewire\Opac\CatalogShow::class)->name('opac.catalog.show');
Route::get('/ebook/{id}', \App\Livewire\Opac\EbookShow::class)->name('opac.ebook.show');
Route::get('/ethesis/{id}', \App\Livewire\Opac\EthesisShow::class)->name('opac.ethesis.show');
Route::get('/news/{slug}', \App\Livewire\Opac\NewsShow::class)->name('opac.news.show');
Route::get('/journals', \App\Livewire\Opac\Journal\JournalIndex::class)->name('opac.journals.index');
Route::get('/journals/{article}', \App\Livewire\Opac\Journal\JournalShow::class)->name('opac.journals.show');
Route::get('/external/{source}/{id}', \App\Livewire\Opac\ExternalBookShow::class)->name('opac.external.show')->where('id', '.*');
Route::get('/shamela/{id}', \App\Livewire\Opac\ShamelaShow::class)->name('opac.shamela.show')->where('id', '[0-9]+');
Route::get('/database-access', \App\Livewire\Opac\DatabaseAccess::class)->name('opac.database-access');
Route::get('/page/{slug}', [\App\Http\Controllers\OpacController::class, 'page'])->name('opac.page');

// Panduan Pages
Route::get('/panduan/cek-plagiasi', fn() => view('opac.pages.cek-plagiasi'))->name('opac.panduan.plagiarism');
Route::get('/panduan/unggah-tugas-akhir', fn() => view('opac.pages.unggah-tugas-akhir'))->name('opac.panduan.thesis');
Route::get('/panduan/member', fn() => view('opac.pages.panduan-member'))->name('opac.panduan.member');


// Auth Routes (Livewire) - with rate limiting
Route::get('/login', \App\Livewire\Opac\Auth\Login::class)
    ->middleware('throttle:login')
    ->name('login');
Route::get('/register', \App\Livewire\Opac\Auth\Register::class)
    ->middleware('throttle:login')
    ->name('opac.register');
Route::get('/verify-email', \App\Livewire\Opac\Auth\VerifyEmail::class)
    ->middleware('throttle:10,1')
    ->name('opac.verify-email');
Route::post('/register/staff', [App\Http\Controllers\Auth\StaffRegisterController::class, 'register'])
    ->middleware('throttle:login')
    ->name('opac.register.staff');
Route::get('/logout', [MemberAuthController::class, 'logout'])->name('opac.logout');

// Google OAuth (tetap controller - redirect/callback nature)
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback']);
Route::get('/auth/choose-role', [App\Http\Controllers\Auth\SocialAuthController::class, 'chooseRole'])->name('auth.choose-role');
Route::get('/auth/select-role/{role}', [App\Http\Controllers\Auth\SocialAuthController::class, 'selectRole'])->name('auth.select-role');
Route::get('/auth/switch-portal/{role}', [App\Http\Controllers\Auth\SocialAuthController::class, 'switchPortal'])->name('auth.switch-portal');

// Complete Profile (Livewire - for OAuth users)
Route::middleware('auth:member')->group(function () {
    Route::get('/member/complete-profile', \App\Livewire\Opac\Auth\CompleteProfile::class)->name('member.complete-profile');
});

// Member Area (Livewire - Protected)
Route::middleware(['auth:member', \App\Http\Middleware\EnsureMemberProfileCompleted::class])->prefix('member')->name('opac.member.')->group(function () {
    Route::get('/', \App\Livewire\Opac\Member\Dashboard::class)->name('dashboard');
    Route::get('/submissions', fn() => view('opac.member.submissions'))->name('submissions');
    Route::get('/submit-thesis', fn() => view('opac.member.submit-thesis'))->name('submit-thesis');
    Route::get('/submit-thesis/{submissionId}', fn($id) => view('opac.member.submit-thesis', ['submissionId' => $id]))->name('edit-submission');
    
    // Settings Profile (Livewire)
    Route::get('/settings', \App\Livewire\Opac\Member\Settings::class)->name('settings');
    
    // Plagiarism Check (Livewire)
    Route::prefix('plagiarism')->name('plagiarism.')->group(function () {
        Route::get('/', \App\Livewire\Opac\Plagiarism\PlagiarismIndex::class)->name('index');
        Route::get('/create', \App\Livewire\Opac\Plagiarism\PlagiarismCreate::class)->name('create');
        Route::get('/{check}', \App\Livewire\Opac\Plagiarism\PlagiarismShow::class)->name('show');
        Route::get('/{check}/status', [App\Http\Controllers\Opac\PlagiarismController::class, 'status'])->name('status'); // AJAX
        Route::get('/{check}/report', [App\Http\Controllers\Opac\PlagiarismController::class, 'viewReport'])->name('report'); // Redirect
        Route::get('/{check}/certificate', \App\Livewire\Opac\Plagiarism\PlagiarismCertificate::class)->name('certificate');
        Route::get('/{check}/certificate/download', [App\Http\Controllers\Opac\PlagiarismController::class, 'downloadCertificate'])->name('certificate.download'); // File download
    });
});

// Public Plagiarism Certificate Verification
Route::get('/verify/{certificate}', [App\Http\Controllers\Opac\PlagiarismController::class, 'verify'])->name('plagiarism.verify');

// Alias for member.dashboard (Livewire)
Route::get('/member/dashboard', \App\Livewire\Opac\Member\Dashboard::class)->middleware(['auth:member', \App\Http\Middleware\EnsureMemberProfileCompleted::class])->name('member.dashboard');

// Note: E-Thesis route is now handled by Livewire at line 16

// Thesis file access (with access control)
Route::get('/thesis-file/{submission}/{type}', [ThesisFileController::class, 'show'])->name('thesis.file');
Route::get('/thesis-file/{submission}/{type}/download', [ThesisFileController::class, 'download'])->name('thesis.file.download');

// DDC API (for admin panel DDC lookup)
Route::get('/api/ddc/search', [DdcController::class, 'search'])->name('api.ddc.search');
Route::get('/api/ddc/main-classes', [DdcController::class, 'mainClasses'])->name('api.ddc.main-classes');

// Print routes
Route::middleware('auth')->group(function () {
    // Item barcode & label
    Route::get('/print/barcode/{item}', [PrintController::class, 'barcode'])->name('print.barcode');
    Route::get('/print/barcodes', [PrintController::class, 'barcodes'])->name('print.barcodes');
    Route::get('/print/label/{item}', [PrintController::class, 'label'])->name('print.label');
    Route::get('/print/labels', [PrintController::class, 'labels'])->name('print.labels');
    
    // Member card
    Route::get('/print/member-card/{member}', [PrintController::class, 'memberCard'])->name('member.card');
    Route::get('/print/member-cards', [PrintController::class, 'memberCards'])->name('member.cards');
    
    // Stock Opname Scan
    Route::post('/stock-opname/{stockOpname}/scan', function (Request $request, $stockOpname) {
        $so = StockOpname::withoutGlobalScopes()->findOrFail($stockOpname);
        $barcode = $request->input('barcode');
        
        $opnameItem = $so->items()
            ->whereHas('item', fn ($q) => $q->withoutGlobalScopes()->where('barcode', $barcode))
            ->with('item.book')
            ->first();

        if (!$opnameItem) {
            return response()->json(['success' => false, 'message' => "Barcode '{$barcode}' tidak ada dalam daftar"]);
        }

        if ($opnameItem->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Item sudah di-scan sebelumnya']);
        }

        $opnameItem->update([
            'status' => 'found',
            'checked_by' => auth()->id(),
            'checked_at' => now(),
        ]);
        
        $so->updateCounts();
        $so->refresh();
        
        $pending = $so->total_items - $so->found_items - $so->missing_items;

        return response()->json([
            'success' => true,
            'title' => $opnameItem->item?->book?->title ?? $barcode,
            'stats' => [
                'pending' => $pending,
                'found' => $so->found_items,
                'missing' => $so->missing_items,
            ]
        ]);
    })->name('stock-opname.scan');
});
