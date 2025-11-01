#!/bin/sh

cd /var/www

# php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan package:discover

/usr/bin/supervisord -c /etc/supervisord.conf
