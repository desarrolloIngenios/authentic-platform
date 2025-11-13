#!/bin/bash

# 🔧 Script para corregir Google Service Account Key
# Fecha: 9 de noviembre de 2025

echo "🔧 Diagnóstico y corrección de GCP Service Account Key"
echo "======================================================"

echo ""
echo "[PASO 1] 📋 Verificar formato del service account key existente"

# Crear un service account key de ejemplo
cat > /tmp/service-account-template.json << 'EOF'
{
  "type": "service_account",
  "project_id": "authentic-prod-464216",
  "private_key_id": "YOUR_PRIVATE_KEY_ID",
  "private_key": "-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY_HERE\n-----END PRIVATE KEY-----\n",
  "client_email": "YOUR_SERVICE_ACCOUNT@authentic-prod-464216.iam.gserviceaccount.com",
  "client_id": "YOUR_CLIENT_ID",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/YOUR_SERVICE_ACCOUNT%40authentic-prod-464216.iam.gserviceaccount.com"
}
EOF

echo "✅ Template creado en /tmp/service-account-template.json"

echo ""
echo "[PASO 2] 🔍 Instrucciones para corregir el secret"
echo ""
echo "🎯 ACCIONES REQUERIDAS EN GOOGLE CLOUD:"
echo ""
echo "1. 🔗 Ir a Google Cloud Console:"
echo "   https://console.cloud.google.com/iam-admin/serviceaccounts?project=authentic-prod-464216"
echo ""
echo "2. 📋 Crear/regenerar service account:"
echo "   - Nombre: github-actions-sa"
echo "   - Roles necesarios:"
echo "     • Container Registry Service Agent"
echo "     • Kubernetes Engine Developer"
echo "     • Storage Admin"
echo "     • Cloud Build Service Account"
echo ""
echo "3. 🔑 Generar nueva key:"
echo "   - Seleccionar service account"
echo "   - Ir a 'Keys' tab"
echo "   - Add Key > Create new key"
echo "   - Tipo: JSON"
echo "   - Descargar archivo JSON"
echo ""
echo "[PASO 3] 🔧 Configurar secret en GitHub"
echo ""
echo "🎯 ACCIONES EN GITHUB:"
echo ""
echo "1. 🔗 Ir a repository settings:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo ""
echo "2. 🗑️ Eliminar secret existente:"
echo "   - Buscar: GCP_SA_KEY"
echo "   - Click 'Remove'"
echo ""
echo "3. ➕ Crear nuevo secret:"
echo "   - Name: GCP_SA_KEY"
echo "   - Value: [COPIAR TODO EL CONTENIDO del archivo JSON descargado]"
echo "   - IMPORTANTE: Copiar como texto plano, no como archivo"
echo ""
echo "[PASO 4] ✅ Validar formato del JSON"

echo ""
echo "🔍 VALIDACIÓN DEL JSON:"
echo "El archivo debe empezar con: {"
echo "Y terminar con: }"
echo "Sin caracteres especiales o codificación binaria"
echo ""

# Crear script de validación
cat > /tmp/validate-json.sh << 'EOF'
#!/bin/bash
echo "🔍 Validador de Service Account JSON"
echo ""
echo "Pegue el contenido de su service account key y presione Ctrl+D:"
echo ""

# Leer JSON del usuario
json_content=$(cat)

echo ""
echo "🔍 Validando formato..."

# Validar JSON
if echo "$json_content" | jq . > /dev/null 2>&1; then
    echo "✅ JSON válido!"
    
    # Verificar campos requeridos
    if echo "$json_content" | jq -e '.type, .project_id, .private_key, .client_email' > /dev/null; then
        echo "✅ Campos requeridos presentes"
        echo ""
        echo "📋 Proyecto: $(echo "$json_content" | jq -r '.project_id')"
        echo "📧 Email: $(echo "$json_content" | jq -r '.client_email')"
        echo ""
        echo "🎯 Este JSON es válido para GitHub Secrets"
    else
        echo "❌ Faltan campos requeridos en el service account"
    fi
else
    echo "❌ JSON inválido - revisar formato"
    echo ""
    echo "Posibles problemas:"
    echo "- Caracteres especiales"
    echo "- Codificación incorrecta"
    echo "- JSON incompleto"
fi
EOF

chmod +x /tmp/validate-json.sh

echo ""
echo "[PASO 5] 🧪 Herramienta de validación creada"
echo ""
echo "Para validar su JSON antes de subirlo:"
echo "bash /tmp/validate-json.sh"
echo ""
echo "[PASO 6] 🔄 Re-ejecutar workflow"
echo ""
echo "Después de corregir el secret:"
echo "1. Hacer push a branch dev"
echo "2. Verificar que el workflow pase"
echo "3. Monitorear logs de Google Cloud Auth"
echo ""

echo "🎯 RESUMEN:"
echo "=========="
echo "❌ Problema: Secret GCP_SA_KEY corrupto en GitHub"
echo "🔧 Solución: Regenerar service account key y reconfigurar secret"
echo "📋 Template: /tmp/service-account-template.json"
echo "🧪 Validador: /tmp/validate-json.sh"
echo ""
echo "💡 IMPORTANTE: El JSON debe copiarse como texto plano, no como archivo binario"
