# 🚀 Simple Deployment Guide - Step by Step

## ✅ Good News!
You already have Git repository set up! I can see your code is connected to GitHub.

---

## Step 1: Commit Migration Fix (2 minutes)

### Using GitHub Desktop (Easiest):

1. **Open GitHub Desktop**
2. You'll see a file changed: `database/migrations/2025_09_22_161630_add_fee_breakdown_to_enrollments_table.php`
3. **At the bottom**, write commit message:
   ```
   Fix: Migration order and foreign key issues for production
   ```
4. Click **"Commit to main"** button
5. Click **"Push origin"** button (top right)
6. ✅ Done! Your fix is now on GitHub

### Using Terminal (Alternative):

```bash
cd /Users/rajesh/Documents/GitHub/v2student
git add database/migrations/2025_09_22_161630_add_fee_breakdown_to_enrollments_table.php
git commit -m "Fix: Migration order and foreign key issues for production"
git push origin main
```

---

## Step 2: Deploy to Hostinger (15 minutes)

### 2.1 Login to Hostinger

1. Go to: **https://hpanel.hostinger.com**
2. Login

### 2.2 Create Subdomain (if not done)

1. Click **"Domains"** → **"Subdomains"**
2. Click **"Create a New Subdomain"**
3. Name: `v2insurance`
4. Document root: `/public_html/v2insurance`
5. Click **"Create"**

### 2.3 Create MySQL Database (if not done)

1. Click **"Databases"** → **"MySQL Databases"**
2. Click **"Create Database"**
3. Database name: `softpromis_v2insurance`
4. Create user and password
5. **Save these credentials!**

### 2.4 Deploy Code

**Option A: Using SSH (Recommended)**

1. In hPanel, go to **"Advanced"** → **"SSH Access"**
2. Enable SSH
3. Open Terminal on your Mac
4. Run:
   ```bash
   ssh your_username@softpromis.com
   ```
5. Then run:
   ```bash
   cd /public_html/v2insurance
   git clone https://github.com/yourusername/v2student.git .
   ```

**Option B: Using File Manager (Easier)**

1. In hPanel, click **"Files"** → **"File Manager"**
2. Go to `public_html/v2insurance` folder
3. Go to GitHub: `https://github.com/yourusername/v2student`
4. Click **"Code"** → **"Download ZIP"**
5. Extract ZIP on your Mac
6. Upload all files to `v2insurance` folder via File Manager

### 2.5 Configure .env File

1. In File Manager, find `.env.example`
2. Copy it and rename to `.env`
3. Edit `.env` and add:

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=softpromis_v2insurance
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@softpromis.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
```

### 2.6 Run Deployment Commands

**Using Hostinger Terminal:**

1. In hPanel, go to **"Advanced"** → **"Terminal"**
2. Click **"Open Terminal"**
3. Run these commands one by one:

```bash
cd /public_html/v2insurance

# Install dependencies
composer install --no-dev --optimize-autoloader --no-interaction

npm install --production
npm run build

# Setup Laravel
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

# Fix permissions
chmod -R 755 storage bootstrap/cache public

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

### 2.7 Test Your Site

1. Visit: **https://v2insurance.softpromis.com**
2. Check if it loads!
3. If errors, check: `storage/logs/laravel.log`

---

## ✅ Quick Checklist

- [ ] Committed migration fix to GitHub
- [ ] Created subdomain in Hostinger
- [ ] Created MySQL database
- [ ] Uploaded code to server
- [ ] Created `.env` file
- [ ] Ran deployment commands
- [ ] Site loads successfully

---

## 🆘 Need Help?

**If something fails:**
1. Check error logs: `storage/logs/laravel.log`
2. Verify `.env` file has correct database credentials
3. Make sure file permissions are correct: `chmod -R 755 storage`

**If you get stuck, tell me which step you're on and I'll help!**

---

**Start with Step 1 (commit fix), then move to Step 2 (deploy)!** 🚀

