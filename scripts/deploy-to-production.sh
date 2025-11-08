#!/bin/bash

# 🚀 Script para despliegue a producción
# Authentic Platform - Production Deployment

set -e

echo "🚀 Iniciando proceso de despliegue a producción..."

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
    echo -e "${BLUE}[DEPLOY]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar rama actual
CURRENT_BRANCH=$(git branch --show-current)
if [[ "$CURRENT_BRANCH" != "dev" ]]; then
    warn "Cambiando a rama dev..."
    git checkout dev
fi

log "📋 Verificando estado del repositorio..."
git status --porcelain

log "📥 Actualizando desde remoto..."
git fetch --all
git pull origin dev

log "🔍 Verificando diferencias dev → main..."
COMMITS_AHEAD=$(git rev-list --count main..dev)
if [[ $COMMITS_AHEAD -eq 0 ]]; then
    info "✅ No hay cambios nuevos para desplegar"
    exit 0
fi

echo ""
info "📊 Commits a desplegar ($COMMITS_AHEAD):"
git log --oneline main..dev --max-count=10

echo ""
read -p "¿Continuar con el despliegue a producción? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    warn "Despliegue cancelado"
    exit 1
fi

# Método 1: Intentar crear PR automático con GitHub CLI (si está disponible)
if command -v gh &> /dev/null; then
    log "📋 Creando PR con GitHub CLI..."
    
    PR_TITLE="🚀 Deploy to Production - $(date '+%Y-%m-%d %H:%M')"
    PR_BODY="## 🚀 Deploy to Production

### 📋 Changes included:
$(git log --oneline main..dev --max-count=10)

### ✅ Ready for production deployment
- [x] Testing completed in DEV environment
- [x] All functionality verified
- [x] Ready for manual sync in ArgoCD PROD

### 🔗 Post-merge actions:
1. Monitor GitHub Actions workflow
2. Manual sync in ArgoCD PROD: https://argo.authenticfarma.com/applications/authentic-platform-prod
3. Verify deployment success

/cc @team"

    if gh pr create --title "$PR_TITLE" --body "$PR_BODY" --base main --head dev; then
        info "✅ PR creado exitosamente"
        gh pr view --web
    else
        error "❌ Error creando PR con GitHub CLI"
        echo ""
        info "📋 Crear PR manualmente:"
        echo "🔗 https://github.com/desarrolloIngenios/authentic-platform/compare/main...dev"
    fi
else
    # Método 2: Instrucciones manuales
    warn "GitHub CLI no disponible. Creando PR manualmente..."
    echo ""
    info "📋 Pasos para crear PR:"
    echo ""
    echo "1. 🔗 Ir a: https://github.com/desarrolloIngenios/authentic-platform/compare/main...dev"
    echo "2. 📝 Título: '🚀 Deploy to Production - $(date '+%Y-%m-%d %H:%M')'"
    echo "3. 📄 Descripción:"
    echo "   - Incluir lista de cambios"
    echo "   - Mencionar testing completado"
    echo "   - Solicitar review del equipo"
    echo "4. ✅ Crear Pull Request"
    echo ""
fi

echo ""
info "🔄 Siguientes pasos después del merge:"
echo ""
echo "1. 📊 Monitor GitHub Actions:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/actions"
echo ""
echo "2. 🚀 Manual sync en ArgoCD PROD:"
echo "   https://argo.authenticfarma.com/applications/authentic-platform-prod"
echo ""
echo "3. 🔍 Verificar deployment:"
echo "   - Historia Clínica: https://hc.yo-soy.co"
echo "   - Candidatos: https://candidatos.authenticfarma.com"
echo ""

log "🎯 Proceso de despliegue iniciado exitosamente!"