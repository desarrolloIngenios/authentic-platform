#!/bin/bash

# 🚀 Script de Monitoreo de Performance - Candidatos AuthenticFarma
# Evalúa el rendimiento de la aplicación en producción

echo "🎯 MONITOREO DE PERFORMANCE - CANDIDATOS"
echo "========================================"

# Configuración
APP_URL="https://candidatos.authenticfarma.com"
HEALTH_ENDPOINT="$APP_URL/health"
LOGIN_ENDPOINT="$APP_URL/login"
API_ENDPOINT="$APP_URL/api/health"

echo "📋 Configuración:"
echo "  URL Principal: $APP_URL"
echo "  Health Check: $HEALTH_ENDPOINT"
echo "  Login: $LOGIN_ENDPOINT"
echo ""

# 1. Test de conectividad básica
echo "🌐 1. Test de Conectividad Básica"
echo "================================="
response_time=$(curl -o /dev/null -s -w '%{time_total}' --max-time 10 "$APP_URL" 2>/dev/null || echo "timeout")
http_code=$(curl -o /dev/null -s -w '%{http_code}' --max-time 10 "$APP_URL" 2>/dev/null || echo "000")

if [ "$http_code" = "200" ]; then
    echo "✅ Conectividad: OK ($http_code)"
    echo "⚡ Tiempo de respuesta: ${response_time}s"
    
    if (( $(echo "$response_time < 2.0" | bc -l) )); then
        echo "🟢 Performance: EXCELENTE (< 2s)"
    elif (( $(echo "$response_time < 5.0" | bc -l) )); then
        echo "🟡 Performance: BUENO (2-5s)"
    else
        echo "🔴 Performance: NECESITA OPTIMIZACIÓN (> 5s)"
    fi
else
    echo "❌ Conectividad: FALLO ($http_code)"
    echo "⚠️  La aplicación podría estar caída o inaccesible"
fi

echo ""

# 2. Test de múltiples requests (carga ligera)
echo "⚡ 2. Test de Carga Ligera (5 requests)"
echo "====================================="
total_time=0
successful_requests=0
failed_requests=0

for i in {1..5}; do
    echo -n "   Request $i: "
    start_time=$(date +%s.%N)
    response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 15 "$APP_URL" 2>/dev/null || echo "000:timeout")
    end_time=$(date +%s.%N)
    
    http_code=$(echo $response | cut -d':' -f1)
    time_taken=$(echo $response | cut -d':' -f2)
    
    if [ "$http_code" = "200" ]; then
        echo "✅ ${time_taken}s (HTTP $http_code)"
        total_time=$(echo "$total_time + $time_taken" | bc -l)
        successful_requests=$((successful_requests + 1))
    else
        echo "❌ FALLO (HTTP $http_code)"
        failed_requests=$((failed_requests + 1))
    fi
done

if [ $successful_requests -gt 0 ]; then
    avg_time=$(echo "scale=3; $total_time / $successful_requests" | bc -l)
    success_rate=$(echo "scale=1; $successful_requests * 100 / 5" | bc -l)
    
    echo ""
    echo "📊 Resultados de Carga Ligera:"
    echo "   ✅ Requests exitosos: $successful_requests/5 (${success_rate}%)"
    echo "   ⚡ Tiempo promedio: ${avg_time}s"
    echo "   ❌ Requests fallidos: $failed_requests/5"
fi

echo ""

# 3. Test de endpoints específicos
echo "🔍 3. Test de Endpoints Específicos"
echo "==================================="

# Test del login page
echo -n "   Login Page: "
login_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 10 "$LOGIN_ENDPOINT" 2>/dev/null || echo "000:timeout")
login_code=$(echo $login_response | cut -d':' -f1)
login_time=$(echo $login_response | cut -d':' -f2)

if [ "$login_code" = "200" ]; then
    echo "✅ ${login_time}s (HTTP $login_code)"
else
    echo "❌ FALLO (HTTP $login_code)"
fi

# Test de health check (si existe)
echo -n "   Health Check: "
health_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 5 "$HEALTH_ENDPOINT" 2>/dev/null || echo "000:timeout")
health_code=$(echo $health_response | cut -d':' -f1)
health_time=$(echo $health_response | cut -d':' -f2)

if [ "$health_code" = "200" ]; then
    echo "✅ ${health_time}s (HTTP $health_code)"
