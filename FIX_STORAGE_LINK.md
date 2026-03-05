# Fix Storage Link Issue

## Problem
`php artisan storage:link` fails because `exec()` function is disabled on Hostinger.

## Solution: Create Symlink Manually

Run this command in your Hostinger SSH session:

```bash
# Remove existing link if it exists
rm -f public/storage

# Create the symlink manually
ln -s ../storage/app/public public/storage

# Verify it was created
ls -la public/storage
```

You should see something like:
```
lrwxrwxrwx 1 user user 23 Dec  6 05:30 public/storage -> ../storage/app/public
```

---

## Complete Deployment Commands

After fixing the storage link, continue with:

```bash
# Create storage link manually (since exec() is disabled)
rm -f public/storage
ln -s ../storage/app/public public/storage

# Set permissions
chmod -R 755 storage bootstrap/cache public

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear

# Verify
php artisan migrate:status
ls -la public/storage
```

---

## Alternative: Use File Manager

If SSH symlink doesn't work:

1. Go to Hostinger File Manager
2. Navigate to `public_html/v2student/public/`
3. Create a symlink named `storage` pointing to `../storage/app/public`
   - Or contact Hostinger support to enable symlinks

---

**Run the manual symlink command above and continue with deployment!** 🚀

