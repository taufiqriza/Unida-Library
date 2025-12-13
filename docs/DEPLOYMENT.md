# Deployment Guide

Panduan deploy Library Portal ke production server.

## Requirements

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2.x
- Node.js 18+ (untuk build assets)
- Nginx / Apache
- Supervisor (optional, untuk queue worker)

## Quick Deploy

```bash
# 1. Clone repository
git clone https://github.com/taufiqriza/Unida-Library.git
cd Unida-Library

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure .env
nano .env

# 5. Database
php artisan migrate --force
php artisan db:seed --force  # Optional: seed data

# 6. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache

# 7. Storage link
php artisan storage:link

# 8. Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Environment Variables

### Required

```env
APP_NAME="Library Portal"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://library.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=perpustakaan
DB_USERNAME=your_user
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
```

### Optional (Google OAuth)

```env
GOOGLE_CLIENT_ID=xxx
GOOGLE_CLIENT_SECRET=xxx
GOOGLE_REDIRECT_URI=https://library.example.com/auth/google/callback
```

## Crontab Setup (PENTING!)

Tambahkan ke crontab untuk scheduler:

```bash
crontab -e
```

```
* * * * * cd /var/www/perpustakaan && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler akan menjalankan:
- Queue worker setiap 5 menit (untuk plagiarism check)
- Task generator harian jam 08:00

## Queue Worker

### Option A: Via Scheduler (Default)

Sudah dikonfigurasi. Cukup setup crontab di atas.

### Option B: Supervisor (High Volume)

```bash
sudo apt install supervisor
```

Buat `/etc/supervisor/conf.d/perpustakaan.conf`:

```ini
[program:perpustakaan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/perpustakaan/artisan queue:work database --sleep=5 --tries=3 --timeout=600 --max-jobs=50 --memory=128
autostart=true
autorestart=true
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

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name library.example.com;
    root /var/www/perpustakaan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Increase upload size for plagiarism check
    client_max_body_size 25M;
}
```

## SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d library.example.com
```

## Post-Deploy Checklist

- [ ] `.env` configured dengan APP_ENV=production
- [ ] Database migrated
- [ ] Storage linked (`php artisan storage:link`)
- [ ] Permissions set (storage, bootstrap/cache)
- [ ] Crontab configured
- [ ] SSL certificate installed
- [ ] Test login (admin & member)
- [ ] Test plagiarism check (jika enabled)
- [ ] Monitor logs (`tail -f storage/logs/laravel.log`)

## Updating

```bash
cd /var/www/perpustakaan

# Maintenance mode
php artisan down

# Pull changes
git pull origin master

# Update dependencies
composer install --no-dev --optimize-autoloader

# Migrate database
php artisan migrate --force

# Clear & rebuild cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue (if using supervisor)
php artisan queue:restart

# Back online
php artisan up
```

## Troubleshooting

### 500 Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
ls -la storage/
chown -R www-data:www-data storage bootstrap/cache
```

### Queue Jobs Not Processing

```bash
# Check pending jobs
php artisan tinker --execute="echo DB::table('jobs')->count();"

# Process manually
php artisan queue:work --once

# Check failed
php artisan queue:failed
```

### Storage Not Accessible

```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

## Related Documentation

- [Plagiarism Check](./PLAGIARISM_CHECK.md) - Setup plagiarism checking
- [OJS Integration](./OJS_INTEGRATION.md) - Journal integration setup
- [Repo Integration](./REPO_INTEGRATION.md) - UNIDA Repository integration
- [Staff Portal](./STAFF_PORTAL_ARCHITECTURE.md) - Staff portal architecture

---

## OJS Journal Integration

### Initial Setup

Setelah deploy, jalankan setup jurnal:

```bash
# 1. Seed journal sources
php artisan db:seed --class=JournalSourceSeeder

# 2. Initial full scrape (jalankan di background, ~30-60 menit)
nohup php artisan journals:scrape > storage/logs/scrape-initial.log 2>&1 &

# 3. Monitor progress
tail -f storage/logs/scrape-initial.log

# 4. Verifikasi
php artisan tinker --execute="echo 'Articles: ' . App\Models\JournalArticle::count();"
```

### Scheduled Tasks

Scheduler sudah dikonfigurasi untuk:

| Task | Schedule | Command |
|------|----------|---------|
| Sync artikel baru | Harian 03:00 | `journals:sync` |
| Full scrape | Minggu 02:00 | `journals:scrape` |

Pastikan crontab sudah aktif (lihat bagian Crontab Setup).

Lihat dokumentasi lengkap: [OJS_INTEGRATION.md](./OJS_INTEGRATION.md)

---

## UNIDA Repository Integration

### Initial Setup

Sync data dari repo.unida.gontor.ac.id:

```bash
# Jalankan di background
nohup php artisan repo:sync > storage/logs/repo-sync.log 2>&1 &

# Monitor
tail -f storage/logs/repo-sync.log

# Verifikasi
php artisan tinker --execute="
echo 'Repo Thesis: ' . App\Models\Ethesis::where('source_type', 'repo')->count() . PHP_EOL;
echo 'Repo Articles: ' . App\Models\JournalArticle::where('source_type', 'repo')->count() . PHP_EOL;
"
```

### Scheduled Tasks

| Task | Schedule | Command |
|------|----------|---------|
| Repo sync | Sabtu 02:00 | `repo:sync` |

Lihat dokumentasi lengkap: [REPO_INTEGRATION.md](./REPO_INTEGRATION.md)

### Verifikasi Schedule

```bash
php artisan schedule:list
```

Lihat dokumentasi lengkap: [OJS_INTEGRATION.md](./OJS_INTEGRATION.md)
