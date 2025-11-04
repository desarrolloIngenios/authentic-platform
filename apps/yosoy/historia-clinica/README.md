# YoSoy Historia Clínica - Sistema Médico para Mujeres

## Descripción
Sistema médico completo especializado en atención ginecológica y de medicina general para mujeres, desarrollado con Node.js, SQLite y desplegado en Google Kubernetes Engine.

## Funcionalidades Principales

### 🏥 Sistema Médico Completo
- **Dashboard médico** con navegación intuitiva
- **Calendario de citas** integrado
- **Gestión de pacientes** con datos completos
- **Historias clínicas** detalladas
- **Prescripciones médicas** digitales
- **Autenticación segura** con JWT

### �‍⚕️ Especialización en Salud Femenina
- Formularios especializados en ginecología
- Historia clínica integral para mujeres
- Seguimiento de salud reproductiva
- Gestión de embarazos y controles prenatales

## Arquitectura Técnica

### Backend
- **Tecnología**: Node.js con Express
- **Base de datos**: SQLite con persistencia
- **Autenticación**: JWT con bcrypt
- **API REST** completa para gestión médica

### Frontend
- **Tecnología**: HTML5, JavaScript, Tailwind CSS
- **Interfaz**: Dashboard responsivo y moderno
- **Componentes**: Calendario, formularios médicos, reportes

### Infraestructura
- **Plataforma**: Google Kubernetes Engine (GKE)
- **Dominio**: hc.yo-soy.co con SSL
- **Escalabilidad**: Pods auto-escalables
- **Persistencia**: Volúmenes persistentes para base de datos

## Acceso al Sistema

### URL de Producción
- **URL**: https://hc.yo-soy.co
- **Usuario**: admin
- **Contraseña**: 123456

### Funcionalidades Disponibles
1. **Dashboard Principal**: Vista general del sistema
2. **Gestión de Pacientes**: Registro y búsqueda de pacientes
3. **Historias Clínicas**: Creación y consulta de historiales médicos
4. **Calendario**: Programación de citas y consultas
5. **Prescripciones**: Generación de recetas médicas digitales

## Estructura del Proyecto

```
yosoy/historia-clinica/
├── server.js                 # Backend Node.js principal
├── index.html               # Frontend completo con dashboard
├── package.json             # Dependencias del proyecto
├── Dockerfile              # Imagen de contenedor
├── nginx.conf              # Configuración del servidor web
├── k8s/                    # Configuraciones de Kubernetes
│   ├── backend-configmap.yaml
│   ├── backend-deployment.yaml
│   ├── frontend-configmap.yaml
│   ├── frontend-deployment.yaml
│   ├── services.yaml
│   ├── pvc.yaml
│   └── ingress.yaml
└── README.md              # Documentación del proyecto
```

## Despliegue en Kubernetes

### Comandos de Despliegue
```bash
# Crear namespace
kubectl create namespace yosoy-historia-clinica

# Aplicar configuraciones
kubectl apply -f k8s/pvc.yaml
kubectl apply -f k8s/backend-configmap.yaml
kubectl apply -f k8s/frontend-configmap.yaml
kubectl apply -f k8s/backend-deployment.yaml
kubectl apply -f k8s/frontend-deployment.yaml
kubectl apply -f k8s/services.yaml
kubectl apply -f k8s/ingress.yaml

# Verificar despliegue
kubectl get pods -n yosoy-historia-clinica
kubectl get services -n yosoy-historia-clinica
kubectl get ingress -n yosoy-historia-clinica
```

## Desarrollo Local

### Instalación
```bash
# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm start

# El servidor estará disponible en http://localhost:3000
```

---

**Versión**: 1.0.0
**Estado**: Producción ✅
**Última actualización**: 2024-12-19
