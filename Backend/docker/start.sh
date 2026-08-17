#!/bin/bash
set -e

# Railway provides $PORT at runtime; rewrite nginx to listen on it
if [ -n "$PORT" ]; then
    sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf
fi

# Generate app key if not already set (safe to run repeatedly)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config/routes/views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations automatically on deploy (remove if you prefer manual control)
php artisan migrate --force

# Start php-fpm + nginx via supervisord
exec supervisord -c /etc/supervisor/supervisord.conf
