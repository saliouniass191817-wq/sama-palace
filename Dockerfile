FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev curl gnupg \
    && docker-php-ext-install pdo pdo_pgsql zip

# Node 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN a2enmod rewrite
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod 755 /usr/local/bin/docker-entrypoint.sh

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

RUN npm ci
RUN npm run build

RUN mkdir -p storage/app/public \
    && php artisan storage:link --force

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# IMPORTANT : pas de cache:clear ici
RUN php artisan config:clear
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

CMD ["/usr/local/bin/docker-entrypoint.sh"]
