#!/bin/bash

# 🔄 CAMBIO DE USUARIO GIT - ottofonseca para despliegues
# Configurar Git para usar ottofonseca como usuario de despliegues

echo "🔄 Configurando Git para ottofonseca como usuario de despliegues"
echo "📅 $(date)"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[CAMBIO]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[INFO]${NC} $1"
}

info() {
    echo -e "${BLUE}[PASO]${NC} $1"
}

# Mostrar configuración actual
info "📊 Configuración Git actual:"
echo "   Usuario: $(git config user.name)"
echo "   Email: $(git config user.email)"
echo ""

# Cambiar configuración para ottofonseca
info "🔄 Cambiando a ottofonseca..."

# Configurar nuevo usuario y email
git config user.name "ottofonseca"
git config user.email "ottofonseca@authenticfarma.com"

log "✅ Usuario cambiado a: ottofonseca"
log "✅ Email configurado: ottofonseca@authenticfarma.com"

echo ""

# Verificar cambio
info "✅ Nueva configuración Git:"
echo "   Usuario: $(git config user.name)"
echo "   Email: $(git config user.email)"
echo ""

# Configurar ottofonseca también a nivel global (opcional)
info "🌐 ¿Configurar ottofonseca como usuario global?"
echo "   Esto afectará TODOS los repositorios Git en este sistema"
echo ""

read -p "¿Configurar como usuario global? (y/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    git config --global user.name "ottofonseca"
    git config --global user.email "ottofonseca@authenticfarma.com"
    log "✅ Configuración global actualizada"
else
    warn "ℹ️ Solo configurado para este repositorio"
fi

echo ""

# Verificar que el cambio funcionó
info "🔍 Verificación final:"
echo "   Repositorio local: $(git config user.name) <$(git config user.email)>"
echo "   Global: $(git config --global user.name 2>/dev/null || echo 'No configurado') <$(git config --global user.email 2>/dev/null || echo 'No configurado')>"
echo ""

# Mostrar próximos commits
info "📋 Próximos commits aparecerán como:"
echo "   Author: ottofonseca <ottofonseca@authenticfarma.com>"
echo ""

# Crear un commit de prueba para verificar
warn "💡 Para verificar que funciona, puedes hacer un commit de prueba:"
echo "   git add . && git commit -m 'test: Verificar configuración ottofonseca'"
echo ""

log "🎯 Configuración de usuario completada para despliegues"