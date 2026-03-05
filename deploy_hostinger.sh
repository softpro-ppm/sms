#!/bin/bash

# Hostinger Deployment Script
# This script connects to Hostinger and deploys your application

SSH_CMD="ssh -p 65002 u820431346@145.14.146.15"

echo "🚀 Connecting to Hostinger and deploying..."
echo ""

# Connect and run deployment commands
$SSH_CMD << 'ENDSSH'
# Try different possible paths (sms.softpromis.com)
if [ -d "/home/u820431346/domains/softpromis.com/public_html/sms" ]; then
    cd /home/u820431346/domains/softpromis.com/public_html/sms
elif [ -d "~/domains/softpromis.com/public_html/sms" ]; then
    cd ~/domains/softpromis.com/public_html/sms
elif [ -d "/home/u820431346/public_html/sms" ]; then
    cd /home/u820431346/public_html/sms
else
    echo "❌ Project directory not found!"
    echo "Current directory: $(pwd)"
    echo "Searching for project..."
    find ~ -name "artisan" -type f 2>/dev/null | head -3
    exit 1
fi

echo "📍 Current directory: $(pwd)"
echo ""

echo "📥 Pulling latest code from GitHub..."
git pull origin main

echo ""
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ""
echo "📦 Node/npm not available on Hostinger - skip. Build assets locally and commit public/build/"
if command -v npm &> /dev/null; then
    npm install --production
    npm run build
fi

echo ""
echo "🔑 Generating application key..."
php artisan key:generate --force

echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force

echo ""
echo "🔗 Creating storage symlink..."
php artisan storage:link

echo ""
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache public

echo ""
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

echo ""
echo "✅ Checking migration status..."
php artisan migrate:status

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "Next steps:"
echo "1. Visit your site: https://sms.softpromis.com"
echo "2. Check logs if needed: tail -50 storage/logs/laravel.log"
ENDSSH

echo ""
echo "✅ Deployment script completed!"

