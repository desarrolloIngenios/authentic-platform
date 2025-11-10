#!/bin/bash
echo "🔍 Monitoreando candidatos cada 10 segundos..."
echo "Presiona Ctrl+C para detener"
echo ""

while true; do
    timestamp=$(date '+%H:%M:%S')
    
    response=$(curl -s -o /dev/null -w "%{time_total}|%{http_code}" "https://candidatos.authenticfarma.com/login" --max-time 8 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        time_total=$(echo $response | cut -d'|' -f1)
        http_code=$(echo $response | cut -d'|' -f2)
        
        if (( $(echo "$time_total > 5.0" | bc -l) )); then
            echo "[$timestamp] 🐌 LENTO: ${time_total}s | HTTP $http_code"
        elif (( $(echo "$time_total > 2.0" | bc -l) )); then
            echo "[$timestamp] ⚠️ Moderado: ${time_total}s | HTTP $http_code"  
        else
            echo "[$timestamp] ✅ OK: ${time_total}s | HTTP $http_code"
        fi
    else
        echo "[$timestamp] ❌ TIMEOUT/ERROR"
    fi
    
    sleep 10
done
