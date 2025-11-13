# Backup de Secret Manager - Authentic Candidatos
# Fecha: 13 de noviembre de 2025
# Proyecto: authentic-prod-464216

## Secretos Críticos Funcionando

### authentic-candidatos-DB_PASSWORD
- **Versión actual funcionando:** 3
- **Valor:** 7OX*:05aoL6{Cg5E
- **Usuario asociado:** candidatosuser
- **Base de datos:** authentic
- **Instancia:** authenticfarma-425820:us-central1:authentic

### Comandos para obtener secretos:

```bash
# Obtener contraseña de DB (versión funcionando)
gcloud secrets versions access 3 --secret="authentic-candidatos-DB_PASSWORD" --project=authentic-prod-464216

# Obtener APP_KEY
gcloud secrets versions access latest --secret="authentic-candidatos-APP_KEY" --project=authentic-prod-464216

# Obtener Google Client Secret
gcloud secrets versions access latest --secret="authentic-candidatos-GOOGLE_CLIENT_SECRET" --project=authentic-prod-464216

# Obtener credenciales de mail
gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_USERNAME" --project=authentic-prod-464216
gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_PASSWORD" --project=authentic-prod-464216
```

### Script para recrear secretos en Kubernetes:

```bash
#!/bin/bash

NAMESPACE="authenticfarma-candidatos"
PROJECT_ID="authentic-prod-464216"

echo "🔐 Recreando secretos desde Secret Manager..."

# Obtener secretos de Secret Manager
DB_PASSWORD=$(gcloud secrets versions access 3 --secret="authentic-candidatos-DB_PASSWORD" --project=$PROJECT_ID)
APP_KEY=$(gcloud secrets versions access latest --secret="authentic-candidatos-APP_KEY" --project=$PROJECT_ID)
GOOGLE_CLIENT_SECRET=$(gcloud secrets versions access latest --secret="authentic-candidatos-GOOGLE_CLIENT_SECRET" --project=$PROJECT_ID)
MAIL_USERNAME=$(gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_USERNAME" --project=$PROJECT_ID)
MAIL_PASSWORD=$(gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_PASSWORD" --project=$PROJECT_ID)

# Crear secreto en Kubernetes
kubectl create secret generic laravel-secrets -n $NAMESPACE \
    --from-literal=DB_USERNAME="candidatosuser" \
    --from-literal=DB_PASSWORD="$DB_PASSWORD" \
    --from-literal=APP_KEY="$APP_KEY" \
    --from-literal=GOOGLE_CLIENT_SECRET="$GOOGLE_CLIENT_SECRET" \
    --from-literal=MAIL_USERNAME="$MAIL_USERNAME" \
    --from-literal=MAIL_PASSWORD="$MAIL_PASSWORD" \
    --dry-run=client -o yaml > working-secrets-from-gcp.yaml

echo "✅ Archivo working-secrets-from-gcp.yaml creado"
echo "📝 Para aplicar: kubectl apply -f working-secrets-from-gcp.yaml"
```

## Configuraciones de Base de Datos Verificadas

### Usuario de Base de Datos
- **Usuario actual funcionando:** candidatosuser
- **Contraseña:** 7OX*:05aoL6{Cg5E (Secret Manager v3)
- **Host:** 127.0.0.1 (a través de Cloud SQL Proxy)
- **Puerto:** 3306
- **Base de datos:** authentic

### Prueba de conectividad exitosa:
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- php -r "
try { 
  \$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=authentic', 'candidatosuser', '7OX*:05aoL6{Cg5E'); 
  echo 'Connection successful!'; 
} catch(Exception \$e) { 
  echo 'Connection failed: ' . \$e->getMessage(); 
}"
```
**Resultado:** ✅ Connection successful!

## ⚠️ Notas Importantes

1. **Contraseña específica:** La versión 3 del secreto `authentic-candidatos-DB_PASSWORD` es la que funciona con el usuario `candidatosuser`

2. **Usuario correcto:** `candidatosuser` (no `user_db_authentic` ni `authenticdbuser`)

3. **Sincronización:** Siempre sincronizar desde Secret Manager a Kubernetes después de cambios

4. **Autenticación:** NO usar `--auto-iam-authn` en Cloud SQL Proxy con este usuario