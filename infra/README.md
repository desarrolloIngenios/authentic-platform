# Infraestructura Base - Authentic Platform
# Orden de instalación para cluster GKE Autopilot

## 1. NGINX Ingress Controller
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.2/deploy/static/provider/cloud/deploy.yaml

## 2. Cert-Manager (configuración específica para GKE Autopilot)
# Service Accounts
kubectl apply -f infra/cert-manager/cert-manager-gke-serviceaccounts.yaml

# Cert-Manager vía Helm
helm repo add jetstack https://charts.jetstack.io
helm repo update
helm install cert-manager jetstack/cert-manager \
  --namespace cert-manager \
  --create-namespace \
  --version v1.13.2 \
  --set installCRDs=true \
  --set global.leaderElection.namespace=cert-manager \
  --set serviceAccount.create=false \
  --set serviceAccount.name=cert-manager \
  --set cainjector.serviceAccount.create=false \
  --set cainjector.serviceAccount.name=cert-manager-cainjector \
  --set webhook.serviceAccount.create=false \
  --set webhook.serviceAccount.name=cert-manager-webhook \
  --set "extraArgs={--cluster-resource-namespace=cert-manager,--leader-election-namespace=cert-manager}"

## 3. ClusterIssuer para Let's Encrypt
kubectl apply -f infra/cert-manager/cert-manager-issuer.yaml

## 4. SSL Certificates
kubectl apply -f infra/ssl-certificates/ssl-certificates.yaml

## 5. NGINX Ingress para aplicaciones
kubectl apply -f infra/nginx-ingress/

## 6. ArgoCD Applications (automatización)
kubectl apply -f infra/argocd/applications/