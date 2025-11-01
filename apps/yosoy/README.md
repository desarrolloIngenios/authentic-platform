# YoSoy - Plataforma Médica Integral

## 🏥 Descripción
YoSoy es una plataforma médica integral que incluye múltiples aplicaciones especializadas para la gestión completa de servicios de salud.

## 📋 Aplicaciones

### 🩺 Historia Clínica
**Ubicación**: `./historia-clinica/`
**Estado**: ✅ Activa en producción
**URL**: http://35.239.195.25

**Funcionalidades**:
- 📋 Registro completo de historias clínicas
- 💊 Fórmulas médicas con generación de PDF
- 🔍 Búsqueda avanzada de pacientes
- 📊 Dashboard con estadísticas y predictores de salud
- 📅 Agenda con videoconsultas (Google Meet)
- 🔐 Seguridad HIPAA/GDPR compliant

**Tecnologías**:
- Frontend: React 18 + Tailwind CSS
- Backend: Node.js (planificado)
- Base de datos: PostgreSQL (Cloud SQL)
- Infraestructura: GKE + Cloud Storage
- Seguridad: AES-256 + JWT

### 🚀 Próximas Aplicaciones

#### 🩺 Telemedicina
- Consultas virtuales avanzadas
- Diagnóstico remoto
- Integración con dispositivos IoT

#### 💊 Farmacia
- Gestión de inventario
- Dispensación automatizada
- Control de recetas

#### 🧪 Laboratorio
- Gestión de muestras
- Resultados en línea
- Integración con equipos

#### 👥 Gestión de Personal
- Horarios médicos
- Asignación de citas
- Evaluación de desempeño

## 🏗️ Estructura del Proyecto

```
yosoy/
├── historia-clinica/          # Aplicación principal
│   ├── src/                  # Código fuente
│   ├── k8s/                  # Manifiestos Kubernetes
│   ├── Dockerfile.fixed      # Container Docker
│   └── README.md            # Documentación específica
├── telemedicina/            # [Próximamente]
├── farmacia/                # [Próximamente]
├── laboratorio/             # [Próximamente]
└── personal/                # [Próximamente]
```

## 🚀 Despliegue

### Producción Actual
- **Cluster**: multi-platform-cluster
- **Namespace**: historia-clinicas
- **Proyecto GCP**: authentic-prod-464216
- **IP Externa**: 35.239.195.25

### Comandos de Despliegue
```bash
# Aplicar manifiestos
kubectl apply -f historia-clinica/k8s/

# Verificar estado
kubectl get pods -n historia-clinicas

# Ver logs
kubectl logs -f deployment/historia-clinicas-frontend -n historia-clinicas
```

## 🔐 Seguridad
- Encriptación AES-256 para datos sensibles
- Autenticación JWT con sesiones seguras
- Cumplimiento HIPAA/GDPR
- Auditoría completa de acciones
- Workload Identity para acceso a GCP

## 📊 Monitoreo
- Métricas de aplicación
- Logs centralizados
- Alertas automáticas
- Dashboard de salud

## 🛠️ Desarrollo

### Requisitos
- Docker 20+
- Kubernetes 1.25+
- Node.js 18+
- React 18+

### Variables de Entorno
```bash
ENVIRONMENT=production
DOMAIN=hc.yo-soy.co
GCP_PROJECT=authentic-prod-464216
```

## 📞 Soporte
Para soporte técnico o reportar problemas, contactar al equipo de desarrollo.

---
**Versión**: 1.0  
**Última actualización**: Noviembre 2025  
**Estado**: Producción activa
