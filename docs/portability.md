# Portability notes

- Terraform modules are split per-provider (gcp/ and aws/). Implement provider-specific resources in their module folders.
- Keep Kubernetes manifests provider-agnostic (use LoadBalancer type or Ingress depending on environment).
- Use image registries that exist across clouds (Artifact Registry on GCP, ECR on AWS); keep image names as variables in CI.
- Abstract secrets with Secret Manager (GCP) and AWS Secrets Manager (AWS) and use External Secrets Operator to sync to k8s.
