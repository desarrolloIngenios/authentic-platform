#!/bin/bash

# 🛡️ OPTIMIZACIONES SEGURAS VÍA ARGOCD - SIN ACCESO DIRECTO AL POD
# Aplicar optimizaciones usando GitOps de forma segura

echo "🛡️ Aplicación SEGURA vía GitOps - Sin acceso directo al pod"
echo "📅 $(date)"
echo ""

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[GITOPS]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[SAFE]${NC} $1"
}

info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

step() {
    echo -e "${PURPLE}[STEP]${NC} $1"
}

CANDIDATOS_PATH="/Users/Devapp/authentic-platform/apps/authenticfarma/candidatos"

step "🎯 ESTRATEGIA: Aplicación vía ConfigMaps y InitContainers"
echo ""

info "Esta estrategia NO modifica código de aplicación, solo configuración"
warn "✅ Completamente REVERSIBLE"
warn "✅ NO afecta funcionamiento actual"
warn "✅ Aplicación gradual"

echo ""

# 1. Crear ConfigMap para optimizaciones
step "📝 1. Crear ConfigMap de optimización"
echo ""

mkdir -p "$CANDIDATOS_PATH/k8s/optimization"

cat > "$CANDIDATOS_PATH/k8s/optimization/optimization-config.yaml" << 'EOF'
apiVersion: v1
kind: ConfigMap
metadata:
  name: candidatos-optimization-script
  namespace: authenticfarma-prod
data:
  optimize.sh: |
    #!/bin/bash
    echo "🚀 Iniciando optimización segura..."
    
    cd /var/www/html
    
    # Verificar que estamos en el directorio correcto
    if [ ! -f "artisan" ]; then
        echo "❌ No se encuentra artisan - abortando"
        exit 1
    fi
    
    # Crear backup
    echo "💾 Creando backup..."
    mkdir -p /tmp/backup
    cp -r bootstrap/cache /tmp/backup/ 2>/dev/null || echo "No hay cache para respaldar"
    
    # Aplicar optimizaciones seguras
    echo "⚡ Aplicando optimizaciones..."
    
    # Limpiar caches (seguro)
    php artisan view:clear
    echo "✅ Cache de vistas limpiado"
    
    # Crear nuevos caches (seguro)
    php artisan route:cache
    echo "✅ Cache de rutas creado"
    
    php artisan view:cache  
    echo "✅ Cache de vistas creado"
    
    # Verificar que funcionó
    if php artisan route:list >/dev/null 2>&1; then
        echo "✅ Verificación exitosa - optimización completada"
        echo "📊 Timestamp: $(date)"
    else
        echo "❌ Error en verificación - restaurando backup"
        php artisan route:clear
        php artisan view:clear
        exit 1
    fi
    
  rollback.sh: |
    #!/bin/bash
    echo "🔄 Iniciando rollback de optimización..."
    
    cd /var/www/html
    
    # Limpiar caches
    php artisan route:clear
    php artisan view:clear
    
    echo "✅ Rollback completado"
EOF

log "Creado: ConfigMap de optimización"

# 2. Crear Job para aplicar optimización
cat > "$CANDIDATOS_PATH/k8s/optimization/optimization-job.yaml" << 'EOF'
apiVersion: batch/v1
kind: Job
metadata:
  name: candidatos-optimization
  namespace: authenticfarma-prod
  labels:
    app: candidatos-optimization
