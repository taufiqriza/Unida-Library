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

        $this->info("Items to import: " . count($slimsItems));

        // Get existing book IDs for this branch
        $existingBookIds = Book::where('branch_id', $branchId)->pluck('id')->toArray();
        $this->info("Books in branch: " . count($existingBookIds));

        // Book ID pattern: book_id = 2000000 + slims_biblio_id
        // This was used during initial migration
        $idOffset = 2000000;

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
            $barcode = $item['barcode']; // Use original barcode - same barcode allowed across branches
            
            // Calculate book_id from biblio_id
            $bookId = $idOffset + $biblioId;

            // Check if book exists
            if (!in_array($bookId, $existingBookIds)) {
                $skipped++;
                continue;
            }

            // Check if this exact item already exists in THIS branch
            if (Item::where('barcode', $barcode)->where('branch_id', $branchId)->exists()) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                Item::create([
                    'book_id' => $bookId,
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
