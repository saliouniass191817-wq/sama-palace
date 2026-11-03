#!/bin/sh

set -u

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
php artisan storage:link --force || true
chown -R www-data:www-data storage bootstrap/cache public/storage 2>/dev/null || true

attempt=1
while [ "$attempt" -le 3 ]; do
    if php artisan migrate --force; then
        break
    fi

    echo "Database migrations failed on startup (attempt ${attempt}/3); Apache will still start." >&2

    if [ "$attempt" -lt 3 ]; then
        sleep 5
    fi

    attempt=$((attempt + 1))
done

exec apache2-foreground
