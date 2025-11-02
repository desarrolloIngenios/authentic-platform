# 🏗️ Authentic Platform - Arquitectura GitOps

Esta es la plataforma centralizada GitOps para gestionar todas las aplicaciones de Authentic con ArgoCD.

## 🏢 Plataformas

### 1. **AuthenticFarma** 🏥
- **candidatos** - Sistema de gestión de RRHH y candidatos
- **inventario** - Sistema de gestión de inventario farmacéutico
- **ventas** - Plataforma de ventas y CRM

### 2. **YoSoy** 👤
- **historia-clinica** - Sistema de historias clínicas médicas
- **telemedicina** - Plataforma de consultas virtuales
- **laboratorio** - Gestión de resultados de laboratorio

### 3. **IsYours** 🏠
- **inmobiliaria** - Plataforma inmobiliaria
- **propiedades** - Gestión de propiedades
- **clientes** - CRM inmobiliario

## 📁 Estructura GitOps

```
authentic-platform/
├── platforms/                    # Configuraciones principales de plataformas
│   ├── authenticfarma/           # AuthenticFarma platform config
│   ├── yosoy/                    # YoSoy platform config  
│   └── isyours/                  # IsYours platform config
├── apps/                         # Código fuente de aplicaciones
│   ├── authenticfarma/
│   │   ├── candidatos/           # ✅ Ya configurado
│   │   ├── inventario/
│   │   └── ventas/
│   ├── yosoy/
│   │   ├── historia-clinica/     # ✅ Ya configurado con ArgoCD
│   │   ├── telemedicina/
│   │   └── laboratorio/
│   └── isyours/
│       ├── inmobiliaria/
│       ├── propiedades/
│       └── clientes/
├── infra/                        # Infraestructura compartida
│   ├── argocd/                   # ✅ ArgoCD configurado con SSL
│   ├── cert-manager/             # Gestión de certificados SSL
│   ├── ingress-nginx/            # Ingress controller
│   ├── monitoring/               # Prometheus + Grafana
│   └── shared/                   # Recursos compartidos
└── environments/                 # Configuraciones por ambiente
    ├── development/
    ├── staging/
    └── production/               # ✅ Actual environment
```

## 🎯 Estado Actual

### ✅ Configurado
- **ArgoCD**: https://argo.authenticfarma.com (SSL habilitado)
- **YoSoy Historia Clínica**: Gestionado por ArgoCD
- **AuthenticFarma Candidatos**: Código en repositorio

### ⏳ Pendiente
- Configurar aplicaciones adicionales en ArgoCD
- Estructura de environments (dev/staging/prod)
- Monitoring y observabilidad
- CI/CD pipelines automáticos

## 🚀 Deployment con ArgoCD

Cada aplicación se despliega automáticamente cuando se detectan cambios en:
- Código fuente (`/apps/`)
- Manifiestos K8s (`/platforms/`)
- Configuración de infraestructura (`/infra/`)

## 📊 GitOps Workflow

1. **Desarrollo** → Push código a `/apps/[platform]/[app]/`
2. **Build** → CI/CD construye imagen Docker
3. **Deploy** → ArgoCD detecta cambios y despliega
4. **Monitor** → Observabilidad en dashboard centralizado

## 🔧 Herramientas

- **GitOps**: ArgoCD
- **Containers**: Docker + Kubernetes
- **SSL**: Google Managed Certificates
- **DNS**: Cloud DNS
- **Monitoring**: Prometheus + Grafana (próximamente)
- **CI/CD**: GitHub Actions (próximamente)

---

**🏢 Authentic Platform** - Arquitectura multi-tenant GitOps  
**📅 Actualizado**: Noviembre 1, 2025