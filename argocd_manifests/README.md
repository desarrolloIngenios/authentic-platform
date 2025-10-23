Argo CD Manifests for Authentic Platform
=======================================

Files in this bundle:
- project.yaml            -> AppProject for the platform
- app-of-apps.yaml        -> Root App that points to ci-cd/argo-apps in authentic-platform repo
- apps/*.yaml             -> Application manifests for each product (authenticfarma, yosoy, isyours)
- apps/k8s/...           -> Example k8s manifests for each app (deployment/service)

IMPORTANT:
- Replace all repoURL placeholders with your actual repository URLs.
- If your repos are private, register the GitHub repo with ArgoCD using a repo credential (username+PAT or SSH key).
- Apply these manifests to the cluster where ArgoCD is installed (namespace: argocd).

Apply sequence example:
1. kubectl create namespace argocd
2. kubectl apply -n argocd -f project.yaml
3. kubectl apply -n argocd -f apps/authenticfarma.yaml
4. kubectl apply -n argocd -f apps/yosoy.yaml
5. kubectl apply -n argocd -f apps/isyours.yaml
6. kubectl apply -n argocd -f app-of-apps.yaml

Recommended workflow:
- Your CI (GitHub Actions) builds images and pushes to Artifact Registry.
- CI updates the deployment manifests in the corresponding deploy repo (e.g. authenticfarma-deploy).
- ArgoCD detects changes in the deploy repo and syncs to the cluster.
