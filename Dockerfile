FROM dunglas/frankenphp:php8.2.30-bookworm

# Install MySQL driver
RUN install-php-extensions pdo_mysql

# Copy application
COPY . /app

# Set working directory
WORKDIR /app

# Install dependencies
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Build frontend
RUN npm install && npm run build

# Cache Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Start command
CMD ["/start-container.sh"]
