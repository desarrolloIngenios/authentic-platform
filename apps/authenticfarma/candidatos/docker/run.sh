#!/bin/sh


cd /var/www

# Asegurar que los directorios de cache existen y tienen permisos correctos
mkdir -p storage/framework/views storage/framework/cache/data bootstrap/cache
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# php artisan migrate:fresh --seed
# Esperar a que el proxy de Cloud SQL acepte conexiones
for i in $(seq 1 45); do
	(bash -lc "</dev/tcp/127.0.0.1/3306" >/dev/null 2>&1) && break
	sleep 2
done

php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan package:discover

/usr/bin/supervisord -c /etc/supervisord.conf
