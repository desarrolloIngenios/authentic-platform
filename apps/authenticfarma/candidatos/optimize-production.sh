#!/bin/bash
# Script para optimizar Laravel en producción

set -e

echo "🚀 Optimizando Laravel para producción..."

# Limpiar caches existentes
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimizar composer
composer dump-autoload --optimize --classmap-authoritative --no-dev

# Crear caches optimizados
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimizar storage
php artisan storage:link

echo "✅ Optimización completada!"
echo "📊 Verificar logs de performance en storage/logs/"
