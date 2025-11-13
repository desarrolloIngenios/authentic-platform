# Configuración Funcionando - Authentic Candidatos

**Fecha del Backup:** 13 de noviembre de 2025  
**Estado:** ✅ FUNCIONANDO CORRECTAMENTE  
**Versión probada:** Deployment funcionando con conectividad DB exitosa

## 🎯 Configuración Crítica

### Cloud SQL Proxy
```yaml
- name: cloud-sql-proxy
  image: us-central1-docker.pkg.dev/authentic-prod-464216/shared-images-repo/cloud-sql-proxy:2.8.0
  args:
    - "--address=0.0.0.0"
    - "--port=3306"
    - "authenticfarma-425820:us-central1:authentic"
```

**⚠️ CRÍTICO:** NO incluir `--auto-iam-authn` ya que usamos autenticación tradicional MySQL.

### Base de Datos
- **Host:** 127.0.0.1 (a través del Cloud SQL Proxy)
- **Puerto:** 3306
- **Base de datos:** authentic
- **Usuario:** candidatosuser
- **Contraseña:** Stored in Secret Manager version 3: `7OX*:05aoL6{Cg5E`
- **Instancia Cloud SQL:** authenticfarma-425820:us-central1:authentic

### Aplicación
- **Imagen:** us-central1-docker.pkg.dev/authentic-prod-464216/authenticfarma-repo/authentic-candidatos:v4.2.0
- **Puerto:** 80
- **Health Check:** /login (retorna 200)
- **Deployment:** authentic-candidatos (NO authenticfarma-candidatos)

## 🔧 Configuraciones de Recursos

### Deployment Name
```
NAME: authentic-candidatos
NAMESPACE: authenticfarma-candidatos
```

### Labels Correctas
```yaml
labels:
  app: authenticfarma-candidatos
  platform: authenticfarma
```

### Service
- **Nombre:** authenticfarma-candidatos-service
- **Selector:** app: authenticfarma-candidatos
- **Puerto:** 80

## 📝 Variables de Entorno Críticas

```yaml
- name: DB_CONNECTION
  value: "mysql"
- name: DB_HOST
  value: "127.0.0.1"
- name: DB_PORT
  value: "3306"
- name: DB_DATABASE
  value: "authentic"
- name: DB_USERNAME
  valueFrom:
    secretKeyRef:
      name: laravel-secrets
      key: DB_USERNAME
- name: DB_PASSWORD
  valueFrom:
    secretKeyRef:
      name: laravel-secrets
      key: DB_PASSWORD
```

## 🔐 Secret Manager

### Secretos en Google Secret Manager (authentic-prod-464216)
- `authentic-candidatos-DB_PASSWORD` (versión 3): `7OX*:05aoL6{Cg5E`
- `authentic-candidatos-APP_KEY`
- `authentic-candidatos-GOOGLE_CLIENT_SECRET`
- `authentic-candidatos-MAIL_PASSWORD`
- `authentic-candidatos-MAIL_USERNAME`

### Secreto Kubernetes: laravel-secrets
```
DB_USERNAME: candidatosuser (base64)
DB_PASSWORD: 7OX*:05aoL6{Cg5E (base64)
```

## ✅ Pruebas de Funcionalidad

### Conectividad DB
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- php -r "
try { 
  \$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=authentic', 'candidatosuser', getenv('DB_PASSWORD')); 
  echo 'Connection successful!'; 
} catch(Exception \$e) { 
  echo 'Connection failed: ' . \$e->getMessage(); 
}"
```
**Resultado esperado:** `Connection successful!`

### Health Check
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- curl -s -o /dev/null -w "%{http_code}" http://localhost/login
```
**Resultado esperado:** `200`

## 🚨 Problemas Comunes y Soluciones

### Error: `Cloud SQL MySQL: Automatic IAM authentication via the auth-proxy requires an IAM-authenticated user`
**Solución:** Remover `--auto-iam-authn` de los argumentos del Cloud SQL Proxy.

### Error: `Access denied for user 'candidatosuser'`
**Solución:** Verificar que la contraseña en Secret Manager esté sincronizada con Kubernetes secrets.

### Error: 503 Service Temporarily Unavailable
**Solución:** Verificar que el deployment tenga las labels correctas para el service selector.

## 📁 Archivos de Backup

- `namespace-complete-backup.yaml`: Backup completo del namespace
- `working-deployment.yaml`: Deployment funcionando exportado de Kubernetes
- `working-secrets.yaml`: Secretos funcionando
- `git-deployment-working.yaml`: Archivo de deployment desde Git
- `restore-script.sh`: Script de restauración automatizada

## 🔄 Comandos de Verificación Rápida

```bash
# Verificar pods
kubectl get pods -n authenticfarma-candidatos

# Verificar deployment
kubectl get deployment authentic-candidatos -n authenticfarma-candidatos

# Verificar service
kubectl get service authenticfarma-candidatos-service -n authenticfarma-candidatos

# Verificar secretos
kubectl get secret laravel-secrets -n authenticfarma-candidatos
```