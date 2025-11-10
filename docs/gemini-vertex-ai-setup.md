# 🤖 Configuración de Gemini (Vertex AI) para Authentic Platform

## 🔐 **SECRETOS REQUERIDOS EN GITHUB**

### Secretos a agregar en GitHub Settings > Secrets and variables > Actions:

```bash
# 1. Service Account Key para Vertex AI
VERTEX_AI_SERVICE_ACCOUNT_KEY='{
  "type": "service_account",
  "project_id": "tu-project-id",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "vertex-ai@tu-project-id.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token"
}'

# 2. Configuración de Vertex AI
VERTEX_AI_PROJECT_ID=tu-project-id
VERTEX_AI_LOCATION=us-central1
VERTEX_AI_MODEL=gemini-1.5-flash
```

### 🛠️ **Crear Service Account para Vertex AI:**

```bash
# 1. Crear service account
gcloud iam service-accounts create vertex-ai-service \
    --display-name="Vertex AI Service Account" \
    --project=tu-project-id

# 2. Otorgar permisos necesarios
gcloud projects add-iam-policy-binding tu-project-id \
    --member="serviceAccount:vertex-ai-service@tu-project-id.iam.gserviceaccount.com" \
    --role="roles/aiplatform.user"

gcloud projects add-iam-policy-binding tu-project-id \
    --member="serviceAccount:vertex-ai-service@tu-project-id.iam.gserviceaccount.com" \
    --role="roles/ml.developer"

# 3. Crear y descargar la key
gcloud iam service-accounts keys create vertex-ai-key.json \
    --iam-account=vertex-ai-service@tu-project-id.iam.gserviceaccount.com
```

## 🔧 **IMPLEMENTACIÓN EN CÓDIGO**

### Para Laravel (AuthenticFarma/Candidatos):

1. **Instalar Google Cloud AI Platform SDK**
2. **Configurar variables de entorno**
3. **Crear servicio de Gemini**
4. **Implementar en controladores**

### Para Node.js (Historia Clínica):

1. **Instalar @google-cloud/aiplatform**  
2. **Configurar autenticación**
3. **Crear cliente de Vertex AI**
4. **Implementar endpoints**

## 🚀 **CASOS DE USO SUGERIDOS**

### AuthenticFarma (Candidatos):
- **Análisis de CV**: Procesamiento inteligente de hojas de vida
- **Matching**: IA para matching candidato-puesto
- **Entrevistas**: Asistente para evaluación de candidatos

### Historia Clínica (YoSoy):
- **Diagnóstico asistido**: Análisis de síntomas
- **Resumen de consultas**: Generación automática de reportes
- **Recomendaciones**: Sugerencias de tratamiento

¿En qué aplicación quieres implementar Gemini primero?