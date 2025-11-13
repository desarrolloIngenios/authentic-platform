#!/bin/bash

# 🔑 Script para generar nueva clave JSON de Google Cloud
# Authentic Platform - GCP Service Account Key Generator

set -e

echo "🔑 Generando nueva clave JSON para GitHub Actions..."

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

info() {
    echo -e "${BLUE}[GCP]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si gcloud está instalado y autenticado
if ! command -v gcloud &> /dev/null; then
    error "❌ gcloud CLI no está instalado"
    echo ""
    echo "Instalar gcloud CLI:"
    echo "https://cloud.google.com/sdk/docs/install"
    exit 1
fi

# Verificar autenticación
if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
    error "❌ No hay cuentas autenticadas en gcloud"
    echo ""
    echo "Ejecutar: gcloud auth login"
    exit 1
fi

log "📋 Verificando Service Account..."

SERVICE_ACCOUNT="github-actions-sa@authentic-prod-464216.iam.gserviceaccount.com"
PROJECT_ID="authentic-prod-464216"

# Verificar que el Service Account existe
if ! gcloud iam service-accounts describe $SERVICE_ACCOUNT --project=$PROJECT_ID &>/dev/null; then
    error "❌ Service Account no encontrado: $SERVICE_ACCOUNT"
    echo ""
    echo "Crear el Service Account primero:"
    echo "gcloud iam service-accounts create github-actions-sa \\"
    echo "  --display-name='GitHub Actions Service Account' \\"
    echo "  --project=$PROJECT_ID"
    exit 1
fi

log "✅ Service Account encontrado: $SERVICE_ACCOUNT"

# Generar nueva clave
KEY_FILE="github-sa-key-$(date +%Y%m%d-%H%M%S).json"

log "🔑 Generando nueva clave JSON..."
if gcloud iam service-accounts keys create $KEY_FILE \
   --iam-account=$SERVICE_ACCOUNT \
   --project=$PROJECT_ID; then
    
    info "✅ Clave generada: $KEY_FILE"
else
    error "❌ Error generando la clave"
    exit 1
fi

echo ""
info "📋 Contenido de la clave JSON:"
echo ""
echo "============================================"
cat $KEY_FILE
echo ""
echo "============================================"
echo ""

log "📝 Pasos para actualizar GitHub Secret:"
echo ""
echo "1. 📋 Copiar TODO el contenido JSON de arriba (desde { hasta })"
echo ""
echo "2. 🔗 Ir a GitHub Secrets:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo ""
echo "3. 🔄 Actualizar secreto 'GCP_SA_KEY':"
echo "   - Click en 'Update'"
echo "   - Pegar el JSON completo"
echo "   - Save"
echo ""
echo "4. ✅ Probar el workflow:"
echo "   git push origin dev"
echo ""

warn "🔒 Seguridad: El archivo $KEY_FILE contiene credenciales sensibles"
echo ""
read -p "¿Eliminar el archivo local después de copiarlo? (Y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]] || [[ -z $REPLY ]]; then
    rm -f $KEY_FILE
    log "🗑️ Archivo $KEY_FILE eliminado"
else
    warn "⚠️ Recuerda eliminar $KEY_FILE manualmente después de usar"
fi

echo ""
log "🎯 ¡Clave JSON generada exitosamente!"
echo ""
echo "🔗 Enlaces útiles:"
echo "   - GitHub Secrets: https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions"
echo "   - GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"