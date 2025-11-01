#!/bin/bash

# Script de configuración de variables de entorno para GCP
# Clinical Records AI - Sistema Médico Seguro

# Configuración del proyecto GCP
export PROJECT_ID="clinical-records-prod"  # Cambiar por su PROJECT_ID
export CLUSTER_NAME="clinical-records-cluster"
export REGION="us-central1"
export ZONE="us-central1-a"

# Configuración de dominio
export DOMAIN="clinical-records.healthcare.com"  # Cambiar por su dominio
export API_DOMAIN="api.clinical-records.healthcare.com"

# Configuración de base de datos
export DB_INSTANCE_NAME="clinical-records-db"
export DB_NAME="clinical_records"
export DB_USER="postgres"

# Configuración de seguridad (GENERAR NUEVAS CLAVES)
export JWT_SECRET=$(openssl rand -base64 64)
export ENCRYPTION_KEY=$(openssl rand -base64 32)
export DB_PASSWORD=$(openssl rand -base64 32)

# Configuración de recursos
export BACKEND_MIN_REPLICAS=3
export BACKEND_MAX_REPLICAS=10
export FRONTEND_MIN_REPLICAS=2
export FRONTEND_MAX_REPLICAS=5

# Configuración de almacenamiento
export STORAGE_BUCKET="${PROJECT_ID}-clinical-records-storage"

# Configuración de monitoreo
export ENABLE_MONITORING=true
export ENABLE_LOGGING=true

# Función para mostrar configuración
show_config() {
    echo "🏥 Configuración de Clinical Records AI"
    echo "======================================"
    echo "Proyecto GCP: $PROJECT_ID"
    echo "Cluster: $CLUSTER_NAME"
    echo "Región: $REGION"
    echo "Dominio: $DOMAIN"
    echo "API: $API_DOMAIN"
    echo "Base de datos: $DB_INSTANCE_NAME"
    echo "======================================"
}

# Función para validar configuración
validate_config() {
    if [ -z "$PROJECT_ID" ]; then
        echo "❌ ERROR: PROJECT_ID no está configurado"
        return 1
    fi
    
    if [ -z "$DOMAIN" ]; then
        echo "❌ ERROR: DOMAIN no está configurado"
        return 1
    fi
    
    if [ -z "$JWT_SECRET" ]; then
        echo "❌ ERROR: JWT_SECRET no está configurado"
        return 1
    fi
    
    echo "✅ Configuración validada correctamente"
}

# Función para generar archivos de configuración
generate_configs() {
    # Actualizar manifiestos de Kubernetes con variables reales
    sed -i.bak "s/your-project-id/$PROJECT_ID/g" ../kubernetes/*.yaml
    sed -i.bak "s/your-region/$REGION/g" ../kubernetes/*.yaml
    sed -i.bak "s/clinical-records.your-domain.com/$DOMAIN/g" ../kubernetes/*.yaml
    sed -i.bak "s/api.clinical-records.your-domain.com/$API_DOMAIN/g" ../kubernetes/*.yaml
    sed -i.bak "s/admin@your-domain.com/admin@$(echo $DOMAIN | cut -d. -f2-)/g" ../kubernetes/*.yaml
    
    # Crear archivo .env para desarrollo
    cat > ../backend/.env << EOF
NODE_ENV=production
PORT=8080

# Base de datos
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASSWORD=$DB_PASSWORD

# Seguridad
JWT_SECRET=$JWT_SECRET
ENCRYPTION_KEY=$ENCRYPTION_KEY

# GCP
GOOGLE_CLOUD_PROJECT=$PROJECT_ID
CLOUD_SQL_CONNECTION_NAME=$PROJECT_ID:$REGION:$DB_INSTANCE_NAME

# Configuración adicional
LOG_LEVEL=info
RATE_LIMIT_WINDOW_MS=900000
RATE_LIMIT_MAX_REQUESTS=100
CORS_ORIGINS=https://$DOMAIN
EOF

    # Crear secrets base64 para Kubernetes
    cat > ../kubernetes/secrets.yaml << EOF
apiVersion: v1
kind: Secret
metadata:
  name: clinical-records-secrets
  namespace: clinical-records
type: Opaque
data:
  JWT_SECRET: $(echo -n $JWT_SECRET | base64)
  ENCRYPTION_KEY: $(echo -n $ENCRYPTION_KEY | base64)
  DB_PASSWORD: $(echo -n $DB_PASSWORD | base64)
EOF

    echo "✅ Archivos de configuración generados"
}

# Función principal
main() {
    case "${1:-show}" in
        "show")
            show_config
            ;;
        "validate")
            validate_config
            ;;
        "generate")
            validate_config && generate_configs
            ;;
        "export")
            echo "export PROJECT_ID=\"$PROJECT_ID\""
            echo "export CLUSTER_NAME=\"$CLUSTER_NAME\""
            echo "export REGION=\"$REGION\""
            echo "export ZONE=\"$ZONE\""
            echo "export DOMAIN=\"$DOMAIN\""
            echo "export API_DOMAIN=\"$API_DOMAIN\""
            ;;
        *)
            echo "Uso: $0 [show|validate|generate|export]"
            echo ""
            echo "  show     - Mostrar configuración actual"
            echo "  validate - Validar configuración"
            echo "  generate - Generar archivos de configuración"
            echo "  export   - Exportar variables de entorno"
            ;;
    esac
}

# Ejecutar función principal
main "$@"