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
