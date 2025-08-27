#!/bin/bash
set -e

echo "Deployment started ..."

# Masuk ke maintenance mode
(php artisan down --message="Sedang dalam proses deployment." --retry=60) || true

# Pull kode terbaru dari repository
git pull origin main

# Install/update Composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Optimasi cache
php artisan clear-compiled
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Atur permission
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

# Keluar dari maintenance mode
php artisan up

echo "✅ Deployment selesai!"