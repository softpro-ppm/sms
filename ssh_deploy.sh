#!/bin/bash

# SSH Deployment Script for Hostinger
# Run this from your Mac terminal

echo "🚀 Hostinger Deployment via SSH"
echo "================================"
echo ""

# SSH Connection Details (UPDATE THESE)
SSH_USER="your_username"
SSH_HOST="your_server.hostinger.com"
# Or use: SSH_HOST="your_server_ip"

echo "📋 Step 1: Connect to Hostinger"
echo "--------------------------------"
echo "Run this command to connect:"
echo ""
echo "ssh ${SSH_USER}@${SSH_HOST}"
echo ""
echo "Or if you have the full connection string:"
echo "ssh your_username@your_server.hostinger.com"
echo ""
echo "Press Enter after you've connected to Hostinger..."
read

echo ""
echo "📋 Step 2: Run Deployment Commands"
echo "-----------------------------------"
echo "Once connected, copy and paste these commands:"
echo ""
echo "cd /home/u820431346/domains/softpromis.com/public_html/sms"
echo "git pull origin main"
echo "composer install --no-dev --optimize-autoloader --no-interaction"
echo "npm install --production"
echo "npm run build"
echo "php artisan key:generate --force"
echo "php artisan migrate --force"
echo "php artisan storage:link"
echo "chmod -R 755 storage bootstrap/cache public"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo "php artisan cache:clear"
echo "php artisan migrate:status"
echo ""
echo "✅ Done! Your site should be updated."

