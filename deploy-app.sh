#!/bin/bash

set -e

echo "Running Laravel deployment steps..."

# Clear caches first
php artisan cache:clear || echo "Cache clear failed, continuing..."
php artisan config:clear || echo "Config clear failed, continuing..."
php artisan route:clear || echo "Route clear failed, continuing..."
php artisan view:clear || echo "View clear failed, continuing..."

# Create SQLite database file if it doesn't exist
echo "Setting up database..."
if [ "$APP_ENV" = "production" ]; then
    DB_FILE="/app/database.sqlite"
    echo "Creating SQLite database at: $DB_FILE"
    mkdir -p $(dirname "$DB_FILE") 2>/dev/null || true
    touch "$DB_FILE"
    chmod 666 "$DB_FILE"
    ls -la "$DB_FILE"
    echo "Database file created successfully"
elif [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "Creating SQLite database at: $DB_DATABASE"
    mkdir -p $(dirname "$DB_DATABASE") 2>/dev/null || true
    touch "$DB_DATABASE"
    chmod 666 "$DB_DATABASE"
    ls -la "$DB_DATABASE"
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed essential data
echo "Seeding database..."
php artisan db:seed --class=UserSeeder --force || echo "User seeder failed, continuing..."
php artisan db:seed --class=ResolutionTeamSeeder --force || echo "ResolutionTeam seeder failed, continuing..."

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link || echo "Storage link already exists"

# Cache for production
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"