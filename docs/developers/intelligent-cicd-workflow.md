# 🧠 Sistema CI/CD Inteligente

## 📋 Resumen General

Este documento describe el nuevo sistema CI/CD inteligente que **detecta automáticamente** qué aplicaciones han cambiado y **construye solo las necesarias**, optimizando significativamente los tiempos de build y recursos.

## 🚀 Características Principales

### ✨ Detección Inteligente de Cambios
- **Automática**: Compara cambios entre commits para identificar qué apps fueron modificadas
- **Granular**: Detecta cambios por aplicación individual
- **Eficiente**: Solo ejecuta builds para apps que realmente cambiaron

### 🏗️ Builds Condicionales
- **AuthenticFarma**: Laravel candidatos app
- **YoSoy**: Historia Clínica (FastAPI + SQLite)  
- **IsYours**: Aplicación IsYours
- **Moodle E-Learning**: Sistema educativo Moodle

### 🌊 Workflows Diferenciados

#### 🔧 Desarrollo (Branch: `dev`)
```yaml
Trigger: Push a dev
├── 🔍 detect-changes
├── 🏗️ build-authenticfarma (condicional)
├── 🏗️ build-yosoy (condicional)  
├── 🏗️ build-isyours (condicional)
├── 🏗️ build-moodle (condicional)
└── 🚀 deploy-dev (si hay builds exitosos)
```

#### 🛡️ Producción (Branch: `main`)
```yaml
Trigger: Push a main
├── 🔍 detect-changes-prod
├── 🏗️ build-prod-authenticfarma (condicional)
├── 🏗️ build-prod-yosoy (condicional)
├── 🏗️ build-prod-isyours (condicional) 
├── 🏗️ build-prod-moodle (condicional)
└── 🛡️ notify-prod-ready (notificación manual)
```

## 🎯 Cómo Funciona la Detección

### Desarrollo (dev branch)
```bash
# Compara dev con origin/dev para detectar cambios
git diff origin/dev HEAD -- apps/authenticfarma/
git diff origin/dev HEAD -- apps/yosoy/
git diff origin/dev HEAD -- apps/isyours/
git diff origin/dev HEAD -- apps/moodle-elearning/
```

### Producción (main branch)  
```bash
# Compara commit actual con el anterior
git diff HEAD~1 HEAD -- apps/authenticfarma/
git diff HEAD~1 HEAD -- apps/yosoy/
git diff HEAD~1 HEAD -- apps/isyours/
git diff HEAD~1 HEAD -- apps/moodle-elearning/
```

## 📊 Outputs de Detección

Cada job de detección de cambios genera outputs booleanos:

```yaml
outputs:
  authenticfarma: "true" | "false"
  yosoy: "true" | "false" 
  isyours: "true" | "false"
  moodle: "true" | "false"
```

## 🏗️ Jobs de Build Condicionales

Cada aplicación tiene su job de build independiente que:

1. **Solo se ejecuta** si `needs.detect-changes.outputs.[app] == 'true'`
2. **Usa las credenciales GCP** configuradas en secrets
3. **Construye y pushea** la imagen Docker correspondiente
4. **Tagea apropiadamente** según el entorno:
   - Dev: `dev-{commit-hash}` + `dev-latest`
   - Prod: `v{YYYY.MM.DD}-{commit-hash}` + `latest`

## 🔄 Lógica de Deployment

### Desarrollo
El deployment a dev ocurre si:
- ✅ La detección de cambios fue exitosa
- ✅ Al menos un build fue exitoso, O todos fueron skipped (sin cambios)

### Producción  
La notificación de prod ready ocurre si:
- ✅ La detección de cambios fue exitosa
- ✅ Al menos un build fue exitoso, O todos fueron skipped (sin cambios)

## 🎛️ Configuración de Imágenes

### AuthenticFarma (Laravel)
```yaml
Dockerfile: apps/authenticfarma/candidatos/dockerfile
Context: apps/authenticfarma/candidatos  
Registry: gcr.io/{PROJECT_ID}/authenticfarma-candidatos
```

