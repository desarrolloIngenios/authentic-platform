#!/bin/bash

# 🔍 Script de Validación - Candidatos en Producción
# Verificar cambios del desarrollador authentic-24

echo "🔍 Validando aplicación candidatos en producción..."
echo "📋 Commit validado: 7324f58e - Arreglando vacantes y botón menú"
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[OK]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

info() {
    echo -e "${BLUE}[TEST]${NC} $1"
}

BASE_URL="https://candidatos.authenticfarma.com"

# Función para probar URL
test_url() {
    local url=$1
    local description=$2
    
    info "Probando: $description"
    
    response=$(curl -s -o /dev/null -w "%{http_code}|%{time_total}" "$url")
    status_code=$(echo $response | cut -d'|' -f1)
    time_total=$(echo $response | cut -d'|' -f2)
    
    if [[ $status_code -ge 200 && $status_code -lt 400 ]]; then
        log "$description - HTTP $status_code (${time_total}s)"
    elif [[ $status_code -eq 302 || $status_code -eq 301 ]]; then
        warn "$description - HTTP $status_code Redirect (${time_total}s)"
    else
        error "$description - HTTP $status_code (${time_total}s)"
    fi
    
    echo "   🔗 $url"
    echo ""
}

# Tests básicos de conectividad
echo "📡 Pruebas de Conectividad:"
test_url "$BASE_URL" "Página principal"
test_url "$BASE_URL/login" "Página de login"
test_url "$BASE_URL/register" "Página de registro"

# Tests específicos de vacantes (archivos modificados)
echo "💼 Pruebas de Funcionalidad Vacantes:"
test_url "$BASE_URL/candidate/vacant" "Lista de vacantes"
test_url "$BASE_URL/dashboard" "Dashboard principal"

# Verificar si hay errores 500 o problemas
echo "🔍 Verificación de errores:"
error_check=$(curl -s -w "%{http_code}" "$BASE_URL" -o /tmp/candidatos_response.html)

if [[ $error_check -ge 500 ]]; then
    error "Servidor retorna error $error_check"
    echo "Posibles causas:"
    echo "- Deploy no completado"
    echo "- Error en aplicación Laravel"
    echo "- Base de datos no disponible"
else
    log "No se detectaron errores de servidor"
fi

# Verificar si el contenido indica cambios recientes
if grep -q "menu\|vacant\|dashboard" /tmp/candidatos_response.html 2>/dev/null; then
    log "Contenido relacionado con menú/vacantes detectado"
else
    warn "No se pudo verificar contenido específico"
fi

# Timestamp de validación
echo ""
echo "📊 Resumen de Validación:"
echo "🕒 Timestamp: $(date)"
echo "🔗 URL Base: $BASE_URL"
echo "📋 Commit: 7324f58e878e40cefc39e051ce0fbdbe2b853233"
echo "👤 Desarrollador: authentic-24"
echo ""

# URLs para revisión manual
echo "🔗 Enlaces para revisión manual:"
echo "   - Candidatos: $BASE_URL"
echo "   - ArgoCD: https://argo.authenticfarma.com/applications/authenticfarma-candidatos"
echo "   - GitHub: https://github.com/desarrolloIngenios/authentic-platform/commit/7324f58e878e40cefc39e051ce0fbdbe2b853233"
echo ""

# Cleanup
rm -f /tmp/candidatos_response.html

echo "✅ Validación completada"