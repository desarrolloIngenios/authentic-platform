#!/bin/bash

# 🚀 Optimizaciones para Laravel - Candidatos
# Script para aplicar optimizaciones de performance

echo "🚀 Aplicando optimizaciones de performance para Laravel..."
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[APPLY]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[SKIP]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

CANDIDATOS_PATH="/Users/Devapp/authentic-platform/apps/authenticfarma/candidatos"

if [ ! -d "$CANDIDATOS_PATH" ]; then
    echo "❌ Directorio candidatos no encontrado: $CANDIDATOS_PATH"
    exit 1
fi

cd "$CANDIDATOS_PATH"

echo "📁 Trabajando en: $(pwd)"
echo ""

# 1. Optimización de Composer
info "🎯 1. Optimizando Composer Autoloader..."
if [ -f "composer.json" ]; then
    log "Ejecutando: composer dump-autoload --optimize --classmap-authoritative"
    # Note: En producción esto se ejecutaría como:
    # composer dump-autoload --optimize --classmap-authoritative --no-dev
    echo "   → Comando preparado para ejecutar en container"
else
    warn "composer.json no encontrado"
fi
echo ""

# 2. Optimización de configuración Laravel
info "🎯 2. Configuraciones de Cache Laravel..."

# Crear archivo de optimización de configuración
cat > optimize-config.php << 'EOF'
<?php
// Configuraciones de optimización para Laravel

return [
    // Cache de configuración
    'config_cache' => [
        'command' => 'php artisan config:cache',
        'description' => 'Cachear toda la configuración en un solo archivo'
    ],
    
    // Cache de rutas
    'route_cache' => [
        'command' => 'php artisan route:cache',
        'description' => 'Cachear todas las rutas registradas'
    ],
    
    // Cache de vistas
    'view_cache' => [
        'command' => 'php artisan view:cache',
        'description' => 'Pre-compilar todas las vistas Blade'
    ],
    
    // Optimización de eventos
    'event_cache' => [
        'command' => 'php artisan event:cache',
        'description' => 'Cachear eventos y listeners'
    ]
];
EOF

log "Creado archivo de configuración: optimize-config.php"
echo ""

# 3. Configuración de Session Driver
info "🎯 3. Verificando configuración de sesiones..."
if grep -q "SESSION_DRIVER=file" .env 2>/dev/null; then
    warn "Usando driver 'file' para sesiones - Considerar Redis"
elif grep -q "SESSION_DRIVER=redis" .env 2>/dev/null; then
    log "✅ Usando Redis para sesiones"
else
    warn "SESSION_DRIVER no especificado en .env"
fi
echo ""

# 4. Optimización de Base de Datos
info "🎯 4. Verificando configuración de Base de Datos..."
if [ -f "app/Models/User.php" ]; then
    log "Verificando modelos para eager loading..."
    
    # Buscar potenciales N+1 queries
    if grep -r "belongsTo\|hasMany\|hasOne" app/Models/ >/dev/null 2>&1; then
        log "Relaciones encontradas - verificar eager loading"
    fi
fi
echo ""

# 5. Optimización de Assets
info "🎯 5. Configuración de Assets..."
if [ -f "package.json" ]; then
    log "package.json encontrado"
    
    if [ -f "webpack.mix.js" ]; then
        log "Laravel Mix detectado"
        echo "   → Ejecutar: npm run production"
    fi
    
    if [ -f "vite.config.js" ]; then
        log "Vite detectado"
        echo "   → Ejecutar: npm run build"
    fi
else
    warn "package.json no encontrado"
fi
echo ""

# 6. Crear Dockerfile optimizado
info "🎯 6. Creando Dockerfile optimizado..."
cat > Dockerfile.optimized << 'EOF'
FROM php:8.1-fpm-alpine

# Instalar extensiones PHP para performance
RUN apk add --no-cache \
    nginx \
    redis \
    && docker-php-ext-install opcache pdo_mysql

# Configurar OPcache
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Configurar PHP-FPM para performance
COPY php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Optimizaciones Laravel en build time
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

EXPOSE 9000
CMD ["php-fpm"]
EOF

log "Creado: Dockerfile.optimized"

