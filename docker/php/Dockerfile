# Syntax: multi-stage build for optimized production images
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Development stage
FROM base AS development

# Install development dependencies
RUN apk add --no-cache \
    $PHPIZE_DEPS

# Install Xdebug (optional, for debugging)
# RUN pecl install xdebug && docker-php-ext-enable xdebug

# Create system user to run Composer and Artisan Commands
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy existing application directory
COPY --chown=www:www .. /var/www

# Install PHP dependencies
RUN composer install --no-interaction --no-scripts --prefer-dist

# Set permissions
RUN chown -R www:www /var/www && \
    chmod -R 755 /var/www/storage /var/www/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]

# Production stage
FROM base AS production

# Install production PHP extensions
RUN docker-php-ext-enable opcache

# Copy custom PHP configuration
COPY docker/php/php-production.ini /usr/local/etc/php/conf.d/custom.ini

# Create system user
RUN addgroup -g 1000 www && \
    adduser -u 1000 -G www -s /bin/sh -D www

# Copy application files
COPY --chown=www:www .. /var/www

# Install production dependencies
RUN composer install --no-dev --no-interaction --no-scripts --optimize-autoloader --prefer-dist

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set permissions
RUN chown -R www:www /var/www && \
    chmod -R 755 /var/www/storage /var/www/bootstrap/cache

USER www

EXPOSE 9000

CMD ["php-fpm"]

