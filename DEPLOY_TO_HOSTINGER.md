# 🚀 Deploy to Hostinger - Quick Guide

## ✅ Step 1: Local to GitHub - DONE!

Your changes have been pushed to GitHub:
- ✅ Migration fix committed
- ✅ Pushed to: `https://github.com/softpro-ppm/v2student.git`

---

## 🚀 Step 2: GitHub to Hostinger

### Option A: Using SSH (Recommended)

1. **Connect to Hostinger via SSH:**
   ```bash
   ssh your_username@softpromis.com
   # Or use the SSH details from Hostinger hPanel
   ```

2. **Navigate to your project:**
   ```bash
   cd /public_html/v2student
   ```

3. **Pull latest code:**
   ```bash
   git pull origin main
   ```

4. **Run deployment:**
   ```bash
   # Make script executable
   chmod +x hostinger_deploy.sh
   
   # Run deployment
   ./hostinger_deploy.sh
   ```

   **OR run commands manually:**
   ```bash
   composer install --no-dev --optimize-autoloader --no-interaction
   npm install --production
   npm run build
   php artisan key:generate --force
   php artisan migrate --force
   php artisan storage:link
   chmod -R 755 storage bootstrap/cache public
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan cache:clear
   ```

---

### Option B: Using Hostinger Terminal (Easier)

1. **Login to Hostinger hPanel:**
   - Go to: https://hpanel.hostinger.com

2. **Open Terminal:**
   - Click **"Advanced"** → **"Terminal"**
   - Click **"Open Terminal"**

3. **Run these commands one by one:**
   ```bash
   cd /public_html/v2student
   
   git pull origin main
   
   composer install --no-dev --optimize-autoloader --no-interaction
   npm install --production
   npm run build
   php artisan key:generate --force
   php artisan migrate --force
   php artisan storage:link
   chmod -R 755 storage bootstrap/cache public
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan cache:clear
   ```

---

## 📋 Quick Command Copy-Paste

**Copy and paste this entire block into Hostinger Terminal:**

```bash
cd /public_html/v2student && git pull origin main && composer install --no-dev --optimize-autoloader --no-interaction && npm install --production && npm run build && php artisan key:generate --force && php artisan migrate --force && php artisan storage:link && chmod -R 755 storage bootstrap/cache public && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan cache:clear && echo "✅ Deployment Complete!"
```

---

## ✅ Verify Deployment

After running commands, check:

```bash
# Check migration status
php artisan migrate:status

# Check for errors
tail -50 storage/logs/laravel.log

# Test your site
# Visit: https://v2insurance.softpromis.com
```

---

## 🆘 Troubleshooting

### If git pull fails:
```bash
# Check if git is initialized
ls -la .git

# If not, initialize git
git init
git remote add origin https://github.com/softpro-ppm/v2student.git
git pull origin main
```

### If migrations fail:
```bash
# Check migration status
php artisan migrate:status

# View detailed error
tail -100 storage/logs/laravel.log
```

### If composer/npm not available:
- Contact Hostinger support to install
- Or use File Manager to upload `vendor/` and `node_modules/` manually (not recommended)

---

**Ready? Run the commands above in Hostinger Terminal!** 🚀

