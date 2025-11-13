#!/bin/bash

# 🚀 Guía para crear Pull Request manual desde GitHub Web
# GitHub ya detectó: "dev had recent pushes 2 minutes ago"

echo "🚀 Crear Pull Request Manual - GitHub Web Interface"
echo "=================================================="

echo ""
echo "✅ GitHub ya detectó los cambios recientes en dev!"
echo ""
echo "📋 PASOS PARA CREAR EL PR:"
echo "========================="
echo ""
echo "1. 🔗 Ir a GitHub:"
echo "   https://github.com/desarrolloIngenios/authentic-platform"
echo ""
echo "2. 📋 Buscar el banner amarillo que dice:"
echo '   "dev had recent pushes 2 minutes ago"'
echo ""
echo "3. ✅ Click en el botón verde:"
echo '   "Compare & pull request"'
echo ""
echo "4. 📝 Configurar el Pull Request:"
echo ""
echo "   Base: main ← head: dev"
echo ""
echo "   Título:"
echo "   🚀 Release: Deploy dev changes to production"
echo ""
echo "   Descripción:"

cat << 'EOF'
## 🚀 Production Release

Este PR contiene cambios validados desde `dev` listos para despliegue en producción.

### 📋 Cambios incluidos

- ✅ Configuración de usuario Git: ottofonseca@gmail.com  
- ✅ Corrección de autenticación GCP (service account key)
- ✅ Corrección de workflow CI/CD (pull request automation)
- ✅ Sincronización con cambios remotos
- ✅ Validación de tests y builds

### 🛡️ Pre-deployment Checklist

- [x] Review all changes
- [x] Verify dev environment testing
- [x] GCP Authentication working  
- [x] CI/CD Pipeline functional
- [ ] Final approval for production

### 🚀 Deployment

Merging this PR will trigger production deployment via ArgoCD.

**Monitor deployment**: https://argo.authenticfarma.com/applications/authentic-platform-prod
EOF

echo ""
echo "5. 🏷️ Agregar labels:"
echo "   - release"
echo "   - production"  
echo "   - ready-for-deploy"
echo ""
echo "6. 👤 Asignar:"
echo "   - Assignee: ottofonseca"
echo "   - Reviewer: (optional)"
echo ""
echo "7. ✅ Click 'Create pull request'"
echo ""

echo "🎯 ALTERNATIVA - URL DIRECTA:"
echo "============================"
echo ""
echo "Si no ves el banner, usar URL directa:"
echo "https://github.com/desarrolloIngenios/authentic-platform/compare/main...dev"
echo ""

echo "📊 RESUMEN DE CAMBIOS EN EL PR:"
echo "=============================="
echo ""
echo "Commits incluidos (dev → main):"
git log main..dev --oneline --max-count=5
echo ""
echo "Total de commits: $(git rev-list --count main..dev)"
echo ""

echo "✅ DESPUÉS DE CREAR EL PR:"
echo "=========================="
echo ""
echo "1. 📋 Verificar que aparece en:"
echo "   https://github.com/desarrolloIngenios/authentic-platform/pulls"
echo ""  
echo "2. 🔍 Review de cambios críticos:"
echo "   - Configuración de ottofonseca ✅"
echo "   - GCP service account key ✅"
echo "   - CI/CD workflow fixes ✅"
echo ""
echo "3. 🚀 Cuando esté listo:"
echo "   - Merge del PR activará producción"
echo "   - Monitorear ArgoCD deployment"
echo ""

echo "💡 TIP:"
echo "======"
echo "El mensaje 'dev had recent pushes' facilita la creación del PR."
echo "Es la forma más rápida de crear el Pull Request ahora."