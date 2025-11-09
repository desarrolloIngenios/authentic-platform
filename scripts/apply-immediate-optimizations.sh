#!/bin/bash

# 🚀 Aplicación Inmediata de Optimizaciones - Candidatos
# Ejecutar directamente en el container de producción

echo "🚀 Aplicando optimizaciones INMEDIATAS en container de producción..."
echo "🕒 $(date)"
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[OK]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# Verificar si estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encuentra artisan. ¿Estás en el directorio correcto de Laravel?"
    exit 1
fi

info "📍 Directorio actual: $(pwd)"

# 1. LIMPIAR CACHES EXISTENTES
info "🧹 1. Limpiando caches existentes..."
php artisan cache:clear && log "Cache de aplicación limpiado" || warn "Error limpiando cache"
php artisan config:clear && log "Cache de configuración limpiado" || warn "Error limpiando config"
php artisan route:clear && log "Cache de rutas limpiado" || warn "Error limpiando rutas"
php artisan view:clear && log "Cache de vistas limpiado" || warn "Error limpiando vistas"
echo ""

# 2. OPTIMIZAR AUTOLOADER
info "🔧 2. Optimizando Composer Autoloader..."
if command -v composer &> /dev/null; then
    composer dump-autoload --optimize && log "Autoloader optimizado" || warn "Error optimizando autoloader"
else
    warn "Composer no encontrado - saltar optimización autoloader"
fi
echo ""

# 3. CREAR CACHES OPTIMIZADOS
info "⚡ 3. Creando caches optimizados..."
php artisan config:cache && log "Cache de configuración creado" || warn "Error creando cache config"
php artisan route:cache && log "Cache de rutas creado" || warn "Error creando cache rutas"
php artisan view:cache && log "Cache de vistas creado" || warn "Error creando cache vistas"

# Verificar si event:cache está disponible
if php artisan list | grep -q "event:cache"; then
    php artisan event:cache && log "Cache de eventos creado" || warn "Error creando cache eventos"
else
    info "event:cache no disponible en esta versión de Laravel"
fi
echo ""

# 4. OPTIMIZAR CONFIGURACIÓN DE SESIONES
info "🔒 4. Verificando configuración de sesiones..."
if grep -q "SESSION_DRIVER=file" .env 2>/dev/null; then
    warn "Usando driver 'file' para sesiones"
    info "💡 Recomendación: Cambiar a Redis para mejor performance"
    echo "   Agregar a .env: SESSION_DRIVER=redis"
elif grep -q "SESSION_DRIVER=redis" .env 2>/dev/null; then
    log "✅ Usando Redis para sesiones"
else
    warn "SESSION_DRIVER no especificado - usando default"
fi
echo ""

# 5. VERIFICAR CONFIGURACIÓN DE CACHE
info "💾 5. Verificando configuración de cache..."
if grep -q "CACHE_DRIVER=redis" .env 2>/dev/null; then
    log "✅ Usando Redis para cache"
elif grep -q "CACHE_DRIVER=file" .env 2>/dev/null; then
    warn "Usando cache de archivos"
    info "💡 Recomendación: Cambiar a Redis"
    echo "   Agregar a .env: CACHE_DRIVER=redis"
else
    warn "CACHE_DRIVER no especificado"
fi
echo ""

# 6. VERIFICAR QUEUE CONFIGURATION
info "📤 6. Verificando configuración de colas..."
if grep -q "QUEUE_CONNECTION=redis" .env 2>/dev/null; then
    log "✅ Usando Redis para colas"
elif grep -q "QUEUE_CONNECTION=database" .env 2>/dev/null; then
    warn "Usando database para colas"
    info "💡 Para mejor performance, considerar Redis"
else
    info "Usando sync queue (desarrollo)"
fi
echo ""

# 7. VERIFICAR Y OPTIMIZAR STORAGE
info "📁 7. Optimizando storage..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link && log "Storage link creado" || warn "Error creando storage link"
else
    log "Storage link ya existe"
fi

# Verificar permisos de storage
if [ -w "storage/logs" ] && [ -w "storage/framework" ]; then
    log "Permisos de storage correctos"
