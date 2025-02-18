set -o errexit

#!/usr/bin/env bash
# echo "Running composer"
# composer install --ignore-platform-reqs --working-dir=/var/www/html
#
# echo "generating application key..."
# php artisan key:generate --show

npm install
npm run dev

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Publishing cloudinary provider..."
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider" --tag="cloudinary-laravel-config"
