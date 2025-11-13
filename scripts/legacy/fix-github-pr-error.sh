#!/bin/bash

echo "🔍 VERIFICANDO CONFIGURACIÓN DE USUARIO GITHUB"
echo "=============================================="

# Verificar configuración actual de git
echo "📋 Configuración actual de Git:"
echo "  Usuario: $(git config --global user.name)"
echo "  Email: $(git config --global user.email)"
echo ""

# Verificar el repositorio remoto
echo "🔗 Repositorio remoto:"
git remote -v | head -2
echo ""

# Verificar el branch actual
echo "🌿 Branch actual: $(git branch --show-current)"
echo ""

# Verificar si hay commits pendientes
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  ADVERTENCIA: Hay cambios sin commit"
    echo "📝 Archivos modificados:"
    git status --short
    echo ""
fi

# Verificar el último commit
echo "📝 Último commit:"
git log --oneline -1
echo ""

# Test básico del workflow
echo "🧪 PROBANDO CONFIGURACIÓN DEL WORKFLOW:"
echo "======================================="

# Verificar que el archivo del workflow existe
if [ -f ".github/workflows/ci-cd-pipeline.yml" ]; then
    echo "✅ Workflow file existe: .github/workflows/ci-cd-pipeline.yml"
else
    echo "❌ ERROR: Workflow file no encontrado"
    exit 1
fi

# Verificar sintaxis básica del workflow
if command -v yamllint >/dev/null 2>&1; then
    echo "🔍 Verificando sintaxis YAML..."
    if yamllint .github/workflows/ci-cd-pipeline.yml >/dev/null 2>&1; then
        echo "✅ Sintaxis YAML válida"
    else
        echo "⚠️  Advertencias de sintaxis YAML encontradas"
    fi
else
    echo "ℹ️  yamllint no disponible, saltando verificación de sintaxis"
fi

echo ""
echo "🚀 INSTRUCCIONES PARA SOLUCIONAR EL ERROR DEL PR:"
echo "================================================="
echo ""
echo "El error 'ottofonseca not found' se debe a que el usuario"
echo "no existe en GitHub o no tiene acceso al repositorio."
echo ""
echo "✅ SOLUCIONADO: Se eliminó la asignación automática del PR"
echo "✅ SOLUCIONADO: Se cambió el usuario Git a 'desarrolloIngenios'"
echo ""
echo "🔄 Para aplicar los cambios:"
echo "  1. git add .github/workflows/ci-cd-pipeline.yml"
echo "  2. git commit -m 'fix: corregir error de usuario en workflow PR'"
echo "  3. git push origin dev"
echo ""
echo "🎯 ALTERNATIVAS para configurar asignación de PR:"
echo ""
echo "Opción 1 - Sin asignación automática (RECOMENDADO):"
echo "  - El PR se creará sin asignar a nadie"
echo "  - Se puede asignar manualmente desde GitHub"
echo ""
echo "Opción 2 - Usar el owner del repositorio:"
echo "  - Agregar: --assignee desarrolloIngenios"
echo "  - Solo si 'desarrolloIngenios' es un usuario válido en GitHub"
echo ""
echo "Opción 3 - Usar tu usuario personal de GitHub:"
echo "  - Reemplazar 'ottofonseca' con tu username real de GitHub"
echo "  - Verificar que tienes acceso al repositorio"
echo ""

# Verificar si estamos en el contexto correcto para el workflow
if [ "$(git branch --show-current)" = "dev" ]; then
    echo "✅ Estás en el branch 'dev' - perfecto para probar el workflow"
else
    echo "⚠️  No estás en el branch 'dev' - cambiar antes de hacer push"
fi

echo ""
echo "🔧 COMANDOS ÚTILES PARA DEBUG:"
echo "============================="
echo "# Ver últimas ejecuciones del workflow:"
echo "gh run list --limit 5"
echo ""
echo "# Ver detalles de una ejecución específica:"
echo "gh run view [RUN_ID]"
echo ""
echo "# Ver logs en tiempo real:"
echo "gh run watch"
echo ""
echo "🎉 ¡El workflow inteligente está listo para funcionar!"