#!/bin/bash

# Runtime setup script for AuthenticFarma with Vertex AI
set -e

echo "🚀 Starting AuthenticFarma Candidatos with Vertex AI integration..."

# Setup Vertex AI credentials if provided via environment
if [ ! -z "$VERTEX_AI_CREDENTIALS_JSON" ]; then
    echo "📋 Setting up Vertex AI credentials from environment..."
    echo "$VERTEX_AI_CREDENTIALS_JSON" > /var/www/storage/vertex-ai-credentials.json
    chmod 644 /var/www/storage/vertex-ai-credentials.json
    echo "✅ Vertex AI credentials configured"
elif [ -f "/run/secrets/vertex-ai-credentials" ]; then
    echo "📋 Setting up Vertex AI credentials from Docker secrets..."
    cp /run/secrets/vertex-ai-credentials /var/www/storage/vertex-ai-credentials.json
    chmod 644 /var/www/storage/vertex-ai-credentials.json
    echo "✅ Vertex AI credentials configured from secrets"
else
    echo "⚠️ No Vertex AI credentials provided - AI features will use placeholder"
fi

# Verify Vertex AI configuration
if [ -f "/var/www/storage/vertex-ai-credentials.json" ]; then
    echo "🔍 Verifying Vertex AI credentials..."
    if jq empty /var/www/storage/vertex-ai-credentials.json 2>/dev/null; then
        PROJECT_ID=$(jq -r '.project_id // "unknown"' /var/www/storage/vertex-ai-credentials.json)
        CLIENT_EMAIL=$(jq -r '.client_email // "unknown"' /var/www/storage/vertex-ai-credentials.json)
        echo "   Project: $PROJECT_ID"
        echo "   Service Account: $CLIENT_EMAIL"
        
        if [ "$PROJECT_ID" != "placeholder" ] && [ "$CLIENT_EMAIL" != "unknown" ]; then
            export GOOGLE_APPLICATION_CREDENTIALS="/var/www/storage/vertex-ai-credentials.json"
            echo "✅ Vertex AI credentials are valid and ready"
        else
            echo "⚠️ Using placeholder credentials - AI features disabled"
        fi
    else
        echo "❌ Invalid JSON in Vertex AI credentials"
    fi
fi

# Set proper ownership
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start the application
echo "🌟 Starting web services..."
exec "$@"