# DDC & Call Number Implementation

## Overview
Sistem klasifikasi DDC dan auto-generate call number untuk perpustakaan.

## DDC Lookup

### Storage
Data DDC disimpan dalam file JSON (`storage/app/ddc.json`) dengan caching untuk performa optimal.
- 4715+ klasifikasi DDC Edition 23
- Cached selama 24 jam
- Search tanpa query database

### Penggunaan di Form Bibliografi
1. Buka form Create/Edit Bibliografi
2. Tab "Klasifikasi" → klik tombol "Cari DDC"
3. Ketik kata kunci atau klik kelas utama
4. Pilih klasifikasi → otomatis terisi

### Export DDC ke JSON
```php
// Via tinker atau artisan command
(new \App\Services\DdcService)->exportToJson();
```

## Call Number

### Format Pattern (SLiMS Style)
```
S        = Kode Koleksi (dari Collection Type)
2X9.12   = Nomor Klasifikasi DDC
TIR      = 3 huruf pertama nama pengarang
m        = Huruf pertama judul (lowercase)
```

### Auto-Generate
1. Isi field Classification
2. Klik tombol "Generate" di field No. Panggil
3. Call number akan dibuat otomatis dari:
   - Classification number
   - Author code (dari SOR/Statement of Responsibility)
   - Title code (huruf pertama judul, skip artikel)

### Services

**DdcService** (`app/Services/DdcService.php`)
- `search($query, $limit)` - Cari DDC
- `find($code)` - Get DDC by code
- `exportToJson()` - Export database ke JSON

**CallNumberService** (`app/Services/CallNumberService.php`)
- `generate($collectionCode, $classification, $author, $title)` - Generate call number
- `getAuthorCode($name)` - Get 3 huruf kode pengarang
- `getTitleCode($title)` - Get huruf pertama judul
- `parse($callNumber)` - Parse call number ke parts

## Print Label

### Barcode Label
Layout: Barcode di kiri, Call Number di kanan
- Judul buku (italic)
- Barcode dengan font Code 39
- Nama perpustakaan
- Call number (4 baris)

### Spine Label
Label punggung buku dengan call number 4 baris.

### Routes
- `/print/barcode/{item}` - Single barcode
- `/print/barcodes?ids=1,2,3` - Multiple barcodes
- `/print/label/{item}` - Single label
- `/print/labels?ids=1,2,3` - Multiple labels

## File Terkait
- `storage/app/ddc.json` - Data DDC (JSON)
- `app/Services/DdcService.php` - DDC Service
- `app/Services/CallNumberService.php` - Call Number Service
- `resources/views/print/barcode.blade.php` - Print barcode
- `resources/views/print/label.blade.php` - Print label
