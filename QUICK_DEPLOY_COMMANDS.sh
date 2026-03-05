#!/bin/bash

# Quick Deployment Commands for Hostinger
# Run these commands step by step

echo "🚀 Quick Deployment Guide"
echo "========================="
echo ""

echo "STEP 1: Commit and Push Migration Fix"
echo "--------------------------------------"
echo "Run these commands locally:"
echo ""
echo "git add database/migrations/2025_09_22_161630_add_fee_breakdown_to_enrollments_table.php"
echo "git commit -m 'Fix: Migration order and foreign key issues for production'"
echo "git push origin main"
echo ""
echo "Press Enter to continue or Ctrl+C to exit..."
read

echo ""
echo "STEP 2: Deploy on Hostinger Server"
echo "-----------------------------------"
echo "SSH into Hostinger and run:"
echo ""
echo "cd /public_html/v2insurance"
echo "git pull origin main"
echo "./deploy.sh"
echo ""
echo "OR manually:"
echo ""
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
echo ""
echo "Done! Visit: https://v2insurance.softpromis.com"

