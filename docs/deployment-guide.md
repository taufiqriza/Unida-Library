# Deployment Guide - Perpustakaan UNIDA Gontor

## Server Information
- **Domain**: https://library.unida.gontor.ac.id
- **Server IP**: 103.195.19.158
- **User**: vm-4
- **OS**: Ubuntu 24.04 LTS

## Directory Structure (Professional Setup)

```
/var/www/perpustakaan-app/
├── current/                    # Application code (git repo)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                 # Web root (only this exposed to web)
│   ├── resources/
│   ├── routes/
│   ├── storage -> ../shared/storage  # Symlink to shared
│   ├── .env -> ../shared/.env        # Symlink to shared
│   └── ...
├── shared/                     # Persistent data (survives deployments)
│   ├── .env                    # Environment configuration
│   └── storage/
│       ├── app/public/         # User uploads
│       ├── framework/
│       │   ├── cache/
│       │   ├── sessions/
│       │   └── views/
│       └── logs/
```

## Security Benefits
- Only `/public` folder is exposed to web server
- Application code is outside web root
- `.env` and sensitive files are not accessible via web
- Storage is separated and persists across deployments

## Git Workflow

### Branches
- `master` - Development branch
- `production` - Production deployment branch

### Deploy New Changes

1. **Local Development**
   ```bash
   # Make changes on master
   git add .
   git commit -m "Your changes"
   git push origin master
   ```

2. **Merge to Production**
   ```bash
   git checkout production
   git merge master
   git push origin production
   ```

3. **Deploy to Server**
   ```bash
   ssh vm-4@103.195.19.158
   cd /var/www/perpustakaan-app/current
   ./deploy.sh
   ```

   Or quick deploy:
   ```bash
   ssh vm-4@103.195.19.158 "cd /var/www/perpustakaan-app/current && ./deploy.sh"
   ```

## Manual Deployment Steps

```bash
# SSH to server
ssh vm-4@103.195.19.158

# Go to app directory
cd /var/www/perpustakaan-app/current

# Pull latest code
git pull origin production

# Install dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm ci && npm run build

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
sudo chown -R www-data:www-data ../shared/storage
sudo chmod -R 775 ../shared/storage

# Restart PHP-FPM
sudo systemctl reload php8.3-fpm
```

## Rollback

If something goes wrong:
```bash
cd /var/www/perpustakaan-app/current
git log --oneline -5  # Find previous commit
git reset --hard <commit-hash>
php artisan config:cache
php artisan route:cache
sudo systemctl reload php8.3-fpm
```

## Database

- **Type**: MariaDB 10.11
- **Database**: perpustakaan
- **User**: perpustakaan
- **Host**: 127.0.0.1

### Backup Database
```bash
mysqldump -u perpustakaan -p perpustakaan > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
mysql -u perpustakaan -p perpustakaan < backup_file.sql
```

## Services

```bash
# Nginx
sudo systemctl status nginx
sudo systemctl reload nginx

# PHP-FPM
sudo systemctl status php8.3-fpm
sudo systemctl reload php8.3-fpm

# MariaDB
sudo systemctl status mariadb
```

## Logs

```bash
# Laravel logs
tail -f /var/www/perpustakaan-app/shared/storage/logs/laravel.log

# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM log
sudo tail -f /var/log/php8.3-fpm.log
```

## SSL Certificate

SSL is managed by Certbot (Let's Encrypt). Auto-renewal is configured.

```bash
# Check certificate
sudo certbot certificates

# Manual renewal
sudo certbot renew
```
