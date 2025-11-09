#!/bin/bash

# 📊 Monitor de Workflow CI/CD - GitHub Actions
# Monitoreo en tiempo real del workflow corregido
# Fecha: 9 de noviembre de 2025

echo "📊 Monitoreando Workflow CI/CD - Authentic Platform"
echo "================================================="

REPO="desarrolloIngenios/authentic-platform"
BRANCH="dev"

echo ""
echo "📋 Información del monitoreo:"
echo "   Repository: $REPO"
echo "   Branch: $BRANCH"
echo "   Commit más reciente: $(git rev-parse --short HEAD)"
echo "   Autor: $(git log -1 --pretty=format:'%an <%ae>')"
echo ""

echo "🔍 VERIFICACIONES A MONITOREAR:"
echo "==============================="
echo ""
echo "1. 🧪 Test Job:"
echo "   ✅ Setup Node.js"
echo "   ✅ Install dependencies"
echo "   ✅ Lint code"
echo "   ✅ Run tests"
echo ""
echo "2. 🏗️ Build Dev Job:"
echo "   ✅ Auth to Google Cloud (GCP_SA_KEY)"
echo "   ✅ Setup Google Cloud SDK"
echo "   ✅ Configure Docker"
echo "   ✅ Build Historia Clinica Backend"
echo "   ✅ Push to Google Container Registry"
echo ""
echo "3. 🚀 Deploy Dev Job:"
echo "   ✅ Trigger ArgoCD Sync"
echo ""
echo "4. 📋 Create Release PR Job:"
echo "   ✅ Create Pull Request (peter-evans/create-pull-request@v5)"
echo "   ✅ Assign to ottofonseca"
echo "   ✅ Add labels (release, production, auto-generated)"
echo ""

echo "🎯 PUNTOS CRÍTICOS A VALIDAR:"
echo "============================="
echo ""
echo "❌ ERRORES ANTERIORES (deben estar corregidos):"
echo "   • google-github-actions/auth failed with: unexpected token '�'"
echo "   • repo-sync/pull-request Docker container error"
echo ""
echo "✅ ÉXITO ESPERADO:"
echo "   • GCP Authentication: Successful"
echo "   • Docker builds: Successful"
echo "   • Image push to gcr.io: Successful"
echo "   • Pull Request creation: Successful"
echo ""

# Función para verificar el estado del workflow
check_workflow_status() {
    echo "🔄 Verificando estado del workflow..."
    echo ""
    
    # Mostrar los últimos commits
    echo "📋 Últimos commits en dev:"
    git log --oneline -3
    echo ""
    
    # Información del último push
    LAST_COMMIT=$(git rev-parse HEAD)
    COMMIT_MESSAGE=$(git log -1 --pretty=format:'%s')
    
    echo "🚀 Último push:"
    echo "   Commit: $LAST_COMMIT"
    echo "   Mensaje: $COMMIT_MESSAGE"
    echo "   Tiempo: $(git log -1 --pretty=format:'%ar')"
    echo ""
    
    echo "🌐 Enlaces de monitoreo:"
    echo "   GitHub Actions: https://github.com/$REPO/actions"
    echo "   Workflow específico: https://github.com/$REPO/actions/workflows/ci-cd-pipeline.yml"
    echo "   ArgoCD Dev: https://argo.authenticfarma.com/applications/authentic-platform-dev"
    echo ""
}

# Función para mostrar logs en tiempo real (simulado)
monitor_logs() {
    echo "📊 MONITOREO EN TIEMPO REAL:"
    echo "==========================="
    echo ""
    
    steps=(
        "📥 Checkout code"
        "🧪 Run Tests & Quality Checks"
        "🔐 Auth to Google Cloud"
        "📋 Setup Google Cloud SDK"
        "🐳 Configure Docker"
        "🏗️ Build Historia Clinica Backend"
        "📤 Push image to GCR"
        "🚀 Deploy to Dev Environment"
        "📋 Create Release PR"
    )
    
    echo "Pasos del workflow a ejecutar:"
    for i in "${!steps[@]}"; do
        echo "   $((i+1)). ${steps[$i]}"
    done
    
    echo ""
    echo "⏱️ Tiempo estimado: 3-5 minutos"
    echo ""
}

# Ejecutar verificaciones
check_workflow_status
monitor_logs

echo "🎯 INSTRUCCIONES DE MONITOREO:"
echo "============================="
echo ""
echo "1. 📊 Abrir GitHub Actions en browser"
echo "2. 🔍 Buscar el workflow más reciente (commit: $(git rev-parse --short HEAD))"
echo "3. 📋 Verificar cada job individualmente:"
echo ""
echo "   🧪 TEST JOB:"
echo "   - Debe pasar sin errores"
echo "   - Verificar setup de Node.js"
echo ""
echo "   🏗️ BUILD-DEV JOB:"
echo "   - ⚠️ CRÍTICO: 'Auth to Google Cloud' debe ser exitoso"
echo "   - ✅ Sin error 'unexpected token'"
echo "   - ✅ Docker build successful"
echo "   - ✅ Image push a gcr.io successful"
echo ""
echo "   📋 CREATE-RELEASE-PR JOB:"
echo "   - ⚠️ CRÍTICO: No debe fallar con error de Docker"
echo "   - ✅ Pull Request creado exitosamente"
echo "   - ✅ ottofonseca asignado como reviewer"
echo ""

echo "🚨 ALERTAS A VIGILAR:"
echo "===================="
echo ""
echo "❌ Si falla GCP Auth:"
echo "   • Verificar que el secret GCP_SA_KEY esté bien configurado"
echo "   • El JSON debe ser válido y completo"
echo ""
echo "❌ Si falla Create PR:"
echo "   • La nueva acción peter-evans/create-pull-request debe funcionar"
echo "   • No debe haber errores de Docker container"
echo ""
echo "❌ Si falla Build:"
echo "   • Verificar que existan los Dockerfiles necesarios"
echo "   • Confirmar permisos de push a Google Container Registry"
echo ""

echo ""
echo "📊 Monitor abierto en browser:"
echo "https://github.com/$REPO/actions"
echo ""
echo "⏳ Esperando resultados del workflow..."
echo ""
echo "💡 Tip: Hacer reload de la página cada 30-60 segundos para ver progreso"