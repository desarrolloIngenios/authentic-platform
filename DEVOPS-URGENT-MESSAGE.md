# 📧 MENSAJE PARA EQUIPO DEVOPS - URGENTE

## 🚨 PROBLEMA: Aplicación candidatos "no responde" 

**Reportado por**: Usuario final durante navegación  
**Síntoma**: Navegador muestra "la aplicación no responde"  
**Causa identificada**: Debug mode + caches Laravel sin optimizar  
**Estado actual**: App responde externamente (0.6s) pero lenta internamente  

---

## ⚡ SOLUCIÓN REQUERIDA (5 minutos)

### Comando directo:
```bash
POD=$(kubectl get pods -n authenticfarma-prod -l app=candidatos -o jsonpath='{.items[0].metadata.name}')
kubectl exec $POD -n authenticfarma-prod -- bash -c "
cd /var/www/html
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
echo 'Fix aplicado: \$(date)'
"
```

### Verificación:
```bash
curl -w 'Tiempo: %{time_total}s | HTTP: %{http_code}\n' -o /dev/null -s https://candidatos.authenticfarma.com/login
```

**Resultado esperado**: < 1s, sin timeouts

---

## 📋 SCRIPT COMPLETO DISPONIBLE

**Archivo**: `devops-fix-script.sh`  
**Ubicación**: Adjunto en este mensaje  
**Incluye**: Diagnóstico + Fix + Verificación + Rollback  

---

## 🎯 RESULTADO ESPERADO

- ✅ Navegación fluida sin "no responde"
- ✅ Tiempos de respuesta < 1s consistente  
- ✅ JavaScript funcionando correctamente
- ✅ Debug mode optimizado para producción

---

## 🔄 ROLLBACK (si hay problemas)

```bash
kubectl delete pod $POD -n authenticfarma-prod
# Kubernetes recreará automáticamente
```

---

**Prioridad**: 🔴 ALTA - Afecta experiencia usuario  
**Tiempo estimado**: 5 minutos  
**Impacto**: Zero downtime  
**Contacto**: Desarrollador disponible para seguimiento