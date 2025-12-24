#!/bin/bash
# Deployment script for Perpustakaan UNIDA Gontor
# Usage: ./deploy.sh

set -e

APP_DIR="/var/www/perpustakaan-app"
CURRENT="$APP_DIR/current"
SHARED="$APP_DIR/shared"

echo "🚀 Starting deployment..."

cd $CURRENT

# Pull latest code
echo "📥 Pulling latest code from production branch..."
git fetch origin production
git reset --hard origin/production

# Install dependencies
echo "📦 Installing composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install npm and build assets
echo "🔨 Building assets..."
npm ci --silent
npm run build

# Clear and cache
echo "🧹 Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
echo "📊 Running migrations..."
php artisan migrate --force

# Optimize
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fix permissions
echo "🔐 Fixing permissions..."
sudo chown -R www-data:www-data $SHARED/storage
sudo chmod -R 775 $SHARED/storage
sudo chmod -R 775 $CURRENT/bootstrap/cache

# Restart PHP-FPM
echo "🔄 Restarting PHP-FPM..."
sudo systemctl reload php8.3-fpm

echo "✅ Deployment completed successfully!"
echo "🌐 Site: https://library.unida.gontor.ac.id"
