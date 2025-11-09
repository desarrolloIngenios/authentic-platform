#!/bin/bash

echo "🔍 MONITOREANDO WORKFLOW INTELIGENTE CI/CD"
echo "=========================================="
echo ""

# Obtener información del commit actual
CURRENT_COMMIT=$(git rev-parse --short HEAD)
CURRENT_BRANCH=$(git branch --show-current)

echo "📋 Información del Push:"
echo "  Branch: $CURRENT_BRANCH"
echo "  Commit: $CURRENT_COMMIT"
echo "  Fecha: $(date)"
echo ""

# Detectar qué cambios se hicieron (simulando la lógica del workflow)
echo "🧠 SIMULANDO DETECCIÓN INTELIGENTE:"
echo "====================================="

# Comparar con origin/dev
echo "📊 Analizando cambios vs origin/dev..."
echo ""

check_app_changes() {
    local app_path=$1
    local app_name=$2
    
    if git diff --quiet origin/dev HEAD -- $app_path 2>/dev/null; then
        echo "❌ $app_name: Sin cambios detectados"
        return 1
    else
        echo "✅ $app_name: CAMBIOS DETECTADOS"
        echo "   📁 Archivos modificados en $app_path:"
        git diff --name-only origin/dev HEAD -- $app_path | head -5 | sed 's/^/      /'
        if [ $(git diff --name-only origin/dev HEAD -- $app_path | wc -l) -gt 5 ]; then
            echo "      ... y $(( $(git diff --name-only origin/dev HEAD -- $app_path | wc -l) - 5 )) más"
        fi
        echo ""
        return 0
    fi
}

# Variables para tracking
AUTHENTICFARMA_CHANGED=false
YOSOY_CHANGED=false
ISYOURS_CHANGED=false
MOODLE_CHANGED=false

# Detectar cambios por aplicación
if check_app_changes "apps/authenticfarma/" "AuthenticFarma"; then
    AUTHENTICFARMA_CHANGED=true
fi

if check_app_changes "apps/yosoy/" "YoSoy (Historia Clínica)"; then
    YOSOY_CHANGED=true
fi

if check_app_changes "apps/isyours/" "IsYours"; then
    ISYOURS_CHANGED=true
fi

if check_app_changes "apps/moodle-elearning/" "Moodle E-Learning"; then
    MOODLE_CHANGED=true
fi

echo ""
echo "🎯 PREDICCIÓN DE EJECUCIÓN DEL WORKFLOW:"
echo "========================================"

total_apps=0
apps_to_build=""

if [ "$AUTHENTICFARMA_CHANGED" = true ]; then
    total_apps=$((total_apps + 1))
    apps_to_build="$apps_to_build build-authenticfarma"
    echo "🏗️  build-authenticfarma: SE EJECUTARÁ"
else
    echo "⏭️  build-authenticfarma: SERÁ SALTADO (skipped)"
fi

if [ "$YOSOY_CHANGED" = true ]; then
    total_apps=$((total_apps + 1))
    apps_to_build="$apps_to_build build-yosoy"
    echo "🏗️  build-yosoy: SE EJECUTARÁ"
else
    echo "⏭️  build-yosoy: SERÁ SALTADO (skipped)"
fi

if [ "$ISYOURS_CHANGED" = true ]; then
    total_apps=$((total_apps + 1))
    apps_to_build="$apps_to_build build-isyours"
    echo "🏗️  build-isyours: SE EJECUTARÁ"  
else
    echo "⏭️  build-isyours: SERÁ SALTADO (skipped)"
fi

if [ "$MOODLE_CHANGED" = true ]; then
    total_apps=$((total_apps + 1))
    apps_to_build="$apps_to_build build-moodle"
    echo "🏗️  build-moodle: SE EJECUTARÁ"
else
    echo "⏭️  build-moodle: SERÁ SALTADO (skipped)"
fi

echo ""
echo "📈 RESUMEN DE OPTIMIZACIÓN:"
echo "  📦 Total de aplicaciones: 4"
echo "  ✅ Apps que se construirán: $total_apps"
echo "  ⚡ Optimización: $((100 - (total_apps * 25)))% menos builds"
echo ""

if [ $total_apps -gt 0 ]; then
    echo "🚀 deploy-dev: SE EJECUTARÁ (hay builds exitosos)"
    echo ""
    echo "🔗 Links útiles:"
    echo "  📊 GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"
    echo "  📝 Workflow file: .github/workflows/ci-cd-pipeline.yml"
else
    echo "🚀 deploy-dev: SE EJECUTARÁ (condición especial: todos skipped)"
fi

echo ""
echo "⏰ Tiempo estimado de ejecución: $((total_apps * 3 + 2)) minutos"
echo ""

# Mostrar cómo verificar el progreso
echo "📋 COMANDOS PARA MONITOREAR:"
echo "============================"
echo "gh run list --branch dev --limit 5"
echo "gh run watch"
echo ""

# Detectar otros cambios relevantes
echo "🔍 OTROS CAMBIOS DETECTADOS:"
echo "============================="

other_changes=$(git diff --name-only origin/dev HEAD | grep -v "^apps/" | head -10)
if [ -n "$other_changes" ]; then
    echo "📁 Archivos modificados fuera de apps/:"
    echo "$other_changes" | sed 's/^/   /'
else
    echo "❌ Sin cambios fuera de las aplicaciones"
fi

echo ""
echo "✨ El sistema inteligente está funcionando perfectamente!"
echo "🎉 Solo se construirán las aplicaciones que realmente cambiaron."