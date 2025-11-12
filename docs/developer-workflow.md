# 🚀 Guía del Desarrollador - Despliegue Automático

## 📋 Flujo de Desarrollo para Candidatos

### ✅ **Proceso Simplificado:**

1. **Desarrollar** → Hacer cambios en `apps/authenticfarma/candidatos/`
2. **Commit** → `git add . && git commit -m "feat: nuevo cambio"`  
3. **Push a dev** → `git push origin dev`
4. **¡Automático!** → CI/CD + ArgoCD despliegan la nueva imagen

---

## 🔄 **Flujo Técnico Automatizado:**

```mermaid
Developer → dev branch → GitHub Actions → Artifact Registry → ArgoCD → Kubernetes
```

### **1. Developer Push:**
```bash
git checkout dev
# ... hacer cambios en el código ...
git add .
git commit -m "feat: nueva funcionalidad X"
git push origin dev
```

### **2. CI/CD Automático:**
- ✅ Detecta cambios en `apps/authenticfarma/candidatos/`
- ✅ Construye imagen: `us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo/authentic-candidatos:latest`
- ✅ Sube al Artifact Registry automáticamente

### **3. ArgoCD Automático:**
- ✅ Detecta nueva imagen en registry
- ✅ Actualiza deployment en Kubernetes
- ✅ Hace rollout de nuevos pods
- ✅ Aplicación actualizada en: https://candidatos.authenticfarma.com

---

## ⏱️ **Tiempos Esperados:**
- **Build CI/CD**: ~3-5 minutos
- **Deployment ArgoCD**: ~2-3 minutos  
- **Total**: ~5-8 minutos desde push hasta producción

---

## 🛠️ **Comandos Útiles para Desarrolladores:**

### Verificar estado del deployment:
```bash
kubectl get pods -n authenticfarma-candidatos -l app=authenticfarma-candidatos
```

### Ver logs de la aplicación:
```bash
kubectl logs -f deployment/authenticfarma-candidatos -n authenticfarma-candidatos -c authenticfarma-candidatos
```

### Verificar imagen actual en producción:
```bash
kubectl get deployment authenticfarma-candidatos -n authenticfarma-candidatos -o jsonpath='{.spec.template.spec.containers[1].image}'
```

### Monitorear CI/CD desde terminal:
```bash
# Usar nuestro script personalizado
./scripts/monitor-github-workflow.sh
```

---

## 🎯 **Ejemplo de Desarrollo:**

### Escenario: Actualizar validación de formulario

```bash
# 1. Checkout a dev
git checkout dev
git pull

# 2. Hacer cambios
echo "// Nueva validación" >> apps/authenticfarma/candidatos/app/Http/Controllers/CandidateController.php

# 3. Commit descriptivo  
git add .
git commit -m "feat: Add enhanced form validation for candidate registration

- Improve email validation regex
- Add phone number format validation  
- Enhance error messages for better UX"

# 4. Push y esperar
git push origin dev

# 5. Monitorear (opcional)
./scripts/monitor-github-workflow.sh
```

### Resultado esperado:
- ✅ Nueva imagen construida automáticamente
- ✅ Pods actualizados en ~5-8 minutos
- ✅ Cambios visibles en https://candidatos.authenticfarma.com

---

## 🔍 **Validación Post-Despliegue:**

### Verificación rápida:
```bash
# Ejecutar nuestro script de verificación
./scripts/final-verification.sh
```

### Verificación manual:
1. **Pods**: Verificar que 2/2 pods estén `Running`
2. **Salud**: Confirmar que `/healthz` responde OK  
3. **Funcionalidad**: Probar la nueva característica en el navegador

---

## ⚠️ **Consideraciones Importantes:**

### ✅ **DO:**
- Usar commits descriptivos y claros
- Probar cambios localmente antes de push
- Monitorear deployment después de push importante
- Usar rama `dev` para todos los cambios

### ❌ **DON'T:**  
- No pushear directamente a `main`
- No hacer cambios manuales en Kubernetes
- No modificar imágenes directamente en registry

---

## 🆘 **Solución de Problemas:**

### Si el deployment falla:
```bash
# Ver estado de ArgoCD
kubectl get application authenticfarma-candidatos -n argocd

# Ver logs de pods problemáticos  
kubectl describe pod <pod-name> -n authenticfarma-candidatos

# Rollback rápido si es necesario
kubectl rollout undo deployment/authenticfarma-candidatos -n authenticfarma-candidatos
```

---

**🎉 ¡El sistema está listo para desarrollo continuo!**

*Última actualización: $(date '+%Y-%m-%d %H:%M:%S')*