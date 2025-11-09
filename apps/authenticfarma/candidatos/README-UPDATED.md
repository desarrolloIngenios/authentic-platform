# 🚀 Authentic Platform - AuthenticFarma Candidatos

## 📋 Descripción
Sistema de gestión de candidatos para AuthenticFarma, desarrollado con Laravel.

## 🔧 Características
- Autenticación OAuth con Google
- Gestión de candidatos
- Panel de administración
- API RESTful

## 🏗️ Tecnologías
- **Backend**: Laravel 10
- **Base de datos**: MySQL
- **Autenticación**: Laravel Sanctum + Google OAuth
- **Frontend**: Blade + Tailwind CSS

## 🚀 Instalación Local

### Prerrequisitos
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+

### Pasos de instalación
```bash
# Clonar repositorio
git clone https://github.com/desarrolloIngenios/authentic-platform.git
cd authentic-platform/apps/authenticfarma/candidatos

# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=candidatos_db
DB_USERNAME=root
DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

## 📊 Performance Optimizations

### Google OAuth Controller
El `GoogleController` ha sido optimizado para máximo rendimiento:

- ✅ **Session Management**: Optimización inteligente de sesiones
- ✅ **Stateless OAuth**: Autenticación sin estado para escalabilidad  
- ✅ **Error Handling**: Manejo robusto de errores
- ✅ **Fallback Methods**: Métodos de respaldo para alta disponibilidad

### Mejoras implementadas (Noviembre 2024)
- 🚀 Tiempo de respuesta reducido de 3-5s a 0.4-0.7s
- 🔒 Seguridad mejorada en autenticación OAuth
- 📈 Escalabilidad optimizada para múltiples usuarios concurrentes
- 🛡️ Manejo de errores más robusto

## 🐳 Docker

### Desarrollo
```bash
docker build -f dockerfile -t candidatos:dev .
docker run -p 8000:80 candidatos:dev
```

### Producción
```bash
docker build -f dockerfile -t gcr.io/PROJECT_ID/authenticfarma-candidatos:latest .
docker push gcr.io/PROJECT_ID/authenticfarma-candidatos:latest
```

## 🔧 API Endpoints

### Autenticación
- `GET /auth/google` - Iniciar OAuth con Google
- `GET /auth/google/callback` - Callback OAuth
- `POST /auth/logout` - Cerrar sesión

### Candidatos
- `GET /api/candidatos` - Listar candidatos
- `POST /api/candidatos` - Crear candidato
- `GET /api/candidatos/{id}` - Ver candidato
- `PUT /api/candidatos/{id}` - Actualizar candidato
- `DELETE /api/candidatos/{id}` - Eliminar candidato

## 🌐 URLs

### Desarrollo
- **Local**: http://localhost:8000
- **Dev**: https://candidatos-dev.authenticfarma.com

### Producción  
- **Prod**: https://candidatos.authenticfarma.com

## 👥 Equipo
- **Lead Developer**: Otto Fonseca (ottofonseca@gmail.com)
- **DevOps**: Equipo desarrolloIngenios

## 📝 Changelog

### v2.1.0 (Noviembre 2024)
- 🧠 Sistema CI/CD inteligente implementado
- 🚀 GoogleController optimizado para performance
- 🔧 Builds condicionales por aplicación
- 📊 Monitoreo de performance mejorado

### v2.0.0 (Octubre 2024)
- 🔄 Migración a Laravel 10
- 🎨 Nueva interfaz con Tailwind CSS
- 🔐 OAuth Google mejorado
- 🐳 Docker optimizado

## 🤝 Contribuir

1. Fork el proyecto
2. Crear feature branch (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'feat: agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## 📄 Licencia
Propietario - desarrolloIngenios

---

**Status**: 🟢 Activo | **Performance**: ⚡ Optimizado | **CI/CD**: 🧠 Inteligente