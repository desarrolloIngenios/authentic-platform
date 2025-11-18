#!/bin/sh

cd /var/www

# Asegurar permisos en directorios críticos
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan package:discover

/usr/bin/supervisord -c /etc/supervisord.conf
