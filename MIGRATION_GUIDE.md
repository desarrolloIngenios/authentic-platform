# Authentic Platform Migration Guide

## Overview
This document guides the migration from the legacy application structure to the new GitOps platform architecture.

## Migration Status

### ✅ Completed
- ArgoCD SSL configuration and authentication
- Platform structure creation
- AppProject configurations for all platforms
- Application manifests for all platforms
- Historia Clínica migration to new structure

### 🔄 In Progress
- Application-specific directory structures
- Environment-specific configurations
- CI/CD pipeline updates

### ⏳ Pending
- Legacy `apps/` directory cleanup
- DNS configuration updates for new applications
- Monitoring and logging setup

## Platform Structure

```
platforms/
├── authenticfarma/
│   ├── project.yaml              # AppProject configuration
│   ├── applications.yaml         # Application definitions
│   ├── candidatos/
│   │   └── k8s/                 # Kubernetes manifests
│   ├── inventario/
│   │   └── k8s/
│   └── ventas/
│       └── k8s/
├── yosoy/
│   ├── project.yaml
│   ├── applications.yaml
│   ├── historia-clinica/
│   │   └── k8s/                 # ✅ Migrated from apps/yosoy/historia-clinica/argocd/
│   ├── fitness/
│   │   └── k8s/
│   └── nutricion/
│       └── k8s/
└── isyours/
    ├── project.yaml
    ├── applications.yaml
    ├── ecommerce/
    │   └── k8s/
    ├── propiedades/
    │   └── k8s/
    └── pagos/
        └── k8s/
```

## Migration Steps

### 1. Apply Platform Configuration
```bash
# Apply AppProject configurations
kubectl apply -f platforms/authenticfarma/project.yaml
kubectl apply -f platforms/yosoy/project.yaml
kubectl apply -f platforms/isyours/project.yaml

# Apply Application definitions
kubectl apply -f platforms/authenticfarma/applications.yaml
kubectl apply -f platforms/yosoy/applications.yaml
kubectl apply -f platforms/isyours/applications.yaml
```

### 2. Update Existing Applications
The Historia Clínica application has been migrated. For other applications:
1. Copy manifests from `apps/` to appropriate `platforms/*/app-name/k8s/`
2. Update ArgoCD Application definitions
3. Test deployment
4. Remove legacy configurations

### 3. Environment Management
Each application should have environment-specific configurations:
```
platforms/platform-name/app-name/
├── k8s/
│   ├── base/                    # Base configurations
│   ├── overlays/
│   │   ├── development/
│   │   ├── staging/
│   │   └── production/
│   └── kustomization.yaml
```

### 4. CI/CD Pipeline Updates
Update your CI/CD pipelines to:
- Build and push images with proper tags
- Update Kubernetes manifests in the correct platform directory
- Trigger ArgoCD sync for the specific application

## ArgoCD Access

### URLs
- **ArgoCD UI**: https://argo.authenticfarma.com
- **Historia Clínica (YoSoy)**: https://historia-clinica.yosoy.com

### Applications in ArgoCD
After applying the configurations, you'll see these applications:

**AuthenticFarma Platform:**
- `authenticfarma-candidatos`
- `authenticfarma-inventario`
- `authenticfarma-ventas`

**YoSoy Platform:**
- `yosoy-historia-clinica` ✅ (Currently running)
- `yosoy-fitness`
- `yosoy-nutricion`

**IsYours Platform:**
- `isyours-ecommerce`
- `isyours-propiedades`
- `isyours-pagos`

## Security and Access Control

Each platform has its own RBAC configuration:
- **Admin groups**: Full access to platform applications
- **Developer groups**: Read and sync access
- **Sync windows**: Automated deployments scheduled for off-hours

## Next Steps

1. **Create application directories** for remaining applications
2. **Migrate manifests** from `apps/` to `platforms/`
3. **Set up monitoring** for each platform
4. **Configure DNS** for new applications
5. **Update documentation** with new deployment procedures

## Troubleshooting

### Common Issues
- **Namespace conflicts**: Ensure each application uses its own namespace
- **Resource permissions**: Check AppProject resource whitelists
- **Sync failures**: Verify manifest syntax and dependencies

### Support
- Check ArgoCD events and logs for deployment issues
- Review platform-specific documentation
- Contact DevOps team for infrastructure concerns

---
**Last Updated**: $(date)
**Version**: 1.0
**Status**: Migration in Progress