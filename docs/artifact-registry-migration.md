# 🐳 Artifact Registry - Migración de Imágenes

## 📊 Estado Actual

### ✅ Completado:
- **authenticfarma-repo**: Contiene imágenes de la aplicación candidatos ✅
- **shared-images-repo**: Creado para imágenes compartidas ✅

### ⏳ Pendiente: 
- **Cloud SQL Proxy**: Migración completa (problema de arquitectura detectado)

## 🔧 Problema Identificado

### Error:
```bash
exec /cloud-sql-proxy: exec format error
```

### Causa:
La imagen construida en Mac (ARM64) no es compatible con el cluster de Kubernetes (linux/amd64)

### Solución Aplicada:
```bash
# Rollback temporal a imagen original
image: gcr.io/cloud-sql-connectors/cloud-sql-proxy:2.8.0
```

## 🛠️ Plan de Corrección Futura

### 1. Construcción Multi-Arquitectura Correcta:
```bash
# Método correcto para buildx multiplataforma
docker buildx build --platform linux/amd64,linux/arm64 \
  --tag us-central1-docker.pkg.dev/authentic-prod-464216/shared-images-repo/cloud-sql-proxy:2.8.0 \
  --push \
  - <<EOF
FROM gcr.io/cloud-sql-connectors/cloud-sql-proxy:2.8.0@sha256:9c84401d9c31d18809b02155e74920d0434a7d8780d2b63b8de7a690fea6f1bf
EOF
```

### 2. Alternativas Recomendadas:

#### Opción A: Usar imagen base con digest específico
```dockerfile
FROM gcr.io/cloud-sql-connectors/cloud-sql-proxy:2.8.0@sha256:9c84401d9c31d18809b02155e74920d0434a7d8780d2b63b8de7a690fea6f1bf
```

#### Opción B: Copy desde imagen oficial
```dockerfile
FROM scratch
COPY --from=gcr.io/cloud-sql-connectors/cloud-sql-proxy:2.8.0 /cloud-sql-proxy /cloud-sql-proxy
ENTRYPOINT ["/cloud-sql-proxy"]
```

#### Opción C: CI/CD automático
- Configurar pipeline de CI/CD para re-build automático de imágenes compartidas
- Usar runners de GitHub Actions con arquitectura correcta

## 📈 Beneficios de la Migración Completa

1. **Control Total**: Todas las imágenes bajo nuestro control
2. **Seguridad**: Escaneo y validación de imágenes propias  
3. **Compliance**: Cumplimiento de políticas corporativas
4. **Performance**: Menor latencia al estar en el mismo proyecto
5. **Costos**: Optimización de transferencia de datos

## 🎯 Próximos Pasos

1. ✅ **Mantener funcionamiento actual** (gcr.io temporal)
2. 🔄 **Investigar mejor método de construcción multi-arch**
3. 📝 **Documentar proceso estándar para imágenes compartidas**
4. 🚀 **Implementar migración cuando esté probada**

---
*Última actualización: $(date '+%Y-%m-%d %H:%M:%S')*
*Estado: Funcionando con imagen original, shared-images-repo listo para uso futuro*