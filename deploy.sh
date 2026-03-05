#!/bin/bash

# Hostinger Deployment Script for sms.softpromis.com
# Run this script on the server after cloning/pulling from GitHub

echo "🚀 Starting deployment process..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ .env file not found!${NC}"
    echo "Please create .env file from .env.example"
    exit 1
fi

echo -e "${GREEN}✅ .env file found${NC}"

# Install/Update Composer dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Composer install failed!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Composer dependencies installed${NC}"

# Install/Update Node dependencies and build assets (skip if npm not available, e.g. Hostinger shared)
if command -v npm &> /dev/null; then
    echo -e "${YELLOW}📦 Installing Node dependencies...${NC}"
    npm install --production
    if [ $? -eq 0 ]; then
        echo -e "${YELLOW}🏗️  Building production assets...${NC}"
        npm run build
    fi
fi

if [ -d "public/build" ]; then
    echo -e "${GREEN}✅ Assets ready (public/build exists)${NC}"
else
    echo -e "${YELLOW}⚠️  No public/build folder. Build assets locally (npm run build) and commit before deploy.${NC}"
fi

# Generate application key if not set
echo -e "${YELLOW}🔑 Checking application key...${NC}"
php artisan key:generate --force

# Run migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Migration failed!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Migrations completed${NC}"

# Seed email templates if needed
echo -e "${YELLOW}📧 Seeding email templates...${NC}"
php artisan db:seed --class=EmailTemplateSeeder --force 2>/dev/null || true

# Create storage link
echo -e "${YELLOW}🔗 Creating storage symlink...${NC}"
php artisan storage:link

# Set permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod -R 755 public
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;

echo -e "${GREEN}✅ Permissions set${NC}"

# Clear and cache config
echo -e "${YELLOW}⚡ Optimizing Laravel...${NC}"
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo -e "${GREEN}✅ Laravel optimized${NC}"

# Clear application cache
php artisan cache:clear

echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo ""
echo "Next steps:"
echo "1. Verify application at your APP_URL"
echo "2. Check error logs: storage/logs/laravel.log"
echo "3. Test all functionality"

