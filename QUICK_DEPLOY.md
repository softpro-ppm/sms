# ⚡ Quick Deployment Reference

Quick reference guide for deploying to Hostinger subdomain: **v2insurance.softpromis.com**

## 🎯 Prerequisites Checklist

- [ ] GitHub account
- [ ] GitHub Desktop installed
- [ ] Hostinger hosting account
- [ ] SSH access enabled in Hostinger
- [ ] PHP 8.2+ on server
- [ ] Composer available on server
- [ ] Node.js/npm available on server

---

## 📝 Step-by-Step Quick Guide

### 1️⃣ Create GitHub Repository

1. Go to GitHub → New Repository
2. Name: `v2student`
3. **Don't** initialize with README
4. Copy repository URL

### 2️⃣ Push to GitHub (GitHub Desktop)

```bash
# In GitHub Desktop:
1. File → Add Local Repository
2. Select: /Users/rajesh/Documents/GitHub/v2student
3. Uncheck: .env, vendor/, node_modules/, database.sqlite
4. Commit: "Initial commit - Production ready"
5. Publish repository
```

### 3️⃣ Create Subdomain in Hostinger

1. hPanel → Domains → Subdomains
2. Create: `v2insurance`
3. Document root: `/public_html/v2insurance`
4. Save

### 4️⃣ Deploy Files (SSH)

```bash
# Connect via SSH
ssh username@softpromis.com

# Navigate to subdomain directory
cd /domains/softpromis.com/public_html/v2insurance
# OR
cd /public_html/v2insurance

# Clone repository
git clone https://github.com/yourusername/v2student.git .

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### 5️⃣ Configure Environment

```bash
# Create .env file
cp .env.example .env

# Edit .env with your settings:
# - Database credentials
# - Mail settings
# - APP_URL=https://v2insurance.softpromis.com
```

### 6️⃣ Run Deployment Script

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

**OR manually:**

```bash
# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup Laravel
php artisan key:generate
php artisan migrate --force
php artisan storage:link

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### 7️⃣ Configure Document Root

**Option A:** Point document root to `public` folder:
- hPanel → Subdomains → Edit `v2insurance`
- Change document root to: `/public_html/v2insurance/public`

**Option B:** Use root `.htaccess` (already included):
- Root `.htaccess` redirects to `public/` folder

### 8️⃣ Test Application

Visit: `https://v2insurance.softpromis.com`

---

## 🔄 Updating Application

```bash
# On server via SSH
cd /domains/softpromis.com/public_html/v2insurance

# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations (if any)
php artisan migrate --force

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🗄️ Database Setup

**Important:** 
- **Local**: Uses SQLite (`database/database.sqlite`) ✅
- **Production**: Uses MySQL (configure below) 🚀

1. **Create MySQL Database in Hostinger:**
   - hPanel → Databases → MySQL Databases
   - Click **"Create Database"**
   - Database name: `softpromis_v2insurance`
   - Create user and assign to database
   - Note down: database name, username, password, host (usually `localhost`)

2. **Update .env for MySQL:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=softpromis_v2insurance
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```
   **Note:** Set `DB_CONNECTION=mysql` (not sqlite) for production!

3. **Run Migrations:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=AdminUserSeeder
   ```

**See [DATABASE_SETUP.md](./DATABASE_SETUP.md) for detailed database guide**

---

## 📧 Email Configuration (Hostinger)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your_email@softpromis.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@softpromis.com"
MAIL_FROM_NAME="Student Management System"
```

---

## 🔐 File Permissions

```bash
# Directories
chmod -R 755 storage bootstrap/cache public

# Files in storage
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
```

---

## 🐛 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| 500 Error | Check `.env`, permissions, `APP_KEY` |
| Assets missing | Run `npm run build` |
| DB error | Verify credentials in `.env` |
| Permission denied | Run `chmod -R 755 storage` |

---

## ✅ Post-Deployment Checklist

- [ ] Application loads at subdomain URL
- [ ] Login works
- [ ] Database connected
- [ ] Assets loading (CSS/JS)
- [ ] File uploads work
- [ ] Email sending works
- [ ] SSL certificate active
- [ ] Error logging works

---

**For detailed instructions, see [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

