#!/bin/bash

# 🔍 Script de verificación de GitHub Actions y Pull Requests
# Fecha: 9 de noviembre de 2025

echo "🔍 Verificando estado de GitHub Actions y Pull Requests"
echo "====================================================="

# Información del último commit
LAST_COMMIT=$(git rev-parse --short HEAD)
COMMIT_MSG=$(git log -1 --pretty=format:'%s')

echo ""
echo "📋 Estado actual:"
echo "   Commit: $LAST_COMMIT"
echo "   Mensaje: $COMMIT_MSG"
echo "   Autor: $(git log -1 --pretty=format:'%an <%ae>')"
echo "   Tiempo: $(git log -1 --pretty=format:'%ar')"
echo ""

echo "🔗 Enlaces para revisar:"
echo "========================"
echo ""
echo "1. 📊 GitHub Actions (workflows):"
echo "   https://github.com/desarrolloIngenios/authentic-platform/actions"
echo ""
echo "2. 📋 Pull Requests:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/pulls"
echo ""
echo "3. 🔍 Workflow específico (CI/CD Pipeline):"
echo "   https://github.com/desarrolloIngenios/authentic-platform/actions/workflows/ci-cd-pipeline.yml"
echo ""

echo "🎯 QUÉ BUSCAR EN GITHUB ACTIONS:"
echo "==============================="
echo ""
echo "✅ Workflow en ejecución:"
echo "   - Nombre: '🚀 CI/CD Pipeline - Authentic Platform'"
echo "   - Commit: $LAST_COMMIT"
echo "   - Trigger: 'push' event"
echo ""
echo "✅ Jobs ejecutándose/completados:"
echo "   1. 🧪 'Run Tests & Quality Checks'"
echo "   2. 📋 'Create Release PR'"
echo ""
echo "🚨 Posibles errores a verificar:"
echo "   - GitHub CLI authentication"
echo "   - Permisos para crear PR"
echo "   - Sintaxis del comando gh pr create"
echo ""

echo "🎯 QUÉ BUSCAR EN PULL REQUESTS:"
echo "=============================="
echo ""
echo "✅ PR automático esperado:"
echo "   - Título: '🚀 Release: Deploy dev changes to production'"
echo "   - De: dev → main"
echo "   - Autor: ottofonseca (via GitHub Actions)"
echo "   - Labels: release, production, auto-generated"
echo "   - Asignado: ottofonseca"
echo ""

echo "🔍 DIAGNÓSTICO PASO A PASO:"
echo "==========================="
echo ""
echo "1. 📊 Revisar Actions:"
echo "   ¿Hay un workflow corriendo para commit $LAST_COMMIT?"
echo "   ¿El job 'Create Release PR' se ejecutó?"
echo "   ¿Hay errores en los logs del job?"
echo ""
echo "2. 📋 Revisar Pull Requests:"
echo "   ¿Aparece un nuevo PR de dev → main?"
echo "   ¿El PR tiene el título correcto?"
echo "   ¿Está asignado a ottofonseca?"
echo ""
echo "3. 🚨 Si no hay PR, posibles causas:"
echo "   - Job 'Create Release PR' falló"
echo "   - Error de permisos del GITHUB_TOKEN"
echo "   - Problema con GitHub CLI (gh command)"
echo "   - Ya existe un PR abierto de dev → main"
echo ""

echo "🔧 COMANDOS DE VERIFICACIÓN LOCAL:"
echo "================================="
echo ""
echo "Para verificar desde terminal:"
echo ""
echo "# Ver últimos commits"
echo "git log --oneline -3"
echo ""
echo "# Ver diferencias entre dev y main"
echo "git log main..dev --oneline"
echo ""

# Verificar diferencias con main
echo "📊 DIFERENCIAS ENTRE DEV Y MAIN:"
echo "==============================="
echo ""

if git show-branch main dev >/dev/null 2>&1; then
    echo "Commits en dev que no están en main:"
    git log main..dev --oneline --max-count=10
    echo ""
    
    DIFF_COUNT=$(git rev-list --count main..dev)
    echo "Total de commits ahead: $DIFF_COUNT"
    
    if [ "$DIFF_COUNT" -gt 0 ]; then
        echo "✅ Hay cambios para hacer PR ($DIFF_COUNT commits)"
    else
        echo "ℹ️ No hay nuevos cambios (puede ser por qué no se crea PR)"
    fi
else
    echo "⚠️ No se puede comparar con main (rama no encontrada localmente)"
    echo "Ejecutar: git fetch origin main"
fi

echo ""
echo "💡 PRÓXIMOS PASOS:"
echo "=================="
echo ""
echo "1. 🔍 Abrir los enlaces de arriba en browser"
echo "2. 📊 Verificar si el workflow completó exitosamente"
echo "3. 📋 Buscar el Pull Request automático"
echo "4. 🚨 Si hay errores, revisar los logs del workflow"
echo ""
echo "⏱️ El proceso debería completarse en 2-3 minutos"