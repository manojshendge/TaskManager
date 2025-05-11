#!/usr/bin/env bash
set -o errexit

# Laravel setup
php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan storage:link

# Permissions (safety net)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache