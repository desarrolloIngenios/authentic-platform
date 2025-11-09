# 🔐 CREDENCIALES Y ACCESOS - AUTHENTIC-24

## 👤 Información del Desarrollador Principal

- **Usuario**: `authentic-24`
- **Email**: `stack.dev@authentic.com.co`
- **Rol**: Lead Developer & DevOps Engineer
- **Permisos**: Owner/Admin en todas las aplicaciones
- **Fecha de configuración**: $(date)

---

## 🔑 ACCESOS REQUERIDOS

### 📋 1. GitHub Repository Access
- **Repositorio**: `desarrolloIngenios/authentic-platform`
- **Permisos requeridos**:
  - ✅ Owner/Admin access
  - ✅ Push to `main` and `dev` branches
  - ✅ Create/merge Pull Requests
  - ✅ Manage GitHub Actions workflows
  - ✅ Manage repository settings & secrets

### 🔐 2. GitHub Actions Secrets
Los siguientes secrets deben estar configurados en el repositorio:

```yaml
GCP_SA_KEY: |
  {
    "type": "service_account",
    "project_id": "PROJECT_ID",
    "private_key_id": "KEY_ID",
    "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
    "client_email": "SERVICE_ACCOUNT@PROJECT_ID.iam.gserviceaccount.com",
    "client_id": "CLIENT_ID",
    "auth_uri": "https://accounts.google.com/o/oauth2/auth",
    "token_uri": "https://oauth2.googleapis.com/token"
  }

GITHUB_TOKEN: # Auto-generado por GitHub Actions
```

### ☁️ 3. Google Cloud Platform Access
- **Project ID**: `authentic-platform-prod` (o similar)
- **Permisos requeridos para authentic-24**:
  - ✅ Project Owner o Editor
  - ✅ Container Registry Admin
  - ✅ Kubernetes Engine Developer
  - ✅ Service Account Admin
  - ✅ Cloud Build Editor

**Service Account Email**: 
```
cicd-service@PROJECT_ID.iam.gserviceaccount.com
```

### 🚀 4. ArgoCD Access
- **URL**: `https://argocd.authentic.com.co` (o URL correspondiente)
- **Usuario**: `authentic-24`
- **Permisos**: Admin access
- **Aplicaciones bajo control**:
  - `authenticfarma-candidatos-dev`
  - `authenticfarma-candidatos-prod`
  - `yosoy-hc-dev`
  - `yosoy-hc-prod`
  - `isyours-dev`
  - `isyours-prod`
  - `moodle-elearning-dev`
  - `moodle-elearning-prod`

---

## 🏗️ APLICACIONES Y URLs

### 🧬 AuthenticFarma Candidatos
- **Dev**: https://candidatos-dev.authenticfarma.com
- **Prod**: https://candidatos.authenticfarma.com
- **Admin**: admin / admin123
- **Tech**: Laravel + MySQL + OAuth Google

### 🏥 YoSoy Historia Clínica  
- **Dev**: https://hc-dev.yo-soy.co
- **Prod**: https://hc.yo-soy.co
- **Admin**: admin / admin123
- **Tech**: FastAPI + SQLite + JWT

### 🌟 IsYours Platform
- **Dev**: https://isyours-dev.com
- **Prod**: https://isyours.com
- **Tech**: React + Node.js + PostgreSQL

### 📚 Moodle E-Learning
- **Dev**: https://moodle-dev.authentic.com.co
- **Prod**: https://moodle.authentic.com.co
- **Tech**: Moodle + MySQL

---

## 🚀 WORKFLOW DE DEPLOYMENT

### 🔄 Desarrollo (Branch: dev)
```bash
# 1. Desarrollo local
git checkout dev
git pull origin dev

# 2. Modificar aplicación(es)
vim apps/authenticfarma/candidatos/...
# o
vim apps/yosoy/historia-clinica/...
# o 
vim apps/isyours/...
# o
vim apps/moodle-elearning/...

# 3. Commit y push
git add apps/[app-modificada]/
git commit -m "feat: nueva funcionalidad"
git push origin dev

# 4. CI/CD inteligente automático:
# → detect-changes: Detecta qué apps cambiaron
# → build-[app]: Solo construye las apps modificadas  
# → deploy-dev: Deploy automático a desarrollo
```

