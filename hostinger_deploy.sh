#!/bin/bash

# Hostinger Deployment Script
# Run this on Hostinger server via SSH

echo "🚀 Starting deployment on Hostinger..."
echo ""

# Navigate to project directory
cd /home/u820431346/domains/softpromis.com/public_html/sms

echo "📍 Current directory: $(pwd)"
echo ""

# Pull latest changes from GitHub
echo "📥 Pulling latest changes from GitHub..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "❌ Git pull failed!"
    exit 1
fi

echo "✅ Code updated from GitHub"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found!"
    echo "Creating from .env.example..."
    cp .env.example .env
    echo "✅ Please edit .env file with your production settings"
    echo ""
fi

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

if [ $? -ne 0 ]; then
    echo "❌ Composer install failed!"
    exit 1
fi

echo "✅ Composer dependencies installed"
echo ""

# Install/Update Node dependencies (skip if npm not available on Hostinger)
if command -v npm &> /dev/null; then
    echo "📦 Installing Node dependencies..."
    npm install --production
    [ $? -eq 0 ] && echo "✅ Node dependencies installed"
    echo "🏗️  Building production assets..."
    npm run build
    [ $? -eq 0 ] && echo "✅ Assets built successfully"
else
    echo "⚠️  npm not available - use local build and commit public/build/"
fi
echo ""

# Generate application key if not set
echo "🔑 Checking application key..."
php artisan key:generate --force
echo ""

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ Migration failed!"
    echo "Check error logs: storage/logs/laravel.log"
    exit 1
fi

echo "✅ Migrations completed"
echo ""

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link
echo ""

# Set permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
echo "✅ Permissions set"
echo ""

# Clear and cache config
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan cache:clear
echo "✅ Laravel optimized"
echo ""

echo "🎉 Deployment completed successfully!"
echo ""
echo "Next steps:"
echo "1. Visit: https://sms.softpromis.com"
echo "2. Check error logs: tail -50 storage/logs/laravel.log"
echo "3. Test all functionality"
echo ""

