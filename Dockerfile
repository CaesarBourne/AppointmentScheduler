# Use official PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    opcache \
    && a2enmod rewrite

# Install Composer (as non-root user)
RUN useradd -d /home/developer -m developer && \
    mkdir -p /home/developer/.composer && \
    chown -R developer:developer /home/developer

USER developer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/home/developer/.composer

# Copy composer files
COPY --chown=developer:developer composer.json composer.lock ./

# Install PHP dependencies (no dev for production)
RUN composer install --prefer-dist --no-autoloader --no-scripts --no-dev

# Copy application
COPY --chown=developer:developer . .

# Ensure var directory exists with correct permissions
RUN mkdir -p var/cache var/log && \
    chmod -R 777 var

RUN composer dump-autoload --optimize

# Switch back to root for Apache
USER root

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    chmod -R 777 var

# Environment variables
ENV APP_ENV=dev
ENV APP_DEBUG=1
ENV DATABASE_URL="mysql://admin:HereiterNu%410@symfony.cnauwe826vmx.eu-north-1.rds.amazonaws.com:3306/symfony"

# Expose port 80
EXPOSE 80