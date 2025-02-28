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
    libmysqlclient-dev \
    && apt-get clean

# Install necessary PHP build dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql

# Install the GD extension for image processing
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Install Composer (the PHP dependency manager)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /var/www

# Copy the entire application to the container
COPY . .

# Install Laravel dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 8000 (the default port for Laravel using `php artisan serve`)
EXPOSE 8000

# Command to start the Laravel application using Artisan
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
