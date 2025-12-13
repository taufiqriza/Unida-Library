# Integrasi Open Journal Systems (OJS)

Dokumentasi lengkap integrasi jurnal UNIDA Gontor dengan sistem perpustakaan.

## Daftar Isi
- [Overview](#overview)
- [Arsitektur](#arsitektur)
- [Metode Sinkronisasi](#metode-sinkronisasi)
- [Setup Production](#setup-production)
- [Artisan Commands](#artisan-commands)
- [Troubleshooting](#troubleshooting)

---

## Overview

Sistem ini mengintegrasikan 35 jurnal dari Open Journal Systems UNIDA Gontor ke dalam katalog perpustakaan. Artikel jurnal dapat dicari melalui Global Search dan memiliki halaman detail sendiri sebelum diarahkan ke sumber asli.

### Fitur
- ✅ Sinkronisasi otomatis artikel jurnal
- ✅ Pencarian terintegrasi di Global Search
- ✅ Halaman detail artikel dengan abstrak, penulis, DOI
- ✅ Filter berdasarkan jurnal dan tahun
- ✅ Statistik view per artikel
- ✅ Link ke sumber asli (Open Journal)

### Statistik Jurnal
| Kategori | Jumlah |
|----------|--------|
| Total Jurnal | 35 |
| Jurnal SINTA 2 | 3 (TSAQAFAH, At-Ta'dib, ETTISAL) |
| Jurnal SINTA 3 | 4 (Kalimah, Lisanudhad, Agrotech, Ijtihad) |

---

## Arsitektur

### Database Schema

```
journal_sources
├── id
├── code (unique) - e.g., 'tsaqafah'
├── name
├── sinta_rank - e.g., '2', '3'
├── issn
├── base_url
├── feed_type - 'atom', 'scrape'
├── feed_url
├── is_active
├── last_synced_at
├── article_count
└── timestamps

journal_articles
├── id
├── external_id (unique) - OJS article ID
├── journal_code (FK)
├── journal_name
├── title
├── abstract
├── abstract_en
├── authors (JSON) - [{name, email}]
├── doi
├── volume
├── issue
├── issue_title
├── pages
├── publish_year
├── published_at
├── url - link ke OJS
├── pdf_url
├── cover_url
├── keywords (JSON)
├── language
├── rights
├── views - counter
├── synced_at
└── timestamps
```

### Services

| Service | File | Fungsi |
|---------|------|--------|
| OjsSyncService | `app/Services/OjsSyncService.php` | Sync via Atom Feed (cepat, artikel terbaru) |
| OjsScraperService | `app/Services/OjsScraperService.php` | Full scrape archive (lengkap, semua artikel) |

---

## Metode Sinkronisasi

### 1. Atom Feed Sync (Harian)
- **Command:** `php artisan journals:sync`
- **Kecepatan:** ~2-5 menit untuk semua jurnal
- **Hasil:** ~10-15 artikel terbaru per jurnal
- **Gunakan untuk:** Update harian artikel baru

### 2. Full Scrape (Mingguan)
- **Command:** `php artisan journals:scrape`
- **Kecepatan:** ~30-60 menit untuk semua jurnal
- **Hasil:** Semua artikel dari archive
- **Gunakan untuk:** Sync awal & memastikan kelengkapan data

### Perbandingan

| Aspek | Atom Feed | Full Scrape |
|-------|-----------|-------------|
| Kecepatan | Cepat (~5 menit) | Lambat (~60 menit) |
| Kelengkapan | Terbatas (~15/jurnal) | Lengkap (semua) |
| Beban Server | Ringan | Sedang |
| Frekuensi | Harian | Mingguan |

---

## Setup Production

### 1. Migrasi Database

```bash
cd /var/www/perpustakaan
php artisan migrate
```

### 2. Seed Journal Sources

```bash
php artisan db:seed --class=JournalSourceSeeder
```

### 3. Initial Full Scrape

Jalankan scrape pertama kali untuk mengambil semua artikel:

```bash
# Jalankan di background
nohup php artisan journals:scrape > storage/logs/scrape-initial.log 2>&1 &

# Monitor progress
tail -f storage/logs/scrape-initial.log

# Atau scrape per jurnal (jika ingin bertahap)
php artisan journals:scrape --journal=tsaqafah
php artisan journals:scrape --journal=tadib
# ... dst
```

### 4. Setup Cron Scheduler

Pastikan Laravel scheduler sudah aktif:

```bash
crontab -e
```

Tambahkan:
```cron
* * * * * cd /var/www/perpustakaan && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Verifikasi Schedule

```bash
php artisan schedule:list
```

Output yang diharapkan:
```
  journals:sync .............. dailyAt 03:00 .... Next Due: tomorrow at 3:00 AM
  journals:scrape ............ weeklyOn 0 02:00 . Next Due: Sunday at 2:00 AM
```

### 6. Verifikasi Data

```bash
php artisan tinker --execute="
echo 'Total Articles: ' . App\Models\JournalArticle::count() . PHP_EOL;
echo 'Total Sources: ' . App\Models\JournalSource::count() . PHP_EOL;
"
```

---

## Artisan Commands

### journals:sync
Sinkronisasi artikel terbaru via Atom Feed.

```bash
# Sync semua jurnal
php artisan journals:sync

# Sync jurnal tertentu
php artisan journals:sync --source=tsaqafah
```

### journals:scrape
Full scrape semua artikel dari archive pages.

```bash
# Scrape semua jurnal (lama, ~60 menit)
php artisan journals:scrape

# Scrape jurnal tertentu
php artisan journals:scrape --journal=tsaqafah

# Jalankan di background
nohup php artisan journals:scrape > storage/logs/scrape.log 2>&1 &
```

---

## Konfigurasi Schedule

File: `routes/console.php`

```php
// Sync artikel terbaru via Atom feed (harian jam 3 pagi)
Schedule::command('journals:sync')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Full scrape semua artikel (mingguan hari Minggu jam 2 pagi)
Schedule::command('journals:scrape')
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping()
    ->runInBackground();
```

---

## Routes

| Route | Method | Controller | Deskripsi |
|-------|--------|------------|-----------|
| `/journals` | GET | JournalController@index | Browse jurnal |
| `/journals/{id}` | GET | JournalController@show | Detail artikel |
| `/search?type=journal` | GET | GlobalSearch | Pencarian jurnal |

---

## Troubleshooting

### Scrape Timeout/Connection Error

**Gejala:** Error "Could not connect to server" atau timeout

**Solusi:**
1. Cek koneksi ke ejournal.unida.gontor.ac.id
2. Tingkatkan timeout di `OjsScraperService.php`:
   ```php
   protected int $timeout = 60; // default 30
   ```
3. Tambah delay antar request:
   ```php
   protected int $delay = 1000; // default 500ms
   ```

### Artikel Tidak Ter-scrape

**Gejala:** Beberapa artikel tidak masuk database

**Solusi:**
1. Cek log: `storage/logs/laravel.log`
2. Jalankan ulang scrape untuk jurnal tersebut:
   ```bash
   php artisan journals:scrape --journal=<kode_jurnal>
   ```

### Duplicate Articles

**Gejala:** Artikel duplikat di database

**Solusi:** Sistem sudah handle via `external_id` unique constraint. Jika tetap duplikat:
```bash
php artisan tinker --execute="
App\Models\JournalArticle::selectRaw('external_id, COUNT(*) as cnt')
    ->groupBy('external_id')
    ->having('cnt', '>', 1)
    ->get();
"
```

### Memory Limit saat Scrape

**Gejala:** "Allowed memory size exhausted"

**Solusi:**
```bash
php -d memory_limit=512M artisan journals:scrape
```

---

## Monitoring

### Cek Status Sync Terakhir

```bash
php artisan tinker --execute="
App\Models\JournalSource::select('code', 'name', 'article_count', 'last_synced_at')
    ->orderByDesc('last_synced_at')
    ->get()
    ->each(fn(\$s) => print(\$s->code . ': ' . \$s->article_count . ' articles, last sync: ' . \$s->last_synced_at . PHP_EOL));
"
```

### Cek Artikel per Jurnal

```bash
php artisan tinker --execute="
App\Models\JournalSource::withCount('articles')
    ->orderByDesc('articles_count')
    ->get()
    ->each(fn(\$s) => print(\$s->name . ': ' . \$s->articles_count . PHP_EOL));
"
```

### Log Files

- Scrape errors: `storage/logs/laravel.log`
- Scrape output (jika pakai nohup): `storage/logs/scrape.log`

---

## Daftar Jurnal

| Kode | Nama | SINTA |
|------|------|-------|
| tsaqafah | TSAQAFAH | 2 |
| tadib | At-Ta'dib | 2 |
| ettisal | ETTISAL | 2 |
| kalimah | Kalimah | 3 |
| lisanu | Lisanudhad | 3 |
| agrotech | Gontor Agrotech Science Journal | 3 |
| ijtihad | Ijtihad | 3 |
| syariah | Jurnal Syariah | - |
| jicl | JICL | - |
| ... | (32 jurnal lainnya) | - |

---

## Changelog

### v1.0.0 (2025-12-11)
- Initial integration dengan Atom Feed
- 35 jurnal terdaftar
- ~251 artikel via Atom feed

### v1.1.0 (2025-12-11)
- Tambah OjsScraperService untuk full scrape
- Halaman detail artikel
- Cover image support
- View counter
- Weekly auto-scrape schedule
