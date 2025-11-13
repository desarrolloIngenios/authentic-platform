# 🏗️ Authentic Platform - GitOps Multi-Cloud

Plataforma centralizada GitOps para gestionar todas las aplicaciones de Authentic con ArgoCD y Kubernetes.

## 🏢 Plataformas y Aplicaciones

### 1. **AuthenticFarma** 🏥
Empleabilidad para el sector farmacéutico
- **candidatos** - Sistema de gestión de RRHH y candidatos con IA
- **empresas** - Portal para empresas farmacéuticas
- **moodle-elearning** - Plataforma de e-learning especializada

### 2. **YoSoy** 👤
Empleabilidad para mujeres migrantes
- **historia-clinica** - Sistema de historias clínicas médicas
- **telemedicina** - Plataforma de consultas virtuales

### 3. **IsYours** 🏠
Empleabilidad para mujeres migrantes en USA
- **inmobiliaria** - Plataforma inmobiliaria

### 4. **AI Agents** 🤖
Agencia de agentes de IA para reclutamiento automatizado
- **orchestrator** - Coordinador de agentes
- **worker** - Procesadores de tareas

## 📁 Estructura del Repositorio

```
authentic-platform/
├── platforms/                    # ✅ Configuraciones GitOps principales
│   ├── authenticfarma/
│   │   └── candidatos/
│   │       └── k8s/             # Manifiestos Kubernetes
│   └── agents/
├── infra/                        # Infraestructura como código
│   ├── gcp/                     # Google Cloud Platform
│   ├── aws/                     # Amazon Web Services
│   └── k8s-manifests/           # Recursos base K8s
├── ci-cd/                       # Pipelines CI/CD
│   ├── argo-apps/               # Aplicaciones ArgoCD
│   └── gitlab-ci/               # Templates GitLab CI
├── docker/                      # Imágenes Docker personalizadas
│   └── cloud-sql-proxy/         # Proxy para Cloud SQL
└── scripts/                     # Scripts de automatización
```

## 🚀 Tecnologías

### **Orquestación**
- **Kubernetes (GKE)** - Orquestación de contenedores
- **ArgoCD** - GitOps y despliegue continuo
- **Helm** - Gestión de paquetes Kubernetes

### **Base de Datos**
- **Cloud SQL (MySQL)** - Base de datos principal
- **Redis** - Cache y sesiones

### **Servicios Cloud (GCP)**
- **Artifact Registry** - Registro de imágenes Docker
- **Cloud Storage** - Almacenamiento de archivos
- **Secret Manager** - Gestión segura de credenciales
- **Cloud Load Balancing** - Balanceador de carga
- **Cloud DNS** - Gestión de dominios

### **IA y Machine Learning**
- **Vertex AI** - Plataforma de IA de Google
- **Gemini 1.5 Flash** - Modelo de IA generativa
- **Google Cloud AI** - Servicios de IA

## ⚙️ Configuración del Entorno

### **Prerrequisitos**
```bash
# Herramientas necesarias
kubectl
docker
gcloud
argocd
helm
```

## 🔄 GitOps con ArgoCD

### **Aplicaciones Configuradas**
- **authenticfarma-candidatos** - `platforms/authenticfarma/candidatos/k8s/`
- **yosoy-historia-clinica** - `platforms/yosoy/historia-clinica/k8s/`
- **isyours** - `platforms/isyours/k8s/`

### **Comandos ArgoCD**
```bash
# Ver estado de aplicaciones
kubectl get applications -n argocd

# Sincronizar aplicación
kubectl patch application authenticfarma-candidatos -n argocd \
  --type merge -p '{"operation":{"sync":{"revision":"HEAD"}}}'

# Forzar refresh
kubectl annotate application authenticfarma-candidatos -n argocd \
  argocd.argoproj.io/refresh=hard --overwrite
```

## 🛠️ Desarrollo y Deployment

### **Build de Imagen Docker**
```bash
# Ejemplo: Candidatos
cd apps/authenticfarma/candidatos
docker build -t $REGION-docker.pkg.dev/$PROJECT_ID/authenticfarma-repo/authentic-candidatos:latest .
docker push $REGION-docker.pkg.dev/$PROJECT_ID/authenticfarma-repo/authentic-candidatos:latest
```

### **Deployment Manual**
```bash
# Aplicar manifiestos
kubectl apply -f platforms/authenticfarma/candidatos/k8s/

# Verificar deployment
kubectl get pods -n authenticfarma-candidatos
kubectl logs -f deployment/authentic-candidatos -n authenticfarma-candidatos
```

### **CI/CD Pipeline**
El pipeline automático se ejecuta en:
1. **Build** - Construcción de imagen Docker
2. **Test** - Validación de aplicación
3. **Deploy** - Actualización de manifiestos GitOps
4. **Sync** - ArgoCD aplica cambios automáticamente

## 🔐 Gestión de Secretos

### **Secret Manager (GCP)**
```bash
# Crear secreto
gcloud secrets create laravel-app-key --data-file=key.txt

# Sincronizar a Kubernetes
kubectl create secret generic laravel-secrets \
  --from-literal=APP_KEY="$(gcloud secrets versions access latest --secret=laravel-app-key)" \
  --namespace=authenticfarma-candidatos
```