### YoSoy (Historia Clínica)
```yaml
Dockerfile: apps/yosoy/historia-clinica/backend/Dockerfile
Context: apps/yosoy/historia-clinica/backend
Registry: gcr.io/{PROJECT_ID}/yosoy-hc-backend
```

### IsYours
```yaml
Dockerfile: apps/isyours/Dockerfile
Context: apps/isyours
Registry: gcr.io/{PROJECT_ID}/isyours
```

### Moodle E-Learning
```yaml
Dockerfile: apps/moodle-elearning/Dockerfile  
Context: apps/moodle-elearning
Registry: gcr.io/{PROJECT_ID}/moodle-elearning
```

## 🚀 Ejemplos de Uso

### Escenario 1: Solo cambios en AuthenticFarma
```bash
# Push cambios solo en apps/authenticfarma/
git add apps/authenticfarma/candidatos/app/Http/Controllers/
git commit -m "feat: optimizar GoogleController"
git push origin dev

# Resultado:
# ✅ detect-changes: authenticfarma=true, otros=false
# ✅ build-authenticfarma: se ejecuta
# ⏭️ build-yosoy, build-isyours, build-moodle: skipped
# ✅ deploy-dev: se ejecuta (hay builds exitosos)
```

### Escenario 2: Cambios en múltiples apps
```bash
# Push cambios en varias apps
git add apps/authenticfarma/ apps/yosoy/
git commit -m "feat: mejoras en AuthenticFarma y YoSoy" 
git push origin dev

# Resultado:
# ✅ detect-changes: authenticfarma=true, yosoy=true, otros=false
# ✅ build-authenticfarma: se ejecuta
# ✅ build-yosoy: se ejecuta  
# ⏭️ build-isyours, build-moodle: skipped
# ✅ deploy-dev: se ejecuta (hay builds exitosos)
```

### Escenario 3: Sin cambios en apps (solo docs/infra)
```bash
# Push cambios solo en documentación
git add docs/ README.md
git commit -m "docs: actualizar documentación"
git push origin dev

# Resultado:
# ✅ detect-changes: todos=false
# ⏭️ Todos los builds: skipped
# ✅ deploy-dev: se ejecuta (condición especial para skips)
```

## 🔧 Mantenimiento

### Agregar Nueva Aplicación
1. **Agregar detección** en `detect-changes`:
```yaml
if git diff --quiet origin/dev HEAD -- apps/nueva-app/; then
  echo "nueva-app=false" >> $GITHUB_OUTPUT
else
  echo "nueva-app=true" >> $GITHUB_OUTPUT  
fi
```

2. **Crear job de build**:
```yaml
build-nueva-app:
  needs: detect-changes
  if: needs.detect-changes.outputs.nueva-app == 'true'
  # ... pasos de build
```

3. **Actualizar dependencias** en deploy jobs

### Monitoreo y Debug
- **GitHub Actions**: Ver logs detallados de cada job
- **Outputs de detección**: Verificar qué apps fueron detectadas
- **Conditional execution**: Revisar qué jobs se ejecutaron vs skipped

## ✅ Ventajas del Sistema

1. **⚡ Más Rápido**: Solo builds necesarios
2. **💰 Más Económico**: Menos recursos GCP utilizados  
3. **🔍 Más Claro**: Fácil identificar qué cambió
4. **🛡️ Más Seguro**: Builds aislados por aplicación
5. **📈 Escalable**: Fácil agregar nuevas aplicaciones

## 🎯 Casos de Uso Optimizados

- ✅ **Desarrollo iterativo** en una sola app
- ✅ **Releases grandes** con múltiples apps
- ✅ **Hotfixes** urgentes en producción  
- ✅ **Cambios de infraestructura** sin rebuilds
- ✅ **Work in progress** con commits frecuentes

Este sistema inteligente garantiza que el CI/CD sea eficiente, rápido y escalable para el crecimiento futuro de la plataforma. 🚀