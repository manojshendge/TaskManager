#!/usr/bin/env bash
set -o errexit

# Create database folder and SQLite file
mkdir -p /var/www/database
touch /var/www/database/database.sqlite

# Laravel setup
php artisan config:clear
php artisan config:cache
php artisan migrate --force
php artisan storage:link