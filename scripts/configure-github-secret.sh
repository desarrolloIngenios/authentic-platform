#!/bin/bash

# 🔑 Configurador automático de GitHub Secret para GCP Service Account
# Archivo: /Users/ottofonseca/Downloads/authentic-prod-464216-11ce7e813a78.json
# Fecha: 9 de noviembre de 2025

echo "🔑 Configurando GitHub Secret GCP_SA_KEY"
echo "========================================"

JSON_FILE="/Users/ottofonseca/Downloads/authentic-prod-464216-11ce7e813a78.json"

echo ""
echo "📋 Validando archivo JSON..."

if [ ! -f "$JSON_FILE" ]; then
    echo "❌ Error: Archivo no encontrado: $JSON_FILE"
    exit 1
fi

# Validar que es JSON válido
if ! jq . "$JSON_FILE" > /dev/null 2>&1; then
    echo "❌ Error: Archivo no es JSON válido"
    exit 1
fi

echo "✅ Archivo JSON válido encontrado"

# Extraer información del service account
PROJECT_ID=$(jq -r '.project_id' "$JSON_FILE")
CLIENT_EMAIL=$(jq -r '.client_email' "$JSON_FILE")
PRIVATE_KEY_ID=$(jq -r '.private_key_id' "$JSON_FILE")

echo ""
echo "📋 Información del Service Account:"
echo "   Project ID: $PROJECT_ID"
echo "   Email: $CLIENT_EMAIL"
echo "   Key ID: $PRIVATE_KEY_ID"
echo ""

echo "🎯 PASOS PARA CONFIGURAR EN GITHUB:"
echo "=================================="
echo ""
echo "1. 🔗 Abrir en browser:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo ""
echo "2. 🗑️ Eliminar secret existente 'GCP_SA_KEY' (si existe)"
echo ""
echo "3. ➕ Crear nuevo secret:"
echo "   - Name: GCP_SA_KEY"
echo "   - Value: [COPIAR EL JSON COMPLETO DE ABAJO]"
echo ""
echo "4. 📋 JSON PARA COPIAR:"
echo "======================="

# Mostrar el JSON completo y minificado para copiar
echo ""
jq -c . "$JSON_FILE"
echo ""

echo "======================="
echo ""
echo "5. 💾 Guardar el secret en GitHub"
echo ""
echo "6. ✅ Verificar configuración:"

# Crear un commit de test para activar el workflow
echo ""
echo "🧪 OPCIONAL - Test automático:"
echo ""
echo "Para probar que el secret funciona, puedo crear un commit de test"
echo "que activará el workflow y validará la autenticación con GCP."
echo ""
echo "¿Crear commit de test? (y/n):"
read -r response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo ""
    echo "🔄 Creando commit de test..."
    
    # Crear archivo de test
    echo "# Test GCP Authentication - $(date)" > /tmp/gcp-auth-test.md
    echo "" >> /tmp/gcp-auth-test.md
    echo "Service Account configurado: $CLIENT_EMAIL" >> /tmp/gcp-auth-test.md
    echo "Project ID: $PROJECT_ID" >> /tmp/gcp-auth-test.md
    echo "Test ejecutado: $(date -Iseconds)" >> /tmp/gcp-auth-test.md
    
    # Agregar al repositorio
    cp /tmp/gcp-auth-test.md docs/gcp-auth-test.md
    
    # Commit y push
    git add docs/gcp-auth-test.md
    git commit -m "test: Validar autenticación GCP con nuevo service account key

- Service Account: $CLIENT_EMAIL
- Project: $PROJECT_ID
- Key ID: $PRIVATE_KEY_ID

Este commit activará el workflow para probar la autenticación con Google Cloud."
    
    echo ""
    echo "✅ Commit de test creado"
    echo "🚀 Push para activar workflow:"
    echo "   git push origin dev"
    echo ""
    echo "📊 Monitorear en:"
    echo "   https://github.com/desarrolloIngenios/authentic-platform/actions"
    
else
    echo "ℹ️ Test no ejecutado. Puedes probar manualmente haciendo push."
fi

echo ""
echo "🎯 RESUMEN DE ACCIONES:"
echo "====================="
echo "✅ JSON validado: $JSON_FILE"
echo "📋 Service Account: $CLIENT_EMAIL"
echo "🔗 GitHub Secrets: https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo "🎯 Secret Name: GCP_SA_KEY"
echo ""
echo "📝 SIGUIENTES PASOS:"
echo "1. Configurar secret en GitHub (copiar JSON de arriba)"
echo "2. Hacer push para activar workflow"
echo "3. Verificar que google-github-actions/auth funcione"
echo ""

# Guardar JSON en archivo temporal para fácil acceso
cp "$JSON_FILE" /tmp/gcp-sa-key-for-github.json
echo "💾 JSON copiado a: /tmp/gcp-sa-key-for-github.json"
echo "📋 Para ver el JSON: cat /tmp/gcp-sa-key-for-github.json"