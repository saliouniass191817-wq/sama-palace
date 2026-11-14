#!/bin/sh

set -eu

export PGCONNECT_TIMEOUT="${PGCONNECT_TIMEOUT:-5}"

port="${PORT:-10000}"

case "$port" in
    ''|*[!0-9]*)
        echo "PORT must contain only digits; received an invalid value." >&2
        exit 1
        ;;
esac

sed -i -E "s/^Listen [0-9]+$/Listen ${port}/" /etc/apache2/ports.conf
sed -i -E "s#<VirtualHost \*:[0-9]+>#<VirtualHost *:${port}>#" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/app/public bootstrap/cache
php artisan storage:link --force
chown -R www-data:www-data storage bootstrap/cache public/storage
php artisan migrate --force --isolated
php artisan optimize

exec apache2-foreground
