# Panduan Migrasi Data dari SLiMS ke Sistem Perpustakaan UNIDA

## Ringkasan

Dokumen ini menjelaskan cara migrasi data dari SLiMS (Senayan Library Management System) ke sistem perpustakaan Laravel baru. Dengan data sekitar **45.000 Item** dan **30.000 Collection (Biblio)**, migrasi harus dilakukan secara bertahap dan terstruktur.

---

## 1. Pemetaan Struktur Database

### 1.1 Tabel Master Data

| SLiMS Table | Laravel Table | Keterangan |
|-------------|---------------|------------|
| `mst_publisher` | `publishers` | Penerbit |
| `mst_author` | `authors` | Pengarang |
| `mst_topic` | `subjects` | Subjek/Topik |
| `mst_location` | `locations` | Lokasi/Rak |
| `mst_coll_type` | `collection_types` | Jenis Koleksi |
| `mst_gmd` | `media_types` | General Material Designation |
| `mst_member_type` | `member_types` | Jenis Anggota |
| `mst_language` | - | Bahasa (field di books) |
| `mst_place` | - | Tempat terbit (field di books) |

### 1.2 Tabel Utama

| SLiMS Table | Laravel Table | Keterangan |
|-------------|---------------|------------|
| `biblio` | `books` | Data bibliografi |
| `biblio_author` | `book_author` | Relasi buku-pengarang |
| `biblio_topic` | `book_subject` | Relasi buku-subjek |
| `item` | `items` | Eksemplar/kopi koleksi |
| `member` | `members` | Anggota perpustakaan |
| `loan` | `loans` | Transaksi peminjaman |
| `fines` | `fines` | Denda |
| `reserve` | `reservations` | Reservasi |

### 1.3 Pemetaan Field Biblio → Books

```
SLiMS biblio              →  Laravel books
─────────────────────────────────────────────
biblio_id                 →  id (auto)
title                     →  title
isbn_issn                 →  isbn
publisher_id              →  publisher_id
publish_year              →  publish_year
publish_place_id          →  publish_place (via mst_place)
edition                   →  edition
collation                 →  collation
series_title              →  series_title
call_number               →  call_number
notes                     →  notes
image                     →  image
gmd_id                    →  media_type_id
language_id               →  language
spec_detail_info          →  abstract
opac_hide                 →  is_opac_visible (inverted)
input_date                →  created_at
last_update               →  updated_at
```

### 1.4 Pemetaan Field Item → Items

```
SLiMS item                →  Laravel items
─────────────────────────────────────────────
item_id                   →  id (auto)
biblio_id                 →  book_id
item_code                 →  barcode
coll_type_id              →  collection_type_id
location_id               →  location_id
item_status_id            →  status (mapped)
inventory_code            →  inventory_code
received_date             →  received_date
price                     →  price
input_date                →  created_at
last_update               →  updated_at
```

### 1.5 Pemetaan Status Item

```
SLiMS item_status_id      →  Laravel status
─────────────────────────────────────────────
(null/empty)              →  'available'
'R' (Repair)              →  'repair'
'NL' (No Loan)            →  'available' (dengan flag)
'MIS' (Missing)           →  'lost'
(on loan - cek loan)      →  'on_loan'
```

---

## 2. Strategi Migrasi

### 2.1 Urutan Migrasi (PENTING!)

Migrasi harus dilakukan dalam urutan berikut karena ada foreign key constraints:

```
1. branches          (buat default branch dulu)
2. publishers        (mst_publisher)
3. authors           (mst_author)
4. subjects          (mst_topic)
5. media_types       (mst_gmd)
6. collection_types  (mst_coll_type)
7. locations         (mst_location)
8. member_types      (mst_member_type)
9. books             (biblio)
10. book_author      (biblio_author)
11. book_subject     (biblio_topic)
12. items            (item)
13. members          (member)
14. loans            (loan)
15. fines            (fines)
```

### 2.2 Estimasi Waktu

| Data | Jumlah | Estimasi |
|------|--------|----------|
| Master Data | ~1000 records | 1-2 menit |
| Books (Biblio) | ~30.000 records | 5-10 menit |
| Items | ~45.000 records | 10-15 menit |
| Members | ~5.000 records | 2-3 menit |
| Loans History | ~100.000 records | 15-20 menit |
| **Total** | | **~45 menit** |

---

## 3. Implementasi Migrasi

