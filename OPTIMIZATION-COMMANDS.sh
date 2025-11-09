#!/bin/bash

# 🚀 COMANDOS PARA APLICAR OPTIMIZACIONES EN PRODUCCIÓN
# Ejecutar estos comandos en el cluster de Kubernetes

echo "🚀 COMANDOS PARA OPTIMIZAR CANDIDATOS EN PRODUCCIÓN"
echo "=================================================="
echo ""

echo "📋 1. OBTENER EL POD DE CANDIDATOS:"
echo 'kubectl get pods -n authenticfarma-prod -l app=candidatos'
echo ""

echo "📋 2. CONECTAR AL POD (reemplazar <pod-name> con el nombre real):"
echo 'kubectl exec -it <pod-name> -n authenticfarma-prod -- bash'
echo ""

echo "📋 3. DENTRO DEL POD, EJECUTAR ESTAS OPTIMIZACIONES:"
echo ""

echo "# Limpiar caches existentes"
echo "php artisan cache:clear"
echo "php artisan config:clear"
echo "php artisan route:clear"
echo "php artisan view:clear"
echo ""

echo "# Optimizar autoloader (si composer está disponible)"
echo "composer dump-autoload --optimize"
echo ""

echo "# Crear caches optimizados"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo ""

echo "# Verificar configuración"
echo "php artisan route:list | head -5  # Test rápido de performance"
echo ""

echo "📋 4. VERIFICAR LOGS DESPUÉS DE OPTIMIZACIÓN:"
echo 'kubectl logs <pod-name> -n authenticfarma-prod --tail=50'
echo ""

echo "📋 5. MONITOREAR MÉTRICAS:"
echo 'kubectl top pods -n authenticfarma-prod'
echo ""

echo "🎯 ALTERNATIVA - SI NO TIENES ACCESO AL POD:"
echo "============================================"
echo ""

echo "1. 📧 Enviar este script al equipo de DevOps:"
cat << 'EOF'

# Script para ejecutar en el pod de candidatos
#!/bin/bash
cd /var/www/html || cd /app
php artisan cache:clear
php artisan config:clear  
php artisan route:clear
php artisan view:clear
composer dump-autoload --optimize 2>/dev/null || echo "Composer no disponible"
php artisan config:cache
php artisan route:cache  
php artisan view:cache
echo "✅ Optimizaciones aplicadas: $(date)"

EOF
echo ""

echo "2. 📊 O crear un Job de Kubernetes para aplicar las optimizaciones:"
cat << 'EOF'

apiVersion: batch/v1
kind: Job
metadata:
  name: optimize-candidatos
  namespace: authenticfarma-prod
spec:
  template:
    spec:
      containers:
      - name: optimizer
        image: <misma-imagen-que-candidatos>
        command: ["/bin/bash"]
        args:
          - -c
          - |
            cd /var/www/html
            php artisan cache:clear
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            echo "Optimización completada"
      restartPolicy: Never
  backoffLimit: 3

EOF
echo ""

echo "🔍 VERIFICACIÓN DE RESULTADOS:"
echo "=============================="
echo ""

echo "Probar estos endpoints después de la optimización:"
echo "curl -w '%{time_total}\\n' -o /dev/null -s https://candidatos.authenticfarma.com/"
echo "curl -w '%{time_total}\\n' -o /dev/null -s https://candidatos.authenticfarma.com/login"
echo ""

echo "🎯 MÉTRICAS OBJETIVO:"
echo "- Tiempo de respuesta < 0.3s para páginas principales"
echo "- Reducción de uso de memoria del pod"
echo "- Menos queries a base de datos"
echo ""

echo "📅 FECHA DE EJECUCIÓN: $(date)"