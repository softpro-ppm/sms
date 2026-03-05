# 🚀 Production Deployment Guide - Hostinger

Complete step-by-step guide to deploy v2student to Hostinger subdomain: **v2insurance.softpromis.com**

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step 1: Prepare Project for Production](#step-1-prepare-project-for-production)
3. [Step 2: Create GitHub Repository](#step-2-create-github-repository)
4. [Step 3: Initialize Git and Push to GitHub](#step-3-initialize-git-and-push-to-github)
5. [Step 4: Configure Hostinger Subdomain](#step-4-configure-hostinger-subdomain)
6. [Step 5: Setup Hostinger File Manager](#step-5-setup-hostinger-file-manager)
7. [Step 6: Deploy via GitHub Desktop](#step-6-deploy-via-github-desktop)
8. [Step 7: Configure Production Environment](#step-7-configure-production-environment)
9. [Step 8: Build Assets and Install Dependencies](#step-8-build-assets-and-install-dependencies)
10. [Step 9: Database Setup](#step-9-database-setup)
11. [Step 10: Final Configuration](#step-10-final-configuration)
12. [Troubleshooting](#troubleshooting)

---

## Prerequisites

- ✅ GitHub account
- ✅ GitHub Desktop installed
- ✅ Hostinger hosting account
- ✅ Access to Hostinger hPanel
- ✅ PHP 8.2+ installed locally
- ✅ Composer installed locally
- ✅ Node.js and npm installed locally

---

## Step 1: Prepare Project for Production

### 1.1 Clean Up Development Files

Remove unnecessary files:
- Backup files (`.backup`, `.backup2`)
- Test files
- Development logs

### 1.2 Update .gitignore

Ensure `.gitignore` includes:
```
.env
.env.backup
.env.production
node_modules/
vendor/
/public/build
/public/hot
/storage/*.key
```

### 1.3 Create Production Environment Template

Create `.env.example` file (already done if following this guide)

---

## Step 2: Create GitHub Repository

### 2.1 Create New Repository on GitHub

1. Go to [GitHub.com](https://github.com)
2. Click **"+"** → **"New repository"**
3. Repository name: `v2student` (or your preferred name)
4. Description: "Student Management System - Laravel"
5. Visibility: **Private** (recommended) or **Public**
6. **DO NOT** initialize with README, .gitignore, or license
7. Click **"Create repository"**

### 2.2 Copy Repository URL

Copy the repository URL (e.g., `https://github.com/yourusername/v2student.git`)

---

## Step 3: Initialize Git and Push to GitHub

### 3.1 Open GitHub Desktop

1. Open **GitHub Desktop**
2. Click **File** → **Add Local Repository**
3. Navigate to: `/Users/rajesh/Documents/GitHub/v2student`
4. Click **"Add repository"**

### 3.2 Configure Git (if not done)

```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### 3.3 Stage and Commit Files

1. In GitHub Desktop, review all files
2. **Uncheck** files that should NOT be committed:
   - `.env` (if exists)
   - `vendor/` folder
   - `node_modules/` folder
   - `storage/logs/*.log`
   - `database/database.sqlite`
   - Any backup files

3. Write commit message: `"Initial commit - Production ready"`
4. Click **"Commit to main"**

### 3.4 Publish to GitHub

1. Click **"Publish repository"** button
2. Ensure repository name matches GitHub
3. **Uncheck** "Keep this code private" if you want it public
4. Click **"Publish repository"**

### 3.5 Verify Upload

1. Go to your GitHub repository
2. Verify all files are uploaded correctly
3. Check that sensitive files (`.env`, `vendor/`) are NOT visible

---

## Step 4: Configure Hostinger Subdomain

### 4.1 Access Hostinger hPanel

1. Login to [Hostinger hPanel](https://hpanel.hostinger.com)
2. Select your domain: **softpromis.com**

### 4.2 Create Subdomain

1. Navigate to **"Domains"** → **"Subdomains"**
2. Click **"Create a New Subdomain"**
3. Subdomain name: `v2insurance`
4. Document root: `/public_html/v2insurance` (or `/domains/softpromis.com/public_html/v2insurance`)
5. Click **"Create"**

### 4.3 Verify DNS (if needed)

- Usually automatic, but verify subdomain resolves
- Wait 5-10 minutes for DNS propagation

### 4.4 Note File Paths

**Important:** Note the exact path to your subdomain directory:
- Example: `/domains/softpromis.com/public_html/v2insurance`
- Or: `/public_html/v2insurance`

---

## Step 5: Setup Hostinger File Manager

### 5.1 Access File Manager

1. In hPanel, go to **"Files"** → **"File Manager"**
2. Navigate to your subdomain directory: `v2insurance`

### 5.2 Create Directory Structure

Create these directories if they don't exist:
- `storage/app/public`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/framework/views`
- `storage/logs`
- `bootstrap/cache`

### 5.3 Set Permissions

Set correct permissions (via File Manager or SSH):
- `storage/` → **755**
- `bootstrap/cache/` → **755**
- Files in `storage/` → **644**
- Directories in `storage/` → **755**

---

## Step 6: Deploy via GitHub Desktop

### 6.1 Clone Repository on Hostinger (via SSH)

**Option A: Using GitHub Desktop + SSH**

1. **Enable SSH Access** in Hostinger hPanel:
   - Go to **"Advanced"** → **"SSH Access"**
   - Enable SSH and note your SSH credentials

2. **Connect via SSH:**
   ```bash
   ssh username@your-server-ip
   # Or
   ssh username@softpromis.com
   ```

3. **Navigate to subdomain directory:**
   ```bash
   cd /domains/softpromis.com/public_html/v2insurance
   # Or
   cd /public_html/v2insurance
   ```

4. **Clone repository:**
   ```bash
   git clone https://github.com/yourusername/v2student.git .
   # Note: The dot (.) clones into current directory
   ```

**Option B: Manual Upload via File Manager**

1. Download repository as ZIP from GitHub
2. Extract ZIP file
3. Upload all files to `v2insurance` directory via File Manager
4. Ensure `.htaccess` is uploaded (may be hidden)

---

## Step 7: Configure Production Environment

### 7.1 Create .env File

1. In File Manager, navigate to `v2insurance` directory
2. Create new file: `.env`
3. Copy content from `.env.example` (or use provided template)

### 7.2 Configure .env Settings

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://v2insurance.softpromis.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file

APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

### 7.3 Get Database Credentials (MySQL Setup)

**Important:** Your local development uses SQLite, but production will use MySQL.

1. In hPanel, go to **"Databases"** → **"MySQL Databases"**
2. Click **"Create Database"**
3. Fill in:
   - **Database Name**: `softpromis_v2insurance` (or your preferred name)
   - **Database User**: Create a new user or use existing
   - **Password**: Set a strong password
   - **Host**: Usually `localhost` (check Hostinger documentation)
4. Click **"Create"**
5. **Note down these credentials:**
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### 7.4 Update .env with MySQL Database Info

**Important:** Set `DB_CONNECTION=mysql` for production (not sqlite).

Update these lines in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=softpromis_v2insurance
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

**Note:** 
- Local development uses SQLite (`database/database.sqlite`)
- Production uses MySQL (configured above)
- See [DATABASE_SETUP.md](./DATABASE_SETUP.md) for detailed database setup guide

### 7.5 Generate Application Key

Via SSH or Terminal:
```bash
cd /domains/softpromis.com/public_html/v2insurance
php artisan key:generate
```

Or manually add to `.env`:
```env
APP_KEY=base64:your-generated-key-here
```

---

## Step 8: Build Assets and Install Dependencies

### 8.1 Install Composer Dependencies

**Via SSH:**
```bash
cd /domains/softpromis.com/public_html/v2insurance
composer install --no-dev --optimize-autoloader
```

**Via File Manager:**
- Upload `composer.json` and `composer.lock`
- Use Hostinger's terminal or request them to run composer

### 8.2 Install Node Dependencies

**Via SSH:**
```bash
cd /domains/softpromis.com/public_html/v2insurance
npm install
```

### 8.3 Build Production Assets

**Via SSH:**
```bash
npm run build
```

This creates optimized assets in `public/build/`

### 8.4 Verify Build

Check that `public/build/` directory contains:
- `build/assets/` folder with CSS and JS files
- `manifest.json` file

---

## Step 9: Database Setup

### 9.1 Run Migrations

**Via SSH:**
```bash
cd /domains/softpromis.com/public_html/v2insurance
php artisan migrate --force
```

### 9.2 Seed Database (Optional)

**Via SSH:**
```bash
php artisan db:seed --class=AdminUserSeeder
```

### 9.3 Create Storage Link

**Via SSH:**
```bash
php artisan storage:link
```

---

## Step 10: Final Configuration

### 10.1 Set Permissions

**Via SSH:**
```bash
cd /domains/softpromis.com/public_html/v2insurance

# Set directory permissions
chmod -R 755 storage bootstrap/cache
chmod -R 755 public

# Set file permissions
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
```

### 10.2 Optimize Laravel

**Via SSH:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 10.3 Create .htaccess (if needed)

Ensure `public/.htaccess` exists with:
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 10.4 Configure Document Root

**Important:** In Hostinger, ensure document root points to `public` folder:

1. Go to **"Domains"** → **"Subdomains"**
2. Edit subdomain: `v2insurance`
3. Change document root to: `/public_html/v2insurance/public`
4. Save changes

**OR** create `.htaccess` in root directory:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 10.5 Test Application

1. Visit: `https://v2insurance.softpromis.com`
2. Check if homepage loads
3. Test login functionality
4. Verify all routes work

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Solutions:**
1. Check `.env` file exists and is configured correctly
2. Verify `APP_KEY` is set
3. Check file permissions (storage, bootstrap/cache)
4. Review error logs: `storage/logs/laravel.log`
5. Ensure PHP version is 8.2+

### Issue: Assets Not Loading

**Solutions:**
1. Run `npm run build` again
2. Clear cache: `php artisan cache:clear`
3. Check `public/build/manifest.json` exists
4. Verify Vite configuration

### Issue: Database Connection Error

**Solutions:**
1. Verify database credentials in `.env`
2. Check database exists in Hostinger
3. Ensure database user has proper permissions
4. Test connection via Hostinger phpMyAdmin

### Issue: Permission Denied

**Solutions:**
1. Set correct permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```
2. Ensure web server user owns files

### Issue: Composer/Node Not Available

**Solutions:**
1. Use Hostinger's terminal/SSH
2. Request Hostinger support to install dependencies
3. Upload `vendor/` and `node_modules/` manually (not recommended)

---

## 🔄 Updating the Application

### Regular Updates via GitHub Desktop

1. Make changes locally
2. Commit changes in GitHub Desktop
3. Push to GitHub
4. **On Hostinger (via SSH):**
   ```bash
   cd /domains/softpromis.com/public_html/v2insurance
   git pull origin main
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 📝 Important Notes

1. **Never commit `.env` file** - Keep it secure
2. **Always backup database** before migrations
3. **Test locally** before deploying to production
4. **Monitor error logs** regularly: `storage/logs/laravel.log`
5. **Keep dependencies updated** for security
6. **Use HTTPS** - Configure SSL certificate in Hostinger

---

## ✅ Deployment Checklist

- [ ] GitHub repository created
- [ ] Code pushed to GitHub
- [ ] Subdomain created in Hostinger
- [ ] Files uploaded/cloned to server
- [ ] `.env` file configured
- [ ] Database created and configured
- [ ] Composer dependencies installed
- [ ] Node dependencies installed
- [ ] Assets built (`npm run build`)
- [ ] Migrations run
- [ ] Storage link created
- [ ] Permissions set correctly
- [ ] Laravel optimized (cache commands)
- [ ] Application tested
- [ ] SSL certificate configured
- [ ] Error logging verified

---

## 🆘 Support

If you encounter issues:
1. Check Hostinger documentation
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check Hostinger error logs in hPanel
4. Contact Hostinger support for server-related issues

---

**Last Updated:** $(date)
**Project:** v2student
**Subdomain:** v2insurance.softpromis.com