### 3.1 Artisan Command

Buat command untuk migrasi:

```bash
php artisan make:command MigrateSlims
```

### 3.2 Konfigurasi Koneksi Database

Di `config/database.php`, tambahkan koneksi ke database SLiMS:

```php
'slims' => [
    'driver' => 'mysql',
    'host' => env('SLIMS_DB_HOST', '127.0.0.1'),
    'port' => env('SLIMS_DB_PORT', '3306'),
    'database' => env('SLIMS_DB_DATABASE', 'slims'),
    'username' => env('SLIMS_DB_USERNAME', 'root'),
    'password' => env('SLIMS_DB_PASSWORD', ''),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
],
```

Di `.env`:

```env
SLIMS_DB_HOST=127.0.0.1
SLIMS_DB_PORT=3306
SLIMS_DB_DATABASE=slims_db
SLIMS_DB_USERNAME=root
SLIMS_DB_PASSWORD=
```

---

## 4. Script Migrasi

### 4.1 Command Utama

```php
<?php
// app/Console/Commands/MigrateSlims.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{Publisher, Author, Subject, MediaType, CollectionType, Location, MemberType, Book, Item, Member};

class MigrateSlims extends Command
{
    protected $signature = 'slims:migrate 
                            {--step= : Specific step to run (publishers, authors, subjects, etc)}
                            {--fresh : Truncate tables before import}
                            {--branch=1 : Default branch ID}';
    
    protected $description = 'Migrate data from SLiMS database';

    protected $branchId;
    protected $idMaps = [];

    public function handle()
    {
        $this->branchId = $this->option('branch');
        
        if ($step = $this->option('step')) {
            $this->runStep($step);
        } else {
            $this->runAllSteps();
        }
    }

    protected function runAllSteps()
    {
        $steps = [
            'publishers', 'authors', 'subjects', 'media_types', 
            'collection_types', 'locations', 'member_types',
            'books', 'book_authors', 'book_subjects', 'items', 
            'members', 'loans'
        ];

        foreach ($steps as $step) {
            $this->runStep($step);
        }

        $this->info('Migration completed!');
    }

    protected function runStep($step)
    {
        $method = 'migrate' . str_replace('_', '', ucwords($step, '_'));
        
        if (method_exists($this, $method)) {
            $this->info("Migrating {$step}...");
            $this->$method();
            $this->newLine();
        } else {
            $this->error("Unknown step: {$step}");
        }
    }

    // ... method implementations below
}
```

### 4.2 Migrasi Publishers

```php
protected function migratePublishers()
{
    if ($this->option('fresh')) {
        DB::table('publishers')->truncate();
    }

    $publishers = DB::connection('slims')
        ->table('mst_publisher')
        ->get();

    $bar = $this->output->createProgressBar($publishers->count());

    foreach ($publishers as $pub) {
        $new = Publisher::create([
            'name' => $pub->publisher_name,
            'city' => null,
        ]);
        
        $this->idMaps['publishers'][$pub->publisher_id] = $new->id;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$publishers->count()} publishers migrated");
}
```

### 4.3 Migrasi Authors

```php
protected function migrateAuthors()
{
    if ($this->option('fresh')) {
        DB::table('authors')->truncate();
    }

    $authors = DB::connection('slims')
        ->table('mst_author')
        ->get();

    $bar = $this->output->createProgressBar($authors->count());

    foreach ($authors as $author) {
        $type = match($author->authority_type) {
            'p' => 'personal',
            'o' => 'organizational', 
            'c' => 'conference',
            default => 'personal'
        };

        $new = Author::create([
            'name' => $author->author_name,
            'type' => $type,
        ]);
        
        $this->idMaps['authors'][$author->author_id] = $new->id;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$authors->count()} authors migrated");
}
```

### 4.4 Migrasi Subjects

```php
protected function migrateSubjects()
{
    if ($this->option('fresh')) {
        DB::table('subjects')->truncate();
    }

    $topics = DB::connection('slims')
        ->table('mst_topic')
        ->get();

    $bar = $this->output->createProgressBar($topics->count());

    foreach ($topics as $topic) {
        $new = Subject::create([
            'name' => $topic->topic,
            'classification' => $topic->classification,
        ]);
        
        $this->idMaps['subjects'][$topic->topic_id] = $new->id;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$topics->count()} subjects migrated");
}
```

### 4.5 Migrasi Books (Biblio)

