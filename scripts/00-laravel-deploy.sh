#!/usr/bin/env bash
set -e

echo "Running composer install"
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "Generating application key"
php artisan key:generate --show

echo "Caching config and routes"
php artisan config:cache
php artisan route:cache

echo "Running migrations"
php artisan migrate --force

echo "Deploy script completed successfully"