### 🛡️ Producción (Branch: main)
```bash
# 1. El workflow automáticamente crea PR de dev → main
# 2. Revisar PR en GitHub
# 3. Aprobar y merge PR
# 4. Workflow automático construye imágenes de producción
# 5. Sync manual en ArgoCD:

argocd app sync authenticfarma-candidatos-prod
argocd app sync yosoy-hc-prod  
argocd app sync isyours-prod
argocd app sync moodle-elearning-prod
```

---

## 📊 MONITOREO Y DEBUGGING

### 📈 GitHub Actions Dashboard
```
https://github.com/desarrolloIngenios/authentic-platform/actions
```

### 🔍 Comandos de Monitoreo
```bash
# Listar workflows recientes
gh run list --limit 10

# Monitor en tiempo real
gh run watch

# Ver logs detallados  
gh run view [RUN_ID] --log

# Ver estado del último run
gh run view --log
```

### 🎯 ArgoCD Dashboard
```bash
# Ver estado de aplicaciones
kubectl get applications -n argocd

# Logs de ArgoCD
kubectl logs -n argocd deployment/argocd-server

# Sync manual de app específica
argocd app sync [APP_NAME]
```

---

## 🧠 SISTEMA INTELIGENTE

### ✨ Detección Automática
El sistema detecta automáticamente qué aplicaciones fueron modificadas:

```yaml
Cambios en apps/authenticfarma/ → Solo build AuthenticFarma
Cambios en apps/yosoy/ → Solo build YoSoy
Cambios en apps/isyours/ → Solo build IsYours
Cambios en apps/moodle-elearning/ → Solo build Moodle
Cambios en múltiples apps → Solo build las modificadas
Sin cambios en apps/ → Todos los builds skipped (100% optimización)
```

### ⚡ Optimización Automática
- **1 app modificada**: 75% menos tiempo de build
- **2 apps modificadas**: 50% menos tiempo de build  
- **0 apps modificadas**: 100% optimización (solo 2 minutos)

---

## 🛡️ SEGURIDAD Y MEJORES PRÁCTICAS

### ✅ Hacer
- Usar conventional commits: `feat:`, `fix:`, `docs:`, etc.
- Probar localmente antes de push
- Revisar logs de CI/CD antes de deployar a prod
- Usar feature branches para desarrollo complejo
- Validar builds antes de sync ArgoCD

### ❌ Evitar  
- Push directo a `main` (siempre usar PRs)
- Commits masivos (preferir commits granulares)
- Deploy a prod sin validar dev primero
- Modificar workflow sin testing exhaustivo

---

## 🎯 CONTACTOS Y SOPORTE

### 🆘 En caso de problemas:
1. **Revisar GitHub Actions logs** primero
2. **Verificar ArgoCD status** de las apps
3. **Consultar documentación** en `docs/developers/`
4. **Crear issue** en GitHub si es necesario

### 📚 Documentación adicional:
- `docs/developers/intelligent-cicd-workflow.md`
- `docs/FINAL-INTELLIGENT-CICD-SUMMARY.md`
- `scripts/setup-authentic24-permissions.sh`

---

## 🎉 ESTADO ACTUAL

✅ **Sistema CI/CD Inteligente**: Completamente funcional  
✅ **Usuario authentic-24**: Configurado con permisos completos  
✅ **Todas las aplicaciones**: Bajo control de authentic-24  
✅ **Detección automática**: Optimización 50-100% de builds  
✅ **GitOps**: ArgoCD operacional para todas las apps  

**¡authentic-24 está listo para desarrollar y deployar cualquier aplicación! 🚀✨**