```php
protected function migrateBooks()
{
    if ($this->option('fresh')) {
        DB::table('books')->truncate();
    }

    // Load ID maps if not in memory
    $this->loadIdMaps();

    $biblios = DB::connection('slims')
        ->table('biblio')
        ->orderBy('biblio_id')
        ->cursor(); // Use cursor for large dataset

    $count = 0;
    $bar = $this->output->createProgressBar(
        DB::connection('slims')->table('biblio')->count()
    );

    foreach ($biblios as $biblio) {
        // Get publish place name
        $publishPlace = null;
        if ($biblio->publish_place_id) {
            $place = DB::connection('slims')
                ->table('mst_place')
                ->where('place_id', $biblio->publish_place_id)
                ->first();
            $publishPlace = $place?->place_name;
        }

        $new = Book::create([
            'branch_id' => $this->branchId,
            'title' => $biblio->title,
            'isbn' => $biblio->isbn_issn,
            'publisher_id' => $this->idMaps['publishers'][$biblio->publisher_id] ?? null,
            'publish_year' => $biblio->publish_year,
            'publish_place' => $publishPlace,
            'edition' => $biblio->edition,
            'collation' => $biblio->collation,
            'series_title' => $biblio->series_title,
            'call_number' => $biblio->call_number,
            'notes' => $biblio->notes,
            'image' => $biblio->image,
            'media_type_id' => $this->idMaps['media_types'][$biblio->gmd_id] ?? null,
            'language' => $biblio->language_id ?? 'id',
            'abstract' => $biblio->spec_detail_info,
            'is_opac_visible' => !$biblio->opac_hide,
            'created_at' => $biblio->input_date,
            'updated_at' => $biblio->last_update,
        ]);

        $this->idMaps['books'][$biblio->biblio_id] = $new->id;
        $count++;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$count} books migrated");
}
```

### 4.6 Migrasi Items

```php
protected function migrateItems()
{
    if ($this->option('fresh')) {
        DB::table('items')->truncate();
    }

    $this->loadIdMaps();

    $items = DB::connection('slims')
        ->table('item')
        ->orderBy('item_id')
        ->cursor();

    $count = 0;
    $bar = $this->output->createProgressBar(
        DB::connection('slims')->table('item')->count()
    );

    foreach ($items as $item) {
        // Map status
        $status = 'available';
        if ($item->item_status_id === 'R') {
            $status = 'repair';
        } elseif ($item->item_status_id === 'MIS') {
            $status = 'lost';
        }

        // Check if on loan
        $onLoan = DB::connection('slims')
            ->table('loan')
            ->where('item_code', $item->item_code)
            ->where('is_lent', 1)
            ->where('is_return', 0)
            ->exists();
        
        if ($onLoan) {
            $status = 'on_loan';
        }

        $bookId = $this->idMaps['books'][$item->biblio_id] ?? null;
        
        if (!$bookId) {
            $bar->advance();
            continue; // Skip if book not found
        }

        Item::create([
            'book_id' => $bookId,
            'branch_id' => $this->branchId,
            'barcode' => $item->item_code,
            'collection_type_id' => $this->idMaps['collection_types'][$item->coll_type_id] ?? null,
            'location_id' => $this->idMaps['locations'][$item->location_id] ?? null,
            'status' => $status,
            'inventory_code' => $item->inventory_code,
            'received_date' => $item->received_date,
            'price' => $item->price,
            'created_at' => $item->input_date,
            'updated_at' => $item->last_update,
        ]);

        $count++;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$count} items migrated");
}
```

### 4.7 Migrasi Members

```php
protected function migrateMembers()
{
    if ($this->option('fresh')) {
        DB::table('members')->truncate();
    }

    $this->loadIdMaps();

    $members = DB::connection('slims')
        ->table('member')
        ->cursor();

    $count = 0;
    $bar = $this->output->createProgressBar(
        DB::connection('slims')->table('member')->count()
    );

    foreach ($members as $member) {
        Member::create([
            'branch_id' => $this->branchId,
            'member_id' => $member->member_id,
            'name' => $member->member_name,
            'gender' => $member->gender == 1 ? 'M' : 'F',
            'birth_date' => $member->birth_date,
            'address' => $member->member_address,
            'phone' => $member->member_phone,
            'email' => $member->member_email,
            'member_type_id' => $this->idMaps['member_types'][$member->member_type_id] ?? null,
            'register_date' => $member->register_date,
            'expire_date' => $member->expire_date,
            'is_active' => !$member->is_pending,
            'created_at' => $member->input_date,
            'updated_at' => $member->last_update,
        ]);

        $count++;
        $bar->advance();
    }

    $bar->finish();
    $this->info(" - {$count} members migrated");
}
```

