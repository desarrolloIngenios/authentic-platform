#!/bin/bash

# 🔍 Script para verificar GitHub Actions workflows
# Usa la API pública de GitHub para verificar el estado

REPO="desarrolloIngenios/authentic-platform"
API_URL="https://api.github.com/repos/$REPO/actions/runs"

echo "🚀 Verificando GitHub Actions para $REPO..."
echo "📡 URL: $API_URL"
echo ""

# Hacer request a la API pública
curl -s -H "Accept: application/vnd.github.v3+json" \
     "$API_URL?per_page=5" | \
     jq -r '.workflow_runs[] | 
     "📋 Run ID: \(.id)
🏷️  Name: \(.name) 
🔄 Status: \(.status)
✅ Conclusion: \(.conclusion // "running")
📅 Created: \(.created_at)
🌿 Branch: \(.head_branch)
💬 Commit: \(.head_commit.message[0:80])...
🔗 URL: \(.html_url)
----------------------------------------"' 2>/dev/null || {
    echo "❌ Error accessing GitHub API or jq not available"
    echo ""
    echo "📋 Manual check:"
    echo "🔗 Visit: https://github.com/desarrolloIngenios/authentic-platform/actions"
    echo ""
    echo "🔍 Look for workflows with:"
    echo "   - Branch: dev" 
    echo "   - Commit: c59390e (test: Activar pipeline)"
    echo "   - Name: 🚀 CI/CD Pipeline - Authentic Platform"
}

echo ""
echo "📱 Direct Links:"
echo "🔗 Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"
echo "🔗 Latest: https://github.com/desarrolloIngenios/authentic-platform/actions/workflows/ci-cd-pipeline.yml"