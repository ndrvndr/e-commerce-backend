#!/bin/sh
set -e

echo "▶ Setting up directories..."
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "▶ Discovering packages..."
php artisan package:discover --ansi

echo "▶ Clearing old cache..."
php artisan config:clear
php artisan cache:clear

echo "▶ Caching config..."
php artisan config:cache

echo "▶ Caching routes..."
php artisan route:cache

echo "▶ Caching views..."
php artisan view:cache

echo "▶ Running migrations..."
php artisan migrate:fresh --force

echo "▶ Linking storage..."
php artisan storage:link || true

echo "▶ Publishing Filament assets..."
php artisan filament:assets

echo "▶ Starting PHP-FPM..."
php-fpm -D

echo "▶ Starting Nginx..."
exec nginx -g "daemon off;"
