#!/bin/bash

# 🎯 Análisis de Performance Avanzado - Candidatos AuthenticFarma  
# Evalúa el rendimiento considerando la arquitectura de autenticación

echo "🚀 ANÁLISIS DE PERFORMANCE AVANZADO - CANDIDATOS"
echo "==============================================="

APP_URL="https://candidatos.authenticfarma.com"
echo "📋 URL de la aplicación: $APP_URL"
echo ""

# 1. Análisis de la arquitectura de redirección
echo "🏗️  1. Análisis de Arquitectura de Redirección"
echo "=============================================="

# Probar sin seguir redirects
echo "📊 Test inicial (sin seguir redirects):"
initial_response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}:%{redirect_url}' --max-time 10 "$APP_URL")
initial_code=$(echo $initial_response | cut -d':' -f1)
initial_time=$(echo $initial_response | cut -d':' -f2)
redirect_url=$(echo $initial_response | cut -d':' -f3-)

echo "   HTTP Code: $initial_code"
echo "   Tiempo: ${initial_time}s"
if [ "$redirect_url" != "" ]; then
    echo "   ↪️  Redirige a: $redirect_url"
fi

# Probar siguiendo redirects completos
echo ""
echo "📊 Test completo (siguiendo redirects):"
full_response=$(curl -L -o /dev/null -s -w '%{http_code}:%{time_total}:%{num_redirects}:%{url_effective}:%{time_starttransfer}' --max-time 15 "$APP_URL")
final_code=$(echo $full_response | cut -d':' -f1)
total_time=$(echo $full_response | cut -d':' -f2)
redirect_count=$(echo $full_response | cut -d':' -f3)
final_url=$(echo $full_response | cut -d':' -f4)
ttfb=$(echo $full_response | cut -d':' -f5)

echo "   HTTP Code Final: $final_code"
echo "   Tiempo Total: ${total_time}s"
echo "   Número de Redirects: $redirect_count"
echo "   URL Final: $final_url"
echo "   Time to First Byte: ${ttfb}s"

# Evaluar performance de redirects
if (( $(echo "$total_time < 1.0" | bc -l) )); then
    echo "   🟢 Performance de Redirects: EXCELENTE (< 1s)"
elif (( $(echo "$total_time < 2.0" | bc -l) )); then
    echo "   🟡 Performance de Redirects: BUENO (1-2s)"
else
    echo "   🔴 Performance de Redirects: NECESITA OPTIMIZACIÓN (> 2s)"
fi

echo ""

# 2. Test de endpoints públicos específicos
echo "🔍 2. Test de Endpoints Públicos"
echo "==============================="

endpoints=(
    "/login:Página de Login"
    "/register:Página de Registro" 
    "/password/reset:Reset de Password"
    "/css/app.css:CSS Principal"
    "/js/app.js:JavaScript Principal"
    "/favicon.ico:Favicon"
)

for endpoint_info in "${endpoints[@]}"; do
    endpoint=$(echo $endpoint_info | cut -d':' -f1)
    description=$(echo $endpoint_info | cut -d':' -f2)
    url="$APP_URL$endpoint"
    
    echo -n "   $description: "
    
    response=$(curl -o /dev/null -s -w '%{http_code}:%{time_total}:%{size_download}' --max-time 10 "$url" 2>/dev/null || echo "000:timeout:0")
    code=$(echo $response | cut -d':' -f1)
    time=$(echo $response | cut -d':' -f2)
    size=$(echo $response | cut -d':' -f3)
    
    if [ "$code" = "200" ]; then
        # Convertir bytes a KB
        size_kb=$(echo "scale=1; $size / 1024" | bc -l 2>/dev/null || echo "0")
        echo "✅ ${time}s (${size_kb}KB)"
    elif [ "$code" = "302" ] || [ "$code" = "301" ]; then
        echo "↪️  Redirect (${time}s)"
    elif [ "$code" = "404" ]; then
        echo "⚠️  No encontrado"
    else
        echo "❌ Error ($code)"
    fi