### **Secretos Principales**
- `laravel-secrets` - Credenciales de aplicación
- `vertex-ai-secrets` - Credenciales de IA
- `mysql-credentials` - Acceso a base de datos

## 🌐 Dominios y SSL

### **Dominios Configurados**
- `candidatos.authenticfarma.com`
- `yosoy.historia-clinica.com`
- `isyours.platform.com`

### **Certificados SSL**
Gestionados automáticamente con:
- **Google Managed Certificates**
- **Let's Encrypt** (fallback)

## 📊 Monitoreo y Observabilidad

### **Health Checks**
```bash
# Estado de aplicaciones
kubectl get pods -A
kubectl get applications -n argocd

# Logs de aplicación
kubectl logs -f deployment/authentic-candidatos -n authenticfarma-candidatos -c app
```

### **Métricas**
- **HPA** - Auto-escalado horizontal
- **Metrics Server** - Métricas de recursos
- **Google Cloud Monitoring** - Observabilidad completa

## 🔄 Portabilidad Multi-Cloud

### **AWS Migration Ready**
- Terraform modules en `infra/aws/`
- Configuración de EKS
- RDS para MySQL
- ECR para imágenes

### **Estructura Cloud-Agnostic**
- Kubernetes estándar
- Helm charts
- GitOps patterns
- Container registry abstraction

## 🆘 Troubleshooting

### **Problemas Comunes**

**ArgoCD Degraded**
```bash
# Verificar HPA target
kubectl describe hpa -n authenticfarma-candidatos

# Corregir deployment name
kubectl patch hpa authenticfarma-candidatos-hpa -n authenticfarma-candidatos \
  -p '{"spec":{"scaleTargetRef":{"name":"authentic-candidatos"}}}'
```

**Base de Datos Connection**
```bash
# Verificar Cloud SQL Proxy
kubectl logs deployment/authentic-candidatos -n authenticfarma-candidatos -c cloud-sql-proxy

# Verificar credenciales
kubectl get secret laravel-secrets -n authenticfarma-candidatos -o yaml
```

**Vertex AI Issues**
```bash
# Verificar credenciales montadas
kubectl exec deployment/authentic-candidatos -n authenticfarma-candidatos -c app -- \
  ls -la /var/www/storage/app/credentials/
```

## 📝 Backup y Recovery

### **Configuración de Backup**
Sistema completo de backup disponible en `backups/authenticfarma-candidatos-working-config/`

```bash
# Restaurar configuración
cd backups/authenticfarma-candidatos-working-config/
./restore-config.sh
```

## 📞 Soporte

Para soporte técnico:
- **Documentación**: `docs/`
- **Arquitectura**: Este README
- **Issues**: GitHub Issues
- **CI/CD**: `ci-cd/docs/`

---

**Última actualización**: Noviembre 2025  
**Versión**: 2.0 - GitOps + Multi-Cloud  
**Estado**: ✅ Producción - Todas las aplicaciones funcionando
kind: ExternalSecret
metadata:
  name: db-credentials
spec:
  refreshInterval: 1h
  secretStoreRef:
    name: gcp-secret-store
    kind: SecretStore
  target:
    name: db-credentials
    creationPolicy: Owner
  data:
    - secretKey: DB_PASSWORD
      remoteRef:
        key: my-database-password
```

---

### 7️⃣ Observabilidad y monitoreo

* **Cloud Monitoring + Logging** para métricas y logs.
* Health & Readiness probes en cada deployment.
* Dashboards por app y alertas en errores críticos.

---

### 8️⃣ Consideraciones multi-nube (portabilidad)

* Mantener módulos Terraform separados por proveedor (`infra/gcp`, `infra/aws`)
* Usar **variables y outputs comunes** (`infra/common`) para no acoplar código a un proveedor
* CI/CD y ArgoCD consumen manifiestos K8s que son **agnósticos del proveedor**
* Para migrar, reemplazar módulos Terraform y endpoints de Artifact Registry/ECR.

---

### 9️⃣ Roadmap de migración incremental

1. Migrar **AuthenticFarma** → CI/CD + ArgoCD
2. Migrar **YoSoy, IsYours, Moodle**
3. Agregar **agencia de agentes IA**
4. Configurar Blue/Green o Canary deployment con **Argo Rollouts**
5. Implementar Observabilidad avanzada y SLOs

---

### 🔐 Buenas prácticas

* Mínimo privilegio en Service Accounts
* No almacenar secretos en repos repositorio
* Versionar Docker images con SHA
* Usar namespaces y etiquetas en Kubernetes para control de recursos

---

### 🤝 Contribuciones

* Crear **feature branch** por tarea
* PR → revisión → merge a `main`
* Pipeline CI se ejecuta automáticamente
* Manifiestos actualizados automáticamente para GitOps

---

### 📌 Lista de checkpoints iniciales

* [ ] Infra GCP provisionada
* [ ] ArgoCD instalado y App of Apps configurado
* [ ] CI/CD build + push funcionando
* [ ] App de prueba desplegada en staging
* [ ] Secret Manager + External Secrets funcionando

```

---

Si quieres, el siguiente paso que puedo hacer es **generarte un ZIP del repositorio base** ya con esta estructura, Dockerfile, k8s manifests y pipeline GitHub Actions listo para copiar y usar, listo para tu proyecto `authentic-platform`.  

¿Quieres que haga eso?
```

