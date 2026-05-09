#!/bin/sh
set -e

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
php artisan migrate --force

echo "▶ Linking storage..."
php artisan storage:link || true

echo "▶ Publishing Filament assets..."
php artisan filament:assets

echo "▶ Starting PHP-FPM..."
php-fpm -D

echo "▶ Starting Nginx..."
exec nginx -g "daemon off;"