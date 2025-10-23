# AWS Terraform skeleton - modules to be implemented
module "vpc" {
  source = "../modules/vpc"
}

module "eks" {
  source = "../modules/eks"
}

module "ecr" {
  source = "../modules/ecr"
}

module "rds" {
  source = "../modules/rds"
}
