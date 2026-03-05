# 🎯 Deployment Steps Summary

## Visual Step-by-Step Guide

```
┌─────────────────────────────────────────────────────────────┐
│  STEP 1: Prepare Project                                    │
└─────────────────────────────────────────────────────────────┘
   ✓ Clean up backup files
   ✓ Review .gitignore
   ✓ Ensure .env is NOT committed

┌─────────────────────────────────────────────────────────────┐
│  STEP 2: Create GitHub Repository                           │
└─────────────────────────────────────────────────────────────┘
   1. Go to github.com → New Repository
   2. Name: v2student
   3. Don't initialize with files
   4. Copy repository URL

┌─────────────────────────────────────────────────────────────┐
│  STEP 3: Push to GitHub (GitHub Desktop)                    │
└─────────────────────────────────────────────────────────────┘
   1. Open GitHub Desktop
   2. File → Add Local Repository
   3. Select: /Users/rajesh/Documents/GitHub/v2student
   4. Uncheck: .env, vendor/, node_modules/, database.sqlite
   5. Commit: "Initial commit - Production ready"
   6. Publish repository

┌─────────────────────────────────────────────────────────────┐
│  STEP 4: Create Subdomain in Hostinger                     │
└─────────────────────────────────────────────────────────────┘
   1. Login to hPanel
   2. Domains → Subdomains
   3. Create: v2insurance
   4. Document root: /public_html/v2insurance
   5. Save

┌─────────────────────────────────────────────────────────────┐
│  STEP 5: Deploy Files (SSH)                                │
└─────────────────────────────────────────────────────────────┘
   ssh username@softpromis.com
   cd /public_html/v2insurance
   git clone https://github.com/yourusername/v2student.git .

┌─────────────────────────────────────────────────────────────┐
│  STEP 6: Configure Environment                              │
└─────────────────────────────────────────────────────────────┘
   cp .env.example .env
   # Edit .env with:
   # - Database credentials
   # - Mail settings
   # - APP_URL=https://v2insurance.softpromis.com

┌─────────────────────────────────────────────────────────────┐
│  STEP 7: Run Deployment                                     │
└─────────────────────────────────────────────────────────────┘
   chmod +x deploy.sh
   ./deploy.sh

   OR manually:
   composer install --no-dev --optimize-autoloader
   npm install
   npm run build
   php artisan key:generate
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache

┌─────────────────────────────────────────────────────────────┐
│  STEP 8: Configure Document Root                           │
└─────────────────────────────────────────────────────────────┘
   Option A: Point to public folder
   Option B: Use root .htaccess (already included)

┌─────────────────────────────────────────────────────────────┐
│  STEP 9: Test Application                                  │
└─────────────────────────────────────────────────────────────┘
   Visit: https://v2insurance.softpromis.com
   ✓ Homepage loads
   ✓ Login works
   ✓ Database connected
   ✓ Assets loading

```

## 📋 Command Cheat Sheet

### Initial Deployment
```bash
# On server
cd /public_html/v2insurance
git clone https://github.com/yourusername/v2student.git .
cp .env.example .env
# Edit .env
chmod +x deploy.sh
./deploy.sh
```

### Update Application
```bash
cd /public_html/v2insurance
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

### Fix Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 755 public
find storage -type f -exec chmod 644 {} \;
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🔑 Key Files

- `.env` - Environment configuration (NEVER commit)
- `.env.example` - Template for .env
- `deploy.sh` - Automated deployment script
- `.htaccess` - Root redirect to public folder
- `public/.htaccess` - Laravel routing

## 📝 Important Notes

1. **Never commit `.env`** - Contains sensitive data
2. **Always backup database** before migrations
3. **Test locally** before deploying
4. **Monitor logs**: `storage/logs/laravel.log`
5. **Use HTTPS** - Configure SSL in Hostinger

## ✅ Pre-Commit Checklist

Before pushing to GitHub:
- [ ] `.env` is NOT staged
- [ ] `vendor/` is NOT staged
- [ ] `node_modules/` is NOT staged
- [ ] `database.sqlite` is NOT staged
- [ ] Backup files removed
- [ ] `.gitignore` is correct

## 🆘 Quick Fixes

| Problem | Command |
|---------|---------|
| 500 Error | Check `.env`, run `php artisan key:generate` |
| Assets missing | `npm run build` |
| Permission error | `chmod -R 755 storage` |
| Cache issues | `php artisan cache:clear` |

---

**For detailed instructions, see [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

