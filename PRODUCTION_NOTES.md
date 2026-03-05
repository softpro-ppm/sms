# 📝 Production Deployment Notes

## ✅ Deployment Status: COMPLETE

**Deployment Date:** December 6, 2025  
**Domain:** https://v2student.softpromis.com  
**Server:** Hostinger (145.14.146.15)  
**Project Path:** `/home/u820431346/public_html/v2student`

---

## 🗄️ Database Configuration

### MySQL Database Created in Production

**Note:** MySQL database has been created in Hostinger production environment.

**Database Details:**
- **Type:** MySQL
- **Host:** localhost
- **Database Name:** (Check .env file on server)
- **Username:** (Check .env file on server)
- **Password:** (Check .env file on server)

**To view database credentials:**
```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student
cat .env | grep DB_
```

---

## 🔐 Admin Credentials

**Default Admin User:**
- Email: `admin@edumanage.com`
- Password: `admin123`
- Role: admin

**To create admin user:**
```bash
cd ~/public_html/v2student
php artisan db:seed --class=AdminUserSeeder
```

---

## 📋 Deployment Checklist

- [x] Code deployed to Hostinger
- [x] MySQL database created
- [x] All migrations run successfully
- [x] Storage symlink created
- [x] Permissions set correctly
- [x] Laravel optimized (caches)
- [ ] Admin user created (run seeder)
- [ ] Domain configured correctly
- [ ] SSL certificate active

---

## 🔧 Important Commands

### Update Code
```bash
cd ~/public_html/v2student
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Create Admin User
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Check Migration Status
```bash
php artisan migrate:status
```

---

## 🌐 Server Information

- **SSH:** `ssh -p 65002 u820431346@145.14.146.15`
- **Project Path:** `/home/u820431346/public_html/v2student`
- **Domain:** `v2student.softpromis.com`
- **GitHub Repo:** `https://github.com/softpro-ppm/v2student.git`

---

## 📝 Notes

- MySQL database created manually in Hostinger hPanel
- Database credentials stored in `.env` file on server
- All migrations completed successfully
- Storage symlink created manually (exec() disabled on server)

---

---

## ⚠️ Force Delete / Form POST Not Working

If Force Delete or other POST forms fail in production:

1. **Add to `.env` on server:**
   ```env
   SESSION_SECURE_COOKIE=true
   ```
2. **If still failing** – Hostinger ModSecurity may block some form data. Options:
   - In hPanel → Security → ModSecurity → temporarily disable for your domain to test
   - Or add to `public/.htaccess` (if allowed): `SecFilterEngine Off` and `SecFilterScanPOST Off`
3. **Clear caches** after .env change:
   ```bash
   php artisan config:clear && php artisan config:cache
   ```

**Last Updated:** February 11, 2026

