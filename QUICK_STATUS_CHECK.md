# 🔍 Quick Server Status Check

## Run Status Check on Hostinger

I've created a comprehensive status check script. Run it on your Hostinger server:

### Step 1: Upload Script (or create it on server)

**Option A: Create directly on server**

Connect via SSH:
```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student
```

Then create the script:
```bash
cat > check_status.sh << 'EOF'
#!/bin/bash
# [Paste the script content here]
EOF
chmod +x check_status.sh
```

**Option B: Copy from local**

The script is saved as `check_server_status.sh` in your project. You can upload it via File Manager or copy-paste it.

---

### Step 2: Run Status Check

```bash
cd ~/public_html/v2student
./check_status.sh
```

Or if you want to run it directly:
```bash
bash check_status.sh
```

---

## Manual Quick Checks

If you prefer to check manually, run these commands:

```bash
cd ~/public_html/v2student

# 1. Check PHP version
php -v

# 2. Check Laravel
php artisan --version

# 3. Check database connection
php artisan migrate:status

# 4. Check storage link
ls -la public/storage

# 5. Check recent errors
tail -20 storage/logs/laravel.log

# 6. Check file permissions
ls -la storage bootstrap/cache

# 7. Check .env exists
ls -la .env

# 8. Check APP_KEY
grep APP_KEY .env
```

---

## What the Script Checks

✅ PHP Version  
✅ Laravel Version  
✅ .env Configuration  
✅ Database Connection  
✅ Migration Status  
✅ Storage Symlink  
✅ File Permissions  
✅ Disk Space  
✅ Recent Errors  
✅ Cache Status  
✅ Git Status  
✅ Composer Dependencies  
✅ Application Routes  
✅ Database Tables  

---

**Run the script and share the output if you want me to review it!** 🚀

