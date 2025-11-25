# Manifiesto para crear el secreto DB_USERNAME en Google Secret Manager usando Terraform
# Guarda este archivo como db-username-secret.tf y aplícalo con terraform apply

resource "google_secret_manager_secret" "db_username" {
  secret_id = "authentic-candidatos-DB_USERNAME"
  replication {
    automatic = true
  }
}

resource "google_secret_manager_secret_version" "db_username_version" {
  secret      = google_secret_manager_secret.db_username.id
  secret_data = "candidatosuser"
}
