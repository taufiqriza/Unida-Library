#!/bin/bash
# Fast deployment script for Perpustakaan UNIDA Gontor
# Usage: bash deploy.sh [--full]

set -e

APP_DIR="/var/www/perpustakaan-app"
CURRENT="$APP_DIR/current"
SHARED="$APP_DIR/shared"

echo "🚀 Starting deployment..."
cd $CURRENT

# Pull latest code
echo "📥 Pulling latest code..."
git fetch origin production
git reset --hard origin/production

# Restore symlinks (git reset removes them)
echo "🔗 Restoring symlinks..."
rm -rf .env storage 2>/dev/null || true
ln -sf ../shared/.env .env
ln -sf ../shared/storage storage

# Fix permissions BEFORE running any artisan commands
echo "🔐 Fixing permissions..."
sudo chown -R www-data:www-data $SHARED/storage
sudo chmod -R 775 $SHARED/storage
sudo chown -R www-data:www-data $CURRENT/bootstrap/cache
sudo chmod -R 775 $CURRENT/bootstrap/cache

# Composer: only if composer.lock changed
if [ "$1" == "--full" ] || ! cmp -s composer.lock vendor/composer/installed.json 2>/dev/null; then
    echo "📦 Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction --quiet || \
    composer update --no-dev --optimize-autoloader --no-interaction --quiet
fi

# NPM: only if --full flag or package.json changed
if [ "$1" == "--full" ]; then
    echo "🔨 Building assets..."
    npm ci --silent
    npm run build
fi

# Clear old cache
echo "🧹 Clearing cache..."
php artisan config:clear --quiet
php artisan route:clear --quiet
php artisan view:clear --quiet

# Migrations (skip if no new migrations)
echo "📊 Running migrations..."
php artisan migrate --force --quiet 2>/dev/null || true

# Rebuild cache
echo "⚡ Optimizing..."
php artisan config:cache --quiet
php artisan route:cache --quiet

# Reload PHP-FPM
sudo systemctl reload php8.3-fpm

echo "✅ Done! https://library.unida.gontor.ac.id"
