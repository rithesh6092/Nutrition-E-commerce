#!/bin/bash

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Wait for database if needed
# php artisan wait:db

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate swagger documentation and publish assets
php artisan l5-swagger:generate
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Create storage link (with force flag to overwrite if exists)
php artisan storage:link --force

# Enable error reporting in .env
sed -i 's/APP_DEBUG=false/APP_DEBUG=true/g' .env

# Start the application
# Use PORT from environment variable or default to 8000
PORT=${PORT:-8000}
php artisan serve --host=0.0.0.0 --port=$PORT 