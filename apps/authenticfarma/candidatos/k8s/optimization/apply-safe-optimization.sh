#!/bin/bash
# Script para aplicar optimización completa de forma segura

echo "🛡️ Aplicando optimización segura de candidatos..."

# 1. Aplicar ConfigMap
echo "📝 Aplicando ConfigMap..."
kubectl apply -f optimization-config.yaml

# 2. Ejecutar optimización
echo "⚡ Ejecutando optimización..."
kubectl delete job candidatos-optimization -n authenticfarma-prod 2>/dev/null || true
kubectl apply -f optimization-job.yaml

# 3. Esperar completación
echo "⏳ Esperando completación..."
kubectl wait --for=condition=complete job/candidatos-optimization -n authenticfarma-prod --timeout=300s

# 4. Verificar resultado
if kubectl logs job/candidatos-optimization -n authenticfarma-prod | grep -q "optimización completada"; then
    echo "✅ Optimización exitosa"
    
    # Test de la aplicación
    echo "🔍 Verificando aplicación..."
    response=$(curl -s -o /dev/null -w "%{http_code}" https://candidatos.authenticfarma.com/login)
    
    if [[ $response -eq 200 || $response -eq 302 ]]; then
        echo "✅ Aplicación funcionando correctamente"
    else
        echo "⚠️ Aplicación retorna código: $response"
        echo "🔄 Ejecutando rollback automático..."
        kubectl apply -f rollback-job.yaml
    fi
else
    echo "❌ Optimización falló - revisar logs:"
    kubectl logs job/candidatos-optimization -n authenticfarma-prod
fi

# 5. Limpiar job
echo "🧹 Limpiando recursos..."
kubectl delete job candidatos-optimization -n authenticfarma-prod --ignore-not-found=true

echo "🏁 Proceso completado"
