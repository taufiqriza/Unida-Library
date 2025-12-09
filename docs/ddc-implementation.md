# DDC (Dewey Decimal Classification) Implementation

## Overview
Sistem klasifikasi DDC untuk katalogisasi buku perpustakaan.

## Struktur File

```
database/data/ddc.json      # Data DDC (4715 klasifikasi)
app/Services/DdcService.php # Service untuk search DDC
app/Http/Controllers/Api/DdcController.php # API endpoint
app/Livewire/DdcLookup.php  # Livewire component
resources/views/filament/components/ddc-lookup-modal.blade.php # Modal UI
resources/views/livewire/ddc-lookup.blade.php # Livewire view
```

## Cara Kerja

1. Data DDC disimpan dalam file JSON (`database/data/ddc.json`)
2. `DdcService` membaca JSON dan cache selama 24 jam
3. API endpoint `/api/ddc/search` untuk pencarian
4. Modal lookup di Filament admin untuk input klasifikasi buku

## API Endpoints

### Search DDC
```
GET /api/ddc/search?q={query}&limit={limit}
```
- `q` - Kata kunci pencarian (min 2 karakter)
- `limit` - Maksimal hasil (default 25, max 50)

### Main Classes
```
GET /api/ddc/main-classes
```
Mengembalikan 10 kelas utama DDC (000-900)

## Kelas Utama DDC

| Kode | Deskripsi |
|------|-----------|
| 000 | Karya Umum & Komputer |
| 100 | Filsafat & Psikologi |
| 200 | Agama |
| 2X | Islam |
| 300 | Ilmu Sosial |
| 400 | Bahasa |
| 500 | Sains & Matematika |
| 600 | Teknologi |
| 700 | Seni & Olahraga |
| 800 | Sastra |
| 900 | Sejarah & Geografi |

## Deployment

Tidak perlu setup tambahan. File JSON sudah termasuk dalam repository dan akan otomatis tersedia saat deploy.

## Clear Cache

Jika perlu clear cache DDC:
```php
app(DdcService::class)->clearCache();
```

Atau via tinker:
```bash
php artisan tinker
>>> app(\App\Services\DdcService::class)->clearCache();
```
