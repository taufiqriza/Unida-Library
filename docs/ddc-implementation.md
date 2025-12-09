# DDC (Dewey Decimal Classification) Implementation

## Overview
Fitur DDC Lookup memungkinkan pustakawan mencari dan memilih nomor klasifikasi DDC saat input bibliografi. Nomor klasifikasi akan otomatis terisi ke field classification.

## Database Setup

### Import Data DDC
```bash
# Via Laravel migration dan seeder (recommended)
php artisan migrate
php artisan db:seed --class=DdcSeeder
```

Data DDC berisi 4715+ klasifikasi dari e-DDC Edition 23.

### Struktur Tabel
- `ddc_classifications` - Tabel utama untuk nomor klasifikasi DDC
  - `id` - Primary key
  - `code` - Nomor klasifikasi (e.g., "004", "297.1")
  - `description` - Deskripsi klasifikasi dalam Bahasa Indonesia

## Penggunaan

### Di Form Bibliografi (Admin Panel)
1. Buka form Create/Edit Bibliografi
2. Pergi ke tab "Klasifikasi"
3. Klik icon 🔍 di samping field "No. Klasifikasi"
4. Ketik kata kunci (min. 2 karakter) untuk mencari
5. Pilih hasil pencarian - nomor klasifikasi akan otomatis terisi

### Contoh Pencarian
- Ketik "004" → hasil: semua klasifikasi komputer
- Ketik "islam" → hasil: klasifikasi terkait Islam
- Ketik "ekonomi" → hasil: klasifikasi ekonomi

### API Endpoints
- `GET /api/ddc/search?q=keyword` - Search DDC
- `GET /api/ddc/main-classes` - Get 10 main classes

## Kelas Utama DDC
| Kode | Subjek |
|------|--------|
| 000 | Karya Umum, Komputer |
| 100 | Filsafat & Psikologi |
| 200 | Agama |
| 300 | Ilmu Sosial |
| 400 | Bahasa |
| 500 | Sains & Matematika |
| 600 | Teknologi |
| 700 | Seni & Olahraga |
| 800 | Sastra |
| 900 | Sejarah & Geografi |

## File Terkait
- `docs/ddc_db.sql` - Data DDC original dari e-DDC
- `app/Models/DdcClassification.php` - Model Eloquent
- `database/migrations/2025_12_09_120000_create_ddc_classifications_table.php` - Migration
- `database/seeders/DdcSeeder.php` - Seeder untuk import data
- `app/Http/Controllers/Api/DdcController.php` - API Controller
