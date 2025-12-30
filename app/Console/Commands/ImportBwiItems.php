<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Item;

class ImportBwiItems extends Command
{
    protected $signature = 'import:bwi-items {--dry-run : Show what would be imported without actually importing}';
    protected $description = 'Import items from Banyuwangi (Gontor 6) SLiMS data';

    // SLiMS item data from SQL dump
    private $slimsItems = [
        // Format: [slims_item_id, slims_biblio_id, call_number, coll_type_id, item_code, received_date, site, source]
        [249, 56, 'S 323.5 REZ H', 2, 'B00000021', '2024-11-01', 'Rak Utama', 1],
        [248, 55, 'S 353.4 OLY P', 0, 'B00000020', '2024-11-01', 'Rak Utama', 1],
        [247, 55, 'S 353.4 OLY P', 0, 'B00000019', '2024-11-01', 'Rak Utama', 1],
        [246, 54, '', 2, 'B00000018', '2024-11-01', 'Rak Utama', 1],
        [229, 46, 'S 342.598 REZ P', 2, 'B00000001', '2024-11-01', 'Ruang Utama', 1],
        [230, 46, 'S 342.598 REZ P', 2, 'B00000002', '2024-11-01', 'Ruang Utama', 1],
        [231, 47, 'S 324.6 LUT S', 0, 'B00000003', '2024-11-01', '', 1],
        [232, 47, 'S 324.6 LUT S', 0, 'B00000004', '2024-11-01', '', 1],
        [233, 48, 'S 346.07 TRI A', 0, 'B00000005', '2024-11-01', '', 1],
    ];

    public function handle()
    {
        $branchId = 20; // Gontor 6 UNIDA
        $dryRun = $this->option('dry-run');

        $this->info("Importing items for Gontor 6 UNIDA (Branch ID: {$branchId})");
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No data will be inserted");
        }

        // Get all books for this branch with their titles for matching
        $books = Book::where('branch_id', $branchId)->get(['id', 'title', 'call_number', 'isbn']);
        $this->info("Found {$books->count()} books in branch {$branchId}");

        // Read items from SQL file
        $sqlFile = base_path('docs/MIGRATION/u1571719_18-BWI.sql');
        if (!file_exists($sqlFile)) {
            $this->error("SQL file not found: {$sqlFile}");
            return 1;
        }

        // Parse biblio data to create title mapping
        $biblioMap = $this->parseBiblioData($sqlFile);
        $this->info("Parsed " . count($biblioMap) . " biblio records from SQL");

        // Parse item data
        $items = $this->parseItemData($sqlFile);
        $this->info("Parsed " . count($items) . " item records from SQL");

        // Create mapping from SLiMS biblio_id to Laravel book_id using title matching
        $bookMap = [];
        foreach ($biblioMap as $biblioId => $biblioData) {
            $title = $biblioData['title'];
            $book = $books->first(fn($b) => strtolower(trim($b->title)) === strtolower(trim($title)));
            if ($book) {
                $bookMap[$biblioId] = $book->id;
            }
        }
        $this->info("Mapped " . count($bookMap) . " biblio IDs to book IDs");

        // Import items
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($items as $item) {
            $biblioId = $item['biblio_id'];
            
            if (!isset($bookMap[$biblioId])) {
                $skipped++;
                continue;
            }

            $bookId = $bookMap[$biblioId];
            $barcode = $item['item_code'];

            // Check if item already exists
            if (Item::where('barcode', $barcode)->exists()) {
                $skipped++;
                continue;
            }

            if (!$dryRun) {
                try {
                    Item::create([
                        'book_id' => $bookId,
                        'branch_id' => $branchId,
                        'barcode' => $barcode,
                        'call_number' => $item['call_number'] ?: null,
                        'collection_type_id' => $this->mapCollectionType($item['coll_type_id']),
                        'location_id' => $this->getLocationId($branchId),
                        'item_status_id' => 1, // Available
                        'received_date' => $item['received_date'],
                        'site' => $item['site'] ?: null,
                        'source' => $this->mapSource($item['source']),
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Item {$barcode}: " . $e->getMessage();
                }
            } else {
                $imported++;
            }
        }

        $this->info("Imported: {$imported}");
        $this->info("Skipped: {$skipped}");
        
        if (count($errors) > 0) {
            $this->error("Errors:");
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }
        }

        return 0;
    }

    private function parseBiblioData(string $sqlFile): array
    {
        $content = file_get_contents($sqlFile);
        $biblioMap = [];

        // Find INSERT INTO biblio section
        if (preg_match('/INSERT INTO `biblio`.*?VALUES\s*(.*?);/s', $content, $matches)) {
            $values = $matches[1];
            // Match each row
            preg_match_all('/\((\d+),\s*\d+,\s*\'([^\']*(?:\'\'[^\']*)*)\'/', $values, $rows, PREG_SET_ORDER);
            foreach ($rows as $row) {
                $biblioId = (int)$row[1];
                $title = str_replace("''", "'", $row[2]);
                $biblioMap[$biblioId] = ['title' => $title];
            }
        }

        return $biblioMap;
    }

    private function parseItemData(string $sqlFile): array
    {
        $content = file_get_contents($sqlFile);
        $items = [];

        // Find all INSERT INTO item sections
        preg_match_all('/INSERT INTO `item`.*?VALUES\s*(.*?);/s', $content, $insertMatches);
        
        foreach ($insertMatches[1] as $values) {
            // Match each row: (item_id, biblio_id, call_number, coll_type_id, item_code, inventory_code, received_date, ...)
            preg_match_all('/\((\d+),\s*(\d+),\s*\'([^\']*)\',\s*(\d+),\s*\'([^\']*)\',\s*(?:NULL|\'[^\']*\'),\s*\'([^\']*)\',\s*\'[^\']*\',\s*\'[^\']*\',\s*\'[^\']*\',\s*\'[^\']*\',\s*\'[^\']*\',\s*\'([^\']*)\',\s*(\d+)/', $values, $rows, PREG_SET_ORDER);
            
            foreach ($rows as $row) {
                $items[] = [
                    'item_id' => (int)$row[1],
                    'biblio_id' => (int)$row[2],
                    'call_number' => $row[3],
                    'coll_type_id' => (int)$row[4],
                    'item_code' => $row[5],
                    'received_date' => $row[6],
                    'site' => $row[7],
                    'source' => (int)$row[8],
                ];
            }
        }

        return $items;
    }

    private function mapCollectionType(int $slimsTypeId): ?int
    {
        // Map SLiMS coll_type_id to Laravel collection_type_id
        // This may need adjustment based on actual data
        return $slimsTypeId > 0 ? $slimsTypeId : 1;
    }

    private function getLocationId(int $branchId): ?int
    {
        // Get default location for branch
        return DB::table('locations')->where('branch_id', $branchId)->value('id');
    }

    private function mapSource(int $source): ?string
    {
        return match($source) {
            1 => 'purchase',
            2 => 'donation',
            3 => 'grant',
            default => null,
        };
    }
}
