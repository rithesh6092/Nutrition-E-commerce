# Use PHP 8.3-fpm image as the base image
FROM php:8.3-fpm

# Install system dependencies and PHP extensions needed for Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    unzip \
    libxml2-dev \
    libcurl4-openssl-dev \
    libmariadb-dev-compat \
    libonig-dev \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath

# Install the GD extension for image processing
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Ensure public directory is writable
RUN mkdir -p /var/www/public/vendor && \
    chown -R www-data:www-data /var/www/public && \
    chmod -R 775 /var/www/public

# Copy and set up entrypoint
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
