# Stage 1: Build Vite frontend assets
FROM node:24-alpine AS frontend

WORKDIR /var/www/html

# Copy package files first for caching
COPY package*.json ./

# Install Node dependencies
RUN npm ci

# Copy files required for Vite
COPY resources ./resources
COPY vite.config.* ./
COPY public ./public

# Build Vite assets
RUN npm run build


# Stage 2: PHP/Laravel application
FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

# Install runtime libraries and build dependencies for PHP extensions
RUN apk add --no-cache \
        icu-libs \
        libzip \
        oniguruma \
        postgresql-client \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        postgresql-dev \
        linux-headers \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mbstring \
        opcache \
        pdo_pgsql \
        pgsql \
        zip \
    && apk del .build-deps

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --no-scripts \
        --optimize-autoloader

# Copy Laravel application
COPY --chown=www-data:www-data . .

# Copy the ONE Vite build created in Stage 1
COPY --from=frontend --chown=www-data:www-data \
    /var/www/html/public/build \
    /var/www/html/public/build

# Laravel package discovery
RUN php artisan package:discover --ansi

# Create writable Laravel directories
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Production PHP configuration
RUN { \
        echo 'memory_limit=256M'; \
        echo 'upload_max_filesize=20M'; \
        echo 'post_max_size=20M'; \
        echo 'max_execution_time=60'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.memory_consumption=128'; \
    } > /usr/local/etc/php/conf.d/production.ini

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
