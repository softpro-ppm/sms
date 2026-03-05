#!/bin/bash

# Script to find the correct project path on Hostinger

echo "🔍 Finding your project path on Hostinger..."
echo ""

ssh -p 65002 u820431346@145.14.146.15 << 'ENDSSH'
echo "📍 Current directory: $(pwd)"
echo ""
echo "🔍 Searching for sms project..."
echo ""

# Check common locations
if [ -d "/home/u820431346/domains/softpromis.com/public_html/sms" ]; then
    echo "✅ Found at: /home/u820431346/domains/softpromis.com/public_html/sms"
    cd /home/u820431346/domains/softpromis.com/public_html/sms
    echo "📂 Contents:"
    ls -la | head -20
elif [ -d "/home/u820431346/public_html/sms" ]; then
    echo "✅ Found at: /home/u820431346/public_html/sms"
    cd /home/u820431346/public_html/sms
    echo "📂 Contents:"
    ls -la | head -20
elif [ -d "~/domains/softpromis.com/public_html/sms" ]; then
    echo "✅ Found at: ~/domains/softpromis.com/public_html/sms"
    cd ~/domains/softpromis.com/public_html/sms
    echo "📂 Contents:"
    ls -la | head -20
else
    echo "❌ Project not found in common locations"
    echo ""
    echo "Searching for Laravel project files..."
    find /home/u820431346 -name "artisan" -type f 2>/dev/null | head -5
    find /home/u820431346 -name "composer.json" -type f 2>/dev/null | head -5
fi

echo ""
echo "📋 Listing domains directory:"
ls -la /home/u820431346/domains/softpromis.com/public_html/ 2>/dev/null || echo "domains directory not found"
ENDSSH

