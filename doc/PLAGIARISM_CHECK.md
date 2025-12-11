# Plagiarism Check System

Dokumentasi sistem pengecekan plagiasi untuk Library Portal.

## Overview

Sistem ini memungkinkan member untuk mengecek tingkat kesamaan dokumen mereka dengan database akademik menggunakan layanan iThenticate/Turnitin.

## Arsitektur

```
┌─────────────────┐     ┌──────────────┐     ┌─────────────────┐
│  Member Portal  │────▶│  Laravel     │────▶│  iThenticate    │
│  (Upload Doc)   │     │  Queue Job   │     │  Turnitin API   │
└─────────────────┘     └──────────────┘     └─────────────────┘
                              │
                              ▼
                        ┌──────────────┐
                        │  Database    │
                        │  (Results)   │
                        └──────────────┘
```

## Provider yang Didukung

| Provider | Status | Keterangan |
|----------|--------|------------|
| `internal` | ✅ Ready | Cek terhadap database E-Thesis lokal |
| `ithenticate` | ✅ Ready | Turnitin iThenticate API (TCA) |
| `turnitin` | ✅ Ready | Sama dengan iThenticate |
| `copyleaks` | ⏳ Planned | Belum diimplementasi |

## Konfigurasi

### 1. Environment Variables

Tidak ada env variable khusus. Semua konfigurasi disimpan di database `settings`.

### 2. Database Settings

Konfigurasi melalui Admin Panel → App Settings → Plagiarism:

| Key | Default | Keterangan |
|-----|---------|------------|
| `plagiarism_enabled` | `true` | Aktifkan/nonaktifkan fitur |
| `plagiarism_provider` | `internal` | Provider: `internal`, `ithenticate`, `turnitin` |
| `plagiarism_pass_threshold` | `25` | Batas lolos (%) |
| `plagiarism_warning_threshold` | `15` | Batas warning (%) |
| `plagiarism_min_words` | `100` | Minimal kata untuk dicek |

### 3. iThenticate/Turnitin API

| Key | Keterangan |
|-----|------------|
| `ithenticate_base_url` | URL API (contoh: `https://unidagontor.turnitin.com`) |
| `ithenticate_integration_name` | Nama integrasi |
| `ithenticate_api_key` | API Key (tidak digunakan di TCA) |
| `ithenticate_api_secret` | Signing Secret (Bearer token) |

## Queue Worker (PENTING!)

Plagiarism check berjalan di background menggunakan Laravel Queue. **Worker HARUS dijalankan** agar job diproses.

### Development

```bash
# Jalankan worker manual
php artisan queue:work --tries=3 --timeout=600

# Atau process sekali saja
php artisan queue:work --once
```

### Production (Pilih Salah Satu)

#### Option A: Cron-based (Recommended - Hemat Resource)

Sudah dikonfigurasi di `routes/console.php`. Cukup setup crontab:

```bash
# Tambahkan ke crontab
* * * * * cd /var/www/perpustakaan && php artisan schedule:run >> /dev/null 2>&1
```

Worker akan jalan otomatis setiap 5 menit dan berhenti saat queue kosong.

#### Option B: Supervisor (Untuk High Volume)

Buat file `/etc/supervisor/conf.d/perpustakaan-worker.conf`:

```ini
[program:perpustakaan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/perpustakaan/artisan queue:work database --sleep=5 --tries=3 --timeout=600 --max-jobs=50 --max-time=1800 --memory=128
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/perpustakaan/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start perpustakaan-worker:*
```

#### Option C: Sync (Tanpa Worker)

Untuk server sangat kecil, proses langsung tanpa queue:

```env
QUEUE_CONNECTION=sync
```

⚠️ User harus menunggu 1-5 menit saat submit.

## Flow Pengecekan

1. **Member Upload** → File disimpan di `storage/app/plagiarism-uploads/`
2. **Create Record** → `PlagiarismCheck` dengan status `pending`
3. **Dispatch Job** → `ProcessPlagiarismCheck` masuk queue
4. **Worker Process**:
   - Status → `processing`
   - Accept EULA (jika iThenticate)
   - Create Submission
   - Upload File
   - Request Similarity Report
   - Poll Results (max 15 menit)
   - Status → `completed` / `failed`
5. **Generate Certificate** → PDF sertifikat otomatis dibuat

## API Endpoints (Member Portal)

| Method | Endpoint | Keterangan |
|--------|----------|------------|
| GET | `/member/plagiarism` | List semua check |
| GET | `/member/plagiarism/create` | Form upload |
| POST | `/member/plagiarism` | Submit dokumen |
| GET | `/member/plagiarism/{id}` | Detail hasil |
| GET | `/member/plagiarism/{id}/status` | AJAX polling status |
| GET | `/member/plagiarism/{id}/certificate` | View sertifikat |
| GET | `/member/plagiarism/{id}/certificate/download` | Download PDF |

## Database Schema

### Table: `plagiarism_checks`

```sql
- id
- member_id (FK)
- thesis_submission_id (FK, nullable)
- document_title
- original_filename
- file_path
- file_type (pdf/docx)
- file_size
- status (pending/processing/completed/failed)
- similarity_score (decimal)
- result (pass/warning/fail)
- matched_sources (JSON)
- report_data (JSON)
- provider
- external_id (Turnitin submission ID)
- external_report_url
- certificate_number
- certificate_path
- word_count
- error_message
- started_at
- checked_at
- created_at
- updated_at
```

## Troubleshooting

### Job Stuck di Pending

```bash
# Cek pending jobs
php artisan tinker --execute="echo DB::table('jobs')->count();"

# Process manual
php artisan queue:work --once

# Cek failed jobs
php artisan queue:failed
```

### Error 404 saat Polling

Submission mungkin expired di Turnitin. Coba submit ulang.

### Timeout Error

Tingkatkan timeout di job atau supervisor config.

### Memory Error

```bash
# Restart worker dengan memory limit
php artisan queue:restart
```

## Testing API

```bash
# Test connection
php artisan tinker --execute="
use App\Services\Plagiarism\Providers\IthenticateProvider;
\$p = new IthenticateProvider();
echo \$p->isConfigured() ? 'OK' : 'NOT CONFIGURED';
"

# Test full flow
php artisan tinker --execute="
use App\Models\PlagiarismCheck;
use App\Services\Plagiarism\PlagiarismService;
\$check = PlagiarismCheck::find(1);
\$service = new PlagiarismService();
\$result = \$service->check(\$check);
print_r(\$result);
"
```

## Quota Member

Default: **3 kali** per member. Dapat diubah di `PlagiarismController::QUOTA_LIMIT`.

## File Locations

| File | Keterangan |
|------|------------|
| `app/Services/Plagiarism/PlagiarismService.php` | Main service |
| `app/Services/Plagiarism/Providers/IthenticateProvider.php` | Turnitin API |
| `app/Services/Plagiarism/CertificateGenerator.php` | Generate PDF |
| `app/Jobs/ProcessPlagiarismCheck.php` | Queue job |
| `app/Http/Controllers/Opac/PlagiarismController.php` | Member controller |
| `app/Models/PlagiarismCheck.php` | Model |
| `resources/views/opac/member/plagiarism/` | Views |

## Checklist Deploy

- [ ] Set `plagiarism_provider` di App Settings
- [ ] Isi `ithenticate_*` credentials (jika pakai Turnitin)
- [ ] Setup crontab untuk scheduler
- [ ] Pastikan `storage/app/plagiarism-uploads` writable
- [ ] Test upload dan cek status
- [ ] Monitor `storage/logs/laravel.log` untuk error
