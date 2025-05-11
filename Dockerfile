# Stage 1: Composer dependencies
FROM composer:2.5 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Stage 2: Final container
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring zip exif pcntl bcmath

WORKDIR /var/www

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p database && touch database/database.sqlite
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD service nginx start && php-fpm