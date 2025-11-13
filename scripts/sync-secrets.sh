#!/bin/bash

# Script para sincronizar secrets desde Google Secret Manager a Kubernetes
# Actualiza el secret laravel-secrets con los valores actuales de Secret Manager

set -e

PROJECT_ID="authentic-prod-464216"
NAMESPACE="authenticfarma-candidatos"
SECRET_NAME="laravel-secrets"

echo "🔄 Sincronizando secrets desde Secret Manager..."

# Obtener valores actuales desde Secret Manager
echo "📥 Obteniendo valores desde Secret Manager..."

APP_KEY=$(gcloud secrets versions access latest --secret="authentic-candidatos-APP_KEY" --project=$PROJECT_ID)
DB_PASSWORD=$(gcloud secrets versions access latest --secret="authentic-candidatos-DB_PASSWORD" --project=$PROJECT_ID)
GOOGLE_CLIENT_SECRET=$(gcloud secrets versions access latest --secret="authentic-candidatos-GOOGLE_CLIENT_SECRET" --project=$PROJECT_ID)
MAIL_PASSWORD=$(gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_PASSWORD" --project=$PROJECT_ID)
MAIL_USERNAME=$(gcloud secrets versions access latest --secret="authentic-candidatos-MAIL_USERNAME" --project=$PROJECT_ID)

# DB_USERNAME no está en Secret Manager, usar valor existente
DB_USERNAME=$(kubectl get secret $SECRET_NAME -n $NAMESPACE -o jsonpath='{.data.DB_USERNAME}' | base64 -d)

echo "✅ Valores obtenidos desde Secret Manager"
echo "   APP_KEY: ${APP_KEY:0:20}..."
echo "   DB_PASSWORD: ${DB_PASSWORD:0:5}..."
echo "   DB_USERNAME: $DB_USERNAME"
echo "   MAIL_USERNAME: $MAIL_USERNAME"
echo "   MAIL_PASSWORD: ${MAIL_PASSWORD:0:5}..."
echo "   GOOGLE_CLIENT_SECRET: ${GOOGLE_CLIENT_SECRET:0:20}..."

# Actualizar el secret en Kubernetes
echo "🔧 Actualizando secret en Kubernetes..."

kubectl create secret generic $SECRET_NAME \
    --from-literal=APP_KEY="$APP_KEY" \
    --from-literal=DB_USERNAME="$DB_USERNAME" \
    --from-literal=DB_PASSWORD="$DB_PASSWORD" \
    --from-literal=MAIL_USERNAME="$MAIL_USERNAME" \
    --from-literal=MAIL_PASSWORD="$MAIL_PASSWORD" \
    --from-literal=GOOGLE_CLIENT_SECRET="$GOOGLE_CLIENT_SECRET" \
    --namespace=$NAMESPACE \
    --dry-run=client -o yaml | kubectl apply -f -

echo "✅ Secret $SECRET_NAME actualizado exitosamente en namespace $NAMESPACE"
echo "🔄 Reiniciando deployment para aplicar los nuevos valores..."

# Reiniciar deployment para que tome los nuevos valores
kubectl rollout restart deployment/authenticfarma-candidatos -n $NAMESPACE

echo "✅ Deployment reiniciado. Los pods usarán los valores actualizados de Secret Manager."