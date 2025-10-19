# --- Composer dependencies stage ---
FROM composer:2 AS vendor
WORKDIR /app
# Ensure Composer resolves deps against PHP 8.2 to match runtime and lock constraints
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PLATFORM_PHP=8.2.0
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader --ignore-platform-req=php

# --- Application runtime stage ---
FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

# System deps and PHP extensions
RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev zlib-dev bash git \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql zip opcache

# Copy application code
COPY . .

# Copy vendor from builder stage
COPY --from=vendor /app/vendor ./vendor

# Ensure correct permissions for runtime dirs
RUN chown -R www-data:www-data storage bootstrap/cache \
    && find storage -type d -exec chmod 775 {} + \
    && find storage -type f -exec chmod 664 {} + \
    && chmod -R 775 bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Default command
CMD ["php-fpm", "-F"]
