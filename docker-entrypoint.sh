#!/bin/bash

# Wait for database to be available
echo "Waiting for database to be ready..."
until php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected'; } catch(Exception \$e) { exit(1); }" > /dev/null 2>&1; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Database is ready! Running migrations..."

# Run database migrations and seeders
php artisan migrate --force
php artisan db:seed --class=UserSeeder --force  
php artisan db:seed --class=ResolutionTeamSeeder --force

echo "Database setup complete!"

# Start Apache
exec apache2-foreground