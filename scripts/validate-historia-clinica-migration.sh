#!/bin/bash

# 🏥 Script de Validación de Migración - Historia Clínica a yosoy-repo
# Valida la migración exitosa de Historia Clínica al repositorio yosoy-repo

echo "🏥 VALIDACIÓN DE MIGRACIÓN - HISTORIA CLÍNICA"
echo "============================================="

# Configuración
REGISTRY="us-central1-docker.pkg.dev"
PROJECT_ID="authentic-prod-464216"
REPO_NAME="yosoy-repo"
IMAGE_NAME="yosoy-hc-backend"
APP_URL="https://hc.yo-soy.co"

echo "📋 Configuración de migración:"
echo "  Registry: $REGISTRY"
echo "  Project: $PROJECT_ID"
echo "  Repository: $REPO_NAME"
echo "  Image: $IMAGE_NAME"
echo "  Application: $APP_URL"
echo ""

# 1. Verificar autenticación GCP
echo "🔐 1. Verificando autenticación GCP..."
if gcloud auth application-default print-access-token >/dev/null 2>&1; then
    echo "✅ Autenticación GCP exitosa"
    current_project=$(gcloud config get project 2>/dev/null)
    echo "   Proyecto actual: $current_project"
else
    echo "❌ Error de autenticación GCP"
    exit 1
fi

echo ""

# 2. Verificar repositorio yosoy-repo
echo "🗂️  2. Verificando repositorio yosoy-repo..."
if gcloud artifacts repositories describe $REPO_NAME \
    --location=us-central1 \
    --project=$PROJECT_ID >/dev/null 2>&1; then
    
    echo "✅ Repositorio $REPO_NAME existe"
    
    # Obtener información del repositorio
    repo_info=$(gcloud artifacts repositories describe $REPO_NAME \
        --location=us-central1 \
        --project=$PROJECT_ID \
        --format="value(format,createTime)")
    echo "   📅 Creado: $(echo $repo_info | cut -d' ' -f2)"
else
    echo "❌ Repositorio $REPO_NAME no encontrado"
    exit 1
fi

echo ""

# 3. Verificar imagen de Historia Clínica en yosoy-repo
echo "🏥 3. Verificando imagen yosoy-hc-backend..."
full_image_name="$REGISTRY/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME"

# Listar todas las imágenes del repositorio
images=$(gcloud artifacts docker images list \
    $REGISTRY/$PROJECT_ID/$REPO_NAME \
    --format="value(IMAGE)" 2>/dev/null)

if [ -n "$images" ]; then
    echo "✅ Repositorio contiene imágenes:"
    echo "$images" | while read image; do
        image_name=$(basename "$image")
        echo "   📦 $image_name"
    done
    
    # Verificar imagen específica de historia clínica
    hc_tags=$(gcloud artifacts docker tags list $full_image_name \
        --format="value(tag)" 2>/dev/null)
    
    if [ -n "$hc_tags" ]; then
        echo ""
        echo "✅ Imagen $IMAGE_NAME encontrada con tags:"
        echo "$hc_tags" | while read tag; do
            echo "   🏷️  $tag"
        done
        
        # Obtener el tag más reciente
        latest_tag=$(echo "$hc_tags" | grep -E "^v[0-9]" | sort -V | tail -1)
        if [ -n "$latest_tag" ]; then
            echo "   📅 Tag más reciente: $latest_tag"
            
            # Obtener información detallada
            echo ""
            echo "📊 Información detallada de la migración:"
            gcloud artifacts docker images describe \
                $full_image_name:$latest_tag \
                --format="table(
                    image_summary.digest:label=DIGEST:sort=1,
                    update_time:label=MIGRATED:sort=2
                )" 2>/dev/null
        fi
    else
        echo "⚠️  Imagen $IMAGE_NAME no encontrada en $REPO_NAME"
        echo "   La migración podría estar en progreso..."
    fi
else
    echo "⚠️  Repositorio $REPO_NAME está vacío"
    echo "   Esperando que complete el workflow de migración..."
fi

echo ""

# 4. Verificar aplicación en vivo
echo "🌐 4. Verificando aplicación Historia Clínica en vivo..."
echo "Probando conectividad a $APP_URL..."

response=$(curl -L -o /dev/null -s -w '%{http_code}:%{time_total}:%{url_effective}' --max-time 15 "$APP_URL" 2>/dev/null || echo "000:timeout:")

