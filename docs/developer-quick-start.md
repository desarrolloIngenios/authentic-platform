# 🚀 QUICK START - CI/CD para Desarrolladores

## ⚡ **TL;DR - Lo que cambió**

**ANTES:** Hacer deploy era complicado y lento  
**AHORA:** `git push` y listo! 🎉

## 🎯 **NUEVO WORKFLOW DIARIO**

### 1. **DESARROLLO (igual que antes)**
```bash
git checkout dev
git pull origin dev
# ... hacer cambios ...
```

### 2. **DEPLOY (súper simple ahora)**
```bash
git add .
git commit -m "feat: nueva funcionalidad"
git push origin dev
```

### 3. **¡YA ESTÁ! 🪄**
- ✅ Tests automáticos
- ✅ Build automático  
- ✅ Deploy automático
- ✅ Live en 5-10 minutos

## 📱 **CÓMO VERIFICAR QUE FUNCIONÓ**

### **Ver estado del deploy:**
```bash
# Estado general
kubectl get applications -n argocd

# Debería mostrar: 
# authenticfarma-candidatos   Synced    Healthy
```

### **Ver tu aplicación corriendo:**
```bash
# Ver pods
kubectl get pods -n authenticfarma-candidatos

# Ver logs en tiempo real
kubectl logs -f deployment/authenticfarma-candidatos -n authenticfarma-candidatos -c app
```

### **Verificar en el browser:**
```
https://candidatos.authenticfarma.com
```

## 🚨 **¿QUÉ HACER SI ALGO SALE MAL?**

### **Si los tests fallan:**
```bash
# Arreglar tests localmente ANTES de push
cd apps/authenticfarma/candidatos
composer install
php artisan test
```

### **Si el deploy falla:**
```bash
# Ver qué pasó
kubectl describe application authenticfarma-candidatos -n argocd

# O pedir ayuda al equipo DevOps
```

### **Rollback rápido:**
```bash
# Hacer revert del commit problemático
git revert HEAD
git push origin dev
# ¡ArgoCD automáticamente hace rollback!
```

## 📋 **BRANCHES Y AMBIENTES**

| **Branch** | **Deploy a** | **Cuándo usar** |
|------------|-------------|----------------|
| `dev` | Desarrollo | Trabajo diario |
| `main` | Producción | Cuando esté listo para users |
| `feature/*` | No deploy automático | Features grandes |

## ✅ **CHECKLIST RÁPIDO**

Antes de cada push, verificar:
- [ ] ✅ Tests pasan localmente
- [ ] ✅ Commit message es descriptivo
- [ ] ✅ No hay secrets/passwords en el código
- [ ] ✅ Feature está completa

## 🆘 **CONTACTS DE EMERGENCIA**

- **DevOps Team:** [slack-channel] o [email]
- **Platform Issues:** [emergency-contact]
- **Documentation:** `/docs/technical-team-cicd-automation.md`

## 🎉 **¡ENJOY!**

Ya no tienes que preocuparte por deployments complicados. Solo enfócate en escribir código awesome, y la plataforma se encarga del resto!

---

**💡 Pro tip:** El primer deploy puede tomar un poco más mientras se build la imagen por primera vez. Los siguientes serán súper rápidos!

---

*Happy coding! 🚀*