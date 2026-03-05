# 🔧 Update Domain Configuration

## ✅ Deployment Status: SUCCESS!

Your deployment completed successfully! All migrations ran. Now we need to update the domain.

---

## Step 1: Update .env File on Hostinger

Connect to Hostinger via SSH and update the `.env` file:

```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student
nano .env
```

**Find this line:**
```env
APP_URL=https://v2insurance.softpromis.com
```

**Change it to:**
```env
APP_URL=https://v2student.softpromis.com
```

**Save:** Press `Ctrl+X`, then `Y`, then `Enter`

---

## Step 2: Clear Config Cache

After updating `.env`, clear the config cache:

```bash
php artisan config:clear
php artisan config:cache
php artisan cache:clear
```

---

## Step 3: Verify

1. Visit: **https://v2student.softpromis.com**
2. Check if the site loads correctly
3. Test login functionality

---

## Quick Copy-Paste Commands

Run this in your Hostinger SSH session:

```bash
cd ~/public_html/v2student && sed -i 's|v2insurance.softpromis.com|v2student.softpromis.com|g' .env && php artisan config:clear && php artisan config:cache && php artisan cache:clear && echo "✅ Domain updated to v2student.softpromis.com"
```

---

**After updating, your site should be live at: https://v2student.softpromis.com** 🚀

