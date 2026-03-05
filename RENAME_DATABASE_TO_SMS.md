# Rename Production Database to `sms`

This guide covers renaming the production MySQL database from `v2student` (or `u820431346_v2student`) to `sms` (`u820431346_sms`) on Hostinger.

---

## Quick Overview

MySQL does not support renaming a database directly. You must:

1. Create a new database `sms` in Hostinger
2. Export data from the old database
3. Import into the new database
4. Update Laravel's `.env`
5. Clear config cache
6. Test and optionally drop the old database

---

## Step-by-Step

### 1. Create New Database in Hostinger

1. Log in to **hPanel** → **Databases** → **MySQL Databases**
2. Under **Create New Database**:
   - **Database name:** `sms` (Hostinger will prefix it as `u820431346_sms`)
   - Click **Create**
3. Note the full database name shown (e.g. `u820431346_sms`)

### 2. Grant User Access to New Database

1. In **MySQL Databases**, find **Add User To Database**
2. Select your MySQL user (e.g. `u820431346_v2student`) and the new database `u820431346_sms`
3. Click **Add**
4. Check **ALL PRIVILEGES** → **Make Changes**

### 3. Migrate Data via SSH

Connect via SSH:

```bash
ssh -p 65002 u820431346@145.14.146.15
```

Then run (replace placeholders with your actual values):

```bash
cd /home/u820431346/domains/softpromis.com/public_html/sms

# Set your MySQL password (get from hPanel if needed)
export DB_PASS='your_mysql_password'

# Export from old DB (replace OLD_DB name if different)
mysqldump -u u820431346_v2student -p"$DB_PASS" u820431346_v2student > /tmp/db_backup.sql

# Import into new DB
mysql -u u820431346_v2student -p"$DB_PASS" u820431346_sms < /tmp/db_backup.sql

# Update .env
sed -i 's/DB_DATABASE=u820431346_v2student/DB_DATABASE=u820431346_sms/' .env

# Clear Laravel cache
php artisan config:clear
php artisan config:cache
php artisan cache:clear

# Cleanup
rm /tmp/db_backup.sql
```

### 4. Or Use the Script

```bash
ssh -p 65002 u820431346@145.14.146.15
cd /home/u820431346/domains/softpromis.com/public_html/sms

# Edit script first to set OLD_DB, NEW_DB, DB_USER
# Then:
export DB_PASS='your_mysql_password'
chmod +x scripts/rename-db-to-sms.sh
./scripts/rename-db-to-sms.sh
```

### 5. Verify

1. Visit **https://sms.softpromis.com**
2. Test login, admin dashboard, student portal
3. Check that data appears correctly

### 6. Delete Old Database (Optional)

Once you've confirmed the new database works:

1. hPanel → **Databases** → **MySQL Databases**
2. Find `u820431346_v2student`
3. Click **Delete**

---

## .env Changes

After migration, your production `.env` should have:

```env
DB_DATABASE=u820431346_sms
DB_USERNAME=u820431346_v2student   # Same user, granted access to new DB
DB_PASSWORD=your_password
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| `Access denied` | Re-grant user privileges on new DB in hPanel |
| `Unknown database` | Ensure new DB was created; check exact name (with prefix) |
| Site shows 500 error | Run `php artisan config:clear && php artisan cache:clear` |
| Data missing | Re-run mysqldump/import; check for errors in output |
