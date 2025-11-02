#!/bin/bash
set -e

echo "🚀 Instalando Authentic Platform Infrastructure..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

warn() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}"
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}"
    exit 1
}

# Verificar herramientas requeridas
log "Verificando herramientas requeridas..."
command -v kubectl >/dev/null 2>&1 || error "kubectl no está instalado"
command -v helm >/dev/null 2>&1 || error "helm no está instalado"

# 1. Instalar NGINX Ingress Controller
log "Instalando NGINX Ingress Controller..."
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.2/deploy/static/provider/cloud/deploy.yaml

# Esperar a que esté listo
log "Esperando a que NGINX Ingress esté listo..."
kubectl wait --namespace ingress-nginx \
  --for=condition=ready pod \
  --selector=app.kubernetes.io/component=controller \
  --timeout=300s

# 2. Crear namespace cert-manager
log "Creando namespace cert-manager..."
kubectl create namespace cert-manager --dry-run=client -o yaml | kubectl apply -f -

# 3. Instalar Service Accounts para cert-manager
log "Instalando Service Accounts para cert-manager..."
kubectl apply -f infra/cert-manager/cert-manager-gke-serviceaccounts.yaml

# 4. Instalar cert-manager vía Helm
log "Instalando cert-manager..."
helm repo add jetstack https://charts.jetstack.io
helm repo update

helm upgrade --install cert-manager jetstack/cert-manager \
  --namespace cert-manager \
  --version v1.13.2 \
  --set installCRDs=true \
  --set global.leaderElection.namespace=cert-manager \
  --set serviceAccount.create=false \
  --set serviceAccount.name=cert-manager \
  --set cainjector.serviceAccount.create=false \
  --set cainjector.serviceAccount.name=cert-manager-cainjector \
  --set webhook.serviceAccount.create=false \
  --set webhook.serviceAccount.name=cert-manager-webhook \
  --set "extraArgs={--cluster-resource-namespace=cert-manager,--leader-election-namespace=cert-manager}"

# Esperar a que cert-manager esté listo
log "Esperando a que cert-manager esté listo..."
kubectl wait --namespace cert-manager \
  --for=condition=ready pod \
  --selector=app=cert-manager \
  --timeout=300s

# 5. Instalar ClusterIssuer
log "Instalando ClusterIssuer para Let's Encrypt..."
kubectl apply -f infra/cert-manager/cert-manager-issuer.yaml

# 6. Instalar certificados SSL
log "Instalando certificados SSL..."
kubectl apply -f infra/ssl-certificates/ssl-certificates.yaml

# 7. Instalar NGINX Ingress para aplicaciones
log "Instalando configuración de NGINX Ingress para aplicaciones..."
kubectl apply -f infra/nginx-ingress/

# 8. Verificar que todo esté funcionando
log "Verificando instalación..."

# Verificar NGINX Ingress
NGINX_IP=$(kubectl get service ingress-nginx-controller --namespace=ingress-nginx -o jsonpath='{.status.loadBalancer.ingress[0].ip}')
if [ -n "$NGINX_IP" ]; then
    log "✅ NGINX Ingress Controller: IP $NGINX_IP"
else
    warn "⚠️  NGINX Ingress Controller: IP pendiente"
fi

# Verificar cert-manager
CERT_MANAGER_READY=$(kubectl get pods -n cert-manager -l app=cert-manager -o jsonpath='{.items[*].status.conditions[?(@.type=="Ready")].status}')
if [ "$CERT_MANAGER_READY" = "True" ]; then
    log "✅ Cert-Manager: Funcionando"
else
    warn "⚠️  Cert-Manager: No está listo"
fi

# Verificar ClusterIssuer
ISSUER_READY=$(kubectl get clusterissuer letsencrypt-prod -o jsonpath='{.status.conditions[?(@.type=="Ready")].status}' 2>/dev/null || echo "NotFound")
if [ "$ISSUER_READY" = "True" ]; then
    log "✅ ClusterIssuer: Listo"
else
    warn "⚠️  ClusterIssuer: No está listo"
fi

log "🎉 Instalación de infraestructura base completada!"
log "📋 Próximos pasos:"
log "   1. Verificar que el IP externo de NGINX esté disponible"
log "   2. Configurar DNS para apuntar a $NGINX_IP"
log "   3. Desplegar aplicaciones con: kubectl apply -f platforms/"

# Mostrar comandos útiles
echo ""
log "🔧 Comandos útiles:"
echo "   # Ver estado de ingress:"
echo "   kubectl get ingress --all-namespaces"
echo ""
echo "   # Ver certificados SSL:"
echo "   kubectl get certificates --all-namespaces"
echo ""
echo "   # Ver IP de NGINX:"
echo "   kubectl get service ingress-nginx-controller --namespace=ingress-nginx"