# 🩺 Historia Clínica - Sistema Médico

## 🎯 Descripción
Sistema integral para la gestión de historias clínicas, fórmulas médicas y atención de pacientes con funcionalidades avanzadas de análisis y predicción de salud.

## ✨ Funcionalidades Principales

### 📋 Gestión de Historia Clínica
- **Formulario completo** con todos los campos médicos
- **Cálculo automático** de edad
- **Almacenamiento seguro** con encriptación AES-256
- **Generación de PDF** para reportes

### 💊 Fórmulas Médicas
- **Prescripción digital** de medicamentos
- **Generación automática de PDF** para descarga
- **Control de dosis** e indicaciones
- **Historial de prescripciones** por paciente

### 🔍 Búsqueda de Pacientes
- **Búsqueda inteligente** por nombre
- **Visualización completa** de historial médico
- **Acceso rápido** a historias clínicas
- **Vista unificada** de fórmulas médicas

### 📊 Dashboard Analítico
- **Estadísticas en tiempo real** de la práctica médica
- **Diagnósticos más frecuentes** (top 5)
- **Medicamentos más recetados** (top 5)
- **Predictores básicos de salud** basados en datos
- **Análisis demográfico** (edad promedio)

### 📅 Agenda Digital
- **Citas presenciales** y virtuales
- **Videoconsultas con Google Meet** integradas
- **Generación automática** de enlaces de reunión
- **Gestión de horarios** y motivos

## 🏗️ Arquitectura Técnica

### Frontend
- **React 18** con hooks modernos
- **Tailwind CSS** para estilos
- **Lucide React** para iconografía
- **jsPDF** para generación de documentos
- **html2canvas** para capturas

### Seguridad
- **Encriptación AES-256** para datos sensibles
- **Autenticación JWT** con sesiones seguras
- **Hashing PBKDF2** para contraseñas
- **Auditoría completa** de acciones
- **Cumplimiento HIPAA/GDPR**

### Infraestructura
- **Google Kubernetes Engine** (GKE)
- **Cloud SQL PostgreSQL** (configurado)
- **Container Registry** para imágenes
- **Load Balancer** con SSL automático
- **Secret Manager** para credenciales

## 🚀 Despliegue

### URLs de Acceso
- **Producción**: http://35.239.195.25
- **Dominio**: hc.yo-soy.co (configurar DNS)

### Credenciales de Acceso
```
Admin:
Usuario: admin
Contraseña: admin123

Doctor:
Usuario: doctor
Contraseña: doctor123
```

### Información de Despliegue
- **Namespace**: historia-clinicas
- **Cluster**: multi-platform-cluster
- **Proyecto GCP**: authentic-prod-464216
- **Imagen**: gcr.io/authentic-prod-464216/yosoy-historia-clinica:v5

## 📁 Estructura de Archivos

```
historia-clinica/
├── secure-medical-system.html     # Aplicación principal
├── historia-clinica-actualizada.html # Versión actualizada
├── Dockerfile.fixed               # Container optimizado
├── nginx.fixed.conf              # Configuración Nginx
├── package.json                  # Dependencias Node.js
├── k8s/                         # Manifiestos Kubernetes
│   ├── 00-namespace-config.yaml
│   ├── 01-frontend-deployment.yaml
│   ├── 02-cert-manager-issuer.yaml
│   ├── 03-ingress.yaml
│   ├── 04-secrets.yaml
│   └── docs/                    # Documentación adicional
├── components/                  # Componentes React
├── lib/                        # Utilidades
└── gcp-deployment/             # Scripts de despliegue GCP
```

## 🛠️ Comandos de Despliegue

### Construcción de Imagen
```bash
docker build --platform linux/amd64 -f Dockerfile.fixed -t gcr.io/authentic-prod-464216/yosoy-historia-clinica:v6 .
docker push gcr.io/authentic-prod-464216/yosoy-historia-clinica:v6
```

### Despliegue en Kubernetes
```bash
# Aplicar todos los manifiestos
kubectl apply -f k8s/

# Actualizar imagen
kubectl set image deployment/historia-clinicas-frontend \
  historia-clinicas-frontend=gcr.io/authentic-prod-464216/yosoy-historia-clinica:v6 \
  -n historia-clinicas

# Verificar estado
kubectl get pods -n historia-clinicas
kubectl logs -f deployment/historia-clinicas-frontend -n historia-clinicas
```

### Port Forward Local
```bash
kubectl port-forward -n historia-clinicas service/historia-clinicas-frontend 8080:8080
# Acceder a: http://localhost:8080
```

## 🔐 Configuración de Seguridad

### Secret Manager (GCP)
- **historia-clinicas-jwt-secret**: Clave JWT
- **historia-clinicas-encryption-key**: Clave encriptación
- **historia-clinicas-gemini-api-key**: API Gemini

### Workload Identity
- **Service Account**: historia-clinicas-sa@authentic-prod-464216.iam.gserviceaccount.com
- **KSA**: historia-clinicas-ksa

## 📊 Métricas y Monitoreo

### Estadísticas Disponibles
- Total de pacientes únicos
- Historias clínicas registradas
- Fórmulas médicas emitidas
- Diagnósticos más comunes
- Medicamentos frecuentes
- Edad promedio de pacientes

### Predictores de Salud
- Análisis de tendencias diagnósticas
- Patrones de prescripción
- Perfiles demográficos
- Indicadores de riesgo básicos

## 🔄 Próximas Mejoras

### Base de Datos Persistente
- [x] Cloud SQL PostgreSQL configurado
- [ ] Migración de localStorage a BD
- [ ] APIs REST completas
- [ ] Sincronización en tiempo real

### Funcionalidades Avanzadas
- [ ] IA para diagnóstico asistido
- [ ] Integración con laboratorios
- [ ] Reportes médicos automáticos
- [ ] Notificaciones push

### Integraciones
- [ ] Sistemas de laboratorio
- [ ] Farmacias externas
- [ ] Seguros médicos
- [ ] Dispositivos IoT

## 📞 Soporte

Para problemas técnicos o consultas:
- Revisar logs: `kubectl logs -f deployment/historia-clinicas-frontend -n historia-clinicas`
- Verificar pods: `kubectl get pods -n historia-clinicas`
- Acceso directo: http://35.239.195.25

---

**Versión**: v5  
**Última actualización**: Noviembre 1, 2025  
**Estado**: ✅ Producción activa  
**Mantenedor**: Equipo YoSoy
