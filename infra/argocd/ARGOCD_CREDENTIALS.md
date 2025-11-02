# 🔐 Credenciales de ArgoCD

## 🌐 URL de Acceso
**URL**: https://argo.authenticfarma.com

## 👤 Credenciales de Admin

**Usuario**: `admin`  
**Contraseña**: `3pRq-HaeNIddWrss`

## 📋 Información de Acceso

### Navegador Web
1. Ir a: https://argo.authenticfarma.com
2. Usuario: admin
3. Contraseña: 3pRq-HaeNIddWrss

### CLI de ArgoCD
```bash
# Instalar ArgoCD CLI (si no está instalado)
brew install argocd

# Login (usar --grpc-web para evitar warnings)
argocd login argo.authenticfarma.com --username admin --password 3pRq-HaeNIddWrss --grpc-web

# Verificar aplicaciones
argocd app list

# Verificar estado del cluster
argocd cluster list
```

## 🔄 Renovar Contraseña (Opcional)

Si deseas cambiar la contraseña por una personalizada:

```bash
# Cambiar contraseña
argocd account update-password --account admin --current-password 3pRq-HaeNIddWrss --new-password TU_NUEVA_CONTRASEÑA

# O usando kubectl
kubectl -n argocd patch secret argocd-secret \
  -p '{"stringData": {
    "admin.password": "$2a$10$HASH_DE_TU_NUEVA_CONTRASEÑA",
    "admin.passwordMtime": "'$(date +%FT%T%Z)'"
  }}'
```

## 📊 Estado Actual del Sistema

- ✅ SSL Certificado válido hasta: enero 2026
- ✅ TLS 1.3 y HTTP/2 habilitados  
- ✅ Backend health checks saludables
- ✅ DNS configurado correctamente
- ✅ ArgoCD UI completamente funcional

## 🛠️ Comandos Útiles

```bash
# Obtener contraseña actual
kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 -d

# Ver pods de ArgoCD
kubectl get pods -n argocd

# Ver logs del servidor
kubectl logs -l app.kubernetes.io/name=argocd-server -n argocd

# Verificar ingress
kubectl get ingress -n argocd

# Estado del certificado SSL
kubectl get managedcertificate -n argocd
```

---

**📅 Generado**: Noviembre 1, 2025  
**🔒 Seguridad**: Mantener estas credenciales seguras  
**🌐 URL**: https://argo.authenticfarma.com