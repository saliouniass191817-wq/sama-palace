FROM php:8.2-apache

# Installer dépendances système + Node 20 + zip/unzip
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    gnupg \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Node.js 20 (IMPORTANT pour Vite 7)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Activer Apache modules
RUN a2enmod rewrite

# FIX conflit MPM
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copier projet
COPY . .

# Autoriser Composer root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer dépendances JS + build Vite
RUN npm install
RUN npm run build

# Vérification (debug utile si problème)
RUN ls -la public/build

# Permissions Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

# Nettoyage cache Laravel (important après config)
RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan view:clear

# Apache pointe vers public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]