FROM php:8.2-apache

RUN a2dismod mpm_event && \
    a2enmod mpm_prefork rewrite

RUN docker-php-ext-install pdo pdo_mysql

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD composer install --no-dev --optimize-autoloader && \
    php artisan key:generate --force && \
    php artisan config:cache && \
    php artisan migrate --force && \
    apache2-foreground