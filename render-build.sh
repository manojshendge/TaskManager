#!/usr/bin/env bash
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Create database folder and SQLite file
mkdir -p /var/www/database
touch /var/www/database/database.sqlite

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Laravel setup
php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan storage:link