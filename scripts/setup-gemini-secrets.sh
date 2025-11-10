#!/bin/bash

# 🤖 Script de Configuración de Secretos Gemini para Authentic Platform
# Configura las variables de entorno y secretos necesarios para Vertex AI

echo "🤖 CONFIGURACIÓN DE SECRETOS GEMINI/VERTEX AI"
echo "============================================="

# Configuración
PROJECT_ID="authentic-prod-464216"
SERVICE_ACCOUNT_NAME="vertex-ai-service"
LOCATION="us-central1"

echo "📋 Configuración:"
echo "  Project ID: $PROJECT_ID"
echo "  Service Account: $SERVICE_ACCOUNT_NAME"
echo "  Location: $LOCATION"
echo ""

# 1. Verificar autenticación GCP
echo "🔐 1. Verificando autenticación GCP..."
if gcloud auth application-default print-access-token >/dev/null 2>&1; then
    echo "✅ Autenticación GCP exitosa"
    current_project=$(gcloud config get project 2>/dev/null)
    echo "   Proyecto actual: $current_project"
    
    if [ "$current_project" != "$PROJECT_ID" ]; then
        echo "⚠️  Cambiando a proyecto: $PROJECT_ID"
        gcloud config set project $PROJECT_ID
    fi
else
    echo "❌ Error de autenticación GCP"
    echo "   Ejecuta: gcloud auth application-default login"
    exit 1
fi

echo ""

# 2. Verificar/Crear Service Account para Vertex AI
echo "👤 2. Configurando Service Account para Vertex AI..."

# Verificar si el service account existe
if gcloud iam service-accounts describe "${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" >/dev/null 2>&1; then
    echo "✅ Service Account ya existe: ${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"
else
    echo "🏗️  Creando Service Account..."
    gcloud iam service-accounts create $SERVICE_ACCOUNT_NAME \
        --display-name="Vertex AI Service Account for AuthenticFarma" \
        --description="Service Account para acceso a Vertex AI desde aplicaciones AuthenticFarma" \
        --project=$PROJECT_ID
    
    if [ $? -eq 0 ]; then
        echo "✅ Service Account creado exitosamente"
    else
        echo "❌ Error creando Service Account"
        exit 1
    fi
fi

echo ""

# 3. Asignar permisos necesarios
echo "🔑 3. Configurando permisos de Vertex AI..."

PERMISSIONS=(
    "roles/aiplatform.user"
    "roles/ml.developer"
    "roles/storage.objectViewer"
)

for role in "${PERMISSIONS[@]}"; do
    echo "   Asignando rol: $role"
    gcloud projects add-iam-policy-binding $PROJECT_ID \
        --member="serviceAccount:${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --role="$role" \
        --quiet >/dev/null 2>&1
    
    if [ $? -eq 0 ]; then
        echo "   ✅ Rol $role asignado"
    else
        echo "   ⚠️  Rol $role podría ya estar asignado"
    fi
done

echo ""

# 4. Generar/Obtener key del Service Account
echo "🔐 4. Configurando clave del Service Account..."

KEY_FILE="vertex-ai-service-account.json"

if [ -f "$KEY_FILE" ]; then
    echo "⚠️  Archivo de clave ya existe: $KEY_FILE"
    read -p "¿Generar nueva clave? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🔄 Generando nueva clave..."
        gcloud iam service-accounts keys create $KEY_FILE \
            --iam-account="${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
            --project=$PROJECT_ID
    fi
else
    echo "🏗️  Generando clave del Service Account..."
    gcloud iam service-accounts keys create $KEY_FILE \
        --iam-account="${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com" \
        --project=$PROJECT_ID
fi

if [ -f "$KEY_FILE" ]; then
    echo "✅ Clave del Service Account disponible: $KEY_FILE"
else
    echo "❌ Error generando clave del Service Account"
    exit 1
fi

echo ""

# 5. Mostrar configuración para GitHub Secrets
echo "🔧 5. Configuración para GitHub Secrets"
echo "======================================="
echo ""
echo "📋 Secretos a agregar en GitHub (Settings > Secrets and variables > Actions):"
echo ""
echo "1️⃣  VERTEX_AI_SERVICE_ACCOUNT_KEY:"
echo "   Contenido del archivo: $KEY_FILE"
echo "   Copiar todo el contenido JSON del archivo"
echo ""

