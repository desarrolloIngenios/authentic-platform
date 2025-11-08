# 🚀 Flujo de Desarrollo CI/CD - Authentic Platform

## ✨ **Para el Desarrollador: Push y Deploy Automático**

### 🔄 **Workflow Simplificado:**

1. **Desarrollo en rama `dev`:**
   ```bash
   git checkout dev
   # Hacer cambios en Historia Clínica, Candidatos, etc.
   git add .
   git commit -m "feat: nueva funcionalidad"
   git push origin dev  # ¡DEPLOY AUTOMÁTICO!
   ```

2. **¡Todo sucede automáticamente!** ⚡
   - ✅ GitHub Actions ejecuta tests
   - 🏗️ Build de imágenes Docker
   - 📤 Push a Google Container Registry
   - 🚀 ArgoCD despliega automáticamente en DEV

### 📊 **Entornos Configurados:**

#### 🧪 **DEV (Desarrollo)**
- **Rama**: `dev`
- **Deploy**: ✅ Automático en cada push
- **Namespaces**: 
  - `yosoy-historia-clinica-dev`
  - `authenticfarma-dev`
- **URLs**: 
  - Historia Clínica DEV: `hc-dev.yo-soy.co` (pendiente configurar)
  - Candidatos DEV: `candidatos-dev.authenticfarma.com` (pendiente configurar)

#### 🚀 **PROD (Producción)**
- **Rama**: `main`
- **Deploy**: 🔒 Manual con aprobación
- **Process**: PR dev→main → Review → Merge → Sync manual
- **URLs**:
  - Historia Clínica: https://hc.yo-soy.co
  - Candidatos: https://candidatos.authenticfarma.com

### 🛠️ **Herramientas de Monitoreo:**

#### 📈 **GitHub Actions**
```bash
# Ver workflows
./scripts/check-github-actions.sh

# URL directa
# https://github.com/desarrolloIngenios/authentic-platform/actions
```

#### 🎯 **ArgoCD**
```bash
# Apps DEV (auto-sync habilitado)
kubectl get applications -n argocd | grep dev

# Apps PROD (sync manual)
kubectl get applications -n argocd | grep -v dev

# URL ArgoCD
# https://argo.authenticfarma.com/
```

#### 🔍 **Verificar Deployments**
```bash
# DEV Historia Clínica
kubectl get pods -n yosoy-historia-clinica-dev

# DEV Candidatos  
kubectl get pods -n authenticfarma-dev

# Logs en tiempo real
kubectl logs -f deployment/APP_NAME -n NAMESPACE
```

### 📋 **Scripts Útiles Creados:**

1. **`./scripts/migrate-to-new-strategy.sh`** - Migración CI/CD (ya ejecutado)
2. **`./scripts/check-github-actions.sh`** - Verificar workflows
3. **`./scripts/sync-candidatos.sh`** - Sincronizar candidatos

### 🎯 **Casos de Uso Típicos:**

#### 🔧 **Desarrollo Normal:**
```bash
# El desarrollador hace cambios
git checkout dev
# ... hacer cambios ...
git add apps/yosoy/historia-clinica/
git commit -m "fix: corregir validación de formularios"
git push origin dev

# ¡Automáticamente despliega en DEV! 🚀
```

#### 🚀 **Deploy a Producción:**
```bash
# GitHub Actions creará automáticamente un PR
# Ir a: https://github.com/desarrolloIngenios/authentic-platform/pulls
# Review → Approve → Merge

# Luego sync manual en ArgoCD PROD:
# https://argo.authenticfarma.com/applications/authentic-platform-prod
```

### ✅ **Beneficios Implementados:**

- 🔄 **Deploy automático DEV** en cada push a `dev`
- 🔒 **Control de calidad** con tests automáticos  
- 🛡️ **Seguridad PROD** con aprobaciones manuales
- 📊 **Visibilidad completa** con ArgoCD y GitHub Actions
- 🚀 **Rollback rápido** si es necesario
- 📈 **Historial de deployments** en ArgoCD

### 🎉 **¡Listo para usar!**

El desarrollador ahora puede hacer `git push origin dev` y ver sus cambios automáticamente desplegados en el entorno de desarrollo. ¡El flujo GitOps está completamente funcional!

---

**🔗 Enlaces Rápidos:**
- ArgoCD: https://argo.authenticfarma.com/
- GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions
- Historia Clínica PROD: https://hc.yo-soy.co
- Candidatos PROD: https://candidatos.authenticfarma.com