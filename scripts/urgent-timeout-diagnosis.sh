#!/bin/bash

# 🚨 DIAGNÓSTICO URGENTE - Aplicación No Responde
# Análisis de timeouts y bloqueos en candidatos

echo "🚨 DIAGNÓSTICO URGENTE - Aplicación candidatos no responde"
echo "📅 $(date)"
echo ""

# Colores
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

error() {
    echo -e "${RED}[CRÍTICO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[ALERTA]${NC} $1"
}

log() {
    echo -e "${GREEN}[OK]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

test_critical() {
    echo -e "${PURPLE}[TEST]${NC} $1"
}

BASE_URL="https://candidatos.authenticfarma.com"

# 1. TESTS DE TIMEOUT INMEDIATOS
echo "🚨 1. TESTS DE TIMEOUT CRÍTICOS"
echo ""

test_endpoints_with_timeout() {
    local url=$1
    local description=$2
    local timeout=$3
    
    test_critical "Probando: $description (timeout ${timeout}s)"
    
    start_time=$(date +%s.%N)
    response=$(timeout ${timeout} curl -s -o /dev/null -w "%{http_code}|%{time_total}|%{time_connect}|%{time_starttransfer}" "$url" 2>/dev/null)
    exit_code=$?
    end_time=$(date +%s.%N)
    total_time=$(echo "$end_time - $start_time" | bc -l)
    
    if [ $exit_code -eq 124 ]; then
        error "❌ TIMEOUT después de ${timeout}s - APLICACIÓN COLGADA"
        return 1
    elif [ $exit_code -ne 0 ]; then
        error "❌ ERROR DE CONEXIÓN (código: $exit_code)"
        return 1
    else
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2)
        time_connect=$(echo $response | cut -d'|' -f3)
        time_ttfb=$(echo $response | cut -d'|' -f4)
        
        if [[ $status_code -ge 200 && $status_code -lt 400 ]]; then
            if (( $(echo "$time_total > 10.0" | bc -l) )); then
                error "⚠️ RESPUESTA MUY LENTA: ${time_total}s (TTFB: ${time_ttfb}s)"
            elif (( $(echo "$time_total > 5.0" | bc -l) )); then
                warn "⚠️ RESPUESTA LENTA: ${time_total}s"
            else
                log "✅ Responde en ${time_total}s"
            fi
            return 0
        else
            error "❌ HTTP $status_code"
            return 1
        fi
    fi
}

# Tests progresivos con timeouts incrementales
timeouts=(5 10 15 30)
endpoints=(
    "$BASE_URL/:Página principal"
    "$BASE_URL/login:Página login"
    "$BASE_URL/dashboard:Dashboard"
    "$BASE_URL/vacant:Lista vacantes"
)

for timeout in "${timeouts[@]}"; do
    info "🕐 Testing con timeout de ${timeout} segundos..."
    
    all_working=true
    for endpoint_info in "${endpoints[@]}"; do
        IFS=':' read -r url description <<< "$endpoint_info"
        
        if ! test_endpoints_with_timeout "$url" "$description" "$timeout"; then
            all_working=false
        fi
        echo ""
    done
    
    if $all_working; then
        log "✅ Todas las rutas responden dentro de ${timeout}s"
        break
    else
        warn "⚠️ Algunos endpoints fallan con timeout ${timeout}s"
    fi
    
    echo ""
done

echo ""

# 2. ANÁLISIS DE BLOQUEOS ESPECÍFICOS
echo "🔍 2. ANÁLISIS DE TIPOS DE BLOQUEO"
echo ""

info "Probando rutas específicas que pueden causar bloqueo..."

# Rutas que típicamente causan problemas en Laravel
problematic_routes=(
    "$BASE_URL/candidate/vacant:Lista de vacantes (puede tener N+1 queries)"
    "$BASE_URL/profile:Perfil de usuario (carga relaciones)"
    "$BASE_URL/postulation:Postulaciones (joins complejos)"
    "$BASE_URL/dashboard:Dashboard (múltiples consultas)"
)

for route_info in "${problematic_routes[@]}"; do
    IFS=':' read -r route description <<< "$route_info"
    
    test_critical "Ruta problemática: $description"
    
    # Test con timeout corto para detectar bloqueos rápidamente
    start=$(date +%s.%N)
    if timeout 8 curl -s -o /dev/null -w "%{http_code}" "$route" >/dev/null 2>&1; then
        end=$(date +%s.%N)
        time=$(echo "$end - $start" | bc -l)
        
        if (( $(echo "$time > 5.0" | bc -l) )); then
            error "🐌 MUY LENTO: ${time}s - Posible problema de base de datos"
        elif (( $(echo "$time > 2.0" | bc -l) )); then
            warn "⚠️ LENTO: ${time}s"
        else
            log "✅ Normal: ${time}s"
        fi
    else
        error "❌ TIMEOUT o ERROR en ruta crítica"
    fi
done

echo ""

# 3. DETECCIÓN DE PROBLEMAS ESPECÍFICOS
echo "🔍 3. DETECCIÓN DE PROBLEMAS ESPECÍFICOS"
echo ""

info "Verificando tipos de error comunes..."

# Test de memory limit / PHP timeout
test_critical "Test de PHP timeout/memory..."
php_test_response=$(timeout 15 curl -s -w "%{http_code}" "$BASE_URL/login" 2>/dev/null)
if [ $? -eq 124 ]; then
    error "❌ Posible PHP timeout o memory limit alcanzado"
