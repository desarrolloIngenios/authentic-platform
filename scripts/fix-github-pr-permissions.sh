#!/bin/bash

echo "🔐 SOLUCIONANDO ERROR DE PERMISOS GITHUB ACTIONS"
echo "==============================================="
echo ""

echo "❌ Error detectado:"
echo "   'GraphQL: Resource not accessible by integration (createPullRequest)'"
echo ""
echo "🎯 Causa: El GITHUB_TOKEN no tiene permisos suficientes para crear PRs"
echo ""

echo "✅ SOLUCIONES APLICADAS:"
echo "======================="
echo ""

echo "1. 🔧 PERMISOS AGREGADOS AL WORKFLOW:"
echo "   ✅ contents: write (para escribir en el repo)"
echo "   ✅ pull-requests: write (para crear PRs)"
echo "   ✅ issues: write (para crear issues)"  
echo "   ✅ repository-projects: write (para proyectos)"
echo "   ✅ actions: read (para leer otros workflows)"
echo "   ✅ checks: read (para leer verificaciones)"
echo ""

echo "2. 📋 VERIFICACIONES ADICIONALES NECESARIAS:"
echo "============================================"
echo ""

echo "🔍 A. CONFIGURACIÓN DEL REPOSITORIO:"
echo "   Ir a: GitHub → Settings → Actions → General"
echo "   Verificar que esté en: 'Read and write permissions'"
echo "   URL: https://github.com/desarrolloIngenios/authentic-platform/settings/actions"
echo ""

echo "🔍 B. BRANCH PROTECTION RULES:"
echo "   Si hay reglas de protección en 'main', verificar que permitan:"
echo "   - PRs automáticos"
echo "   - Bypass para GitHub Actions"
echo "   URL: https://github.com/desarrolloIngenios/authentic-platform/settings/branches"
echo ""

echo "🔍 C. TOKEN SCOPES (Si usas Personal Access Token):"
echo "   El token debe tener estos scopes:"
echo "   - repo (acceso completo a repositorios)"
echo "   - workflow (para modificar workflows)"
echo "   - write:packages (para escribir packages)"
echo ""

echo "🛠️ COMANDOS PARA VERIFICAR Y CORREGIR:"
echo "======================================="
echo ""

cat << 'EOF'
# 1. Verificar permisos actuales del workflow
echo "Revisando workflow actual..."
grep -A 10 "permissions:" .github/workflows/ci-cd-pipeline.yml

# 2. Test manual de creación de PR (requiere gh CLI)
gh pr create --title "Test PR" --body "Prueba de permisos" --head dev --base main

# 3. Verificar configuración del repositorio
gh repo view desarrolloIngenios/authentic-platform --json defaultBranchRef,hasIssuesEnabled

# 4. Listar secrets configurados
gh secret list

EOF

echo "🔄 ALTERNATIVAS SI PERSISTE EL PROBLEMA:"
echo "========================================"
echo ""

echo "📝 ALTERNATIVA 1: Personal Access Token (PAT)"
echo "   1. Crear PAT en: https://github.com/settings/tokens"
echo "   2. Scopes necesarios: repo, workflow, write:packages"
echo "   3. Agregar como secret: PERSONAL_ACCESS_TOKEN"
echo "   4. Usar en workflow: token: \${{ secrets.PERSONAL_ACCESS_TOKEN }}"
echo ""

echo "📝 ALTERNATIVA 2: GitHub App"
echo "   1. Crear GitHub App con permisos necesarios"
echo "   2. Instalar en el repositorio"
echo "   3. Usar token de la app en workflow"
echo ""

echo "📝 ALTERNATIVA 3: Workflow sin PR automático"
echo "   1. Eliminar el job de creación de PR"
echo "   2. Crear PRs manualmente"
echo "   3. Mantener solo builds y deployments automáticos"
echo ""

echo "🧪 PROBAR LA CORRECCIÓN:"
echo "======================="
echo ""
echo "1. Hacer commit del workflow corregido:"
echo "   git add .github/workflows/ci-cd-pipeline.yml"
echo "   git commit -m 'fix: agregar permisos para PRs automáticos'"
echo "   git push origin dev"
echo ""
echo "2. Hacer merge a main para probar:"
echo "   git checkout main"
echo "   git merge dev"
echo "   git push origin main"
echo ""
echo "3. Verificar en GitHub Actions que el PR se crea correctamente"
echo ""

echo "📊 MONITOREO:"
echo "============"
echo ""
echo "# Ver logs del workflow"
echo "gh run list --limit 5"
echo "gh run view --log"
echo ""
echo "# Verificar PRs creados"
echo "gh pr list"
echo ""

echo "🎯 CONFIGURACIÓN REPOSITORIO RECOMENDADA:"
echo "========================================="
echo ""
echo "Settings → Actions → General:"
echo "✅ Workflow permissions: Read and write permissions"
echo "✅ Allow GitHub Actions to create and approve pull requests: ✅"
echo ""
echo "Settings → Branches → main:"
echo "⚠️  Si hay branch protection, agregar excepciones para GitHub Actions"
echo ""

echo "🔐 SECRETS NECESARIOS:"
echo "====================="
echo ""
echo "Repository → Settings → Secrets and variables → Actions:"
echo "✅ GITHUB_TOKEN: (automático)"
echo "✅ GCP_SA_KEY: Service Account para Google Cloud"
echo "⚪ PERSONAL_ACCESS_TOKEN: (opcional, si GITHUB_TOKEN falla)"
echo ""

echo "✨ ESTADO DESPUÉS DE LA CORRECCIÓN:"
echo "=================================="
echo ""
echo "✅ Permisos agregados al workflow"
echo "✅ Sistema CI/CD inteligente mantiene funcionalidad"
echo "✅ Builds condicionales siguen funcionando"
echo "🔧 PRs automáticos: CORREGIDO"
echo ""

echo "🎉 EL WORKFLOW DEBERÍA FUNCIONAR CORRECTAMENTE AHORA!"
echo ""
echo "Si persiste el error, revisar configuración del repositorio"
echo "en GitHub → Settings → Actions → General"