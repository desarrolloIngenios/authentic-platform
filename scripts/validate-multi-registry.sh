#!/bin/bash

# 🔍 Script de Validación Multi-Repositorio - Artifact Registry
# Valida que todas las aplicaciones estén desplegando a sus repositorios específicos

echo "🚀 VALIDACIÓN MULTI-REPOSITORIO - ARTIFACT REGISTRY"
echo "=================================================="

# Configuración
REGISTRY="us-central1-docker.pkg.dev"
PROJECT_ID="authentic-prod-464216"

echo "📋 Configuración:"
echo "  Registry: $REGISTRY"
echo "  Project: $PROJECT_ID"
echo ""

# Definir repositorios y aplicaciones
declare -A repositories=(
    ["authenticfarma-repo"]="authentic-candidatos"
    ["isyours-repo"]="isyoursapp"
    ["yosoy-repo"]="yosoy-hc-backend"
    ["moodle-repo"]="moodle-elearning"
)

echo "🗂️  Repositorios configurados:"
for repo in "${!repositories[@]}"; do
    app="${repositories[$repo]}"
    echo "  📦 $repo → $app"
done
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

# 2. Verificar cada repositorio y sus imágenes
echo "📊 2. Validación por Repositorio"
echo "==============================="

total_repos=0
active_repos=0
total_images=0

for repo_name in "${!repositories[@]}"; do
    app_name="${repositories[$repo_name]}"
    
    echo ""
    echo "🔍 Validando: $repo_name"
    echo "   Aplicación esperada: $app_name"
    
    # Verificar si el repositorio existe
    if gcloud artifacts repositories describe $repo_name \
        --location=us-central1 \
        --project=$PROJECT_ID >/dev/null 2>&1; then
        
        echo "   ✅ Repositorio existe"
        total_repos=$((total_repos + 1))
        
        # Obtener información del repositorio
        repo_info=$(gcloud artifacts repositories describe $repo_name \
            --location=us-central1 \
            --project=$PROJECT_ID \
            --format="value(format,createTime)" 2>/dev/null)
        
        echo "   📅 Creado: $(echo $repo_info | cut -d' ' -f2 2>/dev/null || echo 'N/A')"
        
        # Listar imágenes en el repositorio
        images=$(gcloud artifacts docker images list \
            $REGISTRY/$PROJECT_ID/$repo_name \
            --format="value(IMAGE)" 2>/dev/null)
        
        if [ -n "$images" ]; then
            active_repos=$((active_repos + 1))
            image_count=$(echo "$images" | wc -l)
            total_images=$((total_images + image_count))
            
            echo "   ✅ Imágenes: $image_count encontradas"
            
            # Mostrar las primeras 3 imágenes como muestra
            echo "$images" | head -3 | while read image; do
                image_name=$(basename "$image")
                echo "      📦 $image_name"
            done
            
            if [ $image_count -gt 3 ]; then
                echo "      ... y $((image_count - 3)) más"
            fi
            
            # Verificar si la imagen específica de la app existe
            app_image="$REGISTRY/$PROJECT_ID/$repo_name/$app_name"
            app_tags=$(gcloud artifacts docker tags list $app_image \
                --format="value(tag)" 2>/dev/null)
            
            if [ -n "$app_tags" ]; then
                tag_count=$(echo "$app_tags" | wc -l)
                latest_tag=$(echo "$app_tags" | grep -E "^v[0-9]" | sort -V | tail -1 || echo "$app_tags" | head -1)
                echo "   🎯 Imagen $app_name: ✅ Disponible ($tag_count tags)"
                echo "      🏷️  Tag más reciente: $latest_tag"
            else
                echo "   ⚠️  Imagen $app_name: No encontrada en el repositorio"
            fi
        else
            echo "   ⚠️  Repositorio vacío - esperando primera construcción"
        fi
    else
        echo "   ❌ Repositorio no encontrado"
        echo "   💡 Crear con: gcloud artifacts repositories create $repo_name --repository-format=docker --location=us-central1"
    fi
done

echo ""

# 3. Verificar configuración del workflow
echo "📋 3. Verificación del Workflow CI/CD"
echo "===================================="

