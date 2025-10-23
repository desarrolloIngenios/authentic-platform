#!/bin/bash
set -e

echo "Creando estructura base..."

mkdir -p apps/{authenticfarma,yosoy,isyours,moodle-elearning,agents}
mkdir -p infra/{gcp,aws,common,k8s-manifests}
mkdir -p ci-cd/{gitlab-ci/templates,argo-apps,docs}
mkdir -p docs/{architecture,api,developers}
mkdir -p scripts/{build,deploy,migrate}

# Laravel Apps (base)
for app in authenticfarma yosoy isyours; do
  cat <<EOL > apps/$app/Dockerfile
FROM php:8.2-fpm
WORKDIR /var/www
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip
RUN docker-php-ext-install pdo pdo_mysql
COPY . /var/www
CMD ["php-fpm"]
EOL

  echo "APP_NAME=$app" > apps/$app/.env.example
  echo "# $app - Aplicación Laravel para el dominio correspondiente" > apps/$app/README.md
done

# Moodle
cat <<EOL > apps/moodle-elearning/Dockerfile
FROM bitnami/moodle:latest
EXPOSE 8080
EOL
echo "# Moodle eLearning Platform" > apps/moodle-elearning/README.md
touch apps/moodle-elearning/config.php

# Agents (Python IA)
cat <<EOL > apps/agents/Dockerfile
FROM python:3.11-slim
WORKDIR /app
COPY . .
RUN pip install -r requirements.txt
CMD ["python", "main.py"]
EOL

cat <<EOL > apps/agents/requirements.txt
fastapi
uvicorn
openai
requests
EOL

cat <<EOL > apps/agents/main.py
from fastapi import FastAPI

app = FastAPI()

@app.get("/")
def root():
    return {"message": "Agente IA de Authentic activo"}
EOL

echo "# Agents - Microservicios IA para reclutamiento inteligente" > apps/agents/README.md

# Terraform (GCP)
cat <<EOL > infra/gcp/main.tf
terraform {
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 6.0"
    }
  }
  backend "gcs" {
    bucket = "authentic-tfstate"
    prefix = "terraform/state"
  }
}

provider "google" {
  project = "authentic-prod-464216"
  region  = "us-central1"
}

module "gke" {
  source = "../common"
}
EOL

echo "# Infraestructura GCP con Terraform" > infra/gcp/README.md
touch infra/gcp/{variables.tf,outputs.tf}

# CI/CD
cat <<EOL > ci-cd/gitlab-ci/.gitlab-ci.yml
stages:
  - build
  - deploy

include:
  - local: 'ci-cd/gitlab-ci/templates/build.yml'
  - local: 'ci-cd/gitlab-ci/templates/deploy.yml'
EOL

cat <<EOL > ci-cd/gitlab-ci/templates/build.yml
build_app:
  stage: build
  script:
    - echo "Construyendo imágenes Docker..."
    - docker build -t \$CI_REGISTRY_IMAGE:\$CI_COMMIT_SHORT_SHA .
EOL

cat <<EOL > ci-cd/gitlab-ci/templates/deploy.yml
deploy_app:
  stage: deploy
  script:
    - echo "Desplegando aplicación en GKE mediante ArgoCD..."
EOL

# Scripts
echo '#!/bin/bash\necho "Building all apps..."' > scripts/build/build_all.sh
echo '#!/bin/bash\necho "Deploying all apps..."' > scripts/deploy/deploy_all.sh
echo '#!/bin/bash\necho "Migrating databases..."' > scripts/migrate/migrate_db.sh
chmod +x scripts/*/*.sh

echo "# Authentic Platform Starter Pack" > README.md

echo "✅ Starter Pack creado exitosamente."