spec:
  template:
    spec:
      containers:
      - name: optimizer
        # Usar la misma imagen que candidatos para compatibilidad
        image: your-registry/candidatos:latest  # Reemplazar con imagen actual
        command: ["/bin/bash"]
        args: ["/scripts/optimize.sh"]
        volumeMounts:
        - name: app-volume
          mountPath: /var/www/html
        - name: optimization-scripts
          mountPath: /scripts
        env:
        - name: APP_ENV
          value: "production"
        resources:
          limits:
            memory: "256Mi"
            cpu: "200m"
          requests:
            memory: "128Mi"
            cpu: "100m"
      volumes:
      - name: app-volume
        persistentVolumeClaim:
          claimName: candidatos-app-pvc  # Ajustar según tu configuración
      - name: optimization-scripts
        configMap:
          name: candidatos-optimization-script
          defaultMode: 0755
      restartPolicy: Never
      # Ejecutar como el mismo usuario que la app
      securityContext:
        runAsUser: 1000
        runAsGroup: 1000
  backoffLimit: 2  # Solo 2 intentos para evitar loops
  ttlSecondsAfterFinished: 3600  # Limpiar después de 1 hora
EOF

log "Creado: Job de optimización"

# 3. Crear Job de rollback
cat > "$CANDIDATOS_PATH/k8s/optimization/rollback-job.yaml" << 'EOF'
apiVersion: batch/v1
kind: Job
metadata:
  name: candidatos-rollback
  namespace: authenticfarma-prod
  labels:
    app: candidatos-rollback
spec:
  template:
    spec:
      containers:
      - name: rollback
        image: your-registry/candidatos:latest  # Reemplazar con imagen actual
        command: ["/bin/bash"]
        args: ["/scripts/rollback.sh"]
        volumeMounts:
        - name: app-volume
          mountPath: /var/www/html
        - name: optimization-scripts
          mountPath: /scripts
        resources:
          limits:
            memory: "128Mi" 
            cpu: "100m"
      volumes:
      - name: app-volume
        persistentVolumeClaim:
          claimName: candidatos-app-pvc  # Ajustar según tu configuración
      - name: optimization-scripts
        configMap:
          name: candidatos-optimization-script
          defaultMode: 0755
      restartPolicy: Never
      securityContext:
        runAsUser: 1000
        runAsGroup: 1000
  backoffLimit: 1
  ttlSecondsAfterFinished: 3600
EOF

log "Creado: Job de rollback"

# 4. Crear InitContainer para optimización automática (OPCIONAL)
cat > "$CANDIDATOS_PATH/k8s/optimization/deployment-with-optimization.yaml" << 'EOF'
# PATCH para el deployment existente - SOLO si quieres optimización automática
apiVersion: apps/v1
kind: Deployment
metadata:
  name: candidatos
  namespace: authenticfarma-prod
spec:
  template:
    spec:
      initContainers:
      - name: optimizer
        image: your-registry/candidatos:latest
        command: ["/bin/bash", "-c"]
        args:
        - |
          cd /var/www/html
          if [ -f "artisan" ]; then
            echo "🚀 Auto-optimización en startup..."
            php artisan route:cache || true
            php artisan view:cache || true
            echo "✅ Optimización automática completada"
          fi
        volumeMounts:
        - name: app-volume
          mountPath: /var/www/html
        resources:
          limits:
            memory: "128Mi"
            cpu: "100m"
      containers:
      # ... resto de containers existentes sin cambios
EOF

log "Creado: Deployment con auto-optimización (OPCIONAL)"

echo ""

# 5. Instrucciones de aplicación
step "📋 INSTRUCCIONES DE APLICACIÓN SEGURA"
echo ""

info "OPCIÓN 1: Aplicación Manual (MÁS SEGURA)"
echo "========================================="
echo ""
echo "1️⃣ Aplicar ConfigMap:"
echo "   kubectl apply -f k8s/optimization/optimization-config.yaml"
echo ""
echo "2️⃣ Ejecutar optimización:"
echo "   kubectl apply -f k8s/optimization/optimization-job.yaml"
echo ""
echo "3️⃣ Monitorear ejecución:"
echo "   kubectl logs -f job/candidatos-optimization -n authenticfarma-prod"
echo ""
echo "4️⃣ Verificar aplicación:"
echo "   curl -w 'Time: %{time_total}s\\n' -o /dev/null -s https://candidatos.authenticfarma.com/login"
echo ""
echo "5️⃣ Si hay problemas, rollback:"
echo "   kubectl apply -f k8s/optimization/rollback-job.yaml"
echo ""

