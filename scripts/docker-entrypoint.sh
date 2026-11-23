#!/bin/sh
set -eu
port="${PORT:-8080}"
sed -i "s/Listen 80/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${port}>/" /etc/apache2/sites-available/000-default.conf
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction
php artisan config:cache
php artisan view:cache
exec apache2-foreground
