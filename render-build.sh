#!/usr/bin/env bash
set -e

echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "🗄️ Creating SQLite database file..."
touch /tmp/database.sqlite

echo "🔧 Fixing permissions..."
chmod -R 775 storage bootstrap/cache

echo "🔐 Generating app key if needed..."
php artisan key:generate || echo "App key already set."

echo "🔗 Linking storage..."
php artisan storage:link

echo "⚙️ Caching config..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🛠️ Running migrations..."
php artisan migrate --force
