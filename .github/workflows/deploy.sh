#!/bin/bash
set -e

echo "Deployment started ..."

# Enter maintenance mode or return true
# if already is in maintenance mode
(php artisan down) || true

# Pull kode terbaru dari repository
git pull origin main

# Install/update Composer dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Jalankan migrasi database
# php artisan migrate --force

# Clear dan cache ulang
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set izin yang sesuai untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Restart queue jika digunakan
# php artisan queue:restart

# Selesai
echo "Deployment selesai!"
