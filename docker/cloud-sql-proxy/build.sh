#!/bin/bash

# Build and push Cloud SQL Proxy image to shared-images-repo
# This script ensures the image is built for the correct architecture

set -e

PROJECT_ID="authentic-prod-464216"
REGISTRY="us-central1-docker.pkg.dev"
REPO="shared-images-repo"
IMAGE_NAME="cloud-sql-proxy"
VERSION="2.8.0"

echo "Building Cloud SQL Proxy image for AMD64 architecture..."

# Build the image with explicit platform
docker build --platform linux/amd64 \
    -t ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:${VERSION} \
    -t ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:latest \
    /Users/Devapp/authentic-platform/docker/cloud-sql-proxy/

echo "Pushing image to Artifact Registry..."

# Push both tags
docker push ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:${VERSION}
docker push ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:latest

echo "✅ Cloud SQL Proxy image built and pushed successfully!"
echo "   Image: ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:${VERSION}"
echo "   Latest: ${REGISTRY}/${PROJECT_ID}/${REPO}/${IMAGE_NAME}:latest"