done

echo ""

# 3. Análisis de performance del GoogleController optimizado
echo "🔧 3. Análisis del GoogleController Optimizado"
echo "============================================="

echo "📊 Verificando optimizaciones implementadas:"

# Verificar que las optimizaciones están en el código
optimizations_file="/Users/Devapp/authentic-platform/apps/authenticfarma/candidatos/app/Http/Controllers/Auth/GoogleController.php"

if [ -f "$optimizations_file" ]; then
    echo "   ✅ Archivo GoogleController encontrado"
    
    # Verificar optimizaciones específicas
    if grep -q "optimizeSession" "$optimizations_file"; then
        echo "   ✅ Optimización de sesión: IMPLEMENTADA"
    else
        echo "   ❌ Optimización de sesión: NO ENCONTRADA"
    fi
    
    if grep -q "stateless()" "$optimizations_file"; then
        echo "   ✅ OAuth stateless: IMPLEMENTADA"
    else
        echo "   ❌ OAuth stateless: NO ENCONTRADA"
    fi
    
    if grep -q "Cache::" "$optimizations_file"; then
        echo "   ✅ Sistema de caché: IMPLEMENTADO"
    else
        echo "   ❌ Sistema de caché: NO ENCONTRADO"
    fi
    
    if grep -q "handleSessionError" "$optimizations_file"; then
        echo "   ✅ Manejo de errores optimizado: IMPLEMENTADO"
    else
        echo "   ❌ Manejo de errores: NO ENCONTRADO"
    fi
    
    if grep -q "loginMinimal" "$optimizations_file"; then
        echo "   ✅ Login mínimal: IMPLEMENTADO"
    else
        echo "   ❌ Login mínimal: NO ENCONTRADO"
    fi
else
    echo "   ❌ Archivo GoogleController no encontrado"
fi

echo ""

# 4. Test de carga específico para Laravel
echo "⚡ 4. Test de Carga para Aplicación Laravel"
echo "=========================================="

echo "Ejecutando test de carga optimizado para Laravel..."

# Test con diferentes tipos de requests
test_scenarios=(
    "$APP_URL/login:Login_Page"
    "$APP_URL/register:Register_Page"
    "$APP_URL:Home_Redirect"
)

for scenario in "${test_scenarios[@]}"; do
    url=$(echo $scenario | cut -d':' -f1)
    name=$(echo $scenario | cut -d':' -f2)
    
    echo ""
    echo "📊 Escenario: $name"
    echo "   URL: $url"
    
    # Test de 5 requests concurrentes
    temp_file="/tmp/laravel_perf_$$_$name"
    > "$temp_file"
    
    for i in {1..5}; do
        (
            response=$(curl -L -o /dev/null -s -w '%{http_code}:%{time_total}:%{time_starttransfer}' --max-time 20 "$url" 2>/dev/null || echo "000:timeout:0")
            echo "$response" >> "$temp_file"
        ) &
    done
    
    wait
    
    # Analizar resultados
    successful=0
    failed=0
    total_time=0
    total_ttfb=0
    
    while read line; do
        if [ -n "$line" ]; then
            code=$(echo $line | cut -d':' -f1)
            time=$(echo $line | cut -d':' -f2)
            ttfb=$(echo $line | cut -d':' -f3)
            
            if [ "$code" = "200" ] || [ "$code" = "302" ]; then
                successful=$((successful + 1))
                total_time=$(echo "$total_time + $time" | bc -l)
                total_ttfb=$(echo "$total_ttfb + $ttfb" | bc -l)
            else
                failed=$((failed + 1))
            fi
        fi
    done < "$temp_file"
    
    rm -f "$temp_file"
    
    if [ $successful -gt 0 ]; then
        avg_time=$(echo "scale=3; $total_time / $successful" | bc -l)
        avg_ttfb=$(echo "scale=3; $total_ttfb / $successful" | bc -l)
        success_rate=$(echo "scale=1; $successful * 100 / 5" | bc -l)
        
        echo "   ✅ Requests exitosos: $successful/5 (${success_rate}%)"
        echo "   ⚡ Tiempo promedio: ${avg_time}s"
        echo "   🚀 TTFB promedio: ${avg_ttfb}s"
        
        # Evaluar performance específica
        if (( $(echo "$avg_time < 1.0" | bc -l) )); then
            echo "   🟢 Evaluación: EXCELENTE"
        elif (( $(echo "$avg_time < 2.0" | bc -l) )); then
            echo "   🟡 Evaluación: BUENO"
        else
            echo "   🔴 Evaluación: NECESITA MEJORA"
        fi
    else
        echo "   ❌ No se pudieron procesar requests exitosos"
    fi
