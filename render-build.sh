#!/usr/bin/env bash
set -o errexit

echo "⚙️  Creating SQLite DB file..."
mkdir -p /var/www/database
touch /var/www/database/database.sqlite
echo "✅ SQLite database created."

echo "🔧 Fixing permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "🔄 Laravel setup..."
php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan storage:link

echo "✅ Render build script complete."
