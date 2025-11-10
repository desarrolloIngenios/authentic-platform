#!/bin/bash

# Docker build diagnostic script for AuthenticFarma
set -e

echo "🔍 Docker Build Diagnostics for AuthenticFarma Candidatos"
echo "=========================================================="

# Check PHP version and extensions
echo "🐘 PHP Configuration:"
php -v
echo ""
echo "📋 PHP Extensions:"
php -m | sort
echo ""

# Check Composer
echo "🎼 Composer Configuration:"
composer --version
composer diagnose || true
echo ""

# Check if critical files exist
echo "📁 Critical Files Check:"
files=(
    "/var/www/composer.json"
    "/var/www/composer.lock"
    "/var/www/.env.example"
    "/var/www/docker/supervisor.conf"
    "/var/www/docker/nginx.conf"
    "/var/www/docker/php.ini"
    "/var/www/docker/entrypoint.sh"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "❌ $file MISSING"
    fi
done
echo ""

# Check permissions
echo "🔐 Permissions Check:"
echo "Storage directory: $(ls -la /var/www/ | grep storage || echo 'NOT FOUND')"
echo "Bootstrap cache: $(ls -la /var/www/ | grep bootstrap || echo 'NOT FOUND')"
echo ""

# Check network connectivity
echo "🌐 Network Check:"
ping -c 1 packagist.org &>/dev/null && echo "✅ Packagist reachable" || echo "❌ Packagist unreachable"
ping -c 1 github.com &>/dev/null && echo "✅ GitHub reachable" || echo "❌ GitHub unreachable"
echo ""

# System resources
echo "💾 System Resources:"
echo "Memory: $(free -h | grep Mem || echo 'Unknown')"
echo "Disk: $(df -h / | tail -1 || echo 'Unknown')"
echo ""

echo "🎯 Diagnostic complete!"