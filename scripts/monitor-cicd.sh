#!/bin/bash

# 🚀 AuthenticFarma CI/CD Monitoring Script
# Herramientas para monitorear y troubleshoot el flujo automatizado

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
NAMESPACE="authenticfarma-candidatos"
APP_NAME="authenticfarma-candidatos"
ARGOCD_NAMESPACE="argocd"

print_header() {
    echo -e "${BLUE}=====================================${NC}"
    echo -e "${BLUE}🚀 AuthenticFarma CI/CD Monitor${NC}"
    echo -e "${BLUE}=====================================${NC}"
    echo
}

print_section() {
    echo -e "${YELLOW}📋 $1${NC}"
    echo "-----------------------------------"
}

check_argocd() {
    print_section "ArgoCD Application Status"
    
    if kubectl get application $APP_NAME -n $ARGOCD_NAMESPACE &>/dev/null; then
        echo -e "${GREEN}✅ ArgoCD Application exists${NC}"
        
        SYNC_STATUS=$(kubectl get application $APP_NAME -n $ARGOCD_NAMESPACE -o jsonpath='{.status.sync.status}')
        HEALTH_STATUS=$(kubectl get application $APP_NAME -n $ARGOCD_NAMESPACE -o jsonpath='{.status.health.status}')
        
        echo "🔄 Sync Status: $SYNC_STATUS"
        echo "🏥 Health Status: $HEALTH_STATUS"
        
        if [[ "$SYNC_STATUS" == "Synced" && "$HEALTH_STATUS" == "Healthy" ]]; then
            echo -e "${GREEN}✅ Application is in perfect state!${NC}"
        else
            echo -e "${YELLOW}⚠️  Application needs attention${NC}"
        fi
    else
        echo -e "${RED}❌ ArgoCD Application not found${NC}"
    fi
    echo
}

check_deployment() {
    print_section "Kubernetes Deployment Status"
    
    if kubectl get deployment $APP_NAME -n $NAMESPACE &>/dev/null; then
        echo -e "${GREEN}✅ Deployment exists${NC}"
        
        # Get deployment status
        kubectl get deployment $APP_NAME -n $NAMESPACE
        echo
        
        # Get current image
        CURRENT_IMAGE=$(kubectl get deployment $APP_NAME -n $NAMESPACE -o jsonpath='{.spec.template.spec.containers[1].image}')
        echo "🐳 Current Image: $CURRENT_IMAGE"
        
        # Check if pods are ready
        READY_PODS=$(kubectl get deployment $APP_NAME -n $NAMESPACE -o jsonpath='{.status.readyReplicas}')
        DESIRED_PODS=$(kubectl get deployment $APP_NAME -n $NAMESPACE -o jsonpath='{.status.replicas}')
        
        if [[ "$READY_PODS" == "$DESIRED_PODS" ]]; then
            echo -e "${GREEN}✅ All pods are ready ($READY_PODS/$DESIRED_PODS)${NC}"
        else
            echo -e "${YELLOW}⚠️  Pods not ready: $READY_PODS/$DESIRED_PODS${NC}"
        fi
    else
        echo -e "${RED}❌ Deployment not found${NC}"
    fi
    echo
}

check_pods() {
    print_section "Pod Status"
    
    kubectl get pods -n $NAMESPACE -l app=$APP_NAME
    echo
    
    # Check for any problematic pods
    PROBLEMATIC_PODS=$(kubectl get pods -n $NAMESPACE -l app=$APP_NAME --field-selector=status.phase!=Running --no-headers 2>/dev/null | wc -l)
    
    if [[ $PROBLEMATIC_PODS -eq 0 ]]; then
        echo -e "${GREEN}✅ All pods are running normally${NC}"
    else
        echo -e "${YELLOW}⚠️  Found $PROBLEMATIC_PODS problematic pods${NC}"
        kubectl get pods -n $NAMESPACE -l app=$APP_NAME --field-selector=status.phase!=Running
    fi
    echo
}

check_service() {
    print_section "Service & Networking"
    
    kubectl get svc,ingress -n $NAMESPACE
    echo
    
    # Test endpoint if service exists
    if kubectl get svc $APP_NAME-service -n $NAMESPACE &>/dev/null; then
        echo "🌐 Testing application endpoint..."
        if curl -s -o /dev/null -w "%{http_code}" https://candidatos.authenticfarma.com | grep -q "200\|302"; then
            echo -e "${GREEN}✅ Application is accessible${NC}"
        else
            echo -e "${RED}❌ Application endpoint not responding${NC}"
        fi
    fi
    echo
}

show_recent_events() {
    print_section "Recent Events (last 10)"
    
    kubectl get events -n $NAMESPACE --sort-by='.lastTimestamp' | tail -10
    echo
}

show_app_logs() {
    print_section "Recent Application Logs"
    
    echo "📝 Last 20 lines of application logs:"
    kubectl logs deployment/$APP_NAME -n $NAMESPACE -c app --tail=20
    echo
}

