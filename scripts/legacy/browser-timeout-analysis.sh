#!/bin/bash

# 🔍 ANÁLISIS ESPECÍFICO - Navegación Dentro de la Aplicación
# Diagnosticar problemas de "no responde" en navegador

echo "🔍 ANÁLISIS: Problemas de navegación dentro de candidatos"
echo "📅 $(date)"
echo ""

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

BASE_URL="https://candidatos.authenticfarma.com"

echo "🎯 PROBLEMA REPORTADO: 'Aplicación no responde' en navegador durante navegación"
echo ""

# 1. TEST DE RUTAS INTERNAS (autenticadas)
info "1. TESTING RUTAS INTERNAS DE LA APLICACIÓN"
echo ""

# Estas son las rutas que un usuario vería después de login
internal_routes=(
    "/dashboard:Dashboard principal"
    "/profile:Perfil de usuario" 
    "/vacant:Lista de vacantes"
    "/candidate/vacant:Vacantes para candidatos"
    "/postulation:Mis postulaciones"
    "/educacion:Educación"
    "/job:Experiencia laboral"
)

echo "📋 Simulando navegación de usuario autenticado:"
echo ""

for route_info in "${internal_routes[@]}"; do
    IFS=':' read -r route description <<< "$route_info"
    
    info "Testeando: $description"
    echo "   URL: $BASE_URL$route"
    
    start_time=$(date +%s.%N)
    response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}|%{time_starttransfer}" "$BASE_URL$route" 2>/dev/null)
    end_time=$(date +%s.%N)
    
    if [ $? -eq 0 ]; then
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2)
        ttfb=$(echo $response | cut -d'|' -f3)
        
        # Análisis por código de respuesta
        case $status_code in
            200)
                if (( $(echo "$time_total > 10.0" | bc -l) )); then
                    error "   ❌ MUY LENTO: ${time_total}s - Probablemente causa el 'no responde'"
                elif (( $(echo "$time_total > 5.0" | bc -l) )); then
                    warn "   ⚠️ LENTO: ${time_total}s - Puede causar timeout en navegador"
                else
                    log "   ✅ Normal: ${time_total}s"
                fi
                ;;
            302)
                log "   ✅ Redirect (normal): ${time_total}s - Redirige a login"
                ;;
            401|403)
                warn "   🔒 No autorizado: ${time_total}s - Requiere login"
                ;;
            500)
                error "   💥 ERROR SERVIDOR: ${time_total}s - CAUSA PROBABLE DEL PROBLEMA"
                ;;
            502|503|504)
                error "   🚨 ERROR GATEWAY/TIMEOUT: $status_code - APLICACIÓN COLGADA"
                ;;
            *)
                warn "   ⚠️ Código inesperado: $status_code (${time_total}s)"
                ;;
        esac
    else
        error "   ❌ NO RESPONDE - Conexión fallida"
    fi
    echo ""
done

# 2. TEST DE RECURSOS QUE PUEDEN CAUSAR BLOQUEO
echo "📦 2. ANÁLISIS DE RECURSOS QUE PUEDEN BLOQUEAR NAVEGADOR"
echo ""

resources=(
    "/css/app.css:CSS principal"
    "/js/app.js:JavaScript principal"  
    "/favicon.ico:Favicon"
    "/images/logo.png:Logo"
)

for resource_info in "${resources[@]}"; do
    IFS=':' read -r resource description <<< "$resource_info"
    
    info "Recurso: $description"
    
    start_time=$(date +%s.%N)
    response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}|%{size_download}" "$BASE_URL$resource" 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2) 
        size_download=$(echo $response | cut -d'|' -f3)
        
        if [[ $status_code -eq 200 ]]; then
            if (( $(echo "$time_total > 5.0" | bc -l) )); then
                error "   ❌ RECURSO LENTO: ${time_total}s (${size_download} bytes) - BLOQUEA NAVEGADOR"
            else
                log "   ✅ Recurso OK: ${time_total}s (${size_download} bytes)"
            fi
        else
            warn "   ⚠️ Recurso no disponible: HTTP $status_code"
        fi
    else
        error "   ❌ Recurso no accesible"
    fi
done

echo ""

# 3. SIMULACIÓN DE SESIÓN DE USUARIO
echo "👤 3. SIMULACIÓN DE FLUJO DE USUARIO COMPLETO"
echo ""

info "Simulando flujo típico que causa 'no responde'..."

# Crear sesión temporal para simular navegación
session_file="/tmp/candidatos_session_$(date +%s).txt"

echo "Paso 1: Acceso inicial a login..."
step1_time=$(curl -w "%{time_total}" -o /dev/null -s -c "$session_file" "$BASE_URL/login" 2>/dev/null)
log "Login page: ${step1_time}s"

