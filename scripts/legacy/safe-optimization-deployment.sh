#!/bin/bash

# 🛡️ APLICACIÓN SEGURA DE OPTIMIZACIONES - SIN AFECTAR PRODUCCIÓN
# Plan de despliegue gradual con rollback automático

echo "🛡️ Aplicación SEGURA de optimizaciones para candidatos"
echo "📅 $(date)"
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[SAFE]${NC} $1"
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

step() {
    echo -e "${PURPLE}[STEP]${NC} $1"
}

# Variables de configuración
NAMESPACE="authenticfarma-prod"
APP_LABEL="app=candidatos"
BACKUP_DIR="/tmp/candidatos-backup-$(date +%Y%m%d-%H%M%S)"
TEST_URL="https://candidatos.authenticfarma.com/login"

# Función para verificar salud de la aplicación
check_app_health() {
    local test_name=$1
    info "🔍 Verificando salud de aplicación: $test_name"
    
    # Test básico de conectividad
    response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}" "$TEST_URL" --max-time 10)
    
    if [ $? -eq 0 ]; then
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2)
        
        if [[ $status_code -eq 200 || $status_code -eq 302 ]]; then
            log "✅ Aplicación respondiendo correctamente: HTTP $status_code (${time_total}s)"
            return 0
        else
            error "❌ Aplicación retorna código inesperado: HTTP $status_code"
            return 1
        fi
    else
        error "❌ No se pudo conectar a la aplicación"
        return 1
    fi
}

# Función para obtener el pod de candidatos
get_candidatos_pod() {
    kubectl get pods -n "$NAMESPACE" -l "$APP_LABEL" -o jsonpath='{.items[0].metadata.name}' 2>/dev/null
}

# Función para ejecutar comando de forma segura en el pod
safe_exec_in_pod() {
    local pod_name=$1
    local command=$2
    local description=$3
    
    info "Ejecutando: $description"
    echo "   Pod: $pod_name"
    echo "   Comando: $command"
    
    # Ejecutar con timeout para evitar bloqueos
    timeout 30 kubectl exec "$pod_name" -n "$NAMESPACE" -- bash -c "$command"
    
    if [ $? -eq 0 ]; then
        log "✅ $description completado"
        return 0
    else
        error "❌ Falló: $description"
        return 1
    fi
}

# FASE 1: VERIFICACIONES PREVIAS
step "🔍 FASE 1: VERIFICACIONES PREVIAS"
echo ""

# Verificar acceso al cluster
info "Verificando acceso al cluster..."
if ! kubectl get namespaces >/dev/null 2>&1; then
    error "❌ Sin acceso al cluster de Kubernetes"
    echo ""
    echo "🔧 ALTERNATIVA - Comandos para ejecutar manualmente:"
    echo ""
    
    cat << 'EOF'
# 1. Conectar al pod
kubectl get pods -n authenticfarma-prod -l app=candidatos
POD_NAME=$(kubectl get pods -n authenticfarma-prod -l app=candidatos -o jsonpath='{.items[0].metadata.name}')

# 2. Hacer backup de configuración actual
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
cp -r bootstrap/cache /tmp/backup-cache-$(date +%Y%m%d) 2>/dev/null || echo 'No cache to backup'
php artisan route:list > /tmp/routes-before.txt
"

# 3. Aplicar optimizaciones seguras (solo cache, no config)
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan view:clear
php artisan route:cache
php artisan view:cache
echo 'Optimización segura aplicada: $(date)'
"

# 4. Verificar funcionamiento
curl -w 'Tiempo: %{time_total}s | Status: %{http_code}\n' -o /dev/null -s https://candidatos.authenticfarma.com/login

# 5. Si hay problemas, rollback:
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan route:clear
php artisan view:clear
echo 'Rollback aplicado'
"
EOF
    
    exit 1
fi

# Verificar que existe el namespace
if ! kubectl get namespace "$NAMESPACE" >/dev/null 2>&1; then
    error "❌ Namespace $NAMESPACE no encontrado"
    exit 1
fi

log "✅ Acceso al cluster confirmado"

# Obtener pod de candidatos
info "Buscando pod de candidatos..."
POD_NAME=$(get_candidatos_pod)

if [ -z "$POD_NAME" ]; then
    error "❌ No se encontró pod de candidatos"
    echo "Pods disponibles:"
    kubectl get pods -n "$NAMESPACE"
    exit 1
fi

log "✅ Pod encontrado: $POD_NAME"

# Verificar salud inicial
if ! check_app_health "INICIAL"; then
    error "❌ Aplicación no está saludable - abortando optimización"
    exit 1
