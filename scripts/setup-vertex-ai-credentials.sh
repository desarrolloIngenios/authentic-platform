#!/bin/bash

# Vertex AI Credentials Setup Script for AuthenticFarma
# Service Account: laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com
# Project: authentic-prod-464216

set -e

echo "🤖 Setting up Vertex AI Credentials for AuthenticFarma..."

# Configuration
VERTEX_PROJECT_ID="authentic-prod-464216"
VERTEX_LOCATION="us-central1"
VERTEX_MODEL="gemini-1.5-flash"
SERVICE_ACCOUNT="laravel-gemini-prod@authentic-prod-464216.iam.gserviceaccount.com"

echo "📋 Configuration:"
echo "  Project ID: $VERTEX_PROJECT_ID"
echo "  Location: $VERTEX_LOCATION"
echo "  Model: $VERTEX_MODEL"
echo "  Service Account: $SERVICE_ACCOUNT"

# Check if running in CI/CD environment
if [ "$CI" = "true" ] || [ "$GITHUB_ACTIONS" = "true" ]; then
    echo "🔧 CI/CD Environment detected"
    
    # Create credentials file from secret
    if [ -n "$VERTEX_AI_PRODUCTION" ]; then
        echo "$VERTEX_AI_PRODUCTION" > /tmp/vertex-ai-credentials.json
        echo "✅ Vertex AI credentials file created from GitHub Secret"
        
        # Validate JSON format
        if jq empty /tmp/vertex-ai-credentials.json 2>/dev/null; then
            echo "✅ Vertex AI credentials JSON is valid"
        else
            echo "❌ Invalid JSON format in Vertex AI credentials"
            exit 1
        fi
        
        # Set environment variables for Laravel
        export GOOGLE_APPLICATION_CREDENTIALS="/tmp/vertex-ai-credentials.json"
        export VERTEX_AI_PROJECT_ID="$VERTEX_PROJECT_ID"
        export VERTEX_AI_LOCATION="$VERTEX_LOCATION"
        export VERTEX_AI_MODEL="$VERTEX_MODEL"
        export VERTEX_AI_SERVICE_ACCOUNT="$SERVICE_ACCOUNT"
        
        echo "✅ Environment variables set for CI/CD"
    else
        echo "❌ VERTEX_AI_PRODUCTION secret not found"
        exit 1
    fi
else
    echo "🏠 Local development environment detected"
    
    # Check if credentials file exists locally
    CREDENTIALS_FILE="./vertex-ai-credentials.json"
    if [ -f "$CREDENTIALS_FILE" ]; then
        echo "✅ Found local credentials file: $CREDENTIALS_FILE"
        
        # Validate JSON format
        if jq empty "$CREDENTIALS_FILE" 2>/dev/null; then
            echo "✅ Local credentials JSON is valid"
        else
            echo "❌ Invalid JSON format in local credentials"
            exit 1
        fi
        
        export GOOGLE_APPLICATION_CREDENTIALS="$(pwd)/$CREDENTIALS_FILE"
        export VERTEX_AI_PROJECT_ID="$VERTEX_PROJECT_ID"
        export VERTEX_AI_LOCATION="$VERTEX_LOCATION" 
        export VERTEX_AI_MODEL="$VERTEX_MODEL"
        export VERTEX_AI_SERVICE_ACCOUNT="$SERVICE_ACCOUNT"
        
        echo "✅ Environment variables set for local development"
    else
        echo "❌ Local credentials file not found: $CREDENTIALS_FILE"
        echo "💡 Please download the service account key from GCP Console:"
        echo "   1. Go to IAM & Admin > Service Accounts"
        echo "   2. Find: $SERVICE_ACCOUNT"
        echo "   3. Create and download a new key (JSON format)"
        echo "   4. Save it as: $CREDENTIALS_FILE"
        exit 1
    fi
fi

# Test credentials if gcloud is available
if command -v gcloud &> /dev/null; then
    echo "🧪 Testing Vertex AI credentials..."
    
    # Activate service account
    gcloud auth activate-service-account --key-file="$GOOGLE_APPLICATION_CREDENTIALS" --quiet
    
    # Test access to Vertex AI
    if gcloud ai models list --region="$VERTEX_LOCATION" --project="$VERTEX_PROJECT_ID" --quiet &>/dev/null; then
        echo "✅ Vertex AI access verified"
    else
        echo "⚠️  Could not verify Vertex AI access (may require additional permissions)"
    fi
else
    echo "⚠️  gcloud CLI not available - skipping credential verification"
fi

echo "🎉 Vertex AI credentials setup completed successfully!"
echo ""
echo "Environment variables available:"
echo "  GOOGLE_APPLICATION_CREDENTIALS=$GOOGLE_APPLICATION_CREDENTIALS"
echo "  VERTEX_AI_PROJECT_ID=$VERTEX_AI_PROJECT_ID"
echo "  VERTEX_AI_LOCATION=$VERTEX_AI_LOCATION"
echo "  VERTEX_AI_MODEL=$VERTEX_AI_MODEL"
echo "  VERTEX_AI_SERVICE_ACCOUNT=$VERTEX_AI_SERVICE_ACCOUNT"