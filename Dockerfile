FROM node:22-alpine AS assets

WORKDIR /app

COPY package*.json vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm install
RUN npm run build

FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize

FROM dunglas/frankenphp:1-php8.2-bookworm

RUN install-php-extensions \
    mbstring \
    pdo_mysql \
    zip \
    opcache

WORKDIR /app

COPY --from=vendor /app .
COPY --from=assets /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD php artisan config:cache && php artisan migrate --force && SERVER_NAME=":${PORT:-80}" frankenphp run --config /etc/caddy/Caddyfile