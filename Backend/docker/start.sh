set -e

if [ -n "$PORT" ]; then
    sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/nginx.conf
fi

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan storage:unlink && php artisan storage:link --force

php artisan migrate --force

exec supervisord -c /etc/supervisor/supervisord.conf
