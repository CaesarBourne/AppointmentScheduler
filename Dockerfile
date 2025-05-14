FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libicu-dev libonig-dev libzip-dev zip \
    libpq-dev libxml2-dev libjpeg-dev libpng-dev libfreetype6-dev \
    libmcrypt-dev libxslt1-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
WORKDIR /var/www/html
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html