show_resource_usage() {
    print_section "Resource Usage"
    
    echo "💾 Pod resource usage:"
    kubectl top pods -n $NAMESPACE 2>/dev/null || echo "Metrics not available (metrics-server required)"
    echo
}

show_git_info() {
    print_section "Git Repository Info"
    
    if [[ -d .git ]]; then
        echo "📂 Current branch: $(git branch --show-current)"
        echo "📝 Last commit: $(git log -1 --oneline)"
        echo "🔄 Git status:"
        git status --porcelain | head -5
        
        if [[ -n "$(git status --porcelain)" ]]; then
            echo -e "${YELLOW}⚠️  You have uncommitted changes${NC}"
        else
            echo -e "${GREEN}✅ Working directory clean${NC}"
        fi
    else
        echo "Not in a git repository"
    fi
    echo
}

force_sync() {
    print_section "Forcing ArgoCD Sync"
    
    echo "🔄 Triggering manual sync..."
    kubectl patch application $APP_NAME -n $ARGOCD_NAMESPACE --type='merge' -p='{"operation":{"sync":{"revision":"HEAD"}}}'
    
    echo "✅ Sync triggered. Check status in a few seconds."
    echo
}

show_troubleshooting() {
    print_section "Quick Troubleshooting Commands"
    
    cat << EOF
🔧 Common troubleshooting commands:

1. View ArgoCD app details:
   kubectl describe application $APP_NAME -n $ARGOCD_NAMESPACE

2. View deployment details:
   kubectl describe deployment $APP_NAME -n $NAMESPACE

3. Check pod logs:
   kubectl logs -f deployment/$APP_NAME -n $NAMESPACE -c app

4. View recent events:
   kubectl get events -n $NAMESPACE --sort-by='.lastTimestamp'

5. Force ArgoCD sync:
   kubectl patch application $APP_NAME -n $ARGOCD_NAMESPACE --type='merge' -p='{"operation":{"sync":{"revision":"HEAD"}}}'

6. Scale deployment:
   kubectl scale deployment $APP_NAME -n $NAMESPACE --replicas=3

7. Restart deployment:
   kubectl rollout restart deployment/$APP_NAME -n $NAMESPACE

EOF
    echo
}

# Main menu
main_menu() {
    echo "Choose an option:"
    echo "1. 📊 Full Status Check"
    echo "2. 🔄 ArgoCD Status Only" 
    echo "3. ☸️  Kubernetes Status Only"
    echo "4. 📝 Show Logs"
    echo "5. 🔄 Force ArgoCD Sync"
    echo "6. 🔧 Troubleshooting Guide"
    echo "7. 👀 Live Monitoring (watch mode)"
    echo "8. ❌ Exit"
    echo
    read -p "Enter your choice (1-8): " choice
    
    case $choice in
        1)
            print_header
            check_argocd
            check_deployment  
            check_pods
            check_service
            show_recent_events
            show_resource_usage
            show_git_info
            ;;
        2)
            print_header
            check_argocd
            ;;
        3)
            print_header
            check_deployment
            check_pods
            check_service
            ;;
        4)
            print_header
            show_app_logs
            ;;
        5)
            print_header
            force_sync
            ;;
        6)
            print_header
            show_troubleshooting
            ;;
        7)
            print_header
            echo "🔴 Starting live monitoring (Ctrl+C to stop)..."
            echo
            watch -n 5 "kubectl get applications -n $ARGOCD_NAMESPACE && echo && kubectl get pods -n $NAMESPACE"
            ;;
        8)
            echo "👋 Goodbye!"
            exit 0
            ;;
        *)
            echo -e "${RED}❌ Invalid option${NC}"
            ;;
    esac
    
    echo
    read -p "Press Enter to return to main menu..."
    echo
}

# Check prerequisites
check_prerequisites() {
    if ! command -v kubectl &> /dev/null; then
        echo -e "${RED}❌ kubectl not found. Please install kubectl first.${NC}"
        exit 1
    fi
    
    if ! kubectl cluster-info &> /dev/null; then
        echo -e "${RED}❌ Cannot connect to Kubernetes cluster. Check your kubeconfig.${NC}"
        exit 1
    fi
}

# Main execution
main() {
    check_prerequisites
    
    # If arguments provided, run specific checks
    if [[ $# -gt 0 ]]; then
        case $1 in
            "status"|"check")
                print_header
                check_argocd
                check_deployment  
                check_pods
                ;;
            "sync")
                print_header
                force_sync
                ;;
            "logs")
                print_header
                show_app_logs
                ;;
            "help")
                print_header
                show_troubleshooting
                ;;
            *)
                echo "Usage: $0 [status|sync|logs|help]"
                exit 1
                ;;
        esac
        exit 0
    fi
    
    # Interactive mode
    while true; do
        print_header
        main_menu
    done
}

# Run main function
main "$@"