elif [ "$health_code" = "404" ]; then
    echo "⚠️  Endpoint no disponible (HTTP $health_code)"
else
    echo "❌ FALLO (HTTP $health_code)"
fi

echo ""

# 4. Test de recursos estáticos (CSS/JS)
echo "🎨 4. Test de Recursos Estáticos"
echo "==============================="

# Test CSS
echo -n "   CSS Assets: "
css_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 5 "$APP_URL/css/app.css" 2>/dev/null || echo "000:timeout")
css_code=$(echo $css_response | cut -d':' -f1)
css_time=$(echo $css_response | cut -d':' -f2)

if [ "$css_code" = "200" ]; then
    echo "✅ ${css_time}s"
elif [ "$css_code" = "404" ]; then
    echo "⚠️  Assets compilados (Vite/Laravel Mix)"
else
    echo "❌ Error cargando CSS ($css_code)"
fi

# Test JS
echo -n "   JS Assets: "
js_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 5 "$APP_URL/js/app.js" 2>/dev/null || echo "000:timeout")
js_code=$(echo $js_response | cut -d':' -f1)
js_time=$(echo $js_response | cut -d':' -f2)

if [ "$js_code" = "200" ]; then
    echo "✅ ${js_time}s"
elif [ "$js_code" = "404" ]; then
    echo "⚠️  Assets compilados (Vite/Laravel Mix)"
else
    echo "❌ Error cargando JS ($js_code)"
fi

echo ""

# 5. Análisis de headers HTTP
echo "📡 5. Análisis de Headers HTTP"
echo "============================="
echo "Obteniendo headers del servidor..."

headers=$(curl -I -s --max-time 10 "$APP_URL" 2>/dev/null || echo "Error obteniendo headers")

if [ "$headers" != "Error obteniendo headers" ]; then
    server=$(echo "$headers" | grep -i "server:" | cut -d' ' -f2- | tr -d '\r')
    content_type=$(echo "$headers" | grep -i "content-type:" | cut -d' ' -f2- | tr -d '\r')
    cache_control=$(echo "$headers" | grep -i "cache-control:" | cut -d' ' -f2- | tr -d '\r')
    
    echo "   🖥️  Servidor: ${server:-No detectado}"
    echo "   📄 Content-Type: ${content_type:-No especificado}"
    echo "   💾 Cache-Control: ${cache_control:-No configurado}"
    
    # Verificar HTTPS
    if echo "$headers" | grep -qi "strict-transport-security"; then
        echo "   🔒 HTTPS: ✅ Con HSTS"
    else
        echo "   🔒 HTTPS: ⚠️  Sin HSTS configurado"
    fi
    
    # Verificar compresión
    if echo "$headers" | grep -qi "content-encoding.*gzip"; then
        echo "   🗜️  Compresión: ✅ GZIP habilitado"
    else
        echo "   🗜️  Compresión: ⚠️  GZIP no detectado"
    fi
else
    echo "   ❌ No se pudieron obtener headers HTTP"
fi

echo ""

# 6. Test de carga simulada (si curl soporta parallel)
echo "🏋️  6. Test de Carga Simulada (Concurrencia)"
echo "==========================================="

concurrent_requests=10
echo "Ejecutando $concurrent_requests requests concurrentes..."

# Crear archivo temporal para resultados
temp_file="/tmp/perf_test_$$"
> "$temp_file"

# Ejecutar requests concurrentes
for i in $(seq 1 $concurrent_requests); do
    (
        response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}' --max-time 20 "$APP_URL" 2>/dev/null || echo "000:timeout")
        echo "$response" >> "$temp_file"
    ) &
done

# Esperar a que terminen todos
wait

# Analizar resultados
successful_concurrent=0
failed_concurrent=0
total_concurrent_time=0

while read line; do
    code=$(echo $line | cut -d':' -f1)
    time=$(echo $line | cut -d':' -f2)
    
    if [ "$code" = "200" ]; then
        successful_concurrent=$((successful_concurrent + 1))
        total_concurrent_time=$(echo "$total_concurrent_time + $time" | bc -l)
    else
        failed_concurrent=$((failed_concurrent + 1))
    fi
done < "$temp_file"

rm -f "$temp_file"

concurrent_success_rate=$(echo "scale=1; $successful_concurrent * 100 / $concurrent_requests" | bc -l)

