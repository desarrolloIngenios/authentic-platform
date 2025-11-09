# 🏥 YoSoy - Historia Clínica

## 📋 Descripción
Sistema de Historia Clínica digital para el manejo integral de pacientes, desarrollado con FastAPI y SQLite.

## ✨ Características Principales
- 📊 **Gestión de Pacientes**: CRUD completo 
- 💊 **Fórmulas Médicas**: Prescripción digital
- 🔐 **Autenticación JWT**: Seguridad robusta
- 📱 **API RESTful**: Endpoints optimizados
- 💾 **Base de datos**: SQLite para portabilidad

## 🚀 Tecnologías
- **Backend**: FastAPI 0.104+
- **Base de datos**: SQLite 3
- **Autenticación**: JWT (PyJWT)
- **ORM**: SQLite3 nativo
- **Validación**: Pydantic

## 🔧 Endpoints API

### Autenticación
```
POST /auth/login     - Iniciar sesión
POST /auth/register  - Registrar usuario  
POST /auth/logout    - Cerrar sesión
```

### Pacientes
```
GET    /api/pacientes        - Listar pacientes
POST   /api/pacientes        - Crear paciente
GET    /api/pacientes/{id}   - Ver paciente
PUT    /api/pacientes/{id}   - Actualizar paciente
DELETE /api/pacientes/{id}   - Eliminar paciente
```

### Fórmulas Médicas (Nuevas - Nov 2024)
```
GET  /api/formulas     - Listar fórmulas médicas
POST /api/formulas     - Crear nueva fórmula
GET  /api/formulas/{id} - Ver fórmula específica
```

## 🏗️ Instalación Local

### Prerrequisitos
- Python 3.9+
- pip

### Pasos de instalación
```bash
# Navegar al directorio
cd apps/yosoy/historia-clinica/backend

# Crear entorno virtual
python -m venv venv
source venv/bin/activate  # Linux/Mac
# venv\Scripts\activate   # Windows

# Instalar dependencias
pip install -r requirements.txt

# Inicializar base de datos
python init_db.py

# Ejecutar servidor
python main.py
```

## 🐳 Docker

### Desarrollo
```bash
docker build -f apps/yosoy/historia-clinica/backend/Dockerfile \
  -t yosoy-hc:dev apps/yosoy/historia-clinica/backend

docker run -p 8001:8000 yosoy-hc:dev
```

### Producción
```bash
docker build -f apps/yosoy/historia-clinica/backend/Dockerfile \
  -t gcr.io/PROJECT_ID/yosoy-hc-backend:latest \
  apps/yosoy/historia-clinica/backend

docker push gcr.io/PROJECT_ID/yosoy-hc-backend:latest
```

## 🌐 URLs

### Desarrollo
- **Local**: http://localhost:8001
- **Dev**: https://hc-dev.yo-soy.co

### Producción
- **Prod**: https://hc.yo-soy.co
- **Docs**: https://hc.yo-soy.co/docs (Swagger UI)

## 📊 Estructura de Base de Datos

### Tabla: usuarios
```sql
CREATE TABLE usuarios (
    id INTEGER PRIMARY KEY,
    username TEXT UNIQUE,
    password TEXT,
    email TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: pacientes  
```sql
CREATE TABLE pacientes (
    id INTEGER PRIMARY KEY,
    nombre TEXT NOT NULL,
    edad INTEGER,
    genero TEXT,
    telefono TEXT,
    email TEXT,
    direccion TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Tabla: formulas_medicas (Nueva - Nov 2024)
```sql
CREATE TABLE formulas_medicas (
    id TEXT PRIMARY KEY,
    pacienteId INTEGER,
    medicamentos TEXT,
    indicaciones TEXT,
    duracion TEXT,
    medicoId TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🔐 Autenticación

### Usuarios por defecto
- **Admin**: `admin` / `admin123`
- **Doctor**: `doctor` / `doctor123`

### JWT Token
```python
# Generar token
token = jwt.encode({
    'user_id': user_id,
    'username': username,
    'exp': datetime.utcnow() + timedelta(hours=24)
}, SECRET_KEY, algorithm='HS256')
```

## 📈 Performance

### Optimizaciones recientes (Nov 2024)
- ✅ **Response Time**: 0.4-0.7s promedio
- ✅ **Database**: Índices optimizados
- ✅ **API**: Validación Pydantic mejorada
- ✅ **Error Handling**: Manejo robusto de excepciones

### Monitoreo
```bash
# Verificar tiempo de respuesta
curl -w "@curl-format.txt" -s -o /dev/null https://hc.yo-soy.co/api/pacientes

# Health check
curl https://hc.yo-soy.co/health
```

## 🧪 Testing

### Ejecutar pruebas
```bash
# Pruebas unitarias
python -m pytest tests/

# Pruebas de integración
python -m pytest tests/integration/

# Coverage
python -m pytest --cov=app tests/
```

### Casos de prueba principales
- ✅ Autenticación JWT
- ✅ CRUD de pacientes
- ✅ Validación de datos
- ✅ Manejo de errores
- ✅ Fórmulas médicas (nuevo)

## 🚀 Deployment

### CI/CD Inteligente (Nov 2024)
El sistema detecta automáticamente cambios en `apps/yosoy/` y ejecuta:

1. **Build condicional**: Solo si hay cambios
2. **Docker build**: Imagen optimizada
3. **Push a GCR**: Tagged apropiadamente  
4. **Deploy a K8s**: Via ArgoCD

### Comandos de deployment
```bash
# Deploy manual a dev
kubectl apply -f k8s/dev/

# Sync ArgoCD
argocd app sync yosoy-hc-dev
```

## 👥 Equipo
- **Lead Developer**: Otto Fonseca
- **Medical Advisor**: Dr. Juan Pérez
- **DevOps**: Equipo desarrolloIngenios

## 📝 Changelog

### v1.3.0 (Noviembre 2024)
- 💊 **Nueva funcionalidad**: Fórmulas médicas
- 🧠 **CI/CD Inteligente**: Builds condicionales  
- 🚀 **Performance**: Optimización de endpoints
- 📊 **Monitoring**: Métricas mejoradas

### v1.2.0 (Octubre 2024)  
- 🔐 **Seguridad**: JWT mejorado
- 📱 **API**: Nuevos endpoints
- 🐳 **Docker**: Imagen optimizada

## 🤝 Contribuir

1. Fork el proyecto
2. Crear feature branch (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'feat: agregar fórmulas médicas'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)  
5. Crear Pull Request

## 📄 Licencia
Propietario - desarrolloIngenios

---

**Status**: 🟢 Activo | **Performance**: ⚡ Optimizado | **Medical**: 🏥 Certificado