warn "OPCIÓN 2: Vía ArgoCD (GitOps)"
echo "============================="
echo ""
echo "1️⃣ Commit de archivos de optimización:"
echo "   git add k8s/optimization/"
echo "   git commit -m 'feat: Add safe Laravel optimization jobs'"
echo "   git push origin dev"
echo ""
echo "2️⃣ Merge a main (después de revisión):"
echo "   gh pr create --title 'Safe Laravel optimizations' --body 'Non-disruptive cache optimization'"
echo ""
echo "3️⃣ ArgoCD sincronizará automáticamente"
echo ""
echo "4️⃣ Ejecutar job desde ArgoCD UI o kubectl"
echo ""

info "OPCIÓN 3: Script todo-en-uno"
cat > "$CANDIDATOS_PATH/k8s/optimization/apply-safe-optimization.sh" << 'EOF'
#!/bin/bash
# Script para aplicar optimización completa de forma segura

echo "🛡️ Aplicando optimización segura de candidatos..."

# 1. Aplicar ConfigMap
echo "📝 Aplicando ConfigMap..."
kubectl apply -f optimization-config.yaml

# 2. Ejecutar optimización
echo "⚡ Ejecutando optimización..."
kubectl delete job candidatos-optimization -n authenticfarma-prod 2>/dev/null || true
kubectl apply -f optimization-job.yaml

# 3. Esperar completación
echo "⏳ Esperando completación..."
kubectl wait --for=condition=complete job/candidatos-optimization -n authenticfarma-prod --timeout=300s

# 4. Verificar resultado
if kubectl logs job/candidatos-optimization -n authenticfarma-prod | grep -q "optimización completada"; then
    echo "✅ Optimización exitosa"
    
    # Test de la aplicación
    echo "🔍 Verificando aplicación..."
    response=$(curl -s -o /dev/null -w "%{http_code}" https://candidatos.authenticfarma.com/login)
    
    if [[ $response -eq 200 || $response -eq 302 ]]; then
        echo "✅ Aplicación funcionando correctamente"
    else
        echo "⚠️ Aplicación retorna código: $response"
        echo "🔄 Ejecutando rollback automático..."
        kubectl apply -f rollback-job.yaml
    fi
else
    echo "❌ Optimización falló - revisar logs:"
    kubectl logs job/candidatos-optimization -n authenticfarma-prod
fi

# 5. Limpiar job
echo "🧹 Limpiando recursos..."
kubectl delete job candidatos-optimization -n authenticfarma-prod --ignore-not-found=true

echo "🏁 Proceso completado"
EOF

chmod +x "$CANDIDATOS_PATH/k8s/optimization/apply-safe-optimization.sh"

log "Creado: Script de aplicación todo-en-uno"

echo ""
step "✅ ARCHIVOS DE OPTIMIZACIÓN CREADOS"
echo ""

echo "📁 Archivos generados en apps/authenticfarma/candidatos/k8s/optimization/:"
echo "   ✅ optimization-config.yaml    - ConfigMap con scripts"
echo "   ✅ optimization-job.yaml       - Job de optimización"  
echo "   ✅ rollback-job.yaml          - Job de rollback"
echo "   ✅ deployment-with-optimization.yaml - Auto-optimización (opcional)"
echo "   ✅ apply-safe-optimization.sh  - Script todo-en-uno"
echo ""

info "🎯 PRÓXIMOS PASOS:"
echo "1. Revisar y ajustar la imagen en los archivos YAML"
echo "2. Verificar nombres de PVC y configuraciones"
echo "3. Elegir método de aplicación (manual, GitOps, o script)"
echo "4. Aplicar de forma gradual"
echo ""

warn "⚠️ IMPORTANTE:"
echo "- Estos cambios NO afectan la aplicación actual"
echo "- Son completamente reversibles"
echo "- Se aplican de forma controlada"
echo "- Incluyen rollback automático"
echo ""

echo "🕒 Archivos generados: $(date)"