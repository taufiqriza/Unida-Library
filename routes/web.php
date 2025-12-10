<?php

use App\Http\Controllers\Api\DdcController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\OpacController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ThesisFileController;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// OPAC Routes
Route::get('/', [OpacController::class, 'home'])->name('opac.home');
Route::get('/search', fn() => view('opac.search'))->name('opac.search');
Route::get('/catalog/{id}', [OpacController::class, 'catalogShow'])->name('opac.catalog.show');
Route::get('/ebook/{id}', [OpacController::class, 'ebookShow'])->name('opac.ebook.show');
Route::get('/ethesis/{id}', [OpacController::class, 'ethesisShow'])->name('opac.ethesis.show');
Route::get('/news/{slug}', [OpacController::class, 'newsShow'])->name('opac.news.show');
Route::get('/page/{slug}', [OpacController::class, 'page'])->name('opac.page');

// Panduan Pages
Route::get('/panduan/cek-plagiasi', fn() => view('opac.pages.cek-plagiasi'))->name('opac.panduan.plagiarism');
Route::get('/panduan/unggah-tugas-akhir', fn() => view('opac.pages.unggah-tugas-akhir'))->name('opac.panduan.thesis');


// Member Auth - with rate limiting
Route::match(['get', 'post'], '/login', [MemberAuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login');
Route::match(['get', 'post'], '/register', [MemberAuthController::class, 'register'])
    ->middleware('throttle:login')
    ->name('opac.register');
Route::get('/logout', [MemberAuthController::class, 'logout'])->name('opac.logout');

// Google OAuth
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback']);

// Complete Profile (for OAuth users)
Route::middleware('auth:member')->group(function () {
    Route::get('/member/complete-profile', [App\Http\Controllers\Auth\CompleteProfileController::class, 'show'])->name('member.complete-profile');
    Route::post('/member/complete-profile', [App\Http\Controllers\Auth\CompleteProfileController::class, 'update']);
});

// Member Area (Protected)
Route::middleware('auth:member')->prefix('member')->name('opac.member.')->group(function () {
    Route::get('/', [MemberAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/submissions', fn() => view('opac.member.submissions'))->name('submissions');
    Route::get('/submit-thesis', fn() => view('opac.member.submit-thesis'))->name('submit-thesis');
    Route::get('/submit-thesis/{submissionId}', fn($id) => view('opac.member.submit-thesis', ['submissionId' => $id]))->name('edit-submission');
    
    // Plagiarism Check
    Route::prefix('plagiarism')->name('plagiarism.')->group(function () {
        Route::get('/', [App\Http\Controllers\Opac\PlagiarismController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Opac\PlagiarismController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Opac\PlagiarismController::class, 'store'])->name('store');
        Route::get('/{check}', [App\Http\Controllers\Opac\PlagiarismController::class, 'show'])->name('show');
        Route::get('/{check}/status', [App\Http\Controllers\Opac\PlagiarismController::class, 'status'])->name('status');
        Route::get('/{check}/certificate', [App\Http\Controllers\Opac\PlagiarismController::class, 'certificate'])->name('certificate');
        Route::get('/{check}/certificate/download', [App\Http\Controllers\Opac\PlagiarismController::class, 'downloadCertificate'])->name('certificate.download');
    });
});

// Public Plagiarism Certificate Verification
Route::get('/verify/{certificate}', [App\Http\Controllers\Opac\PlagiarismController::class, 'verify'])->name('plagiarism.verify');

// Alias for member.dashboard
Route::get('/member/dashboard', [MemberAuthController::class, 'dashboard'])->middleware('auth:member')->name('member.dashboard');

// E-Thesis detail
Route::get('/ethesis/{id}', [OpacController::class, 'ethesisShow'])->name('opac.ethesis.show');

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
