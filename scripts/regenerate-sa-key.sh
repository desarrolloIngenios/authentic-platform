#!/bin/bash

# 🔑 Script para regenerar Service Account Key
# Service Account: github-actions-sa@authentic-prod-464216.iam.gserviceaccount.com
# Fecha: 9 de noviembre de 2025

echo "🔑 Regenerando Service Account Key para GitHub Actions"
echo "====================================================="

SERVICE_ACCOUNT="github-actions-sa@authentic-prod-464216.iam.gserviceaccount.com"
PROJECT_ID="authentic-prod-464216"

echo ""
echo "📋 Service Account detectado: $SERVICE_ACCOUNT"
echo "📋 Project ID: $PROJECT_ID"
echo ""

echo "[PASO 1] 🔍 Verificar roles del service account"
echo ""
echo "🎯 Roles requeridos para el service account:"
echo "✅ Container Registry Service Agent"
echo "✅ Kubernetes Engine Developer" 
echo "✅ Storage Admin"
echo "✅ Cloud Build Service Account"
echo "✅ Service Account Token Creator"
echo ""

echo "[PASO 2] 🔑 Generar nueva key JSON"
echo ""
echo "📋 OPCIÓN A - Via Google Cloud Console:"
echo "1. 🔗 Ir a: https://console.cloud.google.com/iam-admin/serviceaccounts?project=authentic-prod-464216"
echo "2. 🔍 Buscar: github-actions-sa"
echo "3. 📝 Click en el service account"
echo "4. 🔑 Ir a pestaña 'Keys'"
echo "5. ➕ Click 'ADD KEY' > 'Create new key'"
echo "6. 📋 Seleccionar 'JSON'"
echo "7. 💾 Download del archivo JSON"
echo ""

echo "📋 OPCIÓN B - Via gcloud CLI:"
echo ""

# Verificar si gcloud está instalado
if command -v gcloud >/dev/null 2>&1; then
    echo "✅ gcloud CLI detectado"
    echo ""
    
    # Verificar autenticación
    if gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
        ACTIVE_ACCOUNT=$(gcloud auth list --filter=status:ACTIVE --format="value(account)")
        echo "🔐 Cuenta activa: $ACTIVE_ACCOUNT"
        echo ""
        
        echo "🚀 Comando para generar key:"
        echo "gcloud iam service-accounts keys create ~/github-actions-sa-key.json \\"
        echo "  --iam-account=$SERVICE_ACCOUNT \\"
        echo "  --project=$PROJECT_ID"
        echo ""
        
        echo "¿Ejecutar comando automáticamente? (y/n):"
        read -r response
        if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
            echo "🔄 Generando key..."
            
            if gcloud iam service-accounts keys create ~/github-actions-sa-key.json \
                --iam-account="$SERVICE_ACCOUNT" \
                --project="$PROJECT_ID"; then
                
                echo "✅ Key generada exitosamente: ~/github-actions-sa-key.json"
                echo ""
                
                # Validar el JSON
                if jq . ~/github-actions-sa-key.json > /dev/null 2>&1; then
                    echo "✅ JSON válido"
                    echo ""
                    echo "📋 Contenido de la key (para copiar a GitHub):"
                    echo "=============================================="
                    cat ~/github-actions-sa-key.json
                    echo ""
                    echo "=============================================="
                    echo ""
                    echo "🎯 SIGUIENTE PASO:"
                    echo "1. Copiar TODO el contenido JSON de arriba"
                    echo "2. Ir a: https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
                    echo "3. Buscar secret 'GCP_SA_KEY'"
                    echo "4. Eliminar secret existente"
                    echo "5. Crear nuevo secret 'GCP_SA_KEY'"
                    echo "6. Pegar el JSON completo como valor"
                else
                    echo "❌ Error: JSON inválido generado"
                fi
            else
                echo "❌ Error generando la key"
            fi
        else
            echo "ℹ️ Comando no ejecutado. Puedes ejecutarlo manualmente."
        fi
        
    else
        echo "❌ No hay cuenta autenticada en gcloud"
        echo "🔄 Ejecutar: gcloud auth login"
    fi
    
else
    echo "❌ gcloud CLI no está instalado"
    echo "📋 Usar OPCIÓN A (Google Cloud Console) instead"
fi

echo ""
echo "[PASO 3] 📋 Verificar permisos del service account"
echo ""
echo "🔍 Comando para verificar roles:"
echo "gcloud projects get-iam-policy $PROJECT_ID \\"
echo "  --flatten=\"bindings[].members\" \\"
echo "  --format='table(bindings.role)' \\"
echo "  --filter=\"bindings.members:$SERVICE_ACCOUNT\""

echo ""
echo "[PASO 4] 🧪 Validar JSON antes de subir a GitHub"
echo ""
echo "Una vez que tengas el archivo JSON:"
echo "bash /tmp/validate-json.sh"

echo ""
echo "[PASO 5] ⚡ Actualizar secret en GitHub"
echo ""
echo "🔗 URL directa: https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo ""
echo "Pasos:"
echo "1. 🗑️ Delete existing 'GCP_SA_KEY'"
echo "2. ➕ Add new secret 'GCP_SA_KEY'"  
echo "3. 📋 Paste JSON content (como texto plano)"
echo "4. 💾 Save secret"

echo ""
echo "[PASO 6] ✅ Test del workflow"
echo ""
echo "Después de actualizar el secret:"
echo "1. 🔄 Push any change to trigger workflow"
echo "2. 📊 Monitor: https://github.com/desarrolloIngenios/authentic-platform/actions"
echo "3. ✅ Verificar que google-github-actions/auth funcione"

echo ""
echo "🎯 RESUMEN:"
echo "=========="
echo "📧 Service Account: $SERVICE_ACCOUNT"
echo "🔑 Generar nueva key JSON"
echo "📋 Reemplazar secret GCP_SA_KEY en GitHub"
echo "🧪 Validar workflow functionality"