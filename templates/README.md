# 📋 Templates Directory

Este directorio contiene plantillas base reutilizables para nuevos proyectos y servicios en la plataforma **authentic-platform**.

## 🗂️ Estructura

### 🐳 **docker/** - Plantillas de Contenedores
```bash
docker/
└── Dockerfile.php         # Template base para aplicaciones PHP/Laravel 8.2
```

### ☸️ **kubernetes/** - Plantillas de Kubernetes  
```bash
kubernetes/
├── configmap.yaml         # ConfigMap genérico con placeholders
├── deployment.yaml        # Deployment base con 2 réplicas
├── ingress.yaml          # Ingress con soporte GCE/nginx
└── service.yaml          # Service básico puerto 80→9000
```

## 🔧 Uso de Templates

### Docker Templates
```bash
# Copiar template PHP para nuevo proyecto
cp templates/docker/Dockerfile.php apps/nuevo-proyecto/

# Personalizar según necesidades del proyecto
```

### Kubernetes Templates
```bash
# Copiar templates base para nueva aplicación
cp -r templates/kubernetes/* platforms/nueva-app/k8s/

# Reemplazar placeholders:
# - APP_NAME → nombre de la aplicación
# - DOMAIN_PLACEHOLDER → dominio real
# - APP_NAME-placeholder → labels reales
```

## 🏷️ Placeholders a Reemplazar

| Placeholder | Descripción | Ejemplo |
|-------------|-------------|---------|
| `APP_NAME` | Nombre de la aplicación | `candidatos`, `historia-clinica` |
| `DOMAIN_PLACEHOLDER` | Dominio de la aplicación | `candidatos.authenticfarma.com` |
| `APP_NAME-placeholder` | Labels y selectores | `candidatos-app` |

## 📝 Template Kubernetes Deployment

El template incluye configuración base para:
- ✅ **2 réplicas** por defecto
- ✅ **Ingress** con controller GCE/nginx
- ✅ **Service** puerto 80→9000 
- ✅ **ConfigMap** con variables de entorno básicas
- ✅ **Labels** estándar para selección

## 🚀 Proceso de Creación Nueva App

1. **Copiar templates**: `cp -r templates/kubernetes/ platforms/nueva-app/k8s/`
2. **Reemplazar placeholders**: Usar editor para cambiar variables
3. **Personalizar configuración**: Ajustar recursos, puertos, variables
4. **Crear ArgoCD application**: Basado en estructura `platforms/`

## 📚 Templates Disponibles

### Dockerfile.php Features:
- **PHP 8.2-FPM** base
- **Composer 2** preinstalado
- **Extensions**: pdo_mysql, mbstring, gd, zip, bcmath
- **Optimizado** para producción (--no-dev, --optimize-autoloader)
- **Permisos** correctos para Laravel storage/cache
- **Compatible** con nginx ingress

---
*Última actualización: Noviembre 2024*
*Templates consolidados desde k8s/base/ y organizados por categoría*