# Crear configuración OPcache
cat > opcache.ini << 'EOF'
[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=0
opcache.fast_shutdown=1
EOF

log "Creado: opcache.ini"

# Crear configuración PHP-FPM optimizada
cat > php-fpm.conf << 'EOF'
[www]
user = www-data
group = www-data
listen = 9000
listen.owner = www-data
listen.group = www-data

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.process_idle_timeout = 60s
pm.max_requests = 1000

; Optimizaciones de performance
request_terminate_timeout = 30
catch_workers_output = yes
EOF

log "Creado: php-fpm.conf"
echo ""

# 7. Configuración Nginx optimizada
info "🎯 7. Creando configuración Nginx optimizada..."
mkdir -p nginx
cat > nginx/nginx.conf << 'EOF'
server {
    listen 80;
    server_name candidatos.authenticfarma.com;
    root /var/www/html/public;
    index index.php;

    # Compresión gzip
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript;
    gzip_min_length 1000;

    # Cache de assets estáticos
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        add_header Vary Accept-Encoding;
        access_log off;
    }

    # Configuración PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass candidatos-php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Cache FastCGI
        fastcgi_cache_valid 200 60m;
        fastcgi_cache_bypass $cookie_laravel_session;
    }

    # Configuración Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Seguridad
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

log "Creado: nginx/nginx.conf"
echo ""

# 8. Configuración Redis
info "🎯 8. Configuración Redis para cache y sesiones..."
cat > redis-config.env << 'EOF'
# Configuración Redis para Laravel
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DATABASE=0

# Database específica para sesiones
REDIS_SESSION_CONNECTION=session
REDIS_SESSION_DATABASE=1

# Database para cache
REDIS_CACHE_CONNECTION=cache
REDIS_CACHE_DATABASE=2
EOF

log "Creado: redis-config.env"
echo ""

# 9. Docker Compose optimizado
info "🎯 9. Creando Docker Compose optimizado..."
cat > docker-compose.performance.yml << 'EOF'
version: '3.8'

services:
  candidatos-nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx/nginx.conf:/etc/nginx/conf.d/default.conf:ro
      - ./public:/var/www/html/public:ro
    depends_on:
      - candidatos-php
    networks:
      - candidatos-network

  candidatos-php:
    build:
      context: .
      dockerfile: Dockerfile.optimized
    volumes:
      - .:/var/www/html
    environment:
      - PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
      - PHP_OPCACHE_REVALIDATE_FREQ=0
    depends_on:
      - redis
      - mysql
    networks:
      - candidatos-network

  redis:
    image: redis:7-alpine
    command: redis-server --maxmemory 256mb --maxmemory-policy allkeys-lru
    volumes:
      - redis_data:/data
    networks:
      - candidatos-network

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: candidatos
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - mysql_data:/var/lib/mysql
    command: --default-authentication-plugin=mysql_native_password --innodb-buffer-pool-size=256M
    networks:
      - candidatos-network

volumes:
  redis_data:
  mysql_data:

networks:
  candidatos-network:
    driver: bridge
EOF

log "Creado: docker-compose.performance.yml"
echo ""

# 10. Script de optimización para producción
info "🎯 10. Creando script de optimización para producción..."
cat > optimize-production.sh << 'EOF'
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
EOF

chmod +x optimize-production.sh
log "Creado script ejecutable: optimize-production.sh"
echo ""

# Resumen
echo "📊 RESUMEN DE OPTIMIZACIONES CREADAS:"
echo ""
log "✅ Dockerfile optimizado con OPcache y PHP-FPM"
log "✅ Configuración Nginx con compresión y cache"
log "✅ Configuración Redis para cache y sesiones"
log "✅ Docker Compose para stack completo optimizado"
log "✅ Scripts de optimización Laravel"
echo ""

echo "🚀 SIGUIENTE PASOS:"
echo "1. Revisar configuraciones generadas"
echo "2. Aplicar en container de producción:"
echo "   kubectl exec -it <pod-candidatos> -- /var/www/html/optimize-production.sh"
echo "3. Rebuilder imagen con Dockerfile.optimized"
echo "4. Configurar Redis como backend de cache"
echo "5. Monitorear métricas post-optimización"
echo ""

echo "🕒 Optimizaciones preparadas: $(date)"