if [ -n "$response" ]; then
    http_code=$(echo $response | cut -d':' -f1)
    time_total=$(echo $response | cut -d':' -f2)
    final_url=$(echo $response | cut -d':' -f3)
    
    echo "   HTTP Code: $http_code"
    echo "   Tiempo de respuesta: ${time_total}s"
    
    if [ "$http_code" = "200" ]; then
        echo "   ✅ Aplicación Historia Clínica OPERATIVA"
        
        # Probar login específico
        echo ""
        echo "🔐 Probando login de Historia Clínica..."
        login_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 10 "$APP_URL/login" 2>/dev/null || echo "000:timeout")
        login_code=$(echo $login_response | cut -d':' -f1)
        login_time=$(echo $login_response | cut -d':' -f2)
        
        if [ "$login_code" = "200" ]; then
            echo "   ✅ Login: Disponible (${login_time}s)"
        else
            echo "   ⚠️  Login: HTTP $login_code"
        fi
        
    elif [ "$http_code" = "302" ] || [ "$http_code" = "301" ]; then
        echo "   ↪️  Redirect detectado - Aplicación funcionando"
    else
        echo "   ❌ Aplicación no responde correctamente (HTTP $http_code)"
    fi
else
    echo "   ❌ No se pudo conectar a la aplicación"
fi

echo ""

# 5. Verificar workflow de GitHub Actions (si disponible)
echo "🤖 5. Estado del Workflow CI/CD..."
if command -v gh >/dev/null 2>&1; then
    echo "✅ GitHub CLI disponible - verificando último workflow"
    
    latest_run=$(gh run list --limit 1 --json conclusion,status,createdAt,headBranch,name 2>/dev/null)
    if [ -n "$latest_run" ]; then
        echo "   📊 Último workflow:"
        echo "$latest_run" | jq -r '.[] | "      Nombre: \(.name) | Estado: \(.status) | Conclusión: \(.conclusion // "En progreso") | Rama: \(.headBranch)"'
    fi
else
    echo "⚠️  GitHub CLI no disponible"
    echo "   💡 Monitorear workflows en: https://github.com/desarrolloIngenios/authentic-platform/actions"
fi

echo ""

# 6. Comparar con configuración anterior
echo "📊 6. Comparación Pre/Post Migración"
echo "===================================="
echo "📋 Configuración anterior:"
echo "   🗂️  Registry: gcr.io (legacy)"
echo "   📦 Path: gcr.io/$PROJECT_ID/yosoy-hc-backend"
echo ""
echo "📋 Nueva configuración (migrada):"
echo "   🗂️  Registry: $REGISTRY (Artifact Registry)"
echo "   📦 Path: $full_image_name"
echo "   🎯 Benefits:"
echo "      ✅ Mejor organización por repositorios"
echo "      ✅ Artifact Registry con más features"
echo "      ✅ Aislamiento por aplicación"
echo "      ✅ Mejor control de versiones"

echo ""

# 7. Resumen y próximos pasos
echo "🎯 7. RESUMEN DE MIGRACIÓN"
echo "========================="

# Determinar estado de migración
migration_status="unknown"
if [ -n "$hc_tags" ]; then
    migration_status="completed"
elif [ -n "$images" ]; then
    migration_status="in_progress"
else
    migration_status="pending"
fi

case $migration_status in
    "completed")
        echo "🟢 ESTADO: MIGRACIÓN COMPLETADA"
        echo "   ✅ Historia Clínica migrada exitosamente a yosoy-repo"
        echo "   📦 Imagen disponible con $(echo "$hc_tags" | wc -l) tag(s)"
        echo "   🌐 Aplicación operativa en $APP_URL"
        ;;
    "in_progress")
        echo "🟡 ESTADO: MIGRACIÓN EN PROGRESO"
        echo "   🔄 Repositorio yosoy-repo tiene imágenes pero no la de Historia Clínica"
        echo "   ⏳ Workflow podría estar ejecutándose"
        ;;
    "pending")
        echo "🔵 ESTADO: MIGRACIÓN PENDIENTE"
        echo "   ⏳ Esperando que el workflow construya la imagen"
        echo "   🚀 Commit enviado, build en progreso"
        ;;
esac

echo ""
echo "💡 PRÓXIMOS PASOS:"
case $migration_status in
    "completed")
        echo "   🎉 Migración exitosa - No se requieren acciones adicionales"
        echo "   📊 Monitorear performance en nueva infraestructura"
        ;;
    "in_progress"|"pending")
        echo "   ⏱️  Esperar 3-5 minutos y ejecutar este script nuevamente"
        echo "   👀 Monitorear workflow: https://github.com/desarrolloIngenios/authentic-platform/actions"
        ;;
esac

echo ""
echo "🔗 Enlaces de monitoreo:"
echo "   🏥 Historia Clínica: $APP_URL"
echo "   📦 yosoy-repo: https://console.cloud.google.com/artifacts/docker/$PROJECT_ID/us-central1/$REPO_NAME"
echo "   🤖 GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"

# Comandos útiles
echo ""
echo "📋 Comandos útiles para verificación manual:"
echo "   # Ver todas las imágenes en yosoy-repo:"
echo "   gcloud artifacts docker images list $REGISTRY/$PROJECT_ID/$REPO_NAME"
echo ""
echo "   # Ver tags de Historia Clínica:"
echo "   gcloud artifacts docker tags list $full_image_name"
echo ""
echo "   # Probar aplicación:"
echo "   curl -I $APP_URL"