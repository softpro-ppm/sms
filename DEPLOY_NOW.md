# 🚀 Deploy to Hostinger - Step by Step

## Current Status
✅ Migration fixes completed
✅ Git repository connected
⚠️ Need to commit and push migration fix

---

## Step 1: Commit Migration Fix Locally

### 1.1 Commit the Fixed Migration

```bash
# In your terminal (or GitHub Desktop)
cd /Users/rajesh/Documents/GitHub/v2student

# Check what changed
git status

# Add the fixed migration
git add database/migrations/2025_09_22_161630_add_fee_breakdown_to_enrollments_table.php

# Commit
git commit -m "Fix: Migration order and foreign key issues for production deployment"

# Push to GitHub
git push origin main
```

**OR using GitHub Desktop:**
1. Open GitHub Desktop
2. You'll see the migration file changed
3. Write commit message: "Fix: Migration order and foreign key issues for production deployment"
4. Click "Commit to main"
5. Click "Push origin"

---

## Step 2: Deploy on Hostinger Server

### 2.1 Connect to Hostinger via SSH

```bash
ssh username@softpromis.com
# Or use the SSH credentials from Hostinger hPanel
```

### 2.2 Navigate to Your Project Directory

```bash
cd /public_html/v2insurance
# OR
cd /domains/softpromis.com/public_html/v2insurance
```

### 2.3 Pull Latest Changes from GitHub

```bash
# Pull the latest code (including migration fixes)
git pull origin main
```

### 2.4 Verify .env File Exists

```bash
# Check if .env exists
ls -la .env

# If not, create it
cp .env.example .env
nano .env  # Edit with your production settings
```

### 2.5 Run Deployment Script

```bash
# Make script executable (if not already)
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

**OR manually run these commands:**

```bash
# 1. Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm install --production
npm run build

# 2. Generate app key (if not set)
php artisan key:generate --force

# 3. Run migrations
php artisan migrate --force

# 4. Create storage link
php artisan storage:link

# 5. Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 755 public

# 6. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Clear cache
php artisan cache:clear
```

---

## Step 3: Verify Deployment

### 3.1 Check Migration Status

```bash
php artisan migrate:status
```

**Expected:** All migrations should show "Ran" status

### 3.2 Test Application

1. Visit: `https://v2insurance.softpromis.com`
2. Check if homepage loads
3. Try login functionality
4. Check error logs if issues: `tail -f storage/logs/laravel.log`

### 3.3 Check for Errors

```bash
# View recent errors
tail -50 storage/logs/laravel.log

# Check if there are any PHP errors
php artisan config:clear
php artisan cache:clear
```

---

## Step 4: Production .env Configuration

Make sure your `.env` file has these settings:

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=softpromis_v2insurance
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Mail (Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@softpromis.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@softpromis.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Step 5: Troubleshooting Common Issues

### Issue: Migration Still Fails

**Solution:**
```bash
# Check migration status
php artisan migrate:status

# If migration is stuck, check what's pending
php artisan migrate --pretend

# Rollback last migration if needed (CAREFUL!)
php artisan migrate:rollback --step=1

# Then run again
php artisan migrate --force
```

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check .env file
cat .env | grep APP_KEY

# Generate key if missing
php artisan key:generate --force

# Check permissions
chmod -R 755 storage bootstrap/cache

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Assets Not Loading

**Solution:**
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan view:clear
php artisan cache:clear
```

### Issue: Permission Denied

**Solution:**
```bash
# Fix permissions
chmod -R 755 storage bootstrap/cache public
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
```

---

## Step 6: Post-Deployment Checklist

- [ ] All migrations ran successfully
- [ ] Application loads at https://v2insurance.softpromis.com
- [ ] Login functionality works
- [ ] Database connection works
- [ ] Assets (CSS/JS) loading correctly
- [ ] File uploads work (if applicable)
- [ ] Email sending works (test email)
- [ ] No errors in `storage/logs/laravel.log`
- [ ] SSL certificate active (HTTPS)
- [ ] Admin user can login

---

## Quick Command Reference

```bash
# Pull latest code
git pull origin main

# Run migrations
php artisan migrate --force

# Build assets
npm run build

# Clear caches
php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear

# Check logs
tail -f storage/logs/laravel.log

# Check migration status
php artisan migrate:status
```

---

## Need Help?

1. Check error logs: `storage/logs/laravel.log`
2. Verify `.env` configuration
3. Check file permissions
4. Verify database connection
5. Check Hostinger error logs in hPanel

---

**Ready to deploy? Start with Step 1 above!** 🚀

