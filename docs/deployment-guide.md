# 🚀 Deployment Guide - Laravel Perpustakaan to VM

## Overview

| Component | Size | Sync Method |
|-----------|------|-------------|
| Application Code | ~500MB | Git/rsync |
| Main Database (SQLite) | 84KB | Direct copy |
| Shamela Database | 21GB | rsync (background) |
| Universitaria PDFs | 4.8GB | rsync (background) |
| Book Databases | 672MB | rsync (background) |
| Total Storage | ~28GB | rsync incremental |

---

## 🎯 Recommended Deployment Strategy

### Phase 1: Application Deployment (15 min)

```bash
# 1. Clone/pull code on VM
cd /var/www
git clone git@github.com:your-repo/perpustakaan.git
# OR git pull if updating

# 2. Install dependencies
cd perpustakaan
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# 3. Copy environment
cp .env.example .env
nano .env  # Configure production values

# 4. Generate key & optimize
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Phase 2: Database Sync (5 min)

**Option A: Copy SQLite from Local (Recommended)**
```bash
# From local machine - main app database only
scp database/database.sqlite user@vm:/var/www/perpustakaan/database/
```

**Option B: Fresh Migration + Seed**
```bash
php artisan migrate --force
php artisan db:seed --force
```

### Phase 3: Large Files Sync (2-8 hours, background)

Use the existing workflow: `/sync-large-files`

```bash
# From local machine, run in screen/tmux
export PROD_HOST="your-vm-ip"
export PROD_USER="your-user"
export PROD_PATH="/var/www/perpustakaan/storage"

# Start with Shamela (largest, run overnight)
screen -S shamela-sync
rsync -avz --progress -e ssh \
  storage/database/shamela_content.db \
  storage/database/master.db \
  storage/database/cover.db \
  storage/database/book/ \
  $PROD_USER@$PROD_HOST:$PROD_PATH/database/

# Ctrl+A, D to detach
```

---

## 📋 Pre-Deployment Checklist

```bash
# Local - Before deployment
[ ] Backup production database
[ ] Run tests: php artisan test
[ ] Check composer audit: composer audit
[ ] Commit all changes
[ ] Tag release: git tag v1.x.x

# VM - Environment
[ ] PHP 8.2+ installed
[ ] Required extensions: pdo, sqlite3, gd, mbstring, xml
[ ] Composer installed
[ ] Node.js 18+ installed
[ ] Storage directories writable
```

---

## 🔧 Production .env Template

```env
APP_NAME="Perpustakaan UNIDA"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://perpustakaan.unida.gontor.ac.id

# Database
DB_CONNECTION=sqlite
# OR for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=perpustakaan
# DB_USERNAME=perpustakaan_user
# DB_PASSWORD=secure_password

# Session Security
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120

# Security
ADMIN_IP_RESTRICTION=true
ADMIN_ALLOWED_IPS=your.office.ip,another.ip
```

---

## 🔄 Update/Maintenance Workflow

```bash
# Quick code update (no large files)
cd /var/www/perpustakaan
git pull origin main
composer install --optimize-autoloader --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

---

## ⚠️ Important Notes

1. **Large files are NOT in git** - must sync via rsync
2. **storage/database/** is gitignored - Shamela, Ebooks, etc.
3. **Main SQLite** (84KB) contains app data - sync via scp
4. **Run Shamela sync overnight** - 21GB takes 2-8 hours
5. **Use incremental sync** - rsync only transfers changes

---

## Verification Commands

```bash
# On VM after deployment
php artisan about
php artisan route:list | wc -l
du -sh storage/database/*
sqlite3 database/database.sqlite "SELECT COUNT(*) FROM users;"
```
