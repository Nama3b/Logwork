set -o errexit

#!/usr/bin/env bash
echo "Running composer"
composer clear-cache
composer install --no-dev --optimize-autoloader --prefer-source

npm install
npm run dev

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force
