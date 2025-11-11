#!/bin/bash

# Script de monitoreo del deployment de candidatos
echo "🚀 Monitoring AuthenticFarma Candidatos Deployment"
echo "=================================================="
echo "⏰ Started at: $(date)"
echo ""

# Información del commit deployado
echo "📋 Deployment Info:"
echo "   Commit: fe17f90 (HTTP 500 fixes + AI rollback)"
echo "   Branch: main → production"
echo "   Target: https://candidatos.authenticfarma.com"
echo ""

# Variables
URL="https://candidatos.authenticfarma.com"
MAX_ATTEMPTS=30
SLEEP_INTERVAL=20
attempt=1

echo "🔍 Starting health checks every ${SLEEP_INTERVAL}s..."
echo ""

while [ $attempt -le $MAX_ATTEMPTS ]; do
    echo "🧪 Attempt $attempt/$MAX_ATTEMPTS - $(date +%H:%M:%S)"
    
    # Test the main page
    response=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 10 --max-time 20 "$URL" 2>/dev/null)
    
    if [ $? -eq 0 ]; then
        case $response in
            200)
                echo "✅ SUCCESS! Site is responding with HTTP $response"
                echo "🎉 Deployment completed successfully!"
                echo "📊 Final status: candidatos.authenticfarma.com is ONLINE"
                echo "⏰ Completed at: $(date)"
                exit 0
                ;;
            500)
                echo "❌ Still getting HTTP 500 (Internal Server Error)"
                ;;
            502|503|504)
                echo "⏳ Getting HTTP $response (Service temporarily unavailable - deployment in progress)"
                ;;
            *)
                echo "⚠️  Getting HTTP $response (Unexpected response)"
                ;;
        esac
    else
        echo "💥 Connection failed (DNS/Network issue or deployment in progress)"
    fi
    
    if [ $attempt -eq $MAX_ATTEMPTS ]; then
        echo ""
        echo "⏰ Timeout reached after $((MAX_ATTEMPTS * SLEEP_INTERVAL / 60)) minutes"
        echo "🔍 Manual verification required at: $URL"
        break
    fi
    
    echo "   Next check in ${SLEEP_INTERVAL}s..."
    echo ""
    sleep $SLEEP_INTERVAL
    ((attempt++))
done

echo "📋 Monitoring completed. Manual verification recommended."