else
    warn "Verificar permisos de directorio storage/"
fi
echo ""

# 8. GENERAR REPORT DE CONFIGURACIÓN
info "📊 8. Generando reporte de configuración..."

echo "📋 CONFIGURACIÓN ACTUAL:" > optimization-report.txt
echo "=========================" >> optimization-report.txt
echo "Fecha: $(date)" >> optimization-report.txt
echo "Laravel Version: $(php artisan --version)" >> optimization-report.txt
echo "PHP Version: $(php -v | head -n1)" >> optimization-report.txt
echo "" >> optimization-report.txt

# App environment
echo "ENVIRONMENT:" >> optimization-report.txt
grep -E "APP_ENV|APP_DEBUG" .env 2>/dev/null >> optimization-report.txt || echo "Variables de entorno no accesibles" >> optimization-report.txt
echo "" >> optimization-report.txt

# Cache config
echo "CACHE DRIVERS:" >> optimization-report.txt
grep -E "CACHE_DRIVER|SESSION_DRIVER|QUEUE_CONNECTION" .env 2>/dev/null >> optimization-report.txt || echo "Configuración de cache no accesible" >> optimization-report.txt
echo "" >> optimization-report.txt

# Cache status
echo "CACHE STATUS:" >> optimization-report.txt
if [ -f "bootstrap/cache/config.php" ]; then
    echo "✅ Config cache: ACTIVO" >> optimization-report.txt
else
    echo "❌ Config cache: INACTIVO" >> optimization-report.txt
fi

if [ -f "bootstrap/cache/routes-v7.php" ] || [ -f "bootstrap/cache/routes.php" ]; then
    echo "✅ Route cache: ACTIVO" >> optimization-report.txt
else
    echo "❌ Route cache: INACTIVO" >> optimization-report.txt
fi

if [ -d "storage/framework/views" ] && [ "$(ls -A storage/framework/views 2>/dev/null)" ]; then
    echo "✅ View cache: ACTIVO" >> optimization-report.txt
else
    echo "❌ View cache: INACTIVO" >> optimization-report.txt
fi

log "Reporte guardado en: optimization-report.txt"
echo ""

# 9. TEST DE PERFORMANCE POST-OPTIMIZACIÓN
info "🚀 9. Test rápido de performance..."
start_time=$(date +%s.%N)
php artisan route:list >/dev/null 2>&1
end_time=$(date +%s.%N)
route_time=$(echo "$end_time - $start_time" | bc -l)

if (( $(echo "$route_time < 1.0" | bc -l) )); then
    log "Tiempo de carga de rutas: ${route_time}s ✅"
else
    warn "Tiempo de carga de rutas: ${route_time}s (lento)"
fi
echo ""

# 10. RECOMENDACIONES FINALES
info "📋 RECOMENDACIONES ADICIONALES:"
echo ""
echo "🔧 CONFIGURACIÓN .ENV ÓPTIMA:"
echo "   APP_ENV=production"
echo "   APP_DEBUG=false"
echo "   CACHE_DRIVER=redis"
echo "   SESSION_DRIVER=redis"
echo "   QUEUE_CONNECTION=redis"
echo ""

echo "⚡ OPTIMIZACIONES DE SERVIDOR:"
echo "   1. Activar OPcache de PHP"
echo "   2. Configurar HTTP/2 en Nginx"
echo "   3. Activar compresión gzip/brotli"
echo "   4. Configurar cache headers"
echo ""

echo "📊 MONITOREO:"
echo "   1. Revisar logs: tail -f storage/logs/laravel.log"
echo "   2. Monitorear memoria PHP"
echo "   3. Verificar uso de Redis"
echo ""

# SUMMARY
echo "✅ OPTIMIZACIÓN COMPLETADA"
echo "📊 Ver reporte completo en: optimization-report.txt"
echo "🕒 Tiempo total: $(($(date +%s) - $(date +%s))) segundos"
echo ""
echo "🎯 SIGUIENTE: Monitorear performance y aplicar optimizaciones adicionales según métricas"