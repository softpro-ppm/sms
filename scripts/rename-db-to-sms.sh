#!/bin/bash
# Rename production database from v2student to sms
# Run this ON THE HOSTINGER SERVER via SSH
# Usage: ./scripts/rename-db-to-sms.sh

set -e

# ========== CONFIG - Update these to match your Hostinger setup ==========
OLD_DB="u820431346_v2student"   # Current DB name (Hostinger adds prefix)
NEW_DB="u820431346_sms"          # New DB name
DB_USER="u820431346_v2student"  # Your MySQL username
DB_PASS=""                       # Set via env: export DB_PASS='yourpass'
PROJECT_DIR="/home/u820431346/domains/softpromis.com/public_html/sms"
# ========================================================================

echo "🔄 Database rename: $OLD_DB → $NEW_DB"
echo ""

if [ -z "$DB_PASS" ]; then
    echo "⚠️  Set DB_PASS before running:"
    echo "   export DB_PASS='your_mysql_password'"
    exit 1
fi

cd "$PROJECT_DIR" || { echo "❌ Project dir not found"; exit 1; }

BACKUP_FILE="/tmp/${OLD_DB}_backup_$(date +%Y%m%d_%H%M%S).sql"
echo "📦 Step 1: Exporting from $OLD_DB..."
mysqldump -u "$DB_USER" -p"$DB_PASS" "$OLD_DB" > "$BACKUP_FILE"
echo "   Saved to: $BACKUP_FILE"
echo ""

echo "📥 Step 2: Importing into $NEW_DB..."
mysql -u "$DB_USER" -p"$DB_PASS" "$NEW_DB" < "$BACKUP_FILE"
echo "   Import complete"
echo ""

echo "✏️  Step 3: Update .env (DB_DATABASE=$NEW_DB)"
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$NEW_DB/" .env
echo "   .env updated"
echo ""

echo "⚡ Step 4: Clear Laravel cache..."
php artisan config:clear
php artisan config:cache
php artisan cache:clear
echo "   Cache cleared"
echo ""

echo "🧹 Step 5: Remove backup file (optional)"
rm -f "$BACKUP_FILE"
echo "   Backup removed"
echo ""

echo "✅ Done! Database renamed to $NEW_DB"
echo ""
echo "Next:"
echo "  1. Test the site: https://sms.softpromis.com"
echo "  2. If OK, delete old DB in hPanel → Databases → $OLD_DB"
echo ""
