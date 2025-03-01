#!/bin/bash

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

# Create storage link
php artisan storage:link

# Start the application
php artisan serve --host=0.0.0.0 --port=$PORT 