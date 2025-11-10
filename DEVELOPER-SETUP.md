# 👩‍💻 Configuración del Desarrollador para Deploy Automático a DEV

## 🚀 **Configuración Inicial (Solo una vez)**

### **Paso 1: Clonar el repositorio**
```bash
git clone git@github.com:desarrolloIngenios/authentic-platform.git
cd authentic-platform
```

### **Paso 2: Configurar Git**
```bash
# Configurar usuario (si no está configurado)
git config user.name "Tu Nombre"
git config user.email "tu-email@authentic.com.co"

# Verificar configuración
git config --list | grep user
```

### **Paso 3: Verificar acceso al repositorio**
```bash
# Probar conexión SSH a GitHub
ssh -T git@github.com

# Debería mostrar: "Hi usuario! You've successfully authenticated..."
```

---

## 🔄 **Workflow Diario del Desarrollador**

### **✨ Deploy Automático a DEV - ¡Así de Simple!**

```bash
# 1. Asegurarse de estar en rama dev
git checkout dev
git pull origin dev

# 2. Hacer cambios en el código
# ... editar archivos ...

# 3. Commit y push (¡Deploy automático!)
git add .
git commit -m "feat: nueva funcionalidad"
git push origin dev  # 🚀 ¡DEPLOY AUTOMÁTICO A DEV!
```

### **🎯 ¡Eso es todo! No hay más configuración.**

---

## 📋 **Scripts Útiles Disponibles**

El desarrollador tiene acceso a estos scripts de monitoreo:

### **1. Verificar GitHub Actions**
```bash
./scripts/check-github-actions.sh
```

### **2. Sincronizar Candidatos**
```bash
./scripts/sync-candidatos.sh
```

### **3. Deploy a Producción**
```bash
./scripts/deploy-to-production.sh
```

### **4. Arreglar Secreto GCP (Si GitHub Actions falla)**
```bash
./scripts/fix-gcp-secret.sh
```

---

## 🔍 **Monitoreo de Deployments**

### **GitHub Actions (Automático)**
- **URL**: https://github.com/desarrolloIngenios/authentic-platform/actions
- **Se ejecuta**: Automáticamente en cada `git push origin dev`
- **Incluye**: Tests, Build, Push de imágenes Docker

### **ArgoCD DEV (Automático)**
- **URL**: https://argo.authenticfarma.com/
- **Aplicaciones DEV**: 
  - `authentic-platform-dev`
  - `yosoy-historia-clinica-dev`
  - `authenticfarma-dev`
- **Comportamiento**: Auto-sync habilitado ✅

### **Verificar Deploy Exitoso**
```bash
# Ver pods en DEV
kubectl get pods -n yosoy-historia-clinica-dev
kubectl get pods -n authenticfarma-dev

# Ver logs en tiempo real
kubectl logs -f deployment/NOMBRE-APP -n NAMESPACE-dev
```

---

## 🌐 **URLs de las Aplicaciones**

### **DEV (Deploy Automático)**
- Historia Clínica DEV: `hc-dev.yo-soy.co` *(pendiente configurar)*
- Candidatos DEV: `candidatos-dev.authenticfarma.com` *(pendiente configurar)*

### **PROD (Deploy Manual)**
- Historia Clínica: https://hc.yo-soy.co
- Candidatos: https://candidatos.authenticfarma.com

---

## 🚨 **Troubleshooting**

### **❌ ERROR: "Invalid JSON in GCP_SA_KEY secret"**

Si GitHub Actions falla con error de JSON inválido en `GCP_SA_KEY`:

#### **Solución: Actualizar secreto con JSON válido**

1. **Generar nueva clave JSON en Google Cloud**:
   ```bash
   # En terminal local con gcloud configurado
   gcloud iam service-accounts keys create github-sa-key-new.json \
     --iam-account=github-actions-sa@authentic-prod-464216.iam.gserviceaccount.com
   ```

2. **Copiar contenido del JSON**:
   ```bash
   # Ver el contenido del archivo
   cat github-sa-key-new.json
   
   # Copiar TODO el contenido (desde { hasta })
   ```

3. **Actualizar secreto en GitHub**:
   - Ve a: https://github.com/desarrolloIngenios/authentic-platform/settings/secrets/actions
   - Buscar secreto `GCP_SA_KEY`
   - Click en "Update" 
   - Pegar el JSON completo (incluye las llaves `{ ... }`)
   - Save

4. **Probar nuevamente**:
   ```bash
   git push origin dev  # Activar workflow
   ./scripts/check-github-actions.sh  # Verificar estado
   ```

---

### **Si otros deploys fallan:**

1. **Verificar GitHub Actions**:
   ```bash
   ./scripts/check-github-actions.sh
   ```

2. **Ver logs detallados**:
   - Ir a: https://github.com/desarrolloIngenios/authentic-platform/actions
   - Click en el workflow fallido
   - Revisar logs de cada step

3. **Verificar ArgoCD**:
   - Ir a: https://argo.authenticfarma.com/
   - Buscar aplicación con estado "Degraded" o "OutOfSync"
   - Revisar detalles del error

### **Comandos de emergencia:**
```bash
# Forzar sync en ArgoCD (si tienes acceso kubectl)
kubectl patch application NOMBRE-APP -n argocd --type merge -p '{"metadata":{"annotations":{"argocd.argoproj.io/refresh":"hard"}}}'

# Ver estado de todas las aplicaciones
kubectl get applications -n argocd

# Rollback si es necesario (volver a commit anterior)
git log --oneline  # Ver commits
git reset --hard COMMIT-HASH
git push origin dev --force-with-lease
```

---

## ✅ **Resumen para el Desarrollador**

### **Configuración: ✅ CERO - Ya está todo listo**
### **Deploy a DEV: ✅ Automático**
### **Monitoreo: ✅ GitHub Actions + ArgoCD**
### **Deploy a PROD: ✅ Script automatizado**

### **🎯 Flujo Diario:**
1. `git checkout dev && git pull origin dev`
2. Hacer cambios
3. `git add . && git commit -m "mensaje"`
4. `git push origin dev` ← **¡DEPLOY AUTOMÁTICO!**
5. Verificar en ArgoCD que todo funcione
6. ¡Listo! 🎉

---

## 🔗 **Enlaces Importantes**

- **GitHub Actions**: https://github.com/desarrolloIngenios/authentic-platform/actions
- **ArgoCD**: https://argo.authenticfarma.com/
- **Repositorio**: https://github.com/desarrolloIngenios/authentic-platform
- **Documentación Completa**: `DEVELOPER-WORKFLOW.md`

---

**💡 Tip**: Guarda este archivo como referencia. ¡El deploy automático ya está funcionando!