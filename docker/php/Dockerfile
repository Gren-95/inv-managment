FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    curl \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install zip pdo pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create composer.json
RUN echo '{"require": {"endroid/qr-code": "^4.8", "picqer/php-barcode-generator": "^2.4"}}' > composer.json

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Create temp directory with proper permissions
RUN mkdir -p /var/www/html/temp && \
    mkdir -p /var/www/html/assets && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

