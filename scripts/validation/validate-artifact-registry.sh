#!/bin/bash

# 🔍 Script de validación integral de Artifact Registry
# Valida que las imágenes de candidatos se estén desplegando correctamente

echo "🚀 VALIDACIÓN INTEGRAL DE ARTIFACT REGISTRY"
echo "=============================================="

# Configuración
REGISTRY="us-central1-docker.pkg.dev"
PROJECT_ID="authentic-prod-464216"
REPO_NAME="authenticfarma-repo"
IMAGE_NAME="authentic-candidatos"

echo "📋 Configuración:"
echo "  Registry: $REGISTRY"
echo "  Project: $PROJECT_ID"
echo "  Repository: $REPO_NAME"
echo "  Image: $IMAGE_NAME"
echo ""

# 1. Verificar autenticación GCP
echo "🔐 1. Verificando autenticación GCP..."
if gcloud auth application-default print-access-token >/dev/null 2>&1; then
    echo "✅ Autenticación GCP exitosa"
    current_project=$(gcloud config get project 2>/dev/null)
    echo "   Proyecto actual: $current_project"
else
    echo "❌ Error de autenticación GCP"
    echo "   Ejecuta: gcloud auth application-default login"
    exit 1
fi

echo ""

# 2. Verificar repositorio
echo "🗂️  2. Verificando repositorio Artifact Registry..."
if gcloud artifacts repositories describe $REPO_NAME \
    --location=us-central1 \
    --project=$PROJECT_ID >/dev/null 2>&1; then
    echo "✅ Repositorio $REPO_NAME existe"
    
    # Obtener información del repositorio
    repo_info=$(gcloud artifacts repositories describe $REPO_NAME \
        --location=us-central1 \
        --project=$PROJECT_ID \
        --format="value(format,createTime)")
    echo "   Formato: Docker"
    echo "   Creado: $(echo $repo_info | cut -d' ' -f2)"
else
    echo "❌ Repositorio $REPO_NAME no encontrado"
    exit 1
fi

echo ""

# 3. Listar todas las imágenes del repositorio
echo "🐳 3. Listando imágenes en el repositorio..."
images=$(gcloud artifacts docker images list \
    $REGISTRY/$PROJECT_ID/$REPO_NAME \
    --format="value(IMAGE)" 2>/dev/null)

if [ -n "$images" ]; then
    echo "✅ Imágenes encontradas:"
    echo "$images" | while read image; do
        echo "   📦 $image"
    done
    
    # Contar imágenes
    image_count=$(echo "$images" | wc -l)
    echo "   Total: $image_count imagen(es)"
else
    echo "⚠️  No se encontraron imágenes en el repositorio"
    echo "   Esto puede ser normal si el workflow aún no se ha ejecutado"
fi

echo ""

# 4. Verificar imagen específica de candidatos
echo "🎯 4. Verificando imagen de candidatos..."
candidatos_image="$REGISTRY/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME"
candidatos_tags=$(gcloud artifacts docker tags list $candidatos_image \
    --format="value(tag)" 2>/dev/null)

if [ -n "$candidatos_tags" ]; then
    echo "✅ Imagen $IMAGE_NAME encontrada con tags:"
    echo "$candidatos_tags" | while read tag; do
        echo "   🏷️  $tag"
    done
    
    # Obtener el tag más reciente
    latest_tag=$(echo "$candidatos_tags" | grep -E "^v[0-9]" | sort -V | tail -1)
    if [ -n "$latest_tag" ]; then
        echo "   📅 Tag más reciente: $latest_tag"
        
        # Obtener información detallada de la imagen más reciente
        echo ""
        echo "📊 5. Información detallada de la imagen más reciente:"
        gcloud artifacts docker images describe \
            $candidatos_image:$latest_tag \
            --format="table(
                image_summary.digest:label=DIGEST:sort=1,
                image_summary.fully_qualified_digest:label=FULL_DIGEST,
                update_time:label=UPDATED:sort=2
            )" 2>/dev/null
    fi
else
    echo "❌ Imagen $IMAGE_NAME no encontrada"
    echo "   Esperando que el workflow de GitHub Actions complete la construcción..."
fi

echo ""

# 5. Verificar configuración de Docker local
echo "🔧 6. Verificando configuración Docker local..."
if docker --version >/dev/null 2>&1; then
    echo "✅ Docker disponible: $(docker --version | cut -d' ' -f3 | tr -d ',')"
    
    # Verificar si podemos hacer pull de la imagen (si existe)
    if [ -n "$latest_tag" ]; then
        echo "🔄 Probando pull de la imagen más reciente..."
        if gcloud auth configure-docker $REGISTRY --quiet >/dev/null 2>&1; then
            echo "✅ Docker configurado para Artifact Registry"
            
            # Intentar pull (solo como prueba, luego limpiar)
            test_image="$candidatos_image:$latest_tag"
            if docker pull $test_image >/dev/null 2>&1; then
                echo "✅ Pull exitoso de: $test_image"
                # Limpiar imagen local para no llenar espacio
                docker rmi $test_image >/dev/null 2>&1
            else
                echo "⚠️  No se pudo hacer pull (puede ser normal si requiere permisos específicos)"
            fi
        else
            echo "⚠️  Error configurando Docker para Artifact Registry"
        fi
    fi
else
    echo "⚠️  Docker no disponible localmente"
fi

echo ""

# 6. Verificar GitHub Actions (si gh CLI está disponible)
echo "🤖 7. Verificando estado de GitHub Actions..."
if command -v gh >/dev/null 2>&1; then
    echo "✅ GitHub CLI disponible"
    
    # Obtener el último workflow run
    latest_run=$(gh run list --limit 1 --json conclusion,status,createdAt,headBranch 2>/dev/null)
    if [ -n "$latest_run" ]; then
        echo "   📊 Último workflow:"
        echo "$latest_run" | jq -r '.[] | "      Estado: \(.status) | Conclusión: \(.conclusion // "En progreso") | Rama: \(.headBranch) | Creado: \(.createdAt)"'
    fi
else
    echo "⚠️  GitHub CLI no disponible"
    echo "   Instalar con: brew install gh"
    echo "   Para monitorear workflows en tiempo real"
fi

echo ""
echo "🎉 RESUMEN DE VALIDACIÓN"
echo "========================"
echo "✅ Configuración de Artifact Registry correcta"
echo "✅ Repositorio $REPO_NAME accesible"

if [ -n "$candidatos_tags" ]; then
    echo "✅ Imagen candidatos disponible con $(echo "$candidatos_tags" | wc -l) tag(s)"
    echo "🚀 DEPLOYMENT EXITOSO - Las imágenes se están desplegando correctamente"
else
    echo "⏳ Imagen candidatos pendiente - Workflow en progreso"
    echo "💡 Ejecutar este script nuevamente en unos minutos"
fi

echo ""
echo "🔗 Enlaces útiles:"
echo "   - Artifact Registry: https://console.cloud.google.com/artifacts/docker/$PROJECT_ID/us-central1/$REPO_NAME"
echo "   - GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"
echo "   - Aplicación: https://candidatos.authenticfarma.com"