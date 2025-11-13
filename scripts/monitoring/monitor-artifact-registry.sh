#!/bin/bash

set -e

PROJECT_ID="authentic-prod-464216"
REGION="us-central1"
REPO_NAME="authenticfarma-repo"
IMAGE_NAME="authentic-candidatos"

echo "🔍 Monitoring Artifact Registry for candidatos images..."
echo "=================================================="

# Verificar autenticación
echo "📋 Checking authentication..."
gcloud auth list --filter=status:ACTIVE --format="value(account)"

echo -e "\n🏗️ Checking latest images in Artifact Registry..."
echo "Repository: $REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME"

# Listar las últimas 10 imágenes
echo -e "\n📦 Recent images:"
gcloud artifacts docker images list \
    $REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME \
    --sort-by="~UPDATE_TIME" \
    --limit=10 \
    --format="table(IMAGE:label=FULL_IMAGE_NAME,UPDATE_TIME:label=UPDATED,TAGS:label=TAGS)"

echo -e "\n🏷️ Checking specific tags..."
for tag in "latest" "dev-latest" "v4.2.0"; do
    echo "Checking tag: $tag"
    gcloud artifacts docker images list \
        $REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME:$tag \
        --format="table(IMAGE,UPDATE_TIME,TAGS)" 2>/dev/null || echo "  ❌ Tag $tag not found"
done

echo -e "\n🔄 Current Kubernetes deployment image:"
kubectl get deployment authentic-candidatos -n authenticfarma -o jsonpath='{.spec.template.spec.containers[0].image}' 2>/dev/null || echo "Deployment not found or not accessible"

echo -e "\n\n💡 To monitor in real-time:"
echo "watch -n 30 'gcloud artifacts docker images list $REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME/$IMAGE_NAME --sort-by=\"~UPDATE_TIME\" --limit=5'"

echo -e "\n📊 Repository summary:"
gcloud artifacts repositories describe $REPO_NAME --location=$REGION --format="table(name,format,createTime,updateTime)"