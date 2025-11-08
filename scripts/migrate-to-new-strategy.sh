#!/bin/bash

# 🚀 Script de Migración CI/CD Strategy
# Authentic Platform - GitOps with Dev/Prod environments

set -e

echo "🚀 Iniciando migración a nueva estrategia CI/CD..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# Verificar prerrequisitos
log "Verificando prerrequisitos..."

if ! command -v kubectl &> /dev/null; then
    error "kubectl no está instalado"
fi

if ! command -v git &> /dev/null; then
    error "git no está instalado"
fi

# Verificar conexión a cluster
if ! kubectl cluster-info &> /dev/null; then
    error "No se puede conectar al cluster de Kubernetes"
fi

log "✅ Prerrequisitos verificados"

# Backup de configuraciones actuales
log "📋 Creando backup de configuraciones actuales..."

BACKUP_DIR="./backup-$(date +%Y%m%d-%H%M%S)"
mkdir -p $BACKUP_DIR

# Backup ArgoCD applications
kubectl get applications -n argocd -o yaml > $BACKUP_DIR/current-applications.yaml 2>/dev/null || warn "No se pudieron hacer backup de las aplicaciones ArgoCD"

log "✅ Backup creado en: $BACKUP_DIR"

# Aplicar nueva estrategia
log "🏗️ Aplicando nueva estructura ArgoCD..."

# 1. Aplicar App-of-Apps DEV
log "Aplicando ArgoCD DEV environment..."
kubectl apply -f ci-cd/environments/dev/app-of-apps.yaml

# 2. Aplicar App-of-Apps PROD  
log "Aplicando ArgoCD PROD environment..."
kubectl apply -f ci-cd/environments/prod/app-of-apps.yaml

# Esperar a que las aplicaciones se creen
log "⏳ Esperando a que ArgoCD procese las nuevas aplicaciones..."
sleep 10

# Verificar aplicaciones
log "🔍 Verificando aplicaciones creadas..."

# Check DEV apps
if kubectl get application -n argocd authentic-platform-dev &> /dev/null; then
    log "✅ App DEV creada correctamente"
else
    warn "❌ App DEV no se creó correctamente"
fi

# Check PROD apps  
if kubectl get application -n argocd authentic-platform-prod &> /dev/null; then
    log "✅ App PROD creada correctamente"
else
    warn "❌ App PROD no se creó correctamente"
fi

# Listar todas las aplicaciones
log "📋 Aplicaciones ArgoCD actuales:"
kubectl get applications -n argocd --no-headers | awk '{print "  - " $1 " (" $3 ")"}'

# Información sobre siguientes pasos
echo ""
echo -e "${BLUE}🎉 MIGRACIÓN COMPLETADA${NC}"
echo ""
echo -e "${YELLOW}📋 SIGUIENTES PASOS:${NC}"
echo ""
echo "1. 🔍 Verificar ArgoCD UI:"
echo "   - DEV:  https://argo.authenticfarma.com/applications/authentic-platform-dev"
echo "   - PROD: https://argo.authenticfarma.com/applications/authentic-platform-prod"
echo ""
echo "2. 🔧 Configurar GitHub Secrets:"
echo "   - GCP_SA_KEY: Service Account key para registry"
echo "   - GITHUB_TOKEN: Token para crear PRs automáticos"
echo ""
echo "3. 🚀 Probar el flujo:"
echo "   - Push cambios a rama 'dev'"
echo "   - Verificar deploy automático en DEV"
echo "   - Review PR automático dev→main"
echo "   - Merge y sync manual en PROD"
echo ""
echo "4. 🧹 Limpiar aplicaciones antiguas (OPCIONAL):"
echo "   kubectl delete application -n argocd <app-antigua>"
echo ""
echo -e "${GREEN}✅ La nueva estrategia CI/CD está lista!${NC}"
echo ""

# Mostrar comandos útiles
echo -e "${BLUE}📋 COMANDOS ÚTILES:${NC}"
echo ""
echo "# Ver aplicaciones ArgoCD:"
echo "kubectl get applications -n argocd"
echo ""
echo "# Sync manual aplicación DEV:"
echo "argocd app sync authentic-platform-dev"
echo ""
echo "# Sync manual aplicación PROD:"
echo "argocd app sync authentic-platform-prod"
echo ""
echo "# Ver logs ArgoCD:"
echo "kubectl logs -n argocd deployment/argocd-application-controller"
echo ""

log "🎯 Migración completada exitosamente!"