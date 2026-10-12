FROM php:8.2-fpm

# System dependencies install karein
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# PHP extensions install karein
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer copy karein
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Project files copy karein
WORKDIR /var/www
COPY . .

# Composer dependencies install karein
RUN composer install --no-dev --optimize-autoloader

# Laravel permissions set karein
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Port expose karein aur server start karein
EXPOSE 80
CMD php artisan serve --host=0.0.0.0 --port=80
