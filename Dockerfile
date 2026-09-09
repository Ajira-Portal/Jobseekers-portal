FROM php:8.4-fpm-alpine AS app

WORKDIR /var/www/html

# Install runtime libraries and build dependencies for PHP extensions.
RUN apk add --no-cache \
        icu-libs \
        libzip \
        oniguruma \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        oniguruma-dev \
        linux-headers \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        mbstring \
        opcache \
        pdo_pgsql \
        pgsql \
        zip \
    && apk del .build-deps

# Copy Composer from the official Composer image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies first to take advantage of Docker layer caching.
COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader

# Copy application source files.
COPY --chown=www-data:www-data . .

# Ensure common Laravel writable directories have the correct permissions.
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Basic PHP production configuration.
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

FROM nginx:1.27-alpine

WORKDIR /var/www/html

# Nginx only needs Laravel's public directory for static files.
COPY public ./public

# Apply the Laravel virtual-host configuration.
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

