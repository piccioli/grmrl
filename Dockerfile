# Stage 1: build asset frontend con Vite
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: immagine PHP-FPM di produzione
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libicu-dev \
    unzip \
    curl \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    gd \
    intl \
    opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=node-builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
