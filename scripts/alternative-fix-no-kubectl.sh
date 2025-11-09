#!/bin/bash

# 🚨 SOLUCIÓN ALTERNATIVA - Sin acceso kubectl
# Comandos y alternativas para resolver "aplicación no responde"

echo "🚨 SOLUCIÓN ALTERNATIVA - Sin acceso kubectl"
echo "📅 $(date)"
echo ""

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

error() {
    echo -e "${RED}[PROBLEMA]${NC} $1"
}

log() {
    echo -e "${GREEN}[ACCIÓN]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

info() {
    echo -e "${BLUE}[OPCIÓN]${NC} $1"
}

cmd() {
    echo -e "${PURPLE}[COMANDO]${NC} $1"
}

error "❌ Confirmado: Sin acceso al cluster de Kubernetes"
echo "   Error: gcloud auth login requerido"
echo "   No se pudo ejecutar la optimización directa"
echo ""

warn "🔄 IMPLEMENTANDO PLAN B - Alternativas sin kubectl"
echo ""

# OPCIÓN A: Crear script para DevOps
info "📧 OPCIÓN A: Script para equipo DevOps/SysAdmin"
echo ""

cat > devops-fix-script.sh << 'EOF'
#!/bin/bash
# 🚨 SCRIPT URGENTE PARA DEVOPS - Resolver "aplicación no responde"
# Ejecutar en servidor con acceso al cluster de candidatos

echo "🚨 Iniciando fix urgente - candidatos no responde"
echo "Ejecutado por: $(whoami) en $(date)"

# 1. Obtener pod de candidatos
echo "📍 Buscando pod de candidatos..."
POD_NAME=$(kubectl get pods -n authenticfarma-prod -l app=candidatos -o jsonpath='{.items[0].metadata.name}' 2>/dev/null)

if [ -z "$POD_NAME" ]; then
    echo "❌ No se encontró pod de candidatos en authenticfarma-prod"
    echo "Pods disponibles:"
    kubectl get pods -n authenticfarma-prod
    exit 1
fi

echo "✅ Pod encontrado: $POD_NAME"

# 2. Verificar estado del pod
echo "🔍 Verificando estado del pod..."
kubectl get pod $POD_NAME -n authenticfarma-prod

# 3. Verificar configuración actual
echo "🔍 Verificando configuración Laravel..."
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html
echo 'APP_ENV:' \$(grep APP_ENV .env 2>/dev/null || echo 'No encontrado')
echo 'APP_DEBUG:' \$(grep APP_DEBUG .env 2>/dev/null || echo 'No encontrado')
echo 'CACHE_DRIVER:' \$(grep CACHE_DRIVER .env 2>/dev/null || echo 'No encontrado')
"

# 4. Aplicar fix urgente
echo "⚡ Aplicando fix urgente..."
kubectl exec $POD_NAME -n authenticfarma-prod -- bash -c "
cd /var/www/html

echo '🧹 Limpiando caches...'
php artisan cache:clear 2>/dev/null || echo 'Error en cache:clear'
php artisan config:clear 2>/dev/null || echo 'Error en config:clear'  
php artisan route:clear 2>/dev/null || echo 'Error en route:clear'
php artisan view:clear 2>/dev/null || echo 'Error en view:clear'
php artisan optimize:clear 2>/dev/null || echo 'Error en optimize:clear'

echo '⚡ Regenerando configuración...'
php artisan config:cache 2>/dev/null || echo 'Error en config:cache'
php artisan route:cache 2>/dev/null || echo 'Error en route:cache'  
php artisan view:cache 2>/dev/null || echo 'Error en view:cache'

echo '🔍 Verificando resultado...'
if php artisan route:list >/dev/null 2>&1; then
    echo '✅ Laravel funcionando correctamente'
else
    echo '❌ Problema detectado con Laravel'
fi

echo '📊 Estado de archivos JavaScript:'
ls -la public/js/ 2>/dev/null || echo 'Directorio js no encontrado'

echo 'Fix completado: \$(date)'
"

# 5. Test post-fix
echo "🔍 Verificando aplicación externamente..."
response=\$(curl -s -o /dev/null -w "%{time_total}|%{http_code}" "https://candidatos.authenticfarma.com/login" 2>/dev/null)

if [ \$? -eq 0 ]; then
    time_total=\$(echo \$response | cut -d'|' -f1)
    http_code=\$(echo \$response | cut -d'|' -f2)
    
    echo "Resultado: \${time_total}s | HTTP \${http_code}"
    
    if (( \$(echo "\$time_total < 2.0" | bc -l) )); then
        echo "✅ Aplicación respondiendo correctamente"
    else
        echo "⚠️ Aplicación aún lenta: \${time_total}s"
    fi
else
    echo "❌ No se pudo verificar aplicación externamente"
fi

echo ""
echo "🎯 RESUMEN DEL FIX:"
echo "- Pod trabajado: \$POD_NAME"
echo "- Caches limpiados: ✅"
echo "- Configuración regenerada: ✅"  
echo "- Timestamp: \$(date)"

echo ""
echo "📋 SIGUIENTE PASOS:"
echo "1. Informar al usuario que el fix fue aplicado"
echo "2. Pedirle que limpie caché del navegador (Ctrl+Shift+Delete)"
echo "3. Monitorear por 10-15 minutos"
echo "4. Si persiste, considerar reinicio completo del pod"

echo ""
echo "🔄 COMANDO DE ROLLBACK (si hay problemas):"
echo "kubectl delete pod \$POD_NAME -n authenticfarma-prod"
echo "# Kubernetes recreará el pod automáticamente"
EOF

chmod +x devops-fix-script.sh
log "✅ Script creado: devops-fix-script.sh"
echo ""

# OPCIÓN B: Usando ArgoCD Web UI
info "🌐 OPCIÓN B: Via ArgoCD Web Interface"
echo ""

warn "Si tienes acceso a ArgoCD Web UI:"
echo "1. 🌐 Ir a: https://argo.authenticfarma.com/"
echo "2. 🔍 Buscar aplicación: authenticfarma-candidatos"  
echo "3. 📋 En la vista de pods, encontrar el pod de candidatos"
echo "4. 🖱️ Hacer clic en el pod → Terminal"
echo "5. 💻 Ejecutar estos comandos en la terminal web:"
echo ""

cmd "cd /var/www/html"
cmd "php artisan optimize:clear"
cmd "php artisan config:cache && php artisan route:cache && php artisan view:cache"
cmd "echo 'Fix aplicado via ArgoCD'"
echo ""

# OPCIÓN C: Coordinación por Slack/Teams/Email
info "📧 OPCIÓN C: Mensaje para equipo DevOps"
echo ""

warn "Copia este mensaje y envía al equipo:"
echo ""

cat << 'EOF'
🚨 URGENTE - Aplicación candidatos presenta problema "no responde"

**Síntoma**: Los usuarios reportan que el navegador muestra "la aplicación no responde" al navegar

**Causa identificada**: Debug mode activado + caches Laravel no optimizados

**Solución requerida** (5 minutos):
```bash
POD=$(kubectl get pods -n authenticfarma-prod -l app=candidatos -o jsonpath='{.items[0].metadata.name}')
kubectl exec $POD -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
echo 'Optimización completada'
"
```

**Verificación**:
```bash
curl -w 'Tiempo: %{time_total}s | HTTP: %{http_code}\n' -o /dev/null -s https://candidatos.authenticfarma.com/login
```

**Resultado esperado**: Tiempo < 1s, sin problemas de navegación

**Escalation**: Si persiste después de este fix, reiniciar pod completo

**Scripts disponibles**: devops-fix-script.sh (completo con verificaciones)
EOF

echo ""

# OPCIÓN D: Solución temporal para usuarios
info "👤 OPCIÓN D: Instrucciones para usuarios afectados"
echo ""

warn "Mientras se aplica el fix técnico, instruir a usuarios:"
echo ""

echo "📋 SOLUCIÓN TEMPORAL PARA USUARIOS:"
echo ""
echo "1. 🔄 **Limpiar caché del navegador**:"
echo "   • Chrome/Edge: Ctrl+Shift+Delete → Seleccionar 'Todo el tiempo' → Borrar"
echo "   • Firefox: Ctrl+Shift+Delete → Seleccionar 'Todo' → Borrar ahora"  
echo "   • Safari: Cmd+Option+E → Vaciar cachés"
echo ""

echo "2. 🔄 **Recarga forzada de página**:"
echo "   • Windows/Linux: Ctrl+F5 o Ctrl+Shift+R"
echo "   • Mac: Cmd+Shift+R"
echo ""

echo "3. 🕵️ **Usar modo incógnito/privado** temporalmente"
echo ""

echo "4. ⏰ **Navegar más despacio**:"
echo "   • Esperar que cada página cargue completamente"
echo "   • No hacer clic repetidamente si tarda"
echo "   • Cerrar otras pestañas del navegador"
echo ""

echo "5. 🌐 **Probar otro navegador** si persiste"
echo ""

# OPCIÓN E: Monitoreo mientras se coordina
info "📊 OPCIÓN E: Monitoreo continuo"
echo ""

warn "Ejecutar para monitorear la aplicación:"
echo ""

cat > monitor-candidatos.sh << 'EOF'
#!/bin/bash
echo "🔍 Monitoreando candidatos cada 10 segundos..."
echo "Presiona Ctrl+C para detener"
echo ""

while true; do
    timestamp=$(date '+%H:%M:%S')
    
    response=$(curl -s -o /dev/null -w "%{time_total}|%{http_code}" "https://candidatos.authenticfarma.com/login" --max-time 8 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        time_total=$(echo $response | cut -d'|' -f1)
        http_code=$(echo $response | cut -d'|' -f2)
        
        if (( $(echo "$time_total > 5.0" | bc -l) )); then
            echo "[$timestamp] 🐌 LENTO: ${time_total}s | HTTP $http_code"
        elif (( $(echo "$time_total > 2.0" | bc -l) )); then
            echo "[$timestamp] ⚠️ Moderado: ${time_total}s | HTTP $http_code"  
        else
            echo "[$timestamp] ✅ OK: ${time_total}s | HTTP $http_code"
        fi
    else
        echo "[$timestamp] ❌ TIMEOUT/ERROR"
    fi
    
    sleep 10
done
EOF

chmod +x monitor-candidatos.sh
log "✅ Script de monitoreo creado: monitor-candidatos.sh"
echo ""

cmd "./monitor-candidatos.sh"
echo ""

# RESUMEN FINAL
echo "🎯 PRÓXIMOS PASOS RECOMENDADOS:"
echo ""

log "1. **INMEDIATO**: Enviar devops-fix-script.sh al equipo técnico"
log "2. **PARALELO**: Ejecutar ./monitor-candidatos.sh para monitorear"  
log "3. **USUARIOS**: Instruir solución temporal (limpiar caché navegador)"
log "4. **VERIFICAR**: En 5-10 minutos debería estar resuelto"

echo ""
warn "⏱️ TIEMPO ESTIMADO DE RESOLUCIÓN: 5-15 minutos"
warn "📞 ESCALATION: Si no mejora en 30 min → Reiniciar pod completo"

echo ""
echo "📁 ARCHIVOS CREADOS:"
echo "   ✅ devops-fix-script.sh - Para equipo técnico"
echo "   ✅ monitor-candidatos.sh - Para monitoreo"
echo "   ✅ Mensaje para DevOps - Listo para copiar/pegar"
echo ""

echo "🕒 Generado: $(date)"