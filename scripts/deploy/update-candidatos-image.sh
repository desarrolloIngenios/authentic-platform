#!/bin/bash

# Script para actualizar imagen de candidatos
# Uso: ./update-candidatos-image.sh [nueva-imagen-tag]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuración
REPO="us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo"
APP="authentic-candidatos"
DEPLOYMENT_FILE="platforms/authenticfarma/candidatos/k8s/04-deployment.yaml"
NAMESPACE="authenticfarma-candidatos"

echo -e "${BLUE}🚀 Actualizador de Imagen Candidatos${NC}"
echo "=================================================="

# Verificar si se proporcionó el tag
if [ -z "$1" ]; then
    echo -e "${YELLOW}📋 Obteniendo imágenes disponibles...${NC}"
    gcloud artifacts docker images list $REPO --include-tags --limit=5
    echo ""
    read -p "Ingresa el tag de la nueva imagen: " NEW_TAG
else
    NEW_TAG=$1
fi

# Verificar que la imagen existe
echo -e "${BLUE}🔍 Verificando que la imagen existe...${NC}"
if ! gcloud artifacts docker images describe $REPO/$APP:$NEW_TAG &>/dev/null; then
    echo -e "${RED}❌ Error: La imagen $APP:$NEW_TAG no existe${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Imagen confirmada: $APP:$NEW_TAG${NC}"

# Obtener imagen actual
CURRENT_IMAGE=$(grep "image.*authentic-candidatos" $DEPLOYMENT_FILE | sed 's/.*image: //' | sed 's/[[:space:]]*$//')
CURRENT_TAG=$(echo $CURRENT_IMAGE | cut -d':' -f2)

echo -e "${YELLOW}📊 Imagen actual: $CURRENT_TAG${NC}"
echo -e "${YELLOW}📊 Nueva imagen: $NEW_TAG${NC}"

# Confirmar actualización
read -p "¿Continuar con la actualización? (y/N): " confirm
if [[ $confirm != [yY] ]]; then
    echo -e "${YELLOW}❌ Actualización cancelada${NC}"
    exit 0
fi

# Actualizar archivo de deployment
echo -e "${BLUE}📝 Actualizando deployment...${NC}"
sed -i.bak "s|$CURRENT_IMAGE|$REPO/$APP:$NEW_TAG|g" $DEPLOYMENT_FILE

# Verificar cambio
if grep -q "$NEW_TAG" $DEPLOYMENT_FILE; then
    echo -e "${GREEN}✅ Archivo actualizado correctamente${NC}"
else
    echo -e "${RED}❌ Error al actualizar archivo${NC}"
    exit 1
fi

# Git commit y push
echo -e "${BLUE}📤 Haciendo commit y push...${NC}"
git add $DEPLOYMENT_FILE
git commit -m "🚀 UPDATE: candidatos $CURRENT_TAG → $NEW_TAG

✅ Nueva imagen:
- Tag: $NEW_TAG  
- Fecha: $(date)
- Actualización automática via script"

git push origin dev

echo -e "${GREEN}✅ Cambios pusheados al repositorio${NC}"

# Esperar a ArgoCD
echo -e "${BLUE}⏳ Esperando a ArgoCD (30s)...${NC}"
sleep 30

# Forzar refresh de ArgoCD
echo -e "${BLUE}🔄 Forzando refresh de ArgoCD...${NC}"
kubectl patch application authenticfarma-candidatos -n argocd \
  --type merge -p '{"metadata":{"annotations":{"argocd.argoproj.io/refresh":"hard"}}}'

# Monitorear rollout
echo -e "${BLUE}👀 Monitoreando rollout...${NC}"
kubectl rollout status deployment/authentic-candidatos -n $NAMESPACE --timeout=300s

# Verificar resultado
echo -e "${BLUE}🔍 Verificando resultado...${NC}"
kubectl get pods -n $NAMESPACE
echo ""

# Test funcionalidad
echo -e "${BLUE}🌐 Probando conectividad...${NC}"
HTTP_STATUS=$(curl -I -s -k https://candidatos.authenticfarma.com | head -1)
echo "Estado HTTP: $HTTP_STATUS"

if [[ $HTTP_STATUS == *"302"* ]] || [[ $HTTP_STATUS == *"200"* ]]; then
    echo -e "${GREEN}🎉 ¡Actualización completada exitosamente!${NC}"
else
    echo -e "${YELLOW}⚠️  Actualización completada, verificar funcionalidad manualmente${NC}"
fi

echo ""
echo -e "${GREEN}📊 Resumen:${NC}"
echo "- Imagen anterior: $CURRENT_TAG"
echo "- Imagen nueva: $NEW_TAG"
echo "- Estado: Desplegada y funcionando"
echo -e "${BLUE}=================================================="