echo "📊 Resultados de Carga Concurrente:"
echo "   ✅ Requests exitosos: $successful_concurrent/$concurrent_requests (${concurrent_success_rate}%)"
echo "   ❌ Requests fallidos: $failed_concurrent/$concurrent_requests"

if [ $successful_concurrent -gt 0 ]; then
    avg_concurrent_time=$(echo "scale=3; $total_concurrent_time / $successful_concurrent" | bc -l)
    echo "   ⚡ Tiempo promedio: ${avg_concurrent_time}s"
    
    # Análisis de performance bajo carga
    if (( $(echo "$concurrent_success_rate >= 90" | bc -l) )) && (( $(echo "$avg_concurrent_time < 5.0" | bc -l) )); then
        echo "   🟢 Performance bajo carga: EXCELENTE"
    elif (( $(echo "$concurrent_success_rate >= 80" | bc -l) )) && (( $(echo "$avg_concurrent_time < 10.0" | bc -l) )); then
        echo "   🟡 Performance bajo carga: ACEPTABLE"
    else
        echo "   🔴 Performance bajo carga: NECESITA OPTIMIZACIÓN"
    fi
fi

echo ""

# 7. Resumen final y recomendaciones
echo "🎯 RESUMEN FINAL DE PERFORMANCE"
echo "==============================="

# Calcular score general
score=0

# Conectividad (30 puntos)
if [ "$http_code" = "200" ]; then
    score=$((score + 30))
fi

# Tiempo de respuesta (25 puntos)
if [ "$response_time" != "timeout" ] && (( $(echo "$response_time < 2.0" | bc -l) )); then
    score=$((score + 25))
elif [ "$response_time" != "timeout" ] && (( $(echo "$response_time < 5.0" | bc -l) )); then
    score=$((score + 15))
elif [ "$response_time" != "timeout" ] && (( $(echo "$response_time < 10.0" | bc -l) )); then
    score=$((score + 5))
fi

# Estabilidad bajo carga (25 puntos)
if (( $(echo "$concurrent_success_rate >= 90" | bc -l) )); then
    score=$((score + 25))
elif (( $(echo "$concurrent_success_rate >= 80" | bc -l) )); then
    score=$((score + 15))
elif (( $(echo "$concurrent_success_rate >= 70" | bc -l) )); then
    score=$((score + 10))
fi

# Configuración de servidor (20 puntos)
if [ "$server" != "" ]; then
    score=$((score + 10))
fi
if echo "$headers" | grep -qi "gzip\|br"; then
    score=$((score + 10))
fi

echo "📊 SCORE DE PERFORMANCE: $score/100"

if [ $score -ge 80 ]; then
    echo "🟢 ESTADO: EXCELENTE"
    echo "   La aplicación está funcionando de manera óptima"
elif [ $score -ge 60 ]; then
    echo "🟡 ESTADO: BUENO"
    echo "   Performance aceptable con oportunidades de mejora"
elif [ $score -ge 40 ]; then
    echo "🟠 ESTADO: REGULAR"
    echo "   Se necesitan optimizaciones importantes"
else
    echo "🔴 ESTADO: CRÍTICO" 
    echo "   Requiere atención inmediata"
fi

echo ""
echo "💡 RECOMENDACIONES:"

if [ "$response_time" != "timeout" ] && (( $(echo "$response_time > 3.0" | bc -l) )); then
    echo "   🚀 Optimizar tiempo de respuesta del servidor"
    echo "   📋 Revisar GoogleController.php para sesiones"
fi

if (( $(echo "$concurrent_success_rate < 90" | bc -l) )); then
    echo "   ⚖️  Mejorar estabilidad bajo carga concurrente"
    echo "   🔧 Considerar optimizaciones de base de datos"
fi

if ! echo "$headers" | grep -qi "gzip\|br"; then
    echo "   🗜️  Habilitar compresión GZIP en el servidor"
fi

if ! echo "$headers" | grep -qi "strict-transport-security"; then
    echo "   🔒 Configurar HSTS para mayor seguridad"
fi

echo ""
echo "🔗 ENLACES PARA MONITOREO:"
echo "   📊 Aplicación: $APP_URL"
echo "   🐳 Artifact Registry: https://console.cloud.google.com/artifacts/docker/authentic-prod-464216/us-central1/authenticfarma-repo"
echo "   ⚙️  GitHub Actions: https://github.com/desarrolloIngenios/authentic-platform/actions"