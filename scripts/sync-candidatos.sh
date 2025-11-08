#!/bin/bash

# 🔄 Script para actualizar aplicación candidatos desde remoto
# Authentic Platform - Candidatos Sync

set -e

echo "🔄 Sincronizando aplicación candidatos con repositorio remoto..."

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

info() {
    echo -e "${BLUE}[SYNC]${NC} $1"
}

# Verificar si estamos en el directorio correcto
if [[ ! -d "apps/authenticfarma/candidatos" ]]; then
    echo "❌ Error: No se encuentra el directorio de candidatos"
    echo "Ejecuta este script desde la raíz del proyecto authentic-platform"
    exit 1
fi

log "📋 Estado actual del repositorio local"
git status --short

log "📥 Obteniendo últimos cambios del remoto"
git fetch --all

log "🔍 Verificando diferencias en candidatos..."
if git diff --quiet origin/main -- apps/authenticfarma/candidatos/; then
    info "✅ La aplicación candidatos está actualizada"
else
    warn "⚠️ Hay diferencias en candidatos entre local y remoto"
    echo ""
    echo "Diferencias encontradas:"
    git diff --name-only origin/main -- apps/authenticfarma/candidatos/
fi

log "🔄 Actualizando desde main..."
git checkout main
git pull origin main

log "📊 Verificando estado de ArgoCD para candidatos..."
kubectl get application authenticfarma-candidatos -n argocd -o jsonpath='{.status.sync.status}' 2>/dev/null || echo "No disponible"

log "🚀 Sincronizando ArgoCD..."
kubectl patch application authenticfarma-candidatos -n argocd --type merge -p '{"metadata":{"annotations":{"argocd.argoproj.io/refresh":"hard"}}}'

log "📋 Estado actual de candidatos en cluster:"
kubectl get deployment authenticfarma-candidatos -n authenticfarma-candidatos
kubectl get pods -n authenticfarma-candidatos

echo ""
info "✅ Sincronización completada"
echo ""
echo "🔗 Enlaces útiles:"
echo "   - ArgoCD: https://argo.authenticfarma.com/applications/authenticfarma-candidatos"
echo "   - Logs: kubectl logs -f deployment/authenticfarma-candidatos -n authenticfarma-candidatos"
echo ""

# Regresar a dev si estaba en dev
if [[ $(git branch --show-current) != "dev" ]] && git show-ref --verify --quiet refs/heads/dev; then
    warn "Regresando a rama dev..."
    git checkout dev
fi

log "🎯 Sincronización de candidatos completada exitosamente!"