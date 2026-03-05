# 🚀 Deploy Without Terminal - Alternative Methods

Since Terminal is not available in Hostinger, here are **3 easy alternatives**:

---

## Method 1: Use SSH from Your Mac Terminal (Recommended)

### Step 1: Get SSH Credentials from Hostinger

1. Login to Hostinger hPanel: https://hpanel.hostinger.com
2. Go to **"Advanced"** → **"SSH Access"**
3. Enable SSH if not enabled
4. **Note down:**
   - SSH Host/Server
   - SSH Username
   - SSH Port (usually 22)

### Step 2: Connect from Your Mac Terminal

Open Terminal on your Mac and run:

```bash
# Replace with your actual SSH details
ssh your_username@your_server_ip
# Or
ssh your_username@softpromis.com
```

### Step 3: Run Deployment Commands

Once connected, run:

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

## Method 2: Use File Manager + Manual Upload

### Step 1: Download Latest Code from GitHub

1. Go to: https://github.com/softpro-ppm/v2student
2. Click **"Code"** → **"Download ZIP"**
3. Extract the ZIP file on your Mac

### Step 2: Upload via File Manager

1. Login to Hostinger hPanel
2. Go to **"Files"** → **"File Manager"**
3. Navigate to: `public_html/v2student`
4. **Upload the updated files:**
   - Upload the entire `database/migrations/` folder (to update the fixed migration)
   - Or upload specific files that changed

### Step 3: Run Commands via SSH (from your Mac)

After uploading, connect via SSH from your Mac:

```bash
ssh your_username@softpromis.com
cd /public_html/v2student
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan cache:clear
```

---

## Method 3: Use Hostinger's PHP Execution (If Available)

### Step 1: Create a Deployment PHP Script

Create a file called `deploy.php` in your project root:

```php
<?php
// deploy.php - Run this once via browser, then DELETE it!

echo "🚀 Starting deployment...<br>";

// Change to project directory
chdir('/public_html/v2student');

// Pull from git
echo "📥 Pulling from GitHub...<br>";
exec('git pull origin main 2>&1', $output, $return);
echo implode('<br>', $output) . '<br>';

// Run migrations
echo "<br>🗄️ Running migrations...<br>";
exec('php artisan migrate --force 2>&1', $output, $return);
echo implode('<br>', $output) . '<br>';

// Clear cache
echo "<br>⚡ Clearing cache...<br>";
exec('php artisan config:clear 2>&1', $output, $return);
exec('php artisan cache:clear 2>&1', $output, $return);
exec('php artisan route:clear 2>&1', $output, $return);
exec('php artisan view:clear 2>&1', $output, $return);

echo "<br>✅ Deployment complete!<br>";
echo "<br>⚠️ IMPORTANT: Delete this deploy.php file now for security!";
```

### Step 2: Upload and Run

1. Upload `deploy.php` to `public_html/v2student/` via File Manager
2. Visit: `https://v2insurance.softpromis.com/deploy.php` in browser
3. **DELETE the file immediately after running!**

---

## Method 4: Contact Hostinger Support

If none of the above work:

1. Contact Hostinger Support
2. Ask them to:
   - Enable SSH access for your account
   - Or run these commands for you:
     ```bash
     cd /public_html/v2student
     git pull origin main
     composer install --no-dev --optimize-autoloader
     php artisan migrate --force
     php artisan config:cache
     ```

---

## ✅ Recommended: Method 1 (SSH from Mac)

**This is the easiest and most reliable method:**

1. Get SSH credentials from Hostinger hPanel → Advanced → SSH Access
2. Open Terminal on your Mac
3. Connect: `ssh username@server`
4. Run deployment commands

**Need help with SSH? Tell me and I'll guide you step by step!**

---

## 🆘 Quick Help

**If you can't find SSH Access:**
- Look for "SSH" in hPanel search
- Check "Advanced" section
- Contact Hostinger support to enable it

**If SSH doesn't work:**
- Use Method 2 (File Manager upload)
- Then contact Hostinger support to run composer/migrations

---

**Which method would you like to try? I can guide you through any of them!** 🚀