### 4.8 Helper: Load ID Maps

```php
protected function loadIdMaps()
{
    // Load from cache or rebuild
    if (empty($this->idMaps['publishers'])) {
        $this->idMaps['publishers'] = DB::connection('slims')
            ->table('mst_publisher')
            ->pluck('publisher_id')
            ->mapWithKeys(fn($old) => [
                $old => Publisher::where('name', 
                    DB::connection('slims')
                        ->table('mst_publisher')
                        ->where('publisher_id', $old)
                        ->value('publisher_name')
                )->value('id')
            ])
            ->toArray();
    }

    // Similar for other maps...
}
```

---

## 5. Migrasi Cover Images

### 5.1 Lokasi File di SLiMS

```
/slims/images/docs/       → Cover buku
/slims/images/persons/    → Foto anggota
/slims/files/             → File attachment
```

### 5.2 Script Copy Images

```php
protected function migrateCoverImages()
{
    $slimsPath = '/path/to/slims/images/docs/';
    $laravelPath = storage_path('app/public/covers/');

    if (!is_dir($laravelPath)) {
        mkdir($laravelPath, 0755, true);
    }

    $books = Book::whereNotNull('image')->cursor();

    foreach ($books as $book) {
        $source = $slimsPath . $book->image;
        $dest = $laravelPath . $book->image;

        if (file_exists($source)) {
            copy($source, $dest);
        }
    }
}
```

---

## 6. Validasi & Verifikasi

### 6.1 Query Verifikasi

```sql
-- Bandingkan jumlah record
SELECT 'SLiMS biblio' as source, COUNT(*) as count FROM slims.biblio
UNION ALL
SELECT 'Laravel books', COUNT(*) FROM perpustakaan.books;

SELECT 'SLiMS item' as source, COUNT(*) as count FROM slims.item
UNION ALL
SELECT 'Laravel items', COUNT(*) FROM perpustakaan.items;

SELECT 'SLiMS member' as source, COUNT(*) as count FROM slims.member
UNION ALL
SELECT 'Laravel members', COUNT(*) FROM perpustakaan.members;
```

### 6.2 Artisan Command Verifikasi

```bash
php artisan slims:verify
```

---

## 7. Rollback Plan

Jika terjadi masalah:

```bash
# Backup dulu
mysqldump perpustakaan > backup_before_migration.sql

# Rollback
php artisan migrate:fresh --seed

# Atau restore dari backup
mysql perpustakaan < backup_before_migration.sql
```

---

## 8. Post-Migration Tasks

1. **Rebuild Search Index**
   ```bash
   php artisan scout:import "App\Models\Book"
   ```

2. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Verify Cover Images**
   ```bash
   php artisan storage:link
   ```

4. **Test OPAC Search**
   - Cari beberapa judul buku
   - Verifikasi detail buku
   - Cek ketersediaan item

---

## 9. Troubleshooting

### 9.1 Memory Limit

Untuk dataset besar, gunakan cursor dan chunk:

```php
// Di php.ini atau runtime
ini_set('memory_limit', '512M');

// Gunakan cursor
$biblios = DB::connection('slims')->table('biblio')->cursor();
```

### 9.2 Timeout

```php
// Disable timeout untuk CLI
set_time_limit(0);
```

### 9.3 Duplicate Key

```php
// Gunakan updateOrCreate
Book::updateOrCreate(
    ['isbn' => $biblio->isbn_issn],
    [...data...]
);
```

---

## 10. Checklist Migrasi

- [ ] Backup database SLiMS
- [ ] Backup database Laravel (jika ada data)
- [ ] Setup koneksi database SLiMS
- [ ] Test koneksi
- [ ] Migrasi master data
- [ ] Migrasi books
- [ ] Migrasi items
- [ ] Migrasi members
- [ ] Migrasi loans (opsional)
- [ ] Copy cover images
- [ ] Verifikasi jumlah data
- [ ] Test OPAC
- [ ] Test sirkulasi

---

## Kontak

Jika ada pertanyaan tentang migrasi, hubungi tim IT Perpustakaan UNIDA.
