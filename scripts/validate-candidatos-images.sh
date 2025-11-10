#!/bin/bash

echo "🐳 VALIDANDO DESPLIEGUE DE IMÁGENES - CANDIDATOS"
echo "==============================================="
echo ""

# Configuración del repositorio
REPO_URL="us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo/authentic-candidatos"
PROJECT_ID="authentic-prod-464216"
REGION="us-central1"
REPO_NAME="authenticfarma-repo"
IMAGE_NAME="authentic-candidatos"

echo "📋 Información del repositorio:"
echo "  🏗️ Proyecto: $PROJECT_ID"
echo "  🌍 Región: $REGION"
echo "  📦 Repositorio: $REPO_NAME"
echo "  🐳 Imagen: $IMAGE_NAME"
echo "  🔗 URL completa: $REPO_URL"
echo ""

echo "🔐 VERIFICANDO AUTENTICACIÓN GCP:"
echo "================================"

# Verificar si gcloud está autenticado
if command -v gcloud >/dev/null 2>&1; then
    echo "✅ gcloud CLI encontrado"
    
    # Verificar autenticación
    CURRENT_ACCOUNT=$(gcloud auth list --filter=status:ACTIVE --format="value(account)" 2>/dev/null)
    if [ -n "$CURRENT_ACCOUNT" ]; then
        echo "✅ Autenticado como: $CURRENT_ACCOUNT"
        
        # Verificar proyecto activo
        CURRENT_PROJECT=$(gcloud config get-value project 2>/dev/null)
        echo "📋 Proyecto activo: $CURRENT_PROJECT"
        
        if [ "$CURRENT_PROJECT" != "$PROJECT_ID" ]; then
            echo "⚠️  Cambiando al proyecto correcto..."
            gcloud config set project $PROJECT_ID
        fi
    else
        echo "❌ No autenticado en gcloud"
        echo "💡 Ejecutar: gcloud auth login"
        echo "💡 Luego: gcloud config set project $PROJECT_ID"
    fi
else
    echo "❌ gcloud CLI no encontrado"
    echo "💡 Instalar: https://cloud.google.com/sdk/docs/install"
fi

echo ""
echo "🐳 CONFIGURANDO DOCKER PARA ARTIFACT REGISTRY:"
echo "=============================================="

# Configurar Docker para Artifact Registry
if command -v gcloud >/dev/null 2>&1; then
    echo "🔧 Configurando autenticación Docker..."
    gcloud auth configure-docker $REGION-docker.pkg.dev --quiet 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ Docker configurado para Artifact Registry"
    else
        echo "❌ Error configurando Docker"
    fi
else
    echo "⏭️ Saltando configuración Docker (gcloud no disponible)"
fi

echo ""
echo "📦 VERIFICANDO REPOSITORIO ARTIFACT REGISTRY:"
echo "============================================"

# Verificar que el repositorio existe
if command -v gcloud >/dev/null 2>&1 && [ -n "$CURRENT_ACCOUNT" ]; then
    echo "🔍 Verificando repositorio $REPO_NAME..."
    
    REPO_EXISTS=$(gcloud artifacts repositories list \
        --location=$REGION \
        --filter="name:$REPO_NAME" \
        --format="value(name)" 2>/dev/null)
    
    if [ -n "$REPO_EXISTS" ]; then
        echo "✅ Repositorio existe: $REPO_NAME"
        
        # Obtener información del repositorio
        gcloud artifacts repositories describe $REPO_NAME \
            --location=$REGION \
            --format="table(name,format,description,createTime)" 2>/dev/null
    else
        echo "❌ Repositorio no encontrado: $REPO_NAME"
        echo "💡 Crear con: gcloud artifacts repositories create $REPO_NAME --repository-format=docker --location=$REGION"
    fi
else
    echo "⏭️ Saltando verificación de repositorio"
fi

echo ""
echo "🐳 LISTANDO IMÁGENES EN EL REPOSITORIO:"
echo "======================================"

if command -v gcloud >/dev/null 2>&1 && [ -n "$CURRENT_ACCOUNT" ]; then
    echo "🔍 Buscando imágenes de $IMAGE_NAME..."
    
    # Listar todas las imágenes en el repositorio
    IMAGES=$(gcloud artifacts docker images list $REPO_URL \
        --include-tags \
        --format="table(IMAGE,TAG,DIGEST,CREATE_TIME)" 2>/dev/null)
    
    if [ $? -eq 0 ] && [ -n "$IMAGES" ]; then
        echo "✅ Imágenes encontradas:"
        echo "$IMAGES"
    else
        echo "❌ No se encontraron imágenes o error de acceso"
        
        # Intentar listar el repositorio completo
        echo ""
        echo "🔍 Listando todo el repositorio..."
        gcloud artifacts docker images list $REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME \
            --include-tags \
            --format="table(IMAGE,TAG,CREATE_TIME)" 2>/dev/null || echo "❌ Error listando repositorio"
    fi
else
    echo "⏭️ Saltando listado de imágenes (gcloud no configurado)"
fi

echo ""
echo "📊 VERIFICANDO ÚLTIMAS IMÁGENES PUSHED:"
echo "======================================"