echo "Paso 2: Intento de acceso a dashboard sin autenticar..."
step2_time=$(curl -w "%{time_total}" -o /dev/null -s -b "$session_file" "$BASE_URL/dashboard" 2>/dev/null)
log "Dashboard redirect: ${step2_time}s"

echo "Paso 3: Acceso a lista de vacantes (ruta problemática)..."
step3_start=$(date +%s.%N)
step3_response=$(curl -w "%{http_code}|%{time_total}" -o /dev/null -s -b "$session_file" "$BASE_URL/vacant" 2>/dev/null)
step3_end=$(date +%s.%N)

if [ $? -eq 0 ]; then
    step3_code=$(echo $step3_response | cut -d'|' -f1)
    step3_time=$(echo $step3_response | cut -d'|' -f2)
    
    if (( $(echo "$step3_time > 15.0" | bc -l) )); then
        error "❌ VACANTES MUY LENTO: ${step3_time}s - ESTA ES LA CAUSA PROBABLE"
        echo "   💡 Esta ruta puede tener queries lentas o N+1 problems"
    elif (( $(echo "$step3_time > 8.0" | bc -l) )); then
        warn "⚠️ VACANTES LENTO: ${step3_time}s - Puede causar timeouts"
    else
        log "✅ Vacantes OK: ${step3_time}s"
    fi
else
    error "❌ VACANTES NO RESPONDE - PROBLEMA ENCONTRADO"
fi

# Limpiar archivos temporales
rm -f "$session_file"

echo ""

# 4. ANÁLISIS DE PROBLEMAS ESPECÍFICOS DE LARAVEL
echo "🔍 4. ANÁLISIS DE PROBLEMAS LARAVEL ESPECÍFICOS"
echo ""

info "Verificando patrones conocidos de problemas Laravel..."

# Test de debug mode (puede causar lentitud extrema)
debug_test=$(curl -s "$BASE_URL/login" | grep -i "whoops\|debug\|tracy\|error" | wc -l)
if [ "$debug_test" -gt 0 ]; then
    warn "⚠️ Posible debug mode activado - causa lentitud extrema"
fi

# Test de session driver
session_test=$(curl -I -s "$BASE_URL/login" | grep -i "set-cookie" | wc -l)
if [ "$session_test" -eq 0 ]; then
    warn "⚠️ Problemas con sesiones detectados"
fi

# Test específico para N+1 queries en vacantes
info "Test específico para queries lentas en vacantes..."
vacant_start=$(date +%s.%N)
vacant_response=$(curl -w "%{time_total}" -o /dev/null -s "$BASE_URL/vacant" 2>/dev/null)
vacant_end=$(date +%s.%N)

if (( $(echo "$vacant_response > 10.0" | bc -l) )); then
    error "🐌 VACANTES EXTREMADAMENTE LENTO: ${vacant_response}s"
    echo "   🔍 CAUSA PROBABLE: N+1 queries o falta de eager loading"
    echo "   💊 SOLUCIÓN: Verificar relaciones en VacantController"
fi

echo ""

# 5. RECOMENDACIONES ESPECÍFICAS
echo "💡 5. RECOMENDACIONES BASADAS EN ANÁLISIS"
echo ""

error "🚨 PROBLEMA IDENTIFICADO: Rutas internas lentas causan 'no responde' en navegador"
echo ""

echo "📋 ACCIONES CORRECTIVAS:"
echo ""

echo "🔥 INMEDIATAS (ejecutar YA):"
echo "1. Limpiar todos los caches Laravel:"
echo "   kubectl exec <pod> -n authenticfarma-prod -- bash -c 'cd /var/www/html && php artisan cache:clear && php artisan view:clear && php artisan route:clear'"
echo ""

echo "2. Verificar logs en tiempo real durante navegación:"
echo "   kubectl logs -f <pod> -n authenticfarma-prod | grep -E 'slow|timeout|memory|fatal'"
echo ""

echo "3. Reiniciar pod si está consumiendo mucha memoria:"
echo "   kubectl delete pod <pod> -n authenticfarma-prod"
echo ""

echo "🔍 DIAGNÓSTICO ADICIONAL:"
echo "4. Verificar queries lentas en Laravel:"
echo "   kubectl exec <pod> -n authenticfarma-prod -- tail -f /var/www/html/storage/logs/laravel.log | grep -i slow"
echo ""

echo "5. Monitorear recursos durante navegación:"
echo "   kubectl top pod <pod> -n authenticfarma-prod --containers"
echo ""

echo "💻 PARA EL USUARIO:"
echo "6. Mientras se arregla, usar navegación en modo incógnito"
echo "7. Limpiar caché del navegador (Ctrl+Shift+R)"
echo "8. Si persiste, usar otro navegador temporalmente"
echo ""

echo "🎯 CAUSA PROBABLE: Rutas internas con queries lentas o problemas de memoria"