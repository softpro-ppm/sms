# 🚀 Complete Setup Guide - From Zero to Deployment

## 📋 What We'll Do

1. ✅ Create GitHub Repository
2. ✅ Setup GitHub Desktop
3. ✅ Push Code to GitHub
4. ✅ Deploy to Hostinger

---

## Step 1: Create GitHub Repository (5 minutes)

### 1.1 Go to GitHub.com

1. Open your browser
2. Go to: **https://github.com**
3. **Login** to your GitHub account (or create one if you don't have it)

### 1.2 Create New Repository

1. Click the **"+"** icon in the top right corner
2. Click **"New repository"**

3. Fill in the form:
   - **Repository name**: `v2student` (or any name you like)
   - **Description**: "Student Management System - Laravel"
   - **Visibility**: 
     - Choose **Private** (recommended) or **Public**
   - **IMPORTANT**: 
     - ❌ **DO NOT** check "Add a README file"
     - ❌ **DO NOT** check "Add .gitignore"
     - ❌ **DO NOT** check "Choose a license"
   - Leave everything **unchecked**

4. Click **"Create repository"** button

### 1.3 Copy Repository URL

After creating, GitHub will show you a page with setup instructions. **Copy the repository URL**:
- It will look like: `https://github.com/yourusername/v2student.git`
- **Save this URL** - you'll need it!

---

## Step 2: Install GitHub Desktop (if not installed)

### 2.1 Download GitHub Desktop

1. Go to: **https://desktop.github.com/**
2. Click **"Download for macOS"** (or Windows if you're on Windows)
3. Install the application
4. Open GitHub Desktop

### 2.2 Sign In to GitHub Desktop

1. Open GitHub Desktop
2. Click **"Sign in to GitHub.com"**
3. Enter your GitHub username and password
4. Authorize GitHub Desktop

---

## Step 3: Connect Your Project to GitHub (10 minutes)

### 3.1 Add Local Repository in GitHub Desktop

1. Open **GitHub Desktop**
2. Click **"File"** → **"Add Local Repository"**
3. Click **"Choose..."** button
4. Navigate to: `/Users/rajesh/Documents/GitHub/v2student`
5. Click **"Add repository"**

### 3.2 Review Files to Commit

GitHub Desktop will show you all files. **IMPORTANT - Uncheck these files:**

❌ **DO NOT commit these:**
- `.env` (if it exists)
- `vendor/` folder
- `node_modules/` folder
- `database/database.sqlite`
- Any `.backup` files
- `storage/logs/*.log` files

✅ **DO commit these:**
- All PHP files
- All migration files
- `composer.json`, `package.json`
- Configuration files
- Views, routes, etc.
- Documentation files

### 3.3 Make Your First Commit

1. At the bottom left, you'll see a text box
2. Write commit message: `Initial commit - Production ready with migration fixes`
3. Click **"Commit to main"** button

### 3.4 Publish to GitHub

1. After committing, you'll see a button: **"Publish repository"**
2. Click **"Publish repository"**
3. A window will pop up:
   - **Name**: `v2student` (should match your GitHub repo name)
   - **Description**: "Student Management System - Laravel"
   - **Keep this code private**: Check if you want private repo
4. Click **"Publish Repository"**

### 3.5 Verify Upload

1. Go back to your browser
2. Visit: `https://github.com/yourusername/v2student`
3. You should see all your files uploaded!
4. ✅ **Success!** Your code is now on GitHub

---

## Step 4: Setup Hostinger Subdomain (5 minutes)

### 4.1 Login to Hostinger

1. Go to: **https://hpanel.hostinger.com**
2. Login with your Hostinger credentials

### 4.2 Create Subdomain

1. In hPanel, go to **"Domains"** → **"Subdomains"**
2. Click **"Create a New Subdomain"**
3. Fill in:
   - **Subdomain name**: `v2insurance`
   - **Document root**: `/public_html/v2insurance`
   - (or `/domains/softpromis.com/public_html/v2insurance` - check your Hostinger structure)
4. Click **"Create"**
5. Wait 2-3 minutes for DNS propagation

---

## Step 5: Create MySQL Database in Hostinger (5 minutes)

### 5.1 Create Database

1. In hPanel, go to **"Databases"** → **"MySQL Databases"**
2. Click **"Create Database"**
3. Fill in:
   - **Database name**: `softpromis_v2insurance` (or your preferred name)
   - **Database user**: Create a new user (or use existing)
   - **Password**: Create a strong password
   - **Host**: Usually `localhost` (check Hostinger docs)
4. Click **"Create"**

### 5.2 Save Database Credentials

**Write down these details:**
- Database name: `_________________`
- Database username: `_________________`
- Database password: `_________________`
- Database host: `localhost` (usually)

---

## Step 6: Deploy Code to Hostinger (15 minutes)

### 6.1 Enable SSH Access (if not enabled)

1. In hPanel, go to **"Advanced"** → **"SSH Access"**
2. Enable SSH access
3. Note your SSH credentials:
   - **Host**: (usually your server IP or domain)
   - **Username**: (your Hostinger username)
   - **Port**: Usually `22`

### 6.2 Connect via SSH

**Option A: Using Terminal (macOS/Linux)**

Open Terminal and run:
```bash
ssh username@softpromis.com
# Or
ssh username@your-server-ip
```

**Option B: Using Hostinger Terminal**

1. In hPanel, go to **"Advanced"** → **"Terminal"**
2. Click **"Open Terminal"**

### 6.3 Navigate to Subdomain Directory

```bash
cd /public_html/v2insurance
# OR (check which one exists)
cd /domains/softpromis.com/public_html/v2insurance
```

### 6.4 Clone Repository from GitHub

```bash
# Clone your GitHub repository
git clone https://github.com/yourusername/v2student.git .

# Note: The dot (.) at the end clones into current directory
```

**If git is not installed:**
```bash
# Install git (if needed)
# Contact Hostinger support or use File Manager method below
```

### 6.5 Alternative: Upload via File Manager

**If SSH/git doesn't work:**

1. In hPanel, go to **"Files"** → **"File Manager"**
2. Navigate to `public_html/v2insurance`
3. Download your repository from GitHub as ZIP:
   - Go to: `https://github.com/yourusername/v2student`
   - Click **"Code"** → **"Download ZIP"**
4. Extract ZIP file on your computer
5. Upload all files to `v2insurance` folder via File Manager
6. Make sure `.htaccess` files are uploaded (they might be hidden)

---

## Step 7: Configure Production Environment (10 minutes)

### 7.1 Create .env File

**Via SSH:**
```bash
cd /public_html/v2insurance
cp .env.example .env
nano .env
```

**Via File Manager:**
1. Go to File Manager
2. Navigate to `v2insurance` folder
3. Find `.env.example` file
4. Copy it and rename to `.env`
5. Edit `.env` file

### 7.2 Configure .env File

Edit `.env` with these settings:

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://v2insurance.softpromis.com

# Database (use credentials from Step 5.2)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=softpromis_v2insurance
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

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

**Save the file** (Ctrl+X, then Y, then Enter in nano)

---

## Step 8: Run Deployment (10 minutes)

### 8.1 Make Deployment Script Executable

```bash
cd /public_html/v2insurance
chmod +x deploy.sh
```

### 8.2 Run Deployment Script

```bash
./deploy.sh
```

**OR run commands manually:**

```bash
# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Install Node dependencies
npm install --production

# 3. Build assets
npm run build

# 4. Generate app key
php artisan key:generate --force

# 5. Run migrations
php artisan migrate --force

# 6. Create storage link
php artisan storage:link

# 7. Set permissions
chmod -R 755 storage bootstrap/cache public

# 8. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

---

## Step 9: Verify Deployment (5 minutes)

### 9.1 Check Migration Status

```bash
php artisan migrate:status
```

**All migrations should show "Ran"**

### 9.2 Test Application

1. Open browser
2. Visit: **https://v2insurance.softpromis.com**
3. Check if homepage loads
4. Try login functionality

### 9.3 Check for Errors

```bash
# View error logs
tail -50 storage/logs/laravel.log
```

---

## Step 10: Configure Document Root (if needed)

### 10.1 Point Document Root to Public Folder

**Option A: Change in Hostinger**

1. In hPanel, go to **"Domains"** → **"Subdomains"**
2. Edit subdomain: `v2insurance`
3. Change document root to: `/public_html/v2insurance/public`
4. Save

**Option B: Use Root .htaccess (already included)**

The root `.htaccess` file will redirect to `public/` folder automatically.

---

## ✅ Deployment Checklist

- [ ] GitHub repository created
- [ ] Code pushed to GitHub
- [ ] GitHub Desktop connected
- [ ] Subdomain created in Hostinger
- [ ] MySQL database created
- [ ] Code deployed to server
- [ ] `.env` file configured
- [ ] Dependencies installed
- [ ] Migrations run successfully
- [ ] Application loads at subdomain URL
- [ ] No errors in logs

---

## 🆘 Troubleshooting

### Can't connect via SSH?

**Use File Manager instead:**
- Upload files manually via File Manager
- Use Hostinger Terminal (in hPanel) instead of SSH

### Git not available?

**Use manual upload:**
1. Download repository as ZIP from GitHub
2. Extract on your computer
3. Upload via File Manager

### Migration errors?

```bash
# Check what's wrong
php artisan migrate:status

# View detailed error
tail -100 storage/logs/laravel.log
```

### 500 Internal Server Error?

```bash
# Check .env has APP_KEY
php artisan key:generate --force

# Fix permissions
chmod -R 755 storage bootstrap/cache

# Clear caches
php artisan config:clear
php artisan cache:clear
```

---

## 📞 Need Help?

1. Check error logs: `storage/logs/laravel.log`
2. Verify `.env` configuration
3. Check file permissions
4. Contact Hostinger support for server issues

---

**Start with Step 1 and work through each step!** 🚀