if command -v gcloud >/dev/null 2>&1 && [ -n "$CURRENT_ACCOUNT" ]; then
    echo "🕒 Últimas 5 imágenes creadas:"
    
    gcloud artifacts docker images list $REPO_URL \
        --sort-by="~CREATE_TIME" \
        --limit=5 \
        --format="table(IMAGE:label=IMAGEN,TAG:label=TAG,CREATE_TIME:label=FECHA_CREACION)" 2>/dev/null
    
    echo ""
    echo "🏷️ Tags específicos a buscar:"
    echo "  - latest (producción actual)"
    echo "  - dev-latest (desarrollo actual)"
    echo "  - v2024.11.* (releases de noviembre)"
    echo "  - dev-[commit-hash] (builds de desarrollo)"
    
    # Verificar tags específicos
    echo ""
    echo "🔍 Verificando tags críticos..."
    
    # Latest
    LATEST_EXISTS=$(gcloud artifacts docker images list $REPO_URL:latest --format="value(digest)" 2>/dev/null)
    if [ -n "$LATEST_EXISTS" ]; then
        echo "✅ Tag 'latest' encontrado"
    else
        echo "❌ Tag 'latest' NO encontrado"
    fi
    
    # Dev-latest
    DEV_LATEST_EXISTS=$(gcloud artifacts docker images list $REPO_URL:dev-latest --format="value(digest)" 2>/dev/null)
    if [ -n "$DEV_LATEST_EXISTS" ]; then
        echo "✅ Tag 'dev-latest' encontrado"
    else
        echo "❌ Tag 'dev-latest' NO encontrado"
    fi
    
else
    echo "⏭️ Saltando verificación de tags"
fi

echo ""
echo "🔍 VERIFICANDO WORKFLOW CI/CD:"
echo "============================="

# Verificar si el workflow está configurado correctamente
if [ -f ".github/workflows/ci-cd-pipeline.yml" ]; then
    echo "✅ Workflow CI/CD encontrado"
    
    # Verificar configuración del registry
    REGISTRY_CONFIG=$(grep -n "us-central1-docker.pkg.dev" .github/workflows/ci-cd-pipeline.yml 2>/dev/null)
    if [ -n "$REGISTRY_CONFIG" ]; then
        echo "✅ Registry configurado en workflow:"
        echo "$REGISTRY_CONFIG"
    else
        echo "⚠️ Registry no encontrado en workflow"
        
        # Verificar configuración actual
        echo ""
        echo "🔍 Configuración actual de registry en workflow:"
        grep -n -A5 -B5 "REGISTRY\|PROJECT_ID\|gcr.io\|docker.pkg.dev" .github/workflows/ci-cd-pipeline.yml 2>/dev/null || echo "❌ No se encontró configuración de registry"
    fi
    
    # Verificar configuración del proyecto
    PROJECT_CONFIG=$(grep -n "authentic-prod-464216" .github/workflows/ci-cd-pipeline.yml 2>/dev/null)
    if [ -n "$PROJECT_CONFIG" ]; then
        echo "✅ Proyecto ID encontrado en workflow:"
        echo "$PROJECT_CONFIG"
    else
        echo "❌ Proyecto ID no encontrado en workflow"
    fi
    
else
    echo "❌ Workflow CI/CD no encontrado"
fi

echo ""
echo "📋 COMANDOS ÚTILES PARA DEBUGGING:"
echo "================================="

cat << 'EOF'
# Listar todas las imágenes en el repositorio
gcloud artifacts docker images list us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo --include-tags

# Ver detalles de una imagen específica
gcloud artifacts docker images describe us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo/authentic-candidatos:latest

# Pull de la imagen para testing local
docker pull us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo/authentic-candidatos:latest

# Ver logs de build de Cloud Build (si se usa)
gcloud builds list --limit=10

# Verificar permisos del Service Account
gcloud projects get-iam-policy authentic-prod-464216 --flatten="bindings[].members" --filter="bindings.members:*@authentic-prod-464216.iam.gserviceaccount.com"

EOF

echo ""
echo "🎯 VERIFICACIÓN DE DEPLOYMENT:"
echo "============================="

echo "Para confirmar que el deployment está funcionando:"
echo "1. ✅ Las imágenes deben aparecer en el repositorio"
echo "2. ✅ Tags 'latest' y 'dev-latest' deben existir"
echo "3. ✅ Timestamps recientes indican builds activos"
echo "4. ✅ Workflow debe referenciar el registry correcto"
echo ""

echo "🚨 SI NO HAY IMÁGENES:"
echo "====================="
echo "Posibles causas:"
echo "- ❌ Workflow no está configurado para el registry correcto"
echo "- ❌ Service Account sin permisos de push"
echo "- ❌ Errores en el build de CI/CD"
echo "- ❌ Dockerfile path incorrecto"
echo "- ❌ Registry no creado o mal configurado"
echo ""

echo "🔧 SIGUIENTE PASO:"
echo "=================="
echo "Ejecutar este script y revisar los resultados."
echo "Si hay problemas, verificar:"
echo "1. GitHub Actions logs"
echo "2. Service Account permissions"
echo "3. Workflow configuration"
echo "4. Artifact Registry settings"