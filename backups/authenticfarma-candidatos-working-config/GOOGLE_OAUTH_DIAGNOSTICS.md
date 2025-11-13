# 🔐 Diagnóstico Google OAuth - Candidatos Authenticfarma

## 📋 Configuración Actual Detectada

### ✅ Variables de Entorno Configuradas:
- **GOOGLE_CLIENT_ID:** `1087239389819-5eck0eo7e3o5f1nku8gumb0mitgl6ag5.apps.googleusercontent.com`
- **GOOGLE_CLIENT_SECRET:** ✅ Configurado desde Secret Manager
- **GOOGLE_REDIRECT:** `https://candidatos.authenticfarma.com/google-auth/callback`

---

## 🚨 Posibles Causas del Error "Acceso bloqueado"

### 1. 🌐 **URLs de Redirección en Google Console**

Verificar en [Google Cloud Console](https://console.cloud.google.com/apis/credentials) que estas URLs estén configuradas:

```
✅ URIs de redirección autorizados:
- https://candidatos.authenticfarma.com/google-auth/callback
- https://candidatos.authenticfarma.com (opcional)

✅ Orígenes de JavaScript autorizados:
- https://candidatos.authenticfarma.com
```

### 2. 🔑 **Estado del Cliente OAuth**

Verificar que el cliente OAuth esté:
- ✅ **Activo** (no eliminado o deshabilitado)
- ✅ **Publicado** (no en estado de prueba)
- ✅ **Sin restricciones de dominio** o con dominio authenticfarma.com autorizado

### 3. 🏢 **Configuración de Workspace/Organización**

Si es Google Workspace:
- ✅ Aplicación autorizada por el administrador
- ✅ OAuth interno configurado correctamente
- ✅ Sin restricciones de seguridad bloqueando terceros

---

## 🛠️ Pasos de Solución Inmediata

### Paso 1: Verificar Google Console
```bash
# Ir a: https://console.cloud.google.com/apis/credentials
# Buscar: 1087239389819-5eck0eo7e3o5f1nku8gumb0mitgl6ag5
# Verificar URIs de redirección
```

### Paso 2: Probar URL de Callback
```bash
# Navegar a: https://candidatos.authenticfarma.com/google-auth/callback
# Debe mostrar error de Laravel (no 404)
```

### Paso 3: Verificar DNS/SSL
```bash
# Verificar que el dominio resuelva correctamente
nslookup candidatos.authenticfarma.com

# Verificar certificado SSL
curl -I https://candidatos.authenticfarma.com
```

### Paso 4: Logs de Laravel
```bash
# Ver logs en tiempo real
kubectl logs -f deployment/authentic-candidatos -n authenticfarma-candidatos -c app
```

---

## 🧪 Pruebas de Diagnóstico

### Test 1: Verificar ruta OAuth
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- php artisan route:list | grep google
```

### Test 2: Verificar configuración Laravel
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- php artisan config:show services.google
```

### Test 3: Test de conectividad Google
```bash
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- curl -s https://accounts.google.com/.well-known/openid_configuration
```

---

## 📝 Configuración Requerida en Google Console

### Crear/Verificar Cliente OAuth 2.0:

1. **Ir a:** [Google Cloud Console → APIs & Services → Credentials](https://console.cloud.google.com/apis/credentials)

2. **Seleccionar proyecto:** `authentic-prod-464216`

3. **Encontrar cliente ID:** `1087239389819-5eck0eo7e3o5f1nku8gumb0mitgl6ag5`

4. **Configurar URIs autorizados:**
   ```
   Orígenes de JavaScript autorizados:
   https://candidatos.authenticfarma.com
   
   URIs de redirección autorizados:
   https://candidatos.authenticfarma.com/google-auth/callback
   ```

5. **Verificar estado:** Publicado (no en prueba)

6. **Pantalla de consentimiento OAuth:** Configurada para usuarios externos o internos según necesidad

---

## ⚠️ Errores Comunes

### Error: "This app isn't verified"
- **Causa:** App en modo prueba
- **Solución:** Publicar app o agregar usuarios de prueba

### Error: "redirect_uri_mismatch" 
- **Causa:** URL no coincide exactamente
- **Solución:** Verificar https:// y path exacto

### Error: "access_denied"
- **Causa:** Usuario o dominio no autorizado
- **Solución:** Revisar restricciones en Google Console

---

## 🔄 Comandos de Verificación Rápida

```bash
# 1. Verificar variables de entorno
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- env | grep GOOGLE

# 2. Verificar aplicación funcionando
kubectl exec -n authenticfarma-candidatos POD_NAME -c app -- curl -s http://localhost/login

# 3. Ver logs en tiempo real
kubectl logs -f deployment/authentic-candidatos -n authenticfarma-candidatos -c app

# 4. Reiniciar deployment si es necesario
kubectl rollout restart deployment/authentic-candidatos -n authenticfarma-candidatos
```