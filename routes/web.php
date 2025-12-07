<?php

use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\OpacController;
use App\Http\Controllers\PrintController;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// OPAC Routes
Route::get('/', [OpacController::class, 'home'])->name('opac.home');
Route::get('/catalog', [OpacController::class, 'catalog'])->name('opac.catalog');
Route::get('/catalog/{id}', [OpacController::class, 'catalogShow'])->name('opac.catalog.show');
Route::get('/ebooks', [OpacController::class, 'ebooks'])->name('opac.ebooks');
Route::get('/etheses', [OpacController::class, 'etheses'])->name('opac.etheses');
Route::get('/news', [OpacController::class, 'news'])->name('opac.news');
Route::get('/news/{slug}', [OpacController::class, 'newsShow'])->name('opac.news.show');

// Member Auth
Route::match(['get', 'post'], '/login', [MemberAuthController::class, 'login'])->name('opac.login');
Route::match(['get', 'post'], '/register', [MemberAuthController::class, 'register'])->name('opac.register');
Route::get('/logout', [MemberAuthController::class, 'logout'])->name('opac.logout');
Route::get('/member', [MemberAuthController::class, 'dashboard'])->middleware('auth:member')->name('opac.member.dashboard');

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
