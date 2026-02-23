FROM php:8.4-fpm-alpine

# ─── System dependencies ────────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite \
    sqlite-dev \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        bcmath \
        opcache \
        exif \
        pcntl

# ─── Composer ───────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ─── PHP config ─────────────────────────────────────────────────────────────
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# ─── Nginx config ───────────────────────────────────────────────────────────
COPY docker/nginx.conf /etc/nginx/nginx.conf

# ─── Supervisor config ──────────────────────────────────────────────────────
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ─── Application ────────────────────────────────────────────────────────────
WORKDIR /var/www/html

COPY . .

# Install PHP dependencies (no dev, optimised autoloader)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress

# Ensure the SQLite database file exists and directories are writable
RUN touch database/database.sqlite \
    && mkdir -p storage/framework/{cache,sessions,testing,views} \
               storage/logs \
               bootstrap/cache \
               /var/log/supervisor \
               /var/log/nginx \
               /var/run/nginx \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

# Create public storage symlink
RUN php artisan storage:link --no-interaction || true

# ─── Entrypoint ─────────────────────────────────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
