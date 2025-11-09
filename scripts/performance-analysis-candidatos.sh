#!/bin/bash

# 🚀 Análisis de Performance - Aplicación Candidatos
# Diagnóstico completo de rendimiento

echo "🚀 Analizando rendimiento de candidatos en producción..."
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

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

perf() {
    echo -e "${PURPLE}[PERF]${NC} $1"
}

BASE_URL="https://candidatos.authenticfarma.com"

# Función para medir performance detallada
measure_performance() {
    local url=$1
    local description=$2
    local num_tests=5
    
    info "Midiendo: $description ($num_tests pruebas)"
    
    local total_time=0
    local min_time=999
    local max_time=0
    local failed_requests=0
    
    for i in $(seq 1 $num_tests); do
        response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}|%{time_namelookup}|%{time_connect}|%{time_starttransfer}|%{size_download}" "$url" --max-time 30)
        
        if [ $? -eq 0 ]; then
            status_code=$(echo $response | cut -d'|' -f1)
            time_total=$(echo $response | cut -d'|' -f2)
            time_dns=$(echo $response | cut -d'|' -f3)
            time_connect=$(echo $response | cut -d'|' -f4)
            time_ttfb=$(echo $response | cut -d'|' -f5)
            size_download=$(echo $response | cut -d'|' -f6)
            
            if [[ $status_code -ge 200 && $status_code -lt 400 ]]; then
                total_time=$(echo "$total_time + $time_total" | bc -l)
                
                # Calcular min/max
                if (( $(echo "$time_total < $min_time" | bc -l) )); then
                    min_time=$time_total
                fi
                if (( $(echo "$time_total > $max_time" | bc -l) )); then
                    max_time=$time_total
                fi
                
                echo "   Test $i: ${time_total}s (DNS: ${time_dns}s, Connect: ${time_connect}s, TTFB: ${time_ttfb}s, Size: ${size_download} bytes)"
            else
                failed_requests=$((failed_requests + 1))
                error "   Test $i: HTTP $status_code"
            fi
        else
            failed_requests=$((failed_requests + 1))
            error "   Test $i: Request failed (timeout/error)"
        fi
    done
    
    if [ $failed_requests -lt $num_tests ]; then
        local avg_time=$(echo "scale=3; $total_time / ($num_tests - $failed_requests)" | bc -l)
        
        perf "📊 Estadísticas: $description"
        echo "     Promedio: ${avg_time}s"
        echo "     Mínimo:   ${min_time}s"
        echo "     Máximo:   ${max_time}s"
        echo "     Fallos:   $failed_requests/$num_tests"
        
        # Evaluación de performance
        if (( $(echo "$avg_time < 1.0" | bc -l) )); then
            log "     ✅ Performance EXCELENTE"
        elif (( $(echo "$avg_time < 2.0" | bc -l) )); then
            warn "     ⚠️  Performance ACEPTABLE"
        elif (( $(echo "$avg_time < 5.0" | bc -l) )); then
            warn "     🐌 Performance LENTA"
        else
            error "     🚨 Performance MUY LENTA"
        fi
    else
        error "     ❌ Todos los tests fallaron"
    fi
    
    echo ""
}

# Tests de performance por endpoint
echo "🔍 Tests de Performance Detallados:"
measure_performance "$BASE_URL" "Página Principal"
measure_performance "$BASE_URL/login" "Página Login"
measure_performance "$BASE_URL/register" "Página Registro"

# Test de carga de recursos estáticos
info "🎨 Analizando recursos estáticos..."
static_response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}" "$BASE_URL/css/app.css" 2>/dev/null)
if [ $? -eq 0 ]; then
    static_code=$(echo $static_response | cut -d'|' -f1)
    static_time=$(echo $static_response | cut -d'|' -f2)
    
    if [[ $static_code -eq 200 ]]; then
        log "CSS estático carga en ${static_time}s"
    else
        warn "CSS estático retorna HTTP $static_code"
    fi
else
    warn "No se pudo acceder a recursos CSS"
fi

# Análisis de headers HTTP para optimización
info "📋 Analizando headers de optimización..."
headers=$(curl -s -I "$BASE_URL" | grep -E "(Cache-Control|Expires|ETag|Last-Modified|Content-Encoding)")

if echo "$headers" | grep -q "gzip\|br"; then
    log "✅ Compresión activada"
else
    warn "⚠️  Compresión no detectada"
fi

if echo "$headers" | grep -q "Cache-Control"; then
    log "✅ Headers de cache presentes"
else
    warn "⚠️  Headers de cache no optimizados"
fi

# Test de DNS
info "🌐 Analizando DNS..."
dns_time=$(dig +short +time=1 candidatos.authenticfarma.com @8.8.8.8 | tail -1)
if [ $? -eq 0 ]; then
    log "DNS resuelve correctamente"
else
    warn "Posible problema con resolución DNS"
fi

# Recommendations basadas en análisis
echo ""
echo "📋 RECOMENDACIONES DE OPTIMIZACIÓN:"
echo ""

echo "🚀 BACKEND (Laravel):"
echo "   1. Activar cache de aplicación: php artisan config:cache"
echo "   2. Activar cache de rutas: php artisan route:cache"
echo "   3. Optimizar autoloader: composer dump-autoload --optimize"
echo "   4. Usar Redis/Memcached para sesiones"
echo "   5. Activar OPcache en PHP"
echo ""

echo "🗄️  BASE DE DATOS:"
echo "   1. Revisar queries N+1 con eager loading"
echo "   2. Añadir índices a columnas frecuentemente consultadas"
echo "   3. Implementar query caching"
echo "   4. Usar connection pooling"
echo ""

echo "🌐 FRONTEND:"
echo "   1. Minificar CSS y JS: npm run production"
echo "   2. Implementar lazy loading de imágenes"
echo "   3. Usar CDN para assets estáticos"
echo "   4. Optimizar imágenes (WebP, compresión)"
echo ""

echo "☁️  INFRAESTRUCTURA:"
echo "   1. Configurar HTTP/2 en ingress"
echo "   2. Activar compresión gzip/brotli"
echo "   3. Configurar cache headers apropiados"
echo "   4. Implementar HTTP caching (Varnish/CloudFlare)"
echo "   5. Horizontal Pod Autoscaling (HPA)"
echo ""

echo "📊 MONITORING:"
echo "   1. Implementar APM (New Relic, Datadog)"
echo "   2. Configurar alertas de performance"
echo "   3. Logs de queries lentas"
echo "   4. Métricas de respuesta por endpoint"
echo ""

# Timestamp
echo "🕒 Análisis completado: $(date)"
echo "🔗 URL analizada: $BASE_URL"