workflow_file="/Users/Devapp/authentic-platform/.github/workflows/ci-cd-pipeline.yml"

if [ -f "$workflow_file" ]; then
    echo "✅ Archivo de workflow encontrado"
    
    # Verificar configuración del registry
    if grep -q "REGISTRY: us-central1-docker.pkg.dev" "$workflow_file"; then
        echo "✅ Registry correctamente configurado"
    else
        echo "❌ Registry no configurado o incorrecto"
    fi
    
    # Verificar configuración de cada aplicación
    echo ""
    echo "🔍 Configuración por aplicación:"
    
    # AuthenticFarma
    if grep -q "authenticfarma-repo" "$workflow_file"; then
        echo "   ✅ AuthenticFarma: authenticfarma-repo configurado"
    else
        echo "   ❌ AuthenticFarma: repositorio no configurado"
    fi
    
    # IsYours
    if grep -q "isyours-repo" "$workflow_file"; then
        echo "   ✅ IsYours: isyours-repo configurado"
    else
        echo "   ❌ IsYours: repositorio no configurado"
    fi
    
    # YoSoy
    if grep -q "yosoy-repo" "$workflow_file"; then
        echo "   ✅ YoSoy: yosoy-repo configurado"
    else
        echo "   ❌ YoSoy: repositorio no configurado"
    fi
    
    # Moodle
    if grep -q "moodle-repo" "$workflow_file"; then
        echo "   ✅ Moodle: moodle-repo configurado"
    else
        echo "   ❌ Moodle: repositorio no configurado"
    fi
    
else
    echo "❌ Archivo de workflow no encontrado"
fi

echo ""

# 4. Resumen y recomendaciones
echo "📊 4. RESUMEN DE VALIDACIÓN"
echo "========================="

echo "📈 Estadísticas:"
echo "   🗂️  Repositorios verificados: $total_repos/4"
echo "   ✅ Repositorios con imágenes: $active_repos/4"
echo "   📦 Total de imágenes: $total_images"

# Calcular score
score=0
if [ $total_repos -ge 3 ]; then score=$((score + 25)); fi
if [ $active_repos -ge 2 ]; then score=$((score + 25)); fi
if [ $total_images -ge 5 ]; then score=$((score + 25)); fi
if grep -q "us-central1-docker.pkg.dev" "$workflow_file" 2>/dev/null; then score=$((score + 25)); fi

echo ""
echo "🎯 SCORE DE CONFIGURACIÓN: $score/100"

if [ $score -ge 75 ]; then
    echo "🟢 ESTADO: EXCELENTE - Configuración multi-repositorio operativa"
elif [ $score -ge 50 ]; then
    echo "🟡 ESTADO: BUENO - Mayoría de repositorios configurados"
elif [ $score -ge 25 ]; then
    echo "🟠 ESTADO: PARCIAL - Algunos repositorios necesitan configuración"
else
    echo "🔴 ESTADO: CRÍTICO - Configuración multi-repositorio incompleta"
fi

echo ""
echo "💡 RECOMENDACIONES:"

if [ $total_repos -lt 4 ]; then
    echo "   🏗️  Crear repositorios faltantes en Artifact Registry"
fi

if [ $active_repos -lt $total_repos ]; then
    echo "   🚀 Ejecutar deployment para poblar repositorios vacíos"
fi

if [ $total_images -lt 10 ]; then
    echo "   🔄 Considerar ejecutar más builds para tener versiones de respaldo"
fi

echo ""
echo "🔗 Enlaces útiles:"
echo "   📊 Artifact Registry Console: https://console.cloud.google.com/artifacts/docker/$PROJECT_ID/us-central1"
echo "   🤖 GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"

echo ""
echo "📋 Comandos de verificación manual:"
echo "   # Listar todos los repositorios:"
echo "   gcloud artifacts repositories list --location=us-central1 --project=$PROJECT_ID"
echo ""
echo "   # Ver imágenes en repositorio específico:"
for repo_name in "${!repositories[@]}"; do
    echo "   gcloud artifacts docker images list $REGISTRY/$PROJECT_ID/$repo_name"
done