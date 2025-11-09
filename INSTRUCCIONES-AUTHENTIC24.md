# 🚀 INSTRUCCIONES PARA AUTHENTIC-24

## 👋 ¡Bienvenido authentic-24!

Eres ahora el **desarrollador principal** con permisos completos para deployar todas las aplicaciones de la plataforma authentic-platform.

---

## ⚡ CONFIGURACIÓN RÁPIDA (5 minutos)

### 1. 📥 Clonar el repositorio
```bash
git clone https://github.com/desarrolloIngenios/authentic-platform.git
cd authentic-platform
```

### 2. 🔧 Configurar tu identidad Git
```bash
git config --global user.name "authentic-24"
git config --global user.email "stack.dev@authentic.com.co"
```

### 3. 🌿 Cambiar a branch de desarrollo
```bash
git checkout dev
git pull origin dev
```

### 4. ✅ Verificar configuración
```bash
git config --global user.name    # Debe mostrar: authentic-24
git config --global user.email   # Debe mostrar: stack.dev@authentic.com.co
```

---

## 🚀 ¡YA PUEDES EMPEZAR A TRABAJAR!

### 🎯 Workflow básico:
```bash
# 1. Asegúrate de estar en dev y actualizado
git checkout dev
git pull origin dev

# 2. Modifica cualquier aplicación
# Ejemplo: AuthenticFarma
vim apps/authenticfarma/candidatos/app/Http/Controllers/SomeController.php

# Ejemplo: YoSoy Historia Clínica
vim apps/yosoy/historia-clinica/backend/main.py

# Ejemplo: IsYours
vim apps/isyours/src/components/Dashboard.tsx

# Ejemplo: Moodle
vim apps/moodle-elearning/config.php

# 3. Commit y push
git add apps/[app-que-modificaste]/
git commit -m "feat: tu nueva funcionalidad"
git push origin dev

# 4. ¡El sistema inteligente hace el resto automáticamente!
# → Detecta qué apps cambiaron
# → Construye SOLO las apps modificadas
# → Deploya automáticamente a desarrollo
```

---

## 🏗️ APLICACIONES BAJO TU CONTROL

### 🧬 AuthenticFarma Candidatos (Laravel)
- **Código**: `apps/authenticfarma/candidatos/`
- **URL Dev**: https://candidatos-dev.authenticfarma.com
- **URL Prod**: https://candidatos.authenticfarma.com
- **Admin**: admin / admin123

### 🏥 YoSoy Historia Clínica (FastAPI)
- **Código**: `apps/yosoy/historia-clinica/backend/`
- **URL Dev**: https://hc-dev.yo-soy.co
- **URL Prod**: https://hc.yo-soy.co
- **Admin**: admin / admin123

### 🌟 IsYours Platform (React)
- **Código**: `apps/isyours/`
- **URL Dev**: https://isyours-dev.com
- **URL Prod**: https://isyours.com

### 📚 Moodle E-Learning (Moodle)
- **Código**: `apps/moodle-elearning/`
- **URL Dev**: https://moodle-dev.authentic.com.co
- **URL Prod**: https://moodle.authentic.com.co

---

## 🧠 SISTEMA CI/CD INTELIGENTE

### ✨ Lo que hace automáticamente:
1. **Detecta** qué aplicaciones modificaste
2. **Construye** SOLO las aplicaciones que cambiaron
3. **Optimiza** tiempo y recursos (50-100% más rápido)
4. **Deploya** automáticamente a desarrollo
5. **Crea PR** automático para producción

### 🎯 Ejemplos de optimización:

#### Modificas SOLO AuthenticFarma:
```bash
git add apps/authenticfarma/
git push origin dev
# → Solo construye AuthenticFarma (75% más rápido)
```

#### Modificas YoSoy + IsYours:
```bash
git add apps/yosoy/ apps/isyours/
git push origin dev
# → Solo construye YoSoy + IsYours (50% más rápido)
```

#### Solo cambias documentación:
```bash
git add README.md docs/
git push origin dev
# → NO construye ninguna app (100% optimización)
```

---

## 🔄 DEPLOYMENT A PRODUCCIÓN

### Automático (Recomendado):
1. Haces push a `dev`
2. El sistema crea automáticamente un PR de `dev` → `main`
3. Revisas y apruebas el PR en GitHub
4. Al hacer merge, se construyen las imágenes de producción
5. Sync manual en ArgoCD para deploy final

### Manual (Si necesitas control total):
```bash
git checkout main
git merge dev
git push origin main
# → Triggers build de producción automáticamente
```

---

## 📊 MONITOREO

### 🔗 GitHub Actions Dashboard:
```
https://github.com/desarrolloIngenios/authentic-platform/actions
```

### 🖥️ Comandos útiles:
```bash
# Ver workflows ejecutándose
gh run list --limit 5

# Monitor en tiempo real
gh run watch

# Ver logs detallados
gh run view --log
```

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### ❓ ¿No tienes acceso al repositorio?
Contacta al admin para:
- Agregar tu usuario GitHub al repositorio
- Permisos de push a `main` y `dev`
- Acceso a GitHub Actions

### ❓ ¿Workflow falla?
1. Revisa los logs en GitHub Actions
2. Verifica que tus cambios no rompan la sintaxis
3. Asegúrate de estar en el branch correcto

### ❓ ¿Necesitas acceso a producción?
Necesitarás:
- Acceso a Google Cloud Platform
- Permisos en ArgoCD
- Credenciales de las aplicaciones

---

## 📚 DOCUMENTACIÓN COMPLETA

- **Workflow inteligente**: `docs/developers/intelligent-cicd-workflow.md`
- **Credenciales**: `docs/AUTHENTIC24-CREDENTIALS.md`
- **Resumen general**: `docs/FINAL-INTELLIGENT-CICD-SUMMARY.md`

---

## 🎉 ¡ESTÁS LISTO!

### ✅ Lo que YA tienes configurado:
- 🧠 **Sistema CI/CD inteligente**: 100% funcional
- ⚡ **Optimización automática**: 50-100% más rápido
- 🔄 **GitOps completo**: ArgoCD + GitHub Actions
- 👤 **Usuario authentic-24**: Configurado en workflow
- 🚀 **Permisos completos**: Para todas las aplicaciones

### 🎯 Tu primer commit de prueba:
```bash
# Haz un cambio pequeño para probar
echo "# Prueba de authentic-24" >> apps/authenticfarma/PRUEBA.md
git add apps/authenticfarma/PRUEBA.md
git commit -m "test: primer commit de authentic-24"
git push origin dev

# Verifica en: https://github.com/desarrolloIngenios/authentic-platform/actions
# → Debe ejecutar SOLO build-authenticfarma
```

---

## 📞 CONTACTO

**¿Preguntas o problemas?**
- Crea un issue en GitHub
- Revisa la documentación en `docs/`
- Consulta los logs de GitHub Actions

---

# 🚀 ¡A DESARROLLAR Y DEPLOYAR! 

**authentic-24, la plataforma está en tus manos. El sistema inteligente se encarga del resto.** ✨

**¡Feliz coding! 🎊**