elif [[ "$php_test_response" == "500" ]]; then
    error "❌ Error 500 - Revisar logs de PHP/Laravel"
elif [[ "$php_test_response" == "502" ]]; then
    error "❌ Bad Gateway - Problema con PHP-FPM o conectividad"
elif [[ "$php_test_response" == "504" ]]; then
    error "❌ Gateway Timeout - Nginx timeout o PHP timeout"
else
    log "✅ No hay errores evidentes de servidor"
fi

# Test de base de datos
test_critical "Test de conectividad base de datos..."
db_test=$(timeout 10 curl -s "$BASE_URL/login" | grep -i "database\|connection\|mysql\|sql" | wc -l)
if [ "$db_test" -gt 0 ]; then
    error "❌ Posible problema de base de datos detectado"
else
    log "✅ No hay mensajes evidentes de error de BD"
fi

# Test de recursos estáticos
test_critical "Test de recursos estáticos..."
css_response=$(timeout 5 curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/css/app.css" 2>/dev/null)
if [[ "$css_response" != "200" ]]; then
    warn "⚠️ Recursos CSS pueden estar causando bloqueos (HTTP $css_response)"
fi

js_response=$(timeout 5 curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/js/app.js" 2>/dev/null)
if [[ "$js_response" != "200" ]]; then
    warn "⚠️ Recursos JS pueden estar causando bloqueos (HTTP $js_response)"
fi

echo ""

# 4. RECOMENDACIONES INMEDIATAS
echo "🚨 4. ACCIONES INMEDIATAS RECOMENDADAS"
echo ""

error "PROBLEMA CRÍTICO DETECTADO - Aplicación no responde consistentemente"
echo ""

echo "📋 ACCIONES INMEDIATAS:"
echo ""

echo "🔥 PRIORIDAD ALTA (EJECUTAR YA):"
echo "1. Verificar logs del pod:"
echo "   kubectl logs -f <pod-candidatos> -n authenticfarma-prod --tail=100"
echo ""

echo "2. Verificar recursos del pod:"
echo "   kubectl top pods -n authenticfarma-prod"
echo "   kubectl describe pod <pod-candidatos> -n authenticfarma-prod"
echo ""

echo "3. Reiniciar pod si está colgado:"
echo "   kubectl delete pod <pod-candidatos> -n authenticfarma-prod"
echo "   # Kubernetes recreará automáticamente"
echo ""

echo "🔍 DIAGNÓSTICO (EJECUTAR PARALELO):"
echo "4. Verificar logs de Laravel:"
echo "   kubectl exec <pod-candidatos> -n authenticfarma-prod -- tail -f /var/www/html/storage/logs/laravel.log"
echo ""

echo "5. Verificar memoria y CPU:"
echo "   kubectl exec <pod-candidatos> -n authenticfarma-prod -- bash -c 'free -h && ps aux --sort=-%mem | head -10'"
echo ""

echo "6. Verificar conexiones de base de datos:"
echo "   kubectl exec <pod-candidatos> -n authenticfarma-prod -- bash -c 'netstat -an | grep 3306'"
echo ""

echo "⚡ OPTIMIZACIÓN RÁPIDA (SI EL POD FUNCIONA):"
echo "7. Limpiar caches que pueden estar causando bloqueos:"
echo "   kubectl exec <pod-candidatos> -n authenticfarma-prod -- bash -c '"
echo "   cd /var/www/html"
echo "   php artisan cache:clear"
echo "   php artisan view:clear"
echo "   php artisan route:clear"
echo "   '"
echo ""

# 5. CREAR SCRIPT DE MONITOREO CONTINUO
echo "📊 5. CREANDO SCRIPT DE MONITOREO CONTINUO..."

cat > monitor-app-health.sh << 'EOF'
#!/bin/bash
# Monitoreo continuo de salud de candidatos

echo "🔍 Iniciando monitoreo continuo de candidatos..."
echo "Presiona Ctrl+C para detener"
echo ""

while true; do
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    # Test rápido de respuesta
    response=$(timeout 8 curl -s -o /dev/null -w "%{http_code}|%{time_total}" "https://candidatos.authenticfarma.com/login" 2>/dev/null)
    
    if [ $? -eq 124 ]; then
        echo "[$timestamp] ❌ TIMEOUT - Aplicación no responde"
    elif [ $? -ne 0 ]; then
        echo "[$timestamp] ❌ ERROR DE CONEXIÓN"
    else
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2)
        
        if [[ $status_code -ge 200 && $status_code -lt 400 ]]; then
            if (( $(echo "$time_total > 5.0" | bc -l) )); then
                echo "[$timestamp] ⚠️ LENTO: ${time_total}s"
            else
                echo "[$timestamp] ✅ OK: ${time_total}s"
            fi
        else
            echo "[$timestamp] ❌ HTTP $status_code"
        fi
    fi
    
    sleep 10
done
EOF

chmod +x monitor-app-health.sh
log "✅ Script de monitoreo creado: monitor-app-health.sh"

echo ""
echo "🎯 RESUMEN:"
echo "La aplicación presenta problemas de timeout/bloqueo"
echo "Ejecutar acciones inmediatas listadas arriba"
echo "Usar ./monitor-app-health.sh para monitoreo continuo"
echo ""
echo "📞 ESCALATION: Si persiste después de reiniciar pod, contactar DevOps"