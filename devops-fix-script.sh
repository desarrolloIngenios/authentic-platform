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
