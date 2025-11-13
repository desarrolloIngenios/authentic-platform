#!/bin/bash

# =================================================================
# SCRIPT DE RESTAURACIÓN - AUTHENTIC CANDIDATOS
# Configuración funcionando del 13 de noviembre de 2025
# =================================================================

set -e  # Exit on any error

NAMESPACE="authenticfarma-candidatos"
BACKUP_DIR="/Users/Devapp/authentic-platform/backups/authenticfarma-candidatos-working-config"
PROJECT_ID="authentic-prod-464216"

echo "🔄 Iniciando restauración de configuración funcionando..."
echo "📅 Backup del: 13 de noviembre de 2025"
echo "🎯 Namespace: $NAMESPACE"
echo ""

# Verificar que el usuario esté conectado al cluster correcto
echo "🔍 Verificando conexión al cluster..."
CURRENT_CONTEXT=$(kubectl config current-context)
echo "📍 Contexto actual: $CURRENT_CONTEXT"

# Verificar que el namespace existe
if ! kubectl get namespace $NAMESPACE > /dev/null 2>&1; then
    echo "⚠️  El namespace $NAMESPACE no existe. Creándolo..."
    kubectl create namespace $NAMESPACE
else
    echo "✅ Namespace $NAMESPACE existe"
fi

# Función para confirmar acciones peligrosas
confirm_action() {
    read -p "⚠️  $1 (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "❌ Operación cancelada"
        exit 1
    fi
}

# Menú de opciones
echo ""
echo "🛠️  Selecciona el tipo de restauración:"
echo "1) 🔄 Restauración completa (PELIGROSO - borra todo y restaura)"
echo "2) 🎯 Aplicar solo deployment funcionando"
echo "3) 🔐 Restaurar solo secretos"
echo "4) 📋 Mostrar configuración actual"
echo "5) 🧪 Ejecutar pruebas de conectividad"
echo ""

read -p "Selecciona opción (1-5): " option

