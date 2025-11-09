#!/bin/bash
# Monitoreo continuo de salud de candidatos

echo "🔍 Iniciando monitoreo continuo de candidatos..."
echo "Presiona Ctrl+C para detener"
echo ""

while true; do
    timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    # Test rápido de respuesta
    response=$(timeout 8 curl -s -o /dev/null -w "%{http_code}|%{time_total}" "https://candidatos.authenticfarma.com/login" 2>/dev/null)
    
    if [ $? -eq 124 ]; then
        echo "[$timestamp] ❌ TIMEOUT - Aplicación no responde"
    elif [ $? -ne 0 ]; then
        echo "[$timestamp] ❌ ERROR DE CONEXIÓN"
    else
        status_code=$(echo $response | cut -d'|' -f1)
        time_total=$(echo $response | cut -d'|' -f2)
        
        if [[ $status_code -ge 200 && $status_code -lt 400 ]]; then
            if (( $(echo "$time_total > 5.0" | bc -l) )); then
                echo "[$timestamp] ⚠️ LENTO: ${time_total}s"
            else
                echo "[$timestamp] ✅ OK: ${time_total}s"
            fi
        else
            echo "[$timestamp] ❌ HTTP $status_code"
        fi
    fi
    
    sleep 10
done
