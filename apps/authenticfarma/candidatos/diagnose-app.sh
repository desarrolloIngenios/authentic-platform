#!/bin/bash

# Script de diagnóstico para candidatos app
echo "🔍 Diagnóstico de la aplicación candidatos"
echo "=========================================="

# Verificar archivos críticos
echo "📁 Verificando archivos críticos..."
critical_files=(
    "composer.json"
    "routes/web.php"
    ".env.example"
    "docker/run.sh"
    "docker/supervisor.conf"
    "docker/nginx.conf"
    "docker/php.ini"
)

for file in "${critical_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file existe"
    else
        echo "❌ $file FALTA"
    fi
done

# Verificar directorios
echo ""
echo "📂 Verificando directorios..."
dirs=(
    "app/Http/Controllers"
    "app/Models"
    "app/Services"
    "config"
    "routes"
    "storage"
    "bootstrap/cache"
)

for dir in "${dirs[@]}"; do
    if [ -d "$dir" ]; then
        echo "✅ $dir existe"
    else
        echo "❌ $dir FALTA"
    fi
done

# Buscar referencias a clases eliminadas
echo ""
echo "🔎 Buscando referencias problemáticas..."
if grep -r "GeminiService\|AIActivity\|GeminiController" app/ config/ routes/ 2>/dev/null; then
    echo "❌ Encontradas referencias a clases de IA eliminadas"
else
    echo "✅ No se encontraron referencias problemáticas"
fi

# Verificar composer.json
echo ""
echo "📦 Verificando dependencias..."
if grep -q "google/cloud" composer.json; then
    echo "⚠️ Todavía hay dependencias de Google Cloud"
    grep "google/cloud" composer.json
else
    echo "✅ No hay dependencias problemáticas de Google Cloud"
fi

echo ""
echo "🎯 Diagnóstico completado!"