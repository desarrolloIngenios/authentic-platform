#!/bin/bash

# ✅ Script de verificación para desarrolladores
# Authentic Platform - Developer Environment Check

set -e

echo "👩‍💻 Verificando configuración del desarrollador..."
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
}

# Verificar Git
echo "🔍 Verificando Git..."
if command -v git &> /dev/null; then
    success "Git está instalado"
    
    # Verificar configuración de usuario
    USER_NAME=$(git config user.name 2>/dev/null || echo "")
    USER_EMAIL=$(git config user.email 2>/dev/null || echo "")
    
    if [[ -n "$USER_NAME" && -n "$USER_EMAIL" ]]; then
        success "Git configurado: $USER_NAME <$USER_EMAIL>"
    else
        warning "Git no está completamente configurado"
        echo "  Ejecutar: git config user.name 'Tu Nombre'"
        echo "  Ejecutar: git config user.email 'tu-email@authentic.com.co'"
    fi
else
    error "Git no está instalado"
fi

echo ""

# Verificar repositorio
echo "📁 Verificando repositorio..."
if [[ -d ".git" ]]; then
    success "Estás en el repositorio authentic-platform"
    
    # Verificar remoto
    REMOTE_URL=$(git remote get-url origin 2>/dev/null || echo "")
    if [[ "$REMOTE_URL" == *"authentic-platform"* ]]; then
        success "Remoto configurado correctamente: $REMOTE_URL"
    else
        warning "Remoto no parece correcto: $REMOTE_URL"
    fi
    
    # Verificar rama actual
    CURRENT_BRANCH=$(git branch --show-current)
    if [[ "$CURRENT_BRANCH" == "dev" ]]; then
        success "Estás en la rama 'dev' (correcto para desarrollo)"
    elif [[ "$CURRENT_BRANCH" == "main" ]]; then
        info "Estás en la rama 'main' (cambiar a 'dev' para desarrollo)"
        echo "  Ejecutar: git checkout dev"
    else
        warning "Estás en la rama '$CURRENT_BRANCH'"
        echo "  Ejecutar: git checkout dev"
    fi
else
    error "No estás en el repositorio authentic-platform"
    echo "  Ejecutar: git clone git@github.com:desarrolloIngenios/authentic-platform.git"
fi

echo ""

# Verificar acceso SSH a GitHub
echo "🔐 Verificando acceso a GitHub..."
if ssh -T git@github.com 2>&1 | grep -q "successfully authenticated"; then
    success "Acceso SSH a GitHub configurado correctamente"
else
    warning "Problema con acceso SSH a GitHub"
    echo "  Verificar: ssh -T git@github.com"
    echo "  Configurar: https://docs.github.com/en/authentication/connecting-to-github-with-ssh"
fi

echo ""

# Verificar scripts disponibles
echo "🛠️ Verificando scripts disponibles..."
SCRIPTS=("check-github-actions.sh" "sync-candidatos.sh" "deploy-to-production.sh")

for script in "${SCRIPTS[@]}"; do
    if [[ -f "scripts/$script" && -x "scripts/$script" ]]; then
        success "Script disponible: ./scripts/$script"
    else
        warning "Script no encontrado o no ejecutable: ./scripts/$script"
    fi
done

echo ""

# Verificar kubectl (opcional)
echo "☸️ Verificando kubectl (opcional)..."
if command -v kubectl &> /dev/null; then
    success "kubectl está disponible (para monitoreo avanzado)"
    
    # Verificar conectividad al cluster
    if kubectl cluster-info &> /dev/null; then
        success "Conectividad al cluster de Kubernetes OK"
    else
        info "kubectl instalado pero no conectado al cluster (no es problema)"
    fi
else
    info "kubectl no está instalado (no es necesario para desarrollo básico)"
fi

echo ""

# Mostrar próximos pasos
echo "🚀 Próximos pasos para el desarrollador:"
echo ""
echo "1. 🔄 Cambiar a rama dev (si no estás ya):"
echo "   git checkout dev && git pull origin dev"
echo ""
echo "2. ✏️ Hacer cambios en el código:"
echo "   # ... editar archivos ..."
echo ""
echo "3. 🚀 Deploy automático a DEV:"
echo "   git add ."
echo "   git commit -m 'feat: nueva funcionalidad'"
echo "   git push origin dev  # ¡DEPLOY AUTOMÁTICO!"
echo ""
echo "4. 📊 Monitorear deploy:"
echo "   ./scripts/check-github-actions.sh"
echo "   # ArgoCD: https://argo.authenticfarma.com/"
echo ""

# Resumen final
echo "📋 Resumen de configuración:"
if [[ -n "$USER_NAME" && -n "$USER_EMAIL" && -d ".git" ]]; then
    success "✅ ¡Configuración lista para desarrollo!"
    echo ""
    info "💡 Comando para empezar:"
    echo "   git checkout dev && git pull origin dev"
    echo ""
    info "🔗 Enlaces útiles:"
    echo "   - GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"
    echo "   - ArgoCD: https://argo.authenticfarma.com/"
    echo "   - Documentación: DEVELOPER-SETUP.md"
else
    warning "⚠️ Completar configuración antes de continuar"
fi

echo ""