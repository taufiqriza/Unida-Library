<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSlims extends Command
{
    protected $signature = 'slims:import {source : pusat or unput} {--step= : Specific step}';
    protected $description = 'Import data from SLiMS temporary database';

    protected $sourceDb;
    protected $branchId;

    public function handle()
    {
        $source = $this->argument('source');
        
        if ($source === 'pusat') {
            $this->sourceDb = 'slims_temp_pusat';
            $this->branchId = 1;
        } elseif ($source === 'unput') {
            $this->sourceDb = 'slims_temp_unput';
            $this->branchId = 2;
        } else {
            $this->error('Invalid source. Use: pusat or unput');
            return 1;
        }

        $this->info("Importing from {$this->sourceDb} to branch_id={$this->branchId}");

        $step = $this->option('step');
        
        if ($step) {
            $this->runStep($step);
        } else {
            $this->runAllSteps();
        }

        return 0;
    }

    protected function runAllSteps()
    {
        $steps = ['publishers', 'authors', 'subjects', 'media_types', 'collection_types', 'locations', 'books', 'book_authors', 'book_subjects', 'items'];
        
        foreach ($steps as $step) {
            $this->runStep($step);
        }
        
        $this->newLine();
        $this->info('✅ Import completed for ' . $this->argument('source'));
    }

    protected function runStep($step)
    {
        $method = 'import' . str_replace('_', '', ucwords($step, '_'));
        
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->error("Unknown step: {$step}");
        }
    }

    protected function importPublishers()
    {
        $this->info('Importing publishers...');
        
        $count = DB::statement("
            INSERT INTO perpustakaan.publishers (id, name, created_at, updated_at)
            SELECT publisher_id, publisher_name, COALESCE(input_date, NOW()), COALESCE(last_update, NOW())
            FROM {$this->sourceDb}.mst_publisher
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");
        
        $total = DB::table('publishers')->count();
        $this->line("  → Publishers: {$total}");
    }

    protected function importAuthors()
    {
        $this->info('Importing authors...');
        
        DB::statement("
            INSERT INTO perpustakaan.authors (id, name, type, created_at, updated_at)
            SELECT 
                author_id,
                author_name,
                CASE authority_type 
                    WHEN 'p' THEN 'personal'
                    WHEN 'o' THEN 'organizational'
                    WHEN 'c' THEN 'conference'
                    ELSE 'personal'
                END,
                COALESCE(input_date, NOW()),
                COALESCE(last_update, NOW())
            FROM {$this->sourceDb}.mst_author
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");
        
        $total = DB::table('authors')->count();
        $this->line("  → Authors: {$total}");
    }

    protected function importSubjects()
    {
        $this->info('Importing subjects...');
        
        DB::statement("
            INSERT INTO perpustakaan.subjects (id, name, classification, created_at, updated_at)
            SELECT topic_id, topic, classification, COALESCE(input_date, NOW()), COALESCE(last_update, NOW())
            FROM {$this->sourceDb}.mst_topic
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");
        
        $total = DB::table('subjects')->count();
        $this->line("  → Subjects: {$total}");
    }

    protected function importMediaTypes()
    {
        $this->info('Importing media types (GMD)...');
        
        DB::statement("
            INSERT INTO perpustakaan.media_types (id, name, code, created_at, updated_at)
            SELECT gmd_id, gmd_name, gmd_code, COALESCE(input_date, NOW()), COALESCE(last_update, NOW())
            FROM {$this->sourceDb}.mst_gmd
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");
        
        $total = DB::table('media_types')->count();
        $this->line("  → Media Types: {$total}");
    }

    protected function importCollectionTypes()
    {
        $this->info('Importing collection types...');
        
        DB::statement("
            INSERT INTO perpustakaan.collection_types (id, name, code, created_at, updated_at)
            SELECT coll_type_id, coll_type_name, NULL, COALESCE(input_date, NOW()), COALESCE(last_update, NOW())
            FROM {$this->sourceDb}.mst_coll_type
            ON DUPLICATE KEY UPDATE name = VALUES(name)
        ");
        
        $total = DB::table('collection_types')->count();
        $this->line("  → Collection Types: {$total}");
    }

    protected function importLocations()
    {
        $this->info('Importing locations...');
        
        $locations = DB::connection('mysql')
            ->select("SELECT location_id, location_name, input_date, last_update FROM {$this->sourceDb}.mst_location");
        
        foreach ($locations as $loc) {
            DB::table('locations')->updateOrInsert(
                ['code' => $loc->location_id, 'branch_id' => $this->branchId],
                [
                    'name' => $loc->location_name,
                    'created_at' => $loc->input_date ?? now(),
                    'updated_at' => $loc->last_update ?? now(),
                ]
            );
        }
        
        $total = DB::table('locations')->where('branch_id', $this->branchId)->count();
        $this->line("  → Locations: {$total}");
    }

    protected function importBooks()
    {
        $this->info('Importing books (biblio)...');
        
        DB::statement("
            INSERT INTO perpustakaan.books (
                id, branch_id, title, isbn, publisher_id, publish_year, publish_place,
                edition, collation, series_title, call_number, notes, image,
                media_type_id, language, abstract, is_opac_visible, created_at, updated_at
            )
            SELECT 
                b.biblio_id + ({$this->branchId} * 100000),
                {$this->branchId},
                b.title,
                NULLIF(b.isbn_issn, ''),
                CASE WHEN b.publisher_id IN (SELECT id FROM perpustakaan.publishers) THEN b.publisher_id ELSE NULL END,
                CASE 
                    WHEN b.publish_year REGEXP '^[0-9]{4}' THEN LEFT(b.publish_year, 4)
                    ELSE NULL 
                END,
                p.place_name,
                NULLIF(b.edition, ''),
                NULLIF(b.collation, ''),
                NULLIF(b.series_title, ''),
                NULLIF(b.call_number, ''),
                b.notes,
                NULLIF(b.image, ''),
                CASE WHEN b.gmd_id IN (SELECT id FROM perpustakaan.media_types) THEN b.gmd_id ELSE NULL END,
                COALESCE(NULLIF(b.language_id, ''), 'id'),
                b.spec_detail_info,
                IF(b.opac_hide = 1, 0, 1),
                COALESCE(b.input_date, NOW()),
                COALESCE(b.last_update, NOW())
            FROM {$this->sourceDb}.biblio b
            LEFT JOIN {$this->sourceDb}.mst_place p ON b.publish_place_id = p.place_id
            ON DUPLICATE KEY UPDATE title = VALUES(title)
        ");
        
        $total = DB::table('books')->where('branch_id', $this->branchId)->count();
        $this->line("  → Books: {$total}");
    }

    protected function importBookAuthors()
    {
        $this->info('Importing book-author relations...');
        
        DB::statement("
            INSERT IGNORE INTO perpustakaan.book_author (book_id, author_id, level)
            SELECT 
                biblio_id + ({$this->branchId} * 100000),
                author_id,
                level
            FROM {$this->sourceDb}.biblio_author
            WHERE author_id IN (SELECT id FROM perpustakaan.authors)
        ");
        
        $total = DB::table('book_author')
            ->whereRaw('book_id > ? AND book_id < ?', [$this->branchId * 100000, ($this->branchId + 1) * 100000])
            ->count();
        $this->line("  → Book-Author relations: {$total}");
    }

    protected function importBookSubjects()
    {
        $this->info('Importing book-subject relations...');
        
        DB::statement("
            INSERT IGNORE INTO perpustakaan.book_subject (book_id, subject_id)
            SELECT 
                biblio_id + ({$this->branchId} * 100000),
                topic_id
            FROM {$this->sourceDb}.biblio_topic
            WHERE topic_id IN (SELECT id FROM perpustakaan.subjects)
        ");
        
        $total = DB::table('book_subject')
            ->whereRaw('book_id > ? AND book_id < ?', [$this->branchId * 100000, ($this->branchId + 1) * 100000])
            ->count();
        $this->line("  → Book-Subject relations: {$total}");
    }

    protected function importItems()
    {
        $this->info('Importing items (eksemplar)...');
        
        // Disable strict mode for this operation
        DB::statement("SET SESSION sql_mode = ''");
        
        DB::statement("
            INSERT INTO perpustakaan.items (
                id, book_id, branch_id, barcode, call_number, collection_type_id, location_id,
                inventory_code, received_date, price, created_at, updated_at
            )
            SELECT 
                i.item_id + ({$this->branchId} * 100000),
                i.biblio_id + ({$this->branchId} * 100000),
                {$this->branchId},
                i.item_code,
                NULLIF(i.call_number, ''),
                CASE WHEN i.coll_type_id IN (SELECT id FROM perpustakaan.collection_types) THEN i.coll_type_id ELSE NULL END,
                (SELECT id FROM perpustakaan.locations WHERE code = i.location_id AND branch_id = {$this->branchId} LIMIT 1),
                NULLIF(i.inventory_code, ''),
                i.received_date,
                i.price,
                COALESCE(i.input_date, NOW()),
                COALESCE(i.last_update, NOW())
            FROM {$this->sourceDb}.item i
            WHERE i.biblio_id IN (SELECT biblio_id FROM {$this->sourceDb}.biblio)
            ON DUPLICATE KEY UPDATE barcode = VALUES(barcode)
        ");
        
        $total = DB::table('items')->where('branch_id', $this->branchId)->count();
        $this->line("  → Items: {$total}");
    }
}
