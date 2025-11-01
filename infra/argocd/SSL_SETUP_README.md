# 🔒 Configuración SSL para ArgoCD - argo.authenticfarma.com

## ✅ Estado Actual

### 📍 IP Estática Creada
- **IP**: `34.111.249.97`
- **Nombre**: `argocd-ip`
- **Tipo**: Global, External

### 🔐 Certificado SSL
- **Estado**: Provisioning (⏳ En proceso)
- **Tipo**: Google Managed Certificate
- **Dominio**: argo.authenticfarma.com

## 📋 Pasos Implementados

1. ✅ **ClusterIssuer Let's Encrypt** creado
2. ✅ **IP estática global** reservada: `34.111.249.97`
3. ✅ **ManagedCertificate** configurado
4. ✅ **Ingress** con SSL configurado
5. ✅ **ArgoCD Server** reiniciado con nueva configuración

## 🌐 Configuración DNS Requerida

**IMPORTANTE**: Debes configurar el DNS para que el dominio apunte a la IP estática:

```
Tipo: A
Nombre: argo.authenticfarma.com
Valor: 34.111.249.97
TTL: 300
```

## 📁 Archivos Creados

### 1. SSL Configuration (`/infra/argocd/ssl-config.yaml`)
- ClusterIssuer para Let's Encrypt
- Ingress con SSL
- Service NodePort

### 2. Server Configuration (`/infra/argocd/server-config.yaml`)
- ConfigMap para parámetros del servidor
- Configuración HTTPS

### 3. Certificate Configuration (`/infra/argocd/certificate.yaml`)
- Certificate manual
- ManagedCertificate de Google

### 4. Managed SSL (`/infra/argocd/managed-ssl.yaml`)
- ManagedCertificate para Google Cloud

## 🚀 Comandos Aplicados

```bash
# Aplicar configuraciones
kubectl apply -f /Users/devapp/authentic-platform/infra/argocd/ssl-config.yaml
kubectl apply -f /Users/devapp/authentic-platform/infra/argocd/server-config.yaml
kubectl apply -f /Users/devapp/authentic-platform/infra/argocd/certificate.yaml
kubectl apply -f /Users/devapp/authentic-platform/infra/argocd/managed-ssl.yaml

# Crear IP estática
gcloud compute addresses create argocd-ip --global --project=authentic-prod-464216

# Reiniciar ArgoCD
kubectl rollout restart deployment/argocd-server -n argocd
```

## ⏱️ Tiempo de Propagación

- **Certificado SSL**: 10-15 minutos
- **Propagación DNS**: 5-10 minutos
- **Activación completa**: 15-30 minutos

## 🔍 Verificación

### Comandos de Monitoreo
```bash
# Verificar certificado
kubectl get managedcertificate -n argocd
kubectl describe managedcertificate argocd-ssl-cert -n argocd

# Verificar ingress
kubectl get ingress -n argocd
kubectl describe ingress argocd-server-ingress -n argocd

# Verificar ArgoCD
kubectl get pods -n argocd
kubectl logs -l app.kubernetes.io/name=argocd-server -n argocd
```

### URLs de Acceso
- **HTTP** (temporal): http://argo.authenticfarma.com
- **HTTPS** (objetivo): https://argo.authenticfarma.com

## ⚠️ Próximos Pasos

1. **Configurar DNS**: Apuntar `argo.authenticfarma.com` a `34.111.249.97`
2. **Esperar certificado**: El ManagedCertificate tardará 10-15 minutos
3. **Verificar acceso**: Probar https://argo.authenticfarma.com
4. **Configurar autenticación**: Revisar configuración OIDC si es necesario

## 🛠️ Troubleshooting

### Si el certificado no se activa:
```bash
# Verificar eventos del certificado
kubectl describe managedcertificate argocd-ssl-cert -n argocd

# Verificar logs de cert-manager
kubectl logs -l app=cert-manager -n cert-manager
```

### Si el ingress no obtiene IP:
```bash
# Verificar el ingress controller
kubectl get pods -n kube-system | grep ingress

# Verificar la configuración del ingress
kubectl describe ingress argocd-server-ingress -n argocd
```

---

**📅 Creado**: Noviembre 1, 2025  
**🎯 Objetivo**: SSL habilitado para argo.authenticfarma.com  
**⚡ Estado**: Configuración completa, esperando propagación DNS