#!/usr/bin/env bash
set -o errexit

echo "🧱 Creating SQLite DB..."
mkdir -p database
touch database/database.sqlite

echo "🔧 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "🔐 Caching config..."
php artisan config:cache || echo "⚠️ Config cache failed"

echo "🚀 Migrating..."
php artisan migrate --force || echo "⚠️ Migration failed"

echo "📂 Linking storage..."
php artisan storage:link || echo "⚠️ Storage link failed"

echo "📄 Dumping Laravel log (if any):"
cat storage/logs/laravel.log || echo "No Laravel log found"
