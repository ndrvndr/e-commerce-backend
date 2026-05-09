#!/bin/bash
set -e

echo "▶ Caching config..."
php artisan config:cache

echo "▶ Caching routes..."
php artisan route:cache

echo "▶ Caching views..."
php artisan view:cache

echo "▶ Publishing Filament assets..."
php artisan filament:assets

echo "▶ Running migrations..."
php artisan migrate --force

echo "▶ Starting PHP-FPM..."
php-fpm -D

echo "▶ Starting Nginx..."
nginx -g "daemon off;"