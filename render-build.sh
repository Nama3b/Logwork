#!/usr/bin/env bash
set -o errexit  # Thoát ngay nếu có lỗi

# Cài PHP (nếu chưa có)
if ! command -v php &> /dev/null
then
    echo "PHP is not installed. Installing now..."
    sudo apt update && sudo apt install -y php-cli php-mbstring php-xml unzip
fi

# Cài Composer (nếu chưa có)
if ! command -v composer &> /dev/null
then
    echo "Composer is not installed. Installing now..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

# Cài Laravel dependencies
composer install --no-dev --optimize-autoloader

# Cài Node.js dependencies
npm install

# Build frontend
npm run build

# Cache config
php artisan config:cache
php artisan route:cache

# Chạy migration
php artisan migrate --force
