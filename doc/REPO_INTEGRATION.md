# Integrasi UNIDA Repository (OAI-PMH)

Dokumentasi integrasi data dari repo.unida.gontor.ac.id ke sistem perpustakaan.

## Overview

Repository UNIDA Gontor menyimpan:
- **Thesis** (Skripsi, Tesis, Disertasi) → masuk ke E-Thesis
- **Article** (Artikel jurnal dosen) → masuk ke Journal Articles

Data diambil via OAI-PMH protocol dan ditampilkan di Global Search dengan badge "Repo".

## Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                    REPO UNIDA OAI-PMH                           │
│         https://repo.unida.gontor.ac.id/cgi/oai2                │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
              ┌───────────────┐
              │RepoSyncService│
              │  (OAI Parser) │
              └───────┬───────┘
                      │
          ┌───────────┴───────────┐
          │                       │
          ▼                       ▼
┌─────────────────┐     ┌─────────────────┐
│  dc:type =      │     │  dc:type =      │
│  "Thesis"       │     │  "Article" /    │
│                 │     │  "PeerReviewed" │
└────────┬────────┘     └────────┬────────┘
         │                       │
         ▼                       ▼
┌─────────────────┐     ┌─────────────────┐
│  etheses table  │     │ journal_articles│
│  source_type    │     │  source_type    │
│  = 'repo'       │     │  = 'repo'       │
└────────┬────────┘     └────────┬────────┘
         │                       │
         ▼                       ▼
┌─────────────────────────────────────────┐
│            GLOBAL SEARCH                │
│                                         │
│  E-Thesis: badge "Repo" (indigo)        │
│  Journal:  badge "Repo" (indigo)        │
└─────────────────────────────────────────┘
```

## Database Schema

### etheses table (tambahan)
```sql
source_type VARCHAR(20) DEFAULT 'local'  -- 'local', 'repo'
external_id VARCHAR(255) NULL            -- repo ID
external_url VARCHAR(500) NULL           -- link ke repo
```

### journal_articles table (tambahan)
```sql
source_type VARCHAR(20) DEFAULT 'ojs'    -- 'ojs', 'repo'
```

## Artisan Commands

### repo:sync
Sinkronisasi data dari UNIDA Repository via OAI-PMH.

```bash
php artisan repo:sync
```

Output:
```
+--------------------+-------+
| Type               | Count |
+--------------------+-------+
| Thesis (E-Thesis)  | 335   |
| Articles (Journal) | 267   |
| Skipped            | 100   |
| Errors             | 0     |
+--------------------+-------+
```

## Setup Production

### 1. Migrasi Database

```bash
php artisan migrate
```

### 2. Initial Sync

```bash
# Jalankan di background (bisa lama tergantung koneksi)
nohup php artisan repo:sync > storage/logs/repo-sync.log 2>&1 &

# Monitor
tail -f storage/logs/repo-sync.log
```

### 3. Verifikasi

```bash
php artisan tinker --execute="
echo 'Repo Thesis: ' . App\Models\Ethesis::where('source_type', 'repo')->count() . PHP_EOL;
echo 'Repo Articles: ' . App\Models\JournalArticle::where('source_type', 'repo')->count() . PHP_EOL;
"
```

## Schedule

| Command | Schedule | Deskripsi |
|---------|----------|-----------|
| `repo:sync` | Sabtu 02:00 | Sync dari UNIDA Repository |

Pastikan cron scheduler aktif:
```bash
* * * * * cd /var/www/perpustakaan && php artisan schedule:run >> /dev/null 2>&1
```

## Tampilan di Global Search

### E-Thesis dari Repo
- Badge: "Repo" (warna indigo)
- Link: Langsung ke repo.unida.gontor.ac.id
- Icon: fa-graduation-cap

### Article dari Repo
- Badge: "Repo" (warna indigo)
- Link: Ke detail page lokal, lalu ke sumber asli
- Icon: fa-file-lines

## Troubleshooting

### Connection Timeout

Server repo.unida.gontor.ac.id kadang tidak stabil. Service sudah include retry logic (3x dengan delay 2 detik).

```bash
# Test koneksi manual
curl -s "https://repo.unida.gontor.ac.id/cgi/oai2?verb=Identify"
```

### Duplicate Entry Error

Jika muncul error duplicate entry, pastikan:
1. `source_type` ada di fillable model
2. Unique constraint adalah composite: `(source_type, external_id)`

### Data Tidak Masuk

Cek log:
```bash
tail -f storage/logs/laravel.log | grep "Repo sync"
```

## Data Types dari OAI-PMH

| dc:type | Masuk ke | Keterangan |
|---------|----------|------------|
| Thesis | etheses | Skripsi/Tesis/Disertasi |
| Article | journal_articles | Artikel jurnal |
| PeerReviewed | journal_articles | Artikel peer-reviewed |
| Conference or Workshop Item | - | Dilewati |
| Book | - | Dilewati |
| Other | - | Dilewati |

## Files

| File | Deskripsi |
|------|-----------|
| `app/Services/RepoSyncService.php` | OAI-PMH parser & sync logic |
| `app/Console/Commands/SyncRepo.php` | Artisan command |
| `database/migrations/*_add_repo_source_fields.php` | Schema changes |

## Related Documentation

- [OJS Integration](./OJS_INTEGRATION.md) - Integrasi jurnal dari OJS
- [Deployment](./DEPLOYMENT.md) - Panduan deploy ke production
