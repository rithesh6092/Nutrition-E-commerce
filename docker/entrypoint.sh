#!/bin/bash

# Set proper permissions
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! php artisan db:monitor --timeout=1 2>/dev/null; do
    sleep 1
done
echo "MySQL connection successful!"

# Run migrations if needed
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generate swagger documentation and publish assets
php artisan l5-swagger:generate
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider" --tag=assets --force

# Create storage link (with force flag to overwrite if exists)
php artisan storage:link --force

# Start the application
PORT=${PORT:-8000}
php artisan serve --host=0.0.0.0 --port=$PORT 