# 🎯 Backup Configuración Funcionando - Authentic Candidatos

**📅 Fecha:** 13 de noviembre de 2025  
**✅ Estado:** FUNCIONANDO CORRECTAMENTE  
**🧪 Probado:** Conectividad DB exitosa, Health check 200, aplicación operativa

---

## 📁 Archivos de Backup

### 🔧 Configuraciones Kubernetes
- **`namespace-complete-backup.yaml`** - Backup completo de todos los recursos del namespace
- **`working-deployment.yaml`** - Deployment exportado de Kubernetes (estado funcionando)
- **`working-secrets.yaml`** - Secretos funcionando exportados de Kubernetes

### 📝 Configuraciones Git
- **`git-deployment-working.yaml`** - Archivo de deployment desde Git (fuente de verdad)
- **`platform-deployment-reference.yaml`** - Archivo de referencia alternativo

### 🔐 Secret Manager
- **`SECRET_MANAGER_BACKUP.md`** - Documentación completa de secretos
- **`secret-db-password-v3.txt`** - Contraseña funcionando (versión 3)

### 📚 Documentación
- **`WORKING_CONFIG_DOCUMENTATION.md`** - Documentación técnica completa
- **`README.md`** - Este archivo (guía de uso)

---

## 🚀 Cómo Restaurar

### Opción 1: Script Automatizado (RECOMENDADO)
```bash
# Ejecutar script interactivo
./restore-script.sh

# Seleccionar opción según necesidad:
# 1 = Restauración completa (elimina todo y restaura)
# 2 = Solo deployment
# 3 = Solo secretos
# 4 = Ver configuración actual
# 5 = Ejecutar pruebas
```

### Opción 2: Manual
```bash
# 1. Aplicar deployment funcionando
kubectl apply -f git-deployment-working.yaml

# 2. Aplicar secretos si es necesario
kubectl apply -f working-secrets.yaml

# 3. Verificar estado
kubectl get pods -n authenticfarma-candidatos -l app=authenticfarma-candidatos
```

---

## 🧪 Pruebas de Verificación

### Conectividad Base de Datos
```bash
POD=$(kubectl get pods -n authenticfarma-candidatos -l app=authenticfarma-candidatos -o jsonpath='{.items[0].metadata.name}')

kubectl exec -n authenticfarma-candidatos $POD -c app -- php -r "
try { 
    \$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=authentic', 'candidatosuser', getenv('DB_PASSWORD')); 
    echo 'Connection successful!'; 
} catch(Exception \$e) { 
    echo 'Connection failed: ' . \$e->getMessage(); 
}"
```
**✅ Resultado esperado:** `Connection successful!`

### Health Check Aplicación
```bash
kubectl exec -n authenticfarma-candidatos $POD -c app -- curl -s -o /dev/null -w "%{http_code}" http://localhost/login
```
**✅ Resultado esperado:** `200`

---

## ⚠️ Configuración Crítica

### Cloud SQL Proxy (SIN auto-iam-authn)
```yaml
args:
  - "--address=0.0.0.0"
  - "--port=3306"
  - "authenticfarma-425820:us-central1:authentic"
```

### Base de Datos
- **Usuario:** candidatosuser
- **Contraseña:** 7OX*:05aoL6{Cg5E (Secret Manager v3)
- **Host:** 127.0.0.1 (Cloud SQL Proxy)
- **BD:** authentic

### Deployment Names
- **Funcionando:** `authentic-candidatos`
- **Evitar:** `authenticfarma-candidatos` (causa conflictos)

---

## 🆘 Resolución de Problemas

### Error: `Auto IAM authentication requires IAM user`
```bash
# Verificar que Cloud SQL Proxy NO tenga --auto-iam-authn
kubectl describe deployment authentic-candidatos -n authenticfarma-candidatos | grep -A 10 "Args:"
```

### Error: `Access denied for user`
```bash
# Verificar contraseña en secreto
kubectl get secret laravel-secrets -n authenticfarma-candidatos -o jsonpath='{.data.DB_PASSWORD}' | base64 -d
# Debe ser: 7OX*:05aoL6{Cg5E
```

### Error: 503 Service Unavailable
```bash
# Verificar que el service apunte al deployment correcto
kubectl get service authenticfarma-candidatos-service -n authenticfarma-candidatos -o yaml | grep selector
# Debe tener: app: authenticfarma-candidatos
```

---

## 📞 Contacto

Para dudas sobre esta configuración, referirse a la documentación completa en `WORKING_CONFIG_DOCUMENTATION.md` o ejecutar `./restore-script.sh` opción 4 para ver el estado actual.

**🎉 ¡Esta configuración está probada y funcionando al 100%!**