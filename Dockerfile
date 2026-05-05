FROM php:8.2-apache

# Installer dépendances système + zip/unzip
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip

# Activer Apache modules nécessaires
RUN a2enmod rewrite

# FIX: éviter conflit MPM
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copier projet
COPY . .

# Éviter warning Composer en root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Build frontend (Vite)
RUN npm install && npm run build

# Permissions Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Apache pointe vers public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Port (Railway)
EXPOSE 8080

CMD ["apache2-foreground"]