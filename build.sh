#!/bin/bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link

echo "Build completed successfully!"