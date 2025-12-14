# Spesifikasi Kebutuhan API Integrasi Katalog (Kubuku x UNIDA)

Dokumen ini berisi spesifikasi teknis kebutuhan API (*Application Programming Interface*) untuk keperluan integrasi data katalog E-Book Kubuku ke dalam sistem OPAC (*Online Public Access Catalog*) Perpustakaan Universitas Darussalam Gontor.

## 1. Tujuan Integrasi
Memungkinkan sistem perpustakaan UNIDA untuk menarik (*harvesting*) metadata koleksi E-Book yang dilanggan secara otomatis, sehingga koleksi tersebut:
1.  Dapat dicari (*searchable*) melalui OPAC UNIDA.
2.  Menampilkan status ketersediaan di hasil pencarian.
3.  Mengarahkan pengguna (*redirect*) ke platform Kubuku untuk membaca konten.

## 2. Metode Integrasi yang Diusulkan
Sistem kami akan menggunakan metode **Scheduled Data Synchronization**. Server UNIDA akan mengakses API Kubuku secara berkala (misalnya 1x sehari) untuk memperbarui database katalog lokal.

## 3. Spesifikasi Teknis API

Kami memohon pihak Kubuku menyediakan layanan REST API dengan spesifikasi referensi sebagai berikut:

### A. Endpoint Target
*   **Fungsi**: Mendapatkan daftar seluruh koleksi E-Book UNIDA.
*   **Method**: `GET`
*   **Response Format**: `JSON`

### B. Struktur Data (JSON Response)
Kami membutuhkan data atribut buku sebagai berikut:

| Field Key (Saran) | Tipe Data | Keterangan / Kegunaan | Wajib/Opsional |
| :--- | :--- | :--- | :--- |
| `id` / `external_id` | String | ID unik buku dalam sistem Kubuku (untuk sinkronisasi update). | **Wajib** |
| `title` | String | Judul lengkap buku. | **Wajib** |
| `isbn` | String | Nomor ISBN/E-ISBN. | **Wajib** |
| `authors` | String / Array | Nama penulis (contoh: "Penulis A; Penulis B"). | **Wajib** |
| `publisher` | String | Nama penerbit. | **Wajib** |
| `publish_year` | Integer | Tahun terbit. | **Wajib** |
| `cover_url` | String (URL) | Tautan langsung ke file gambar sampul (JPG/PNG). | **Wajib** |
| `description` | String | Sinopsis atau abstrak buku. | Opsional |
| `read_url` | String (URL) | **Direct Link / Deep Link** untuk membuka buku tersebut di Web Reader Kubuku UNIDA. | **Wajib** |
| `category` | String | Kategori atau Klasifikasi buku. | Opsional |
| `updated_at` | Timestamp | Waktu terakhir data diubah (untuk incremental sync). | Opsional |

### C. Contoh JSON Response yang Diharapkan
```json
{
  "status": "success",
  "data": [
    {
      "id": "kb-10234",
      "title": "Aplikasi Metodologi Penelitian Kesehatan",
      "isbn": "978-602-1234-56-7",
      "authors": "Dr. Fulanah, S.KM.",
      "publisher": "Nuha Medika",
      "publish_year": 2023,
      "cover_url": "https://ebook.digilib-unida.id/covers/10234.jpg",
      "read_url": "https://ebook.digilib-unida.id/detail/aplikasi-metodologi/10234",
      "description": "Buku ini membahas tuntas tentang metodologi..."
    },
    ...
  ]
}
```

### D. Keamanan (Authentication)
Kami siap menyesuaikan dengan standar keamanan Kubuku, baik menggunakan:
*   **API Key** (di Header).
*   **Bearer Token** (OAuth / Static Token).
*   **IP Whitelist**: Jika diperlukan, kami akan memberikan alamat IP Server tetap (*Static IP*) kami.

## 4. Status Implementasi

### Fitur yang Sudah Diimplementasikan (14 Desember 2025):

| Fitur | Status | Lokasi |
|-------|--------|--------|
| App Settings UI | ✅ | `App\Filament\Pages\AppSettings` (Tab Integrasi) |
| Toggle Enable/Disable | ✅ | Setting: `kubuku_enabled` |
| Jadwal Sync | ✅ | Setting: `kubuku_sync_schedule` (daily/weekly/disabled) |
| API URL Config | ✅ | Setting: `kubuku_api_url` |
| API Key Config | ✅ | Setting: `kubuku_api_key` |
| Library ID Config | ✅ | Setting: `kubuku_library_id` |
| Test Connection | ✅ | Button di App Settings |
| Sync Now | ✅ | Button di App Settings |
| Artisan Command | ✅ | `php artisan kubuku:sync` |
| Scheduled Sync | ✅ | `routes/console.php` (jam 02:00) |

### Files Terkait:

```
app/Filament/Pages/AppSettings.php        - UI Konfigurasi
app/Console/Commands/SyncKubukuCommand.php - Artisan Command
routes/console.php                        - Scheduler
docs/KUBUKU_API_REQUIREMENTS.md           - Dokumentasi
```

### Catatan:
- Sync dijalankan di **jam 02:00 dini hari** sesuai rekomendasi pihak Kubuku (low traffic)
- Dokumentasi API lengkap dari Kubuku akan tersedia hari Senin (16 Desember 2025)
- Struktur response API mengikuti spesifikasi di dokumen ini, akan disesuaikan jika berbeda

## 5. Penutup
Spesifikasi di atas adalah referensi standar untuk memudahkan integrasi dengan framework sistem kami (Laravel 12). Kami sangat terbuka untuk menyesuaikan dengan struktur API yang sudah ada di sistem Kubuku jika berbeda format.

