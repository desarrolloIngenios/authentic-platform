#!/bin/bash

echo "🔄 Sincronizando repositorio con remoto..."
echo "📅 $(date)"
echo ""

# Verificar branch actual
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 Branch actual: $CURRENT_BRANCH"
echo ""

# Fetch últimos cambios
echo "🔄 Obteniendo cambios remotos..."
git fetch origin

# Verificar si hay cambios en main
echo ""
echo "🔍 Verificando cambios en main..."
MAIN_CHANGES=$(git log HEAD..origin/main --oneline | wc -l | xargs)
if [ "$MAIN_CHANGES" -gt 0 ]; then
    echo "📥 Hay $MAIN_CHANGES cambios en origin/main"
    echo "💡 ¿Quieres traer los cambios de main? (y/n)"
    read -r PULL_MAIN
    if [ "$PULL_MAIN" = "y" ]; then
        echo "🔄 Integrando cambios de main..."
        git pull origin main
    fi
else
    echo "✅ No hay cambios nuevos en main"
fi

# Verificar cambios locales pendientes
echo ""
echo "🔍 Verificando cambios locales..."
LOCAL_CHANGES=$(git log origin/$CURRENT_BRANCH..HEAD --oneline | wc -l | xargs)
if [ "$LOCAL_CHANGES" -gt 0 ]; then
    echo "📤 Hay $LOCAL_CHANGES commits locales pendientes de subir"
    echo "💡 ¿Quieres enviar los cambios al remoto? (y/n)"
    read -r PUSH_CHANGES
    if [ "$PUSH_CHANGES" = "y" ]; then
        echo "🚀 Enviando cambios al remoto..."
        git push origin $CURRENT_BRANCH
    fi
else
    echo "✅ No hay cambios locales pendientes"
fi

echo ""
echo "🎯 Sincronización completada"
echo "📊 Estado final:"
git status -sb

