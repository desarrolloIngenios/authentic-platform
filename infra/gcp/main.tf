# GCP Terraform skeleton - modules to be implemented
module "network" {
  source = "../modules/network"
  project_id = var.project_id
  region = var.region
}

module "gke" {
  source = "../modules/gke"
  project_id = var.project_id
  region = var.region
}

module "artifact_registry" {
  source = "../modules/artifact-registry"
  project_id = var.project_id
  region = var.region
}

module "cloudsql" {
  source = "../modules/cloudsql"
  project_id = var.project_id
  region = var.region
}
