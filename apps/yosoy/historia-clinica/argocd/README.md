# 🚀 Historia Clínica - ArgoCD Deployment

## ✅ Configuración Completada

La aplicación **Historia Clínica** está ahora completamente gestionada por **ArgoCD** con SSL habilitado.

### 🎯 Información de Acceso

| Recurso | URL/IP | Estado |
|---------|--------|--------|
| **App Historia Clínica** | http://35.201.117.50 | ✅ Funcionando |
| **Dominio SSL** | https://hc.yo-soy.co | ⏳ Certificado en provisioning |
| **ArgoCD Dashboard** | https://argo.authenticfarma.com | ✅ Gestión activa |

### 📊 Estado de la Aplicación

```
Namespace: historia-clinicas
Replicas: 2/2 Running
Sync Status: Synced ✅
Health: Progressing ⏳
```

### 🔧 Componentes Desplegados

#### 1. **Namespace**
- `historia-clinicas` con labels ArgoCD

#### 2. **Deployment**
- **Nombre**: `historia-clinicas-frontend`
- **Imagen**: `gcr.io/authentic-prod-464216/yosoy-historia-clinica:v5`
- **Replicas**: 2
- **Recursos**: 128Mi-512Mi RAM, 100m-500m CPU
- **Health Checks**: Liveness + Readiness probes

#### 3. **Service**
- **Nombre**: `historia-clinicas-service`
- **Tipo**: ClusterIP
- **Puerto**: 80 → 8080
- **BackendConfig**: Health checks optimizados

#### 4. **Ingress SSL**
- **Host**: `hc.yo-soy.co`
- **IP Estática**: `35.201.117.50`
- **SSL**: Google ManagedCertificate
- **Class**: GCE Load Balancer

#### 5. **BackendConfig**
- Health checks cada 15s
- Timeout 10s
- Connection draining 300s

### 🌐 Configuración SSL

#### IP Estática
```bash
Name: historia-clinicas-ip
IP: 35.201.117.50
Type: Global External
```

#### Certificado SSL
```yaml
Domain: hc.yo-soy.co
Type: Google ManagedCertificate
Status: Provisioning → Active (10-15 min)
```

### ⚙️ ArgoCD Configuration

#### Sync Policy
```yaml
Automated: true
Prune: true
Self-heal: true
CreateNamespace: true
```

#### Repository
```
URL: https://github.com/desarrolloIngenios/authentic-platform.git
Path: apps/yosoy/historia-clinica/argocd
Target: HEAD (main branch)
```

### 📋 DNS Configuration Requerida

Para habilitar el acceso por dominio, configurar DNS:

```
Type: A
Name: hc.yo-soy.co
Value: 35.201.117.50
TTL: 300
```

### 🛠️ Comandos de Gestión

#### ArgoCD CLI
```bash
# Ver estado de la aplicación
argocd app get historia-clinica

# Sincronizar manualmente
argocd app sync historia-clinica

# Ver logs
argocd app logs historia-clinica

# Ver diferencias
argocd app diff historia-clinica
```

#### Kubectl
```bash
# Ver recursos en el namespace
kubectl get all -n historia-clinicas

# Ver logs de los pods
kubectl logs -l app=historia-clinicas-frontend -n historia-clinicas

# Ver estado del ingress
kubectl describe ingress historia-clinicas-ingress -n historia-clinicas

# Ver estado del certificado SSL
kubectl get managedcertificate historia-clinicas-ssl -n historia-clinicas
```

### 🔄 Workflow de Desarrollo

1. **Desarrollar** → Cambios en código
2. **Build** → Nueva imagen Docker
3. **Update** → Actualizar tag en manifiestos
4. **Commit/Push** → Git repository
5. **Auto-sync** → ArgoCD despliega automáticamente

### 📁 Estructura de Archivos

```
apps/yosoy/historia-clinica/argocd/
├── 01-namespace.yaml          # Namespace con labels
├── 02-deployment.yaml         # Deployment + Service
├── 03-ingress.yaml           # Ingress + ManagedCertificate
└── 04-backend-config.yaml    # Health checks

infra/argocd/
└── historia-clinica-application.yaml  # ArgoCD Application
```

### 🎯 Próximos Pasos

1. **Configurar DNS** para `hc.yo-soy.co` → `35.201.117.50`
2. **Esperar SSL** (10-15 minutos para activación)
3. **Verificar HTTPS** en `https://hc.yo-soy.co`
4. **Configurar CD Pipeline** para builds automáticos

### 🔍 Monitoring & Observability

#### Health Status
- **ArgoCD UI**: https://argo.authenticfarma.com/applications/historia-clinica
- **App Health**: Progressing → Healthy
- **Sync Status**: Synced ✅

#### Logs Access
```bash
# Logs en tiempo real
kubectl logs -f deployment/historia-clinicas-frontend -n historia-clinicas

# Logs de ArgoCD
argocd app logs historia-clinica --follow
```

---

**📅 Configurado**: Noviembre 1, 2025  
**🎯 Estado**: ArgoCD gestión activa, SSL en provisioning  
**🚀 Resultado**: Aplicación completamente gestionada por GitOps