case $option in
    1)
        echo ""
        confirm_action "¿ESTÁS SEGURO? Esto eliminará TODOS los recursos del namespace $NAMESPACE y los restaurará desde backup"
        
        echo "🗑️  Eliminando recursos existentes..."
        kubectl delete all --all -n $NAMESPACE --ignore-not-found=true
        kubectl delete secrets,configmaps,pvc --all -n $NAMESPACE --ignore-not-found=true
        
        echo "📥 Restaurando desde backup completo..."
        kubectl apply -f "$BACKUP_DIR/namespace-complete-backup.yaml"
        
        echo "⏳ Esperando a que los pods estén listos..."
        kubectl wait --for=condition=ready pod -l app=authenticfarma-candidatos -n $NAMESPACE --timeout=300s
        ;;
        
    2)
        echo ""
        echo "🎯 Aplicando deployment funcionando..."
        
        # Eliminar deployment actual si existe
        if kubectl get deployment authentic-candidatos -n $NAMESPACE > /dev/null 2>&1; then
            confirm_action "¿Eliminar deployment actual 'authentic-candidatos'?"
            kubectl delete deployment authentic-candidatos -n $NAMESPACE
        fi
        
        if kubectl get deployment authenticfarma-candidatos -n $NAMESPACE > /dev/null 2>&1; then
            confirm_action "¿Eliminar deployment actual 'authenticfarma-candidatos'?"
            kubectl delete deployment authenticfarma-candidatos -n $NAMESPACE
        fi
        
        # Aplicar deployment funcionando
        kubectl apply -f "$BACKUP_DIR/git-deployment-working.yaml"
        
        echo "⏳ Esperando a que el deployment esté listo..."
        kubectl rollout status deployment/authentic-candidatos -n $NAMESPACE --timeout=300s
        ;;
        
    3)
        echo ""
        echo "🔐 Restaurando secretos..."
        
        # Backup secreto actual si existe
        if kubectl get secret laravel-secrets -n $NAMESPACE > /dev/null 2>&1; then
            echo "💾 Haciendo backup del secreto actual..."
            kubectl get secret laravel-secrets -n $NAMESPACE -o yaml > "/tmp/laravel-secrets-backup-$(date +%Y%m%d_%H%M%S).yaml"
            
            confirm_action "¿Reemplazar secreto actual 'laravel-secrets'?"
            kubectl delete secret laravel-secrets -n $NAMESPACE
        fi
        
        kubectl apply -f "$BACKUP_DIR/working-secrets.yaml"
        ;;
        
    4)
        echo ""
        echo "📋 Configuración actual:"
        echo ""
        echo "🏷️  Deployments:"
        kubectl get deployments -n $NAMESPACE -o wide || echo "❌ No se encontraron deployments"
        
        echo ""
        echo "🏗️  Pods:"
        kubectl get pods -n $NAMESPACE -o wide || echo "❌ No se encontraron pods"
        
        echo ""
        echo "🔗 Services:"
        kubectl get services -n $NAMESPACE || echo "❌ No se encontraron services"
        
        echo ""
        echo "🔐 Secrets:"
        kubectl get secrets -n $NAMESPACE || echo "❌ No se encontraron secrets"
        
        if kubectl get deployment authentic-candidatos -n $NAMESPACE > /dev/null 2>&1; then
            echo ""
            echo "🔧 Argumentos del Cloud SQL Proxy actual:"
            kubectl get deployment authentic-candidatos -n $NAMESPACE -o jsonpath='{.spec.template.spec.containers[0].args}' | jq . || echo "❌ No se pudo obtener configuración"
        fi
        ;;
        
    5)
        echo ""
        echo "🧪 Ejecutando pruebas de conectividad..."
        
        # Verificar que hay pods corriendo
        PODS=$(kubectl get pods -n $NAMESPACE -l app=authenticfarma-candidatos --field-selector=status.phase=Running -o jsonpath='{.items[*].metadata.name}')
        
        if [ -z "$PODS" ]; then
            echo "❌ No se encontraron pods en estado Running"
            exit 1
        fi
        
        POD=$(echo $PODS | awk '{print $1}')
        echo "🎯 Usando pod: $POD"
        
        echo ""
        echo "🔗 Prueba 1: Conectividad a la base de datos..."
        kubectl exec -n $NAMESPACE $POD -c app -- php -r "
        try { 
            \$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=authentic', 'candidatosuser', getenv('DB_PASSWORD')); 
            echo '✅ Conexión a base de datos: EXITOSA\n'; 
        } catch(Exception \$e) { 
            echo '❌ Conexión a base de datos: FALLÓ - ' . \$e->getMessage() . '\n'; 
        }"
        
        echo ""
        echo "🌐 Prueba 2: Health check de la aplicación..."
        HTTP_CODE=$(kubectl exec -n $NAMESPACE $POD -c app -- curl -s -o /dev/null -w "%{http_code}" http://localhost/login)
        
        if [ "$HTTP_CODE" = "200" ]; then
            echo "✅ Health check: EXITOSO (HTTP $HTTP_CODE)"
        else
            echo "❌ Health check: FALLÓ (HTTP $HTTP_CODE)"
        fi
        
        echo ""
        echo "📊 Prueba 3: Recursos del pod..."
        kubectl describe pod $POD -n $NAMESPACE | grep -A 10 "Containers:" | head -20
        ;;
        
    *)
        echo "❌ Opción no válida"
        exit 1
        ;;
esac

echo ""
echo "🎉 Operación completada!"

# Mostrar estado final si aplicamos recursos
if [[ $option == 1 || $option == 2 ]]; then
    echo ""
    echo "📊 Estado final:"
    kubectl get pods -n $NAMESPACE -l app=authenticfarma-candidatos
    
    echo ""
    echo "🔍 Para verificar que todo funciona, ejecuta:"
    echo "   $0"
    echo "   Luego selecciona opción 5 (pruebas de conectividad)"
fi

echo ""
echo "📚 Documentación completa disponible en:"
echo "   $BACKUP_DIR/WORKING_CONFIG_DOCUMENTATION.md"