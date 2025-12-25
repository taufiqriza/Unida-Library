#!/bin/bash
# Deployment script for Perpustakaan UNIDA Gontor
# Usage: bash deploy.sh

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
rm -rf .env storage
ln -sf ../shared/.env .env
ln -sf ../shared/storage storage

# Install composer (only if lock changed)
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --quiet

# Build assets (only if needed)
if [ package.json -nt node_modules ]; then
    echo "🔨 Building assets..."
    npm ci --silent
    npm run build
fi

# Migrations
echo "📊 Running migrations..."
php artisan migrate --force

# Cache
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
echo "🔐 Fixing permissions..."
sudo chown -R www-data:www-data $SHARED/storage
sudo chmod -R 775 $SHARED/storage
sudo chmod -R 775 $CURRENT/bootstrap/cache

# Reload PHP
sudo systemctl reload php8.3-fpm

echo "✅ Done! https://library.unida.gontor.ac.id"