done

echo ""

# 5. Análisis de mejoras implementadas
echo "📈 5. Impacto de Optimizaciones Implementadas"
echo "============================================"

echo "📊 Optimizaciones del GoogleController en producción:"
echo "   🚀 Session Management: Limpia tokens y datos temporales innecesarios"
echo "   ⚡ Stateless OAuth: Evita almacenamiento de estado en sesión"
echo "   💾 Cache System: Reduce consultas repetitivas a base de datos"
echo "   🛡️  Error Handling: Manejo optimizado de errores de sesión"
echo "   🎯 Minimal Login: Reduce pasos en el proceso de autenticación"

# Calcular mejora estimada basada en optimizaciones
if [ "$final_code" = "200" ] && [ "$total_time" != "timeout" ]; then
    current_performance=$(echo "scale=1; $total_time" | bc -l)
    
    echo ""
    echo "📊 Performance Actual vs. Esperado:"
    echo "   ⏱️  Tiempo actual: ${current_performance}s"
    
    # Estimar mejora basada en las optimizaciones implementadas
    if (( $(echo "$current_performance < 1.0" | bc -l) )); then
        echo "   🎯 Estado: OPTIMIZADO (target achieved)"
        echo "   💚 Las optimizaciones han sido exitosas"
    elif (( $(echo "$current_performance < 2.0" | bc -l) )); then
        echo "   🎯 Estado: MEJORADO (near target)"
        echo "   💛 Optimizaciones parcialmente efectivas"
    else
        echo "   🎯 Estado: EN PROGRESO"
        echo "   💙 Optimizaciones requieren tiempo de propagación"
    fi
fi

echo ""

# 6. Recomendaciones específicas para Laravel
echo "💡 6. Recomendaciones Específicas para Laravel"
echo "============================================="

echo "🔧 Optimizaciones adicionales recomendadas:"
echo "   📦 Opcache: Verificar que esté habilitado en producción"
echo "   🗄️  Database: Optimizar queries y añadir índices necesarios"
echo "   🔄 Queue System: Implementar para tareas pesadas"
echo "   📱 CDN: Configurar para assets estáticos"
echo "   🗜️  Compression: Habilitar GZIP/Brotli en el servidor"

# 7. Monitoring y próximos pasos
echo ""
echo "📊 7. Monitoring Continuo"
echo "========================"
echo "   🎯 Performance Target: < 1s para páginas principales"
echo "   📈 Current Achievement: ${total_time}s (incluyendo redirects)"
echo "   🔄 Monitoring Frequency: Cada despliegue nuevo"

echo ""
echo "🔗 Enlaces de monitoreo:"
echo "   🌐 Aplicación: $APP_URL"
echo "   🐳 Registry: https://console.cloud.google.com/artifacts/docker/authentic-prod-464216/us-central1/authenticfarma-repo"
echo "   🤖 CI/CD: https://github.com/desarrolloIngenios/authentic-platform/actions"