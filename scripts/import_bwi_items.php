<?php
// Script to import BWI items - run via: php artisan tinker < import_bwi_items.php

$branchId = 20; // Gontor 6 UNIDA

// Biblio mapping from SLiMS SQL (biblio_id => title)
$biblioMap = [
    46 => 'Pengujian Konstitusionalitas Undang-Undang',
    47 => 'Sistem Pemilu, Partai Politik, dan Parlemen Studi tentang Sistem Pemilu dan Peran Politik dalam Pencalonan dan Recall Anggota Parlemen',
    48 => 'Aspek Hukum Lembaga Pembiayaan Modal Ventura sebagai Alternatiif Pembiayaan bagi Usaha Mikro, Kecil, dan Menengah',
    49 => 'Hukum Tata Negara Beberapa Lembaga Negara dan Sistem dalam Pemerintahan',
    50 => 'Judicial Preview Terhadap UU Ratifikasi Perjanjian Internasional',
    51 => 'Hukum Acara Perdata Teori dan Praktik',
    52 => 'Hukum Acara Pidana Suatu Pengantar',
    53 => 'Hukum Pidana Internasional',
    54 => 'Hukum Pidana Islam',
    55 => 'Pengantar Ilmu Hukum',
    56 => 'Hak Asasi Manusia dalam Perspektif Islam',
    // ... akan dilengkapi dari SQL
];

// Items from SLiMS SQL (biblio_id, item_code, call_number)
$slimsItems = [
    [56, 'B00000021', 'S 323.5 REZ H'],
    [55, 'B00000020', 'S 353.4 OLY P'],
    [55, 'B00000019', 'S 353.4 OLY P'],
    [54, 'B00000018', ''],
    [46, 'B00000001', 'S 342.598 REZ P'],
    [46, 'B00000002', 'S 342.598 REZ P'],
    [47, 'B00000003', 'S 324.6 LUT S'],
    [47, 'B00000004', 'S 324.6 LUT S'],
    [48, 'B00000005', 'S 346.07 TRI A'],
    // ... akan dilengkapi dari SQL
];

// Get books for this branch
$books = App\Models\Book::where('branch_id', $branchId)->get(['id', 'title']);
echo "Found {$books->count()} books\n";

// Create title -> book_id mapping
$bookByTitle = [];
foreach ($books as $book) {
    $bookByTitle[strtolower(trim($book->title))] = $book->id;
}

// Create biblio_id -> book_id mapping
$biblioToBook = [];
foreach ($biblioMap as $biblioId => $title) {
    $key = strtolower(trim($title));
    if (isset($bookByTitle[$key])) {
        $biblioToBook[$biblioId] = $bookByTitle[$key];
    }
}
echo "Mapped " . count($biblioToBook) . " biblio IDs to book IDs\n";

// Get default location for branch
$locationId = DB::table('locations')->where('branch_id', $branchId)->value('id');
echo "Location ID: {$locationId}\n";

// Import items
$imported = 0;
$skipped = 0;

foreach ($slimsItems as $item) {
    [$biblioId, $barcode, $callNumber] = $item;
    
    if (!isset($biblioToBook[$biblioId])) {
        $skipped++;
        continue;
    }
    
    // Check if already exists
    if (App\Models\Item::where('barcode', $barcode)->exists()) {
        $skipped++;
        continue;
    }
    
    App\Models\Item::create([
        'book_id' => $biblioToBook[$biblioId],
        'branch_id' => $branchId,
        'barcode' => $barcode,
        'call_number' => $callNumber ?: null,
        'collection_type_id' => 1,
        'location_id' => $locationId,
        'item_status_id' => 1,
        'received_date' => '2024-11-01',
    ]);
    $imported++;
}

echo "Imported: {$imported}\n";
echo "Skipped: {$skipped}\n";