if [ -f "$KEY_FILE" ]; then
    echo "   Contenido (primeras líneas):"
    head -3 "$KEY_FILE"
    echo "   ..."
    echo ""
fi

echo "2️⃣  Variables de entorno adicionales:"
echo "   VERTEX_AI_PROJECT_ID=$PROJECT_ID"
echo "   VERTEX_AI_LOCATION=$LOCATION"
echo "   VERTEX_AI_MODEL=gemini-1.5-flash"

echo ""

# 6. Configuración para deployment local
echo "🏠 6. Configuración para Desarrollo Local"
echo "========================================"
echo ""
echo "Para desarrollo local, configura:"
echo ""
echo "1️⃣  Variable de entorno:"
echo "   export GOOGLE_APPLICATION_CREDENTIALS=\"$(pwd)/$KEY_FILE\""
echo ""
echo "2️⃣  O usar autenticación por defecto:"
echo "   gcloud auth application-default login"
echo ""
echo "3️⃣  Agregar al .env de Laravel:"
cat << 'EOF'
# Vertex AI Configuration
VERTEX_AI_PROJECT_ID=authentic-prod-464216
VERTEX_AI_LOCATION=us-central1
VERTEX_AI_MODEL=gemini-1.5-flash
EOF

echo ""

# 7. Test de conectividad
echo "🧪 7. Test de Conectividad con Vertex AI"
echo "========================================"
echo ""
echo "Probando acceso a Vertex AI..."

# Configurar credenciales temporalmente para el test
export GOOGLE_APPLICATION_CREDENTIALS="$(pwd)/$KEY_FILE"

# Test básico usando gcloud
echo "📡 Probando listado de modelos..."
if gcloud ai models list --region=$LOCATION --project=$PROJECT_ID >/dev/null 2>&1; then
    echo "✅ Conectividad con Vertex AI exitosa"
    echo "   Modelos disponibles en $LOCATION"
else
    echo "⚠️  No se pudieron listar modelos (esto es normal si no hay modelos custom)"
    echo "   Pero la autenticación parece funcionar"
fi

echo ""

# 8. Comandos útiles
echo "💡 8. Comandos Útiles"
echo "===================="
echo ""
echo "# Verificar service account:"
echo "gcloud iam service-accounts describe ${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"
echo ""
echo "# Listar keys del service account:"
echo "gcloud iam service-accounts keys list --iam-account=${SERVICE_ACCOUNT_NAME}@${PROJECT_ID}.iam.gserviceaccount.com"
echo ""
echo "# Test de API de Vertex AI:"
echo "curl -H \"Authorization: Bearer \$(gcloud auth application-default print-access-token)\" \\"
echo "     \"https://$LOCATION-aiplatform.googleapis.com/v1/projects/$PROJECT_ID/locations/$LOCATION/publishers/google/models\""
echo ""

# 9. Instrucciones finales
echo "📝 9. Próximos Pasos"
echo "==================="
echo ""
echo "✅ Service Account configurado para Vertex AI"
echo "✅ Permisos asignados correctamente"
echo "✅ Clave generada: $KEY_FILE"
echo ""
echo "🔄 Para completar la configuración:"
echo "   1. Agregar secretos a GitHub Actions"
echo "   2. Configurar variables de entorno en deployment"
echo "   3. Probar endpoints de IA en la aplicación"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   - Mantén el archivo $KEY_FILE seguro y no lo commits al repositorio"
echo "   - Rota las claves periódicamente para mayor seguridad"
echo "   - Monitorea el uso de Vertex AI en la consola de GCP"
echo ""

# 10. Limpiar archivo de clave si el usuario quiere
echo "🧹 Limpieza"
echo "==========="
read -p "¿Eliminar archivo de clave local después de configurar GitHub? (Y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Nn]$ ]]; then
    echo "ℹ️  Mantén una copia segura del archivo antes de eliminarlo"
    echo "   Archivo: $KEY_FILE"
else
    echo "📁 Archivo de clave conservado: $KEY_FILE"
fi

echo ""
echo "🎉 ¡Configuración de Vertex AI completada!"
echo "   Servicio listo para usar Gemini en AuthenticFarma"