# 📁 Scripts Directory

Este directorio contiene todos los scripts de automatización y utilidades para el proyecto **authentic-platform**.

## 🗂️ Estructura Organizada

### 🔐 **secrets/** - Gestión de Secretos y Credenciales
```bash
secrets/
├── sync-secrets.sh                    # Sincroniza Secret Manager → Kubernetes
├── setup-vertex-ai-credentials.sh    # Configura credenciales Vertex AI/Gemini
└── setup-gemini-secrets.sh          # Configura secretos específicos de Gemini
```

### ✅ **validation/** - Validación y Verificación
```bash
validation/
├── validate-candidatos-production.sh # Validación completa del entorno de producción
├── validate-artifact-registry.sh     # Validación de Artifact Registry
└── final-verification.sh            # Verificación final post-despliegue
```

### 📊 **monitoring/** - Monitoreo y Observabilidad
```bash
monitoring/
└── monitor-artifact-registry.sh     # Monitoreo del estado de Artifact Registry
```

### 🔄 **sync/** - Sincronización
```bash
sync/
├── sync-candidatos.sh               # Sincronización específica de candidatos
└── sync-remote.sh                   # Sincronización con repositorios remotos
```

### 🔧 **build/** - Construcción y Build
```bash
build/
└── build_all.sh                     # Script maestro de construcción (placeholder)
```

### 🚀 **deploy/** - Despliegue
```bash
deploy/
└── deploy_all.sh                    # Script maestro de despliegue (placeholder)
```

### 📦 **migrate/** - Migraciones
```bash
migrate/
└── migrate_db.sh                    # Migraciones de base de datos (placeholder)
```

### 📚 **legacy/** - Scripts Archivados
```bash
legacy/                              # 15 scripts temporales/específicos archivados
├── advanced-performance-analysis.sh  # Análisis avanzado de performance
├── browser-timeout-analysis.sh      # Análisis de timeouts de navegador
├── fix-*.sh                         # Scripts de fixes específicos
├── performance-*.sh                 # Scripts de análisis de performance
└── setup-*.sh                      # Configuraciones específicas
```

## 🎯 Scripts Principales (Uso Frecuente)

| Script | Descripción | Uso |
|--------|-------------|-----|
| `secrets/sync-secrets.sh` | Sincroniza secretos de GCP Secret Manager a K8s | `./secrets/sync-secrets.sh` |
| `secrets/setup-vertex-ai-credentials.sh` | Configura Vertex AI | `./secrets/setup-vertex-ai-credentials.sh` |
| `validation/validate-candidatos-production.sh` | Valida entorno completo | `./validation/validate-candidatos-production.sh` |
| `sync/sync-candidatos.sh` | Sync específico candidatos | `./sync/sync-candidatos.sh` |

## 📈 Estadísticas de Limpieza

- **Scripts originales**: ~48
- **Scripts eliminados**: 21 (obsoletos/duplicados)
- **Scripts organizados**: 12 (en categorías específicas)
- **Scripts archivados**: 15 (en legacy/)
- **Reducción**: ~56% menos archivos en directorio principal

## 🔧 Scripts Pendientes de Implementación

Los siguientes scripts están como placeholders y requieren implementación:

- `build/build_all.sh` - Script maestro de construcción
- `deploy/deploy_all.sh` - Script maestro de despliegue  
- `migrate/migrate_db.sh` - Migraciones de base de datos

## 📝 Notas

- Los scripts en `legacy/` se mantienen por compatibilidad pero no se usan activamente
- Todos los scripts útiles están organizados por funcionalidad
- Se eliminaron 21 scripts obsoletos/duplicados para mejorar mantenibilidad
- La nueva estructura facilita encontrar y mantener scripts específicos

---
*Última actualización: Noviembre 2024*
*Limpieza realizada: Eliminación de scripts obsoletos y reorganización por categorías*