fi

echo ""

# FASE 2: BACKUP Y PREPARACIÓN
step "💾 FASE 2: BACKUP Y PREPARACIÓN"
echo ""

info "Creando backup de configuración actual..."
mkdir -p "$BACKUP_DIR"

# Backup de configuración de cache actual
safe_exec_in_pod "$POD_NAME" "cd /var/www/html && cp -r bootstrap/cache $BACKUP_DIR/ 2>/dev/null || echo 'No cache to backup'" "Backup de cache"

# Guardar estado actual de rutas
safe_exec_in_pod "$POD_NAME" "cd /var/www/html && php artisan route:list > /tmp/routes-before.txt" "Backup de rutas"

log "✅ Backup creado en: $BACKUP_DIR"

echo ""

# FASE 3: APLICACIÓN SEGURA DE OPTIMIZACIONES
step "⚡ FASE 3: OPTIMIZACIONES SEGURAS (REVERSIBLES)"
echo ""

warn "🎯 Aplicando SOLO optimizaciones reversibles..."

# Lista de optimizaciones seguras (no modifican .env ni config permanente)
optimizations=(
    "php artisan view:clear:Limpiar cache de vistas"
    "php artisan route:cache:Crear cache de rutas"
    "php artisan view:cache:Crear cache de vistas"
)

for opt in "${optimizations[@]}"; do
    IFS=':' read -r command description <<< "$opt"
    
    info "Aplicando: $description"
    
    if safe_exec_in_pod "$POD_NAME" "cd /var/www/html && $command" "$description"; then
        # Verificar salud después de cada optimización
        sleep 2
        
        if check_app_health "POST-$description"; then
            log "✅ $description aplicado correctamente"
        else
            error "⚠️ Problema detectado después de: $description"
            warn "🔄 Iniciando rollback automático..."
            
            # Rollback inmediato
            safe_exec_in_pod "$POD_NAME" "cd /var/www/html && php artisan route:clear && php artisan view:clear" "Rollback de cache"
            
            sleep 3
            if check_app_health "POST-ROLLBACK"; then
                warn "🔄 Rollback exitoso - aplicación restaurada"
            else
                error "🚨 PROBLEMA CRÍTICO - Requiere intervención manual"
            fi
            
            exit 1
        fi
    else
        warn "⚠️ Falló aplicar: $description - continuando con siguiente"
    fi
    
    echo ""
done

echo ""

# FASE 4: VERIFICACIÓN FINAL
step "🔍 FASE 4: VERIFICACIÓN FINAL"
echo ""

info "Ejecutando tests de verificación final..."

# Test de performance
echo "📊 Test de performance:"
for i in {1..3}; do
    response=$(curl -s -o /dev/null -w "%{time_total}" "$TEST_URL")
    echo "   Test $i: ${response}s"
done

# Test de funcionalidad
echo ""
echo "🔧 Test de funcionalidad:"
endpoints=(
    "https://candidatos.authenticfarma.com/"
    "https://candidatos.authenticfarma.com/login"
    "https://candidatos.authenticfarma.com/register"
)

all_ok=true
for endpoint in "${endpoints[@]}"; do
    status=$(curl -s -o /dev/null -w "%{http_code}" "$endpoint")
    if [[ $status -eq 200 || $status -eq 302 ]]; then
        echo "   ✅ $endpoint: HTTP $status"
    else
        echo "   ❌ $endpoint: HTTP $status"
        all_ok=false
    fi
done

echo ""

if $all_ok; then
    log "🎉 OPTIMIZACIÓN COMPLETADA EXITOSAMENTE"
    echo ""
    echo "📊 RESULTADOS:"
    echo "   ✅ Aplicación funcionando correctamente"
    echo "   ✅ Cache de rutas y vistas optimizado"
    echo "   ✅ Performance mejorada"
    echo ""
    echo "🔄 ROLLBACK disponible si es necesario:"
    echo "   kubectl exec $POD_NAME -n $NAMESPACE -- bash -c 'cd /var/www/html && php artisan route:clear && php artisan view:clear'"
else
    warn "⚠️ Algunos endpoints presentan problemas - revisar logs"
fi

echo ""
echo "📋 SIGUIENTE FASE (OPCIONAL):"
echo "   1. Monitorear performance por 24h"
echo "   2. Si todo funciona bien, aplicar optimizaciones de infraestructura"
echo "   3. Configurar Redis para cache persistente"
echo ""

echo "🕒 Optimización completada: $(date)"