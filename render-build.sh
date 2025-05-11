#!/usr/bin/env bash
set -o errexit

echo "🔧 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "🧱 Creating SQLite DB..."
mkdir -p database
touch database/database.sqlite

echo "🔐 Caching Laravel config..."
php artisan config:clear
php artisan config:cache
php artisan route:cache

echo "🔗 Linking storage..."
php artisan storage:link

echo "🚀 Running migrations..."
php artisan migrate --force