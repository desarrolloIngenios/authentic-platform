#!/bin/bash

set -e

WORKFLOW_ID="19280792089"
REPO="desarrolloIngenios/authentic-platform"

echo "🚀 Monitoring GitHub Actions Workflow"
echo "====================================="
echo "Workflow ID: $WORKFLOW_ID"
echo "Repository: $REPO"
echo ""

# Función para obtener el estado del workflow
get_workflow_status() {
    curl -s -H "Accept: application/vnd.github+json" \
        "https://api.github.com/repos/$REPO/actions/runs/$WORKFLOW_ID" | \
        jq -r '. | "\(.status)|\(.conclusion)|\(.created_at)|\(.updated_at)"'
}

# Función para obtener los jobs del workflow
get_workflow_jobs() {
    curl -s -H "Accept: application/vnd.github+json" \
        "https://api.github.com/repos/$REPO/actions/runs/$WORKFLOW_ID/jobs" | \
        jq -r '.jobs[] | "  \(.name): \(.status) - \(.conclusion // "running")"'
}

echo "📊 Current Status:"
STATUS_INFO=$(get_workflow_status)
IFS='|' read -r status conclusion created_at updated_at <<< "$STATUS_INFO"

echo "  Status: $status"
echo "  Conclusion: ${conclusion:-"N/A"}"
echo "  Started: $created_at"
echo "  Updated: $updated_at"

echo ""
echo "🔧 Jobs Status:"
get_workflow_jobs

echo ""
echo "🌐 Workflow URL:"
echo "https://github.com/$REPO/actions/runs/$WORKFLOW_ID"

# Monitoreo continuo si está en progreso
if [ "$status" = "in_progress" ]; then
    echo ""
    echo "⏳ Workflow is running. Monitoring for completion..."
    echo "Press Ctrl+C to stop monitoring"
    
    while [ "$status" = "in_progress" ]; do
        sleep 30
        STATUS_INFO=$(get_workflow_status)
        IFS='|' read -r status conclusion created_at updated_at <<< "$STATUS_INFO"
        
        echo "$(date '+%H:%M:%S') - Status: $status"
        if [ "$status" != "in_progress" ]; then
            break
        fi
    done
    
    echo ""
    echo "✅ Workflow completed with status: $status"
    echo "   Conclusion: ${conclusion:-"N/A"}"
    
    echo ""
    echo "📋 Final Jobs Status:"
    get_workflow_jobs
fi