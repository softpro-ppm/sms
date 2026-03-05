#!/bin/bash

# Server Status Check Script for sms.softpromis.com
# Run this on Hostinger server to check all systems

echo "🔍 Checking Server Status for sms.softpromis.com"
echo "========================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Navigate to project
cd ~/domains/softpromis.com/public_html/sms 2>/dev/null || cd /home/u820431346/domains/softpromis.com/public_html/sms

if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Not in project directory!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Project directory found${NC}"
echo "📍 Location: $(pwd)"
echo ""

# 1. Check PHP Version
echo "1️⃣  PHP Version:"
php -v | head -1
echo ""

# 2. Check Laravel Version
echo "2️⃣  Laravel Version:"
php artisan --version
echo ""

# 3. Check .env file
echo "3️⃣  Environment Configuration:"
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ .env file exists${NC}"
    echo "   APP_ENV: $(grep APP_ENV .env | cut -d '=' -f2)"
    echo "   APP_DEBUG: $(grep APP_DEBUG .env | cut -d '=' -f2)"
    echo "   APP_URL: $(grep APP_URL .env | cut -d '=' -f2)"
    if grep -q "APP_KEY=base64:" .env; then
        echo -e "   ${GREEN}✅ APP_KEY is set${NC}"
    else
        echo -e "   ${RED}❌ APP_KEY not set${NC}"
    fi
else
    echo -e "${RED}❌ .env file not found!${NC}"
fi
echo ""

# 4. Check Database Connection
echo "4️⃣  Database Connection:"
php artisan migrate:status > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database connection successful${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
    echo "   Check .env DB credentials"
fi
echo ""

# 5. Check Migration Status
echo "5️⃣  Migration Status:"
PENDING=$(php artisan migrate:status 2>/dev/null | grep -c "Pending" || echo "0")
if [ "$PENDING" -eq 0 ]; then
    echo -e "${GREEN}✅ All migrations completed${NC}"
else
    echo -e "${YELLOW}⚠️  $PENDING migrations pending${NC}"
fi
echo ""

# 6. Check Storage Link
echo "6️⃣  Storage Symlink:"
if [ -L "public/storage" ]; then
    if [ -e "public/storage" ]; then
        echo -e "${GREEN}✅ Storage symlink exists and works${NC}"
        echo "   Target: $(readlink public/storage)"
    else
        echo -e "${RED}❌ Storage symlink broken${NC}"
    fi
else
    echo -e "${RED}❌ Storage symlink not found${NC}"
fi
echo ""

# 7. Check File Permissions
echo "7️⃣  File Permissions:"
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    echo -e "${GREEN}✅ Storage and cache directories writable${NC}"
else
    echo -e "${RED}❌ Permission issues detected${NC}"
fi
echo ""

# 8. Check Disk Space
echo "8️⃣  Disk Space:"
df -h . | tail -1 | awk '{print "   Available: " $4 " / " $2 " (" $5 " used)"}'
echo ""

# 9. Check Recent Errors
echo "9️⃣  Recent Errors (last 10 lines):"
if [ -f "storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(tail -100 storage/logs/laravel.log | grep -c "ERROR\|CRITICAL\|Exception" || echo "0")
    if [ "$ERROR_COUNT" -eq 0 ]; then
        echo -e "   ${GREEN}✅ No recent errors${NC}"
    else
        echo -e "   ${YELLOW}⚠️  Found $ERROR_COUNT errors in last 100 log lines${NC}"
        echo "   Recent errors:"
        tail -100 storage/logs/laravel.log | grep -i "ERROR\|CRITICAL\|Exception" | tail -3 | sed 's/^/   /'
    fi
else
    echo -e "   ${YELLOW}⚠️  Log file not found${NC}"
fi
echo ""

# 10. Check Cache Status
echo "🔟 Cache Status:"
if [ -f "bootstrap/cache/config.php" ]; then
    echo -e "   ${GREEN}✅ Config cache exists${NC}"
else
    echo -e "   ${YELLOW}⚠️  Config cache not found${NC}"
fi
if [ -f "bootstrap/cache/routes-v7.php" ] || [ -f "bootstrap/cache/routes.php" ]; then
    echo -e "   ${GREEN}✅ Route cache exists${NC}"
else
    echo -e "   ${YELLOW}⚠️  Route cache not found${NC}"
fi
echo ""

# 11. Check Git Status
echo "1️⃣1️⃣  Git Status:"
if [ -d ".git" ]; then
    CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
    echo "   Branch: $CURRENT_BRANCH"
    BEHIND=$(git rev-list --count HEAD..origin/main 2>/dev/null || echo "0")
    if [ "$BEHIND" -eq 0 ]; then
        echo -e "   ${GREEN}✅ Up to date with origin/main${NC}"
    else
        echo -e "   ${YELLOW}⚠️  $BEHIND commits behind origin/main${NC}"
    fi
else
    echo -e "   ${YELLOW}⚠️  Not a git repository${NC}"
fi
echo ""

# 12. Check Composer Dependencies
echo "1️⃣2️⃣  Composer Dependencies:"
if [ -d "vendor" ]; then
    echo -e "   ${GREEN}✅ Vendor directory exists${NC}"
    if [ -f "vendor/autoload.php" ]; then
        echo -e "   ${GREEN}✅ Autoload file exists${NC}"
    else
        echo -e "   ${RED}❌ Autoload file missing - run composer install${NC}"
    fi
else
    echo -e "   ${RED}❌ Vendor directory not found${NC}"
fi
echo ""

# 13. Check Application Routes
echo "1️⃣3️⃣  Application Routes:"
ROUTE_COUNT=$(php artisan route:list 2>/dev/null | wc -l)
if [ "$ROUTE_COUNT" -gt 0 ]; then
    echo -e "   ${GREEN}✅ Routes loaded ($ROUTE_COUNT routes)${NC}"
else
    echo -e "   ${RED}❌ No routes found${NC}"
fi
echo ""

# 14. Check Database Tables
echo "1️⃣4️⃣  Database Tables:"
TABLE_COUNT=$(php artisan db:show 2>/dev/null | grep -c "Tables:" || echo "0")
if [ "$TABLE_COUNT" -gt 0 ]; then
    echo -e "   ${GREEN}✅ Database tables exist${NC}"
    php artisan db:show 2>/dev/null | grep "Tables:" | head -1
else
    echo -e "   ${YELLOW}⚠️  Could not check tables${NC}"
fi
echo ""

# Summary
echo "========================================"
echo "📊 Status Summary:"
echo "========================================"
echo ""

# Count issues
ISSUES=0

if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file missing${NC}"
    ISSUES=$((ISSUES + 1))
fi

if ! php artisan migrate:status > /dev/null 2>&1; then
    echo -e "${RED}❌ Database connection issue${NC}"
    ISSUES=$((ISSUES + 1))
fi

if [ ! -L "public/storage" ] || [ ! -e "public/storage" ]; then
    echo -e "${RED}❌ Storage symlink issue${NC}"
    ISSUES=$((ISSUES + 1))
fi

if [ "$ISSUES" -eq 0 ]; then
    echo -e "${GREEN}✅ All critical checks passed!${NC}"
    echo ""
    echo "🎉 Your application appears to be configured correctly!"
else
    echo -e "${YELLOW}⚠️  Found $ISSUES critical issue(s)${NC}"
    echo ""
    echo "Please review the issues above and fix them."
fi

echo ""
echo "🌐 Application URL: https://sms.softpromis.com"
echo ""

