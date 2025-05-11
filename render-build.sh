#!/usr/bin/env bash
set -e

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "🗄️ Creating SQLite database..."
touch /tmp/database.sqlite

echo "🔐 App key setup..."
php artisan key:generate || echo "App key exists"

echo "🔧 Fixing permissions..."
chmod -R 775 storage bootstrap/cache

echo "🔄 Clearing caches..."
php artisan config:clear
php artisan view:clear

echo "🔗 Linking storage..."
php artisan storage:link

echo "📦 Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🛠 Running migrations..."
php artisan migrate --force
