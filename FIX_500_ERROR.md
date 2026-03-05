# 🔧 Fix 500 Server Error

## Step 1: Check Error Logs

Connect to Hostinger and check the Laravel error log:

```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student
tail -100 storage/logs/laravel.log
```

This will show the actual error causing the 500.

---

## Step 2: Common Fixes

### Fix 1: Clear All Caches

```bash
cd ~/public_html/v2student
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Fix 2: Regenerate App Key

```bash
php artisan key:generate --force
php artisan config:cache
```

### Fix 3: Check File Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 755 public
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
```

### Fix 4: Verify .env File

```bash
# Check if .env exists and has APP_KEY
cat .env | grep APP_KEY
cat .env | grep APP_DEBUG
```

Should show:
- `APP_KEY=base64:...` (should have a value)
- `APP_DEBUG=false` (for production)

### Fix 5: Check Storage Link

```bash
ls -la public/storage
```

Should show: `public/storage -> ../storage/app/public`

If not:
```bash
rm -f public/storage
ln -s ../storage/app/public public/storage
```

---

## Step 3: Complete Fix Command (Copy-Paste)

Run this entire command:

```bash
cd ~/public_html/v2student && php artisan optimize:clear && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan key:generate --force && chmod -R 755 storage bootstrap/cache public && php artisan config:cache && php artisan route:cache && php artisan view:cache && echo "✅ Fixed! Check logs: tail -50 storage/logs/laravel.log"
```

---

## Step 4: Check Error Logs Again

After running fixes, check logs:

```bash
tail -50 storage/logs/laravel.log
```

Share the error message if it persists.

---

## Common 500 Error Causes:

1. **Missing APP_KEY** - Fixed by `php artisan key:generate --force`
2. **Permission issues** - Fixed by `chmod -R 755 storage`
3. **Cache issues** - Fixed by clearing all caches
4. **Database connection** - Check `.env` DB credentials
5. **Missing storage link** - Create symlink manually
6. **Missing vendor files** - Run `composer install`

---

**Start with Step 1 to see the actual error, then apply the fixes!**

