<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Item;

class ImportBwiItems extends Command
{
    protected $signature = 'import:bwi {--dry-run}';
    protected $description = 'Import BWI (Gontor 6) items from SLiMS data';

    public function handle()
    {
        $branchId = 20;
        $dryRun = $this->option('dry-run');

        // Load data
        require base_path('scripts/bwi_data_clean.php');

        $this->info("Biblios: " . count($biblioMap));
        $this->info("Items to import: " . count($slimsItems));

        // Get books
        $books = Book::where('branch_id', $branchId)->get(['id', 'title']);
        $this->info("Books in branch: " . $books->count());

        // Create title mapping
        $bookByTitle = [];
        foreach ($books as $book) {
            $bookByTitle[mb_strtolower(trim($book->title))] = $book->id;
        }

        // Map biblio_id to book_id
        $biblioToBook = [];
        foreach ($biblioMap as $biblioId => $title) {
            $key = mb_strtolower(trim($title));
            if (isset($bookByTitle[$key])) {
                $biblioToBook[$biblioId] = $bookByTitle[$key];
            }
        }
        $this->info("Mapped: " . count($biblioToBook));

        // Get/create location
        $locationId = DB::table('locations')->where('branch_id', $branchId)->value('id');
        if (!$locationId) {
            $locationId = DB::table('locations')->insertGetId([
                'branch_id' => $branchId,
                'name' => 'Rak Utama',
                'code' => 'G6-MAIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $statusId = DB::table('item_statuses')->where('name', 'Available')->value('id') ?? 1;

        // Import
        $imported = 0;
        $skipped = 0;

        foreach ($slimsItems as $item) {
            $biblioId = $item['biblio_id'];
            $barcode = $item['barcode'];

            if (!isset($biblioToBook[$biblioId])) {
                $skipped++;
                continue;
            }

            if (Item::where('barcode', $barcode)->exists()) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                Item::create([
                    'book_id' => $biblioToBook[$biblioId],
                    'branch_id' => $branchId,
                    'barcode' => $barcode,
                    'call_number' => $item['call_number'] ?: null,
                    'collection_type_id' => 1,
                    'location_id' => $locationId,
                    'item_status_id' => $statusId,
                    'received_date' => '2024-11-01',
                ]);
            }
            $imported++;
        }

        $this->info("Imported: $imported");
        $this->info("Skipped: $skipped");

        $total = Item::where('branch_id', $branchId)->count();
        $this->info("Total items in branch: $total");

        return 0;
    }
}
