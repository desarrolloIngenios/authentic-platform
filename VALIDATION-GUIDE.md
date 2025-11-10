# 🔍 GUÍA MANUAL DE VALIDACIÓN EN PRODUCCIÓN

## 🎯 Validaciones críticas que debes hacer

### 1. 🧬 **AuthenticFarma Candidatos - GoogleController**

#### ✅ **Test de Login con Google:**
1. Ve a: https://candidatos.authenticfarma.com/login
2. Haz clic en "Iniciar con Google"
3. **Validar que:**
   - ⚡ La redirección es rápida (< 2 segundos)
   - ✅ No hay errores de "Invalid State" 
   - ✅ Login exitoso sin bucles infinitos
   - ✅ Dashboard carga correctamente después del login

#### ✅ **Test de Performance:**
```bash
# Desde terminal, medir tiempos de respuesta:
curl -w "@curl-format.txt" -o /dev/null -s https://candidatos.authenticfarma.com/login

# Archivo curl-format.txt:
echo 'time_namelookup:  %{time_namelookup}\ntime_connect:     %{time_connect}\ntime_appconnect:  %{time_appconnect}\ntime_pretransfer: %{time_pretransfer}\ntime_redirect:    %{time_redirect}\ntime_starttransfer: %{time_starttransfer}\ntime_total:       %{time_total}\n' > curl-format.txt
```

#### ✅ **Validar optimizaciones específicas:**
- **Sesión optimizada**: Login debe ser fluido sin recargas
- **Stateless OAuth**: Múltiples usuarios pueden logearse simultáneamente  
- **Error handling**: Probar con usuario deshabilitado debe mostrar mensaje claro

---

### 2. 🏥 **YoSoy Historia Clínica - Fórmulas Médicas**

#### ✅ **Test de Login básico:**
1. Ve a: https://hc.yo-soy.co
2. Login con: `admin` / `admin123`
3. **Validar que:**
   - ✅ Login exitoso
   - ✅ Dashboard carga correctamente
   - ✅ Tiempo de respuesta < 1 segundo

#### ✅ **Test de nuevas Fórmulas Médicas:**
1. Dentro del sistema, buscar sección de "Fórmulas" o "Prescripciones"
2. **Validar endpoints API:**
```bash
# Test básico de API (requiere token):
curl -X GET https://hc.yo-soy.co/api/formulas \
  -H "Authorization: Bearer [TOKEN]"

# Debe retornar 401 (sin token) o 200 (con token válido)
```

#### ✅ **Test de funcionalidad completa:**
- **Crear fórmula médica**: Debe guardarse correctamente
- **Listar fórmulas**: Debe mostrar fórmulas existentes
- **Asociar a paciente**: Debe vincular correctamente

---

### 3. 🚀 **Sistema CI/CD Inteligente**

#### ✅ **Verificar último deployment:**
1. Ve a: https://github.com/desarrolloIngenios/authentic-platform/actions
2. **Validar que:**
   - ✅ Último workflow: SUCCESS
   - ✅ Builds condicionales funcionando
   - ✅ No errores de permisos en PRs

#### ✅ **Validar detección inteligente:**
```bash
# Verificar último commit y qué se construyó:
git log --oneline -5
```

**En GitHub Actions, validar que:**
- Solo las apps modificadas se construyeron
- Builds no necesarios fueron "skipped"
- Tiempo total optimizado

---

### 4. 🔐 **Validación de Seguridad**

#### ✅ **HTTPS y Certificados:**
```bash
# Validar certificado SSL:
curl -I https://candidatos.authenticfarma.com
curl -I https://hc.yo-soy.co

# Debe mostrar: HTTP/2 200 o HTTP/1.1 200
```

#### ✅ **Headers de Seguridad:**
```bash
# Verificar headers importantes:
curl -I https://candidatos.authenticfarma.com | grep -E "(X-Frame|X-Content|Strict-Transport)"
```

---

### 5. 📊 **Monitoreo Post-Deployment**

#### ✅ **Logs de aplicación:**
```bash
# Si tienes acceso a Kubernetes:
kubectl logs -f deployment/authenticfarma-candidatos -n production
kubectl logs -f deployment/yosoy-hc -n production
```

#### ✅ **Métricas de ArgoCD:**
1. Ve a tu dashboard de ArgoCD
2. **Validar que:**
   - ✅ Aplicaciones: "Healthy" y "Synced"
   - ✅ Pods: Running y Ready
   - ✅ Services: Endpoints activos

---

## 🧪 **Tests manuales críticos**

### Test 1: **Flujo completo AuthenticFarma**
```
1. Ir a https://candidatos.authenticfarma.com
2. Login con Google → Debe ser rápido y sin errores
3. Navegar dashboard → Debe cargar < 2 segundos
4. Logout y re-login → Debe mantener optimización
```

### Test 2: **Flujo completo Historia Clínica**
```
1. Ir a https://hc.yo-soy.co
2. Login admin/admin123 → Debe ser inmediato
3. Navegar a pacientes → Debe listar correctamente
4. Intentar crear fórmula médica → Debe funcionar
```

### Test 3: **CI/CD Intelligence**
```
1. Hacer cambio menor en una app
2. Push a dev
3. Verificar que solo esa app se construye
4. Confirmar optimización de tiempo
```

---

## 🎯 **Checklist de Validación Final**

### ✅ **GoogleController Optimizado:**
- [ ] Login Google < 2 segundos
- [ ] Sin errores Invalid State
- [ ] Sesión optimizada funcionando
- [ ] Múltiples usuarios simultáneos OK

### ✅ **Historia Clínica Actualizada:**
- [ ] Login básico funcionando  
- [ ] API de fórmulas médicas activa
- [ ] Funcionalidad completa operativa
- [ ] Performance < 1 segundo

### ✅ **Sistema CI/CD Inteligente:**
- [ ] Workflows SUCCESS sin errores
- [ ] Detección inteligente funcionando
- [ ] Builds condicionales optimizados
- [ ] PRs automáticos creándose

### ✅ **Seguridad y Performance:**
- [ ] HTTPS funcionando correctamente
- [ ] Headers de seguridad presentes
- [ ] Tiempos de respuesta optimizados
- [ ] Monitoreo y logging activo

---

## 🆘 **Qué hacer si encuentras problemas**

### ❌ **Si AuthenticFarma falla:**
1. Revisar logs de la aplicación
2. Verificar configuración OAuth Google
3. Comprobar variables de entorno
4. Validar base de datos MySQL

### ❌ **Si Historia Clínica falla:**
1. Verificar API endpoints manualmente
2. Comprobar base de datos SQLite
3. Revisar autenticación JWT
4. Validar nuevas tablas de fórmulas

### ❌ **Si CI/CD falla:**
1. Revisar GitHub Actions logs
2. Verificar permisos del repositorio
3. Comprobar secrets de GCP
4. Validar ArgoCD sync status

---

## 📞 **Contactos y Recursos**

- **GitHub Actions**: https://github.com/desarrolloIngenios/authentic-platform/actions
- **Documentación**: `docs/` en el repositorio
- **Scripts de validación**: `scripts/validate-production-changes.sh`
- **Usuario principal**: `authentic-24` (stack.dev@authentic.com.co)

---

**🎉 ¡Con esta validación confirmarás que todos los cambios están funcionando correctamente en producción!**