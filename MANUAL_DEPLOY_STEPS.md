# 🔧 Manual Deployment Steps - Fix Path Issue

## Problem Found
The script tried to use `/public_html/v2student` but it doesn't exist. We need to find the correct path.

---

## Step 1: Connect to Hostinger Manually

Open Terminal and run:
```bash
ssh -p 65002 u820431346@145.14.146.15
```
(Enter your password when prompted)

---

## Step 2: Find Your Project Path

Once connected, run these commands to find your project:

```bash
# Check current location
pwd

# List home directory
ls -la

# Check if project is in public_html
ls -la public_html/

# Or check full path
ls -la /home/u820431346/public_html/

# Search for artisan file (Laravel project indicator)
find ~ -name "artisan" -type f 2>/dev/null
```

**Look for output showing:**
- `/home/u820431346/public_html/v2student/artisan`
- Or similar path

---

## Step 3: Navigate to Correct Path

Based on what you find, navigate to your project:

```bash
# Try one of these:
cd ~/public_html/v2student
# OR
cd /home/u820431346/public_html/v2student
# OR (if different)
cd /domains/softpromis.com/public_html/v2student
```

**Verify you're in the right place:**
```bash
pwd
ls -la | grep artisan
```

You should see `artisan` file if you're in the right directory.

---

## Step 4: Deploy Once You're in the Right Directory

Once you're in the project directory, run:

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node (if available)
npm install --production 2>/dev/null || echo "npm not available, skipping"
npm run build 2>/dev/null || echo "npm build skipped"

# Setup Laravel
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

# Fix permissions
chmod -R 755 storage bootstrap/cache public 2>/dev/null

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Verify
php artisan migrate:status
```

---

## Step 5: If Git Pull Fails

If `git pull` doesn't work, check:

```bash
# Check if git is initialized
ls -la .git

# If not, initialize git
git init
git remote add origin https://github.com/softpro-ppm/v2student.git
git pull origin main --allow-unrelated-histories
```

---

## Quick Copy-Paste Commands

**After you find the correct path and navigate there:**

```bash
cd ~/public_html/v2student && git pull origin main && composer install --no-dev --optimize-autoloader --no-interaction && php artisan key:generate --force && php artisan migrate --force && php artisan storage:link && chmod -R 755 storage bootstrap/cache public && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan cache:clear && php artisan migrate:status
```

---

## What to Do Now

1. **Connect manually:** `ssh -p 65002 u820431346@145.14.146.15`
2. **Find the path:** Run the find commands above
3. **Navigate:** `cd` to the correct directory
4. **Deploy:** Run the deployment commands
5. **Share the path:** Tell me what path you found, and I'll update the script!

---

**Start with Step 1 - connect and find the path!** 🚀

