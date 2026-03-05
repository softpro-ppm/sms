# 🚀 Quick SSH Deployment - Your Specific Commands

## Your SSH Connection
```bash
ssh -p 65002 u820431346@145.14.146.15
```

---

## Method 1: Run Automated Script (Easiest)

I've created a script for you. Run this in your Mac terminal:

```bash
cd /Users/rajesh/Documents/GitHub/v2student
./deploy_hostinger.sh
```

This will automatically connect and deploy everything!

---

## Method 2: Manual Step-by-Step

### Step 1: Connect to Hostinger

Open Terminal and run:
```bash
ssh -p 65002 u820431346@145.14.146.15
```

### Step 2: Once Connected, Run These Commands

Copy and paste this entire block:

```bash
cd /public_html/v2student && git pull origin main && composer install --no-dev --optimize-autoloader --no-interaction && npm install --production && npm run build && php artisan key:generate --force && php artisan migrate --force && php artisan storage:link && chmod -R 755 storage bootstrap/cache public && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan cache:clear && echo "✅ Done!" && php artisan migrate:status
```

---

## Method 3: Step-by-Step Commands

If you prefer to run commands one by one:

```bash
# 1. Navigate to project
cd /public_html/v2student

# 2. Pull latest code
git pull origin main

# 3. Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm install --production
npm run build

# 4. Setup Laravel
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

# 5. Fix permissions
chmod -R 755 storage bootstrap/cache public

# 6. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# 7. Verify
php artisan migrate:status
```

---

## ✅ After Deployment

1. **Check your site:** https://v2insurance.softpromis.com
2. **Check for errors:**
   ```bash
   tail -50 storage/logs/laravel.log
   ```
3. **Test login and functionality**

---

## 🆘 Troubleshooting

### If git pull fails:
```bash
cd /public_html/v2student
git status
git pull origin main --no-edit
```

### If migrations fail:
```bash
php artisan migrate:status
tail -100 storage/logs/laravel.log
```

### If composer/npm not found:
Contact Hostinger support to install them, or they can run the commands for you.

---

**Ready? Start with Method 1 (automated script) or Method 2 (manual)!** 🚀

