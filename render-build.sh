#!/usr/bin/env bash
set -o errexit

# Create SQLite database file if using SQLite
mkdir -p /var/www/database
touch /var/www/database/database.sqlite

#  Fix permissions BEFORE Artisan commands
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

#  Laravel setup
php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan storage:link
