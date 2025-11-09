#!/bin/bash

# 🚨 SOLUCIÓN URGENTE - "Aplicación no responde" en navegador
# Comandos específicos para resolver el problema identificado

echo "🚨 SOLUCIÓN URGENTE - Aplicación candidatos 'no responde'"
echo "📅 $(date)"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

error() {
    echo -e "${RED}[URGENTE]${NC} $1"
}

log() {
    echo -e "${GREEN}[ACCIÓN]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

info() {
    echo -e "${BLUE}[PASO]${NC} $1"
}

cmd() {
    echo -e "${PURPLE}[CMD]${NC} $1"
}

error "🎯 PROBLEMA IDENTIFICADO:"
echo "   • Debug mode posiblemente activado (causa lentitud extrema)"
echo "   • JavaScript app.js no disponible (404 error)"
echo "   • Posibles queries lentas en rutas autenticadas"
echo ""

echo "🚀 SOLUCIÓN EN 3 FASES - EJECUTAR EN ORDEN"
echo "============================================="
echo ""

# FASE 1: IDENTIFICAR EL POD
info "📍 FASE 1: IDENTIFICAR POD DE CANDIDATOS"
echo ""

cmd "kubectl get pods -n authenticfarma-prod -l app=candidatos"
echo ""
warn "👆 Copia el nombre del pod para usar en los siguientes comandos"
echo "Ejemplo: candidatos-deployment-abc123-xyz"
echo ""

# FASE 2: DIAGNÓSTICO INMEDIATO
info "🔍 FASE 2: DIAGNÓSTICO INMEDIATO (reemplazar <POD_NAME>)"
echo ""

echo "2.1. Verificar configuración de debug:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- grep -E 'APP_DEBUG|APP_ENV' /var/www/html/.env"
echo ""

echo "2.2. Verificar uso de memoria y CPU:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- bash -c 'free -h && ps aux --sort=-%mem | head -5'"
echo ""

echo "2.3. Verificar logs recientes de errores:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- tail -20 /var/www/html/storage/logs/laravel.log"
echo ""

echo "2.4. Verificar procesos PHP colgados:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- ps aux | grep php | grep -v grep"
echo ""

# FASE 3: CORRECCIONES INMEDIATAS
info "⚡ FASE 3: CORRECCIONES INMEDIATAS"
echo ""

echo "3.1. Limpiar TODOS los caches (SEGURO):"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- bash -c '"
cmd "cd /var/www/html"
cmd "php artisan cache:clear"
cmd "php artisan config:clear"  
cmd "php artisan route:clear"
cmd "php artisan view:clear"
cmd "php artisan optimize:clear"
cmd "echo \"Todos los caches limpiados: \$(date)\""
cmd "'"
echo ""

echo "3.2. Regenerar configuración optimizada:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- bash -c '"
cmd "cd /var/www/html"
cmd "php artisan config:cache"
cmd "php artisan route:cache"
cmd "php artisan view:cache"
cmd "echo \"Configuración optimizada: \$(date)\""
cmd "'"
echo ""

echo "3.3. Verificar assets JavaScript (si falta app.js):"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- ls -la /var/www/html/public/js/"
echo ""

echo "3.4. Si falta JavaScript, regenerar assets:"
cmd "kubectl exec <POD_NAME> -n authenticfarma-prod -- bash -c '"
cmd "cd /var/www/html"
cmd "npm run production 2>/dev/null || echo 'NPM no disponible - usar imagen con assets pre-compilados'"
cmd "'"
echo ""

# FASE 4: VERIFICACIÓN
info "✅ FASE 4: VERIFICACIÓN POST-FIX"
echo ""

echo "4.1. Test inmediato de la aplicación:"
cmd "curl -w 'Tiempo: %{time_total}s | HTTP: %{http_code}\\n' -o /dev/null -s https://candidatos.authenticfarma.com/login"
echo ""

echo "4.2. Test de JavaScript:"
cmd "curl -s -o /dev/null -w '%{http_code}' https://candidatos.authenticfarma.com/js/app.js"
echo ""

echo "4.3. Monitoreo continuo (ejecutar en terminal separado):"
cmd "kubectl logs -f <POD_NAME> -n authenticfarma-prod | grep -E 'error|slow|memory|timeout' --color=always"
echo ""

# COMANDOS ALTERNATIVOS SI NO HAY ACCESO AL CLUSTER
echo ""
error "🔄 SI NO TIENES ACCESO AL CLUSTER - ALTERNATIVA"
echo "==============================================="
echo ""

warn "Envía estos comandos al equipo DevOps/SysAdmin:"
echo ""

cat << 'EOF'
# SCRIPT PARA DEVOPS - Resolver "aplicación no responde"
#!/bin/bash

POD_NAME=$(kubectl get pods -n authenticfarma-prod -l app=candidatos -o jsonpath='{.items[0].metadata.name}')
echo "🔧 Trabajando con pod: $POD_NAME"

echo "1. Verificando configuración..."
kubectl exec $POD_NAME -n authenticfarma-prod -- grep APP_DEBUG /var/www/html/.env

echo "2. Limpiando caches..."
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan cache:clear
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan optimize:clear
echo 'Caches limpiados'
"

echo "3. Optimizando configuración..."
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo 'Configuración optimizada'
"

echo "4. Verificando resultado..."
curl -w 'Test post-fix: %{time_total}s | %{http_code}\n' -o /dev/null -s https://candidatos.authenticfarma.com/login

echo "✅ Optimización completada: $(date)"
EOF

echo ""

# PARA EL USUARIO FINAL
error "👤 PARA EL USUARIO - MIENTRAS SE ARREGLA"
echo "========================================"
echo ""

echo "1. 🔄 Limpiar caché del navegador:"
echo "   • Chrome/Edge: Ctrl+Shift+Delete → Borrar todo"
echo "   • Firefox: Ctrl+Shift+Delete → Borrar todo"
echo "   • Safari: Cmd+Option+E → Vaciar cachés"
echo ""

echo "2. 🔄 Recargar página forzado:"
echo "   • Ctrl+F5 (Windows/Linux)"
echo "   • Cmd+Shift+R (Mac)"
echo ""

echo "3. 🕵️ Usar modo incógnito/privado temporalmente"
echo ""

echo "4. 🌐 Probar otro navegador si persiste"
echo ""

echo "5. ⏰ Evitar navegar rápidamente entre páginas"
echo "   (dar tiempo a que cargue cada página)"
echo ""

# MONITOREO
info "📊 PARA MONITOREAR LA SOLUCIÓN"
echo ""

echo "Ejecutar en terminal separado para ver mejoras en tiempo real:"
cmd "while true; do curl -w '\$(date): %{time_total}s | %{http_code}\\n' -o /dev/null -s https://candidatos.authenticfarma.com/login; sleep 5; done"
echo ""

warn "⏱️ TIEMPO ESPERADO DE RESOLUCIÓN:"
echo "   • Limpieza de cache: Inmediata (30 segundos)"
echo "   • Regeneración assets: 2-5 minutos"  
echo "   • Reinicio de pod (si necesario): 1-2 minutos"
echo ""

log "🎯 RESULTADO ESPERADO:"
echo "   • Tiempo de respuesta: <1s consistente"
echo "   • Sin mensajes de 'no responde'"
echo "   • JavaScript funcionando correctamente"
echo ""

echo "📞 ESCALATION: Si persiste después de estas acciones → Contactar arquitecto de software"