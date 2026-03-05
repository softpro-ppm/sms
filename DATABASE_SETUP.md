# 🗄️ Database Setup Guide

## Current Database Configuration

### Local Development (Current)
- **Database Type**: SQLite
- **Database File**: `database/database.sqlite`
- **Configuration**: Default in `config/database.php` is set to `sqlite`

### Production (Target)
- **Database Type**: MySQL
- **Host**: Hostinger MySQL server
- **Configuration**: Will be set via `.env` file

---

## 📋 Local Database (SQLite) - Current Setup

Your local environment is currently using **SQLite**, which is perfect for development:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/database.sqlite
```

**Advantages:**
- ✅ No server setup needed
- ✅ Fast for development
- ✅ Easy to backup (just copy the file)
- ✅ Perfect for local testing

**Current Status:**
- Database file exists: `database/database.sqlite` (336KB)
- Contains your development data
- Ready to use

---

## 🚀 Production Database (MySQL) - Setup Instructions

### Step 1: Create MySQL Database in Hostinger

1. **Login to Hostinger hPanel**
2. Navigate to **"Databases"** → **"MySQL Databases"**
3. Click **"Create Database"**
4. Fill in:
   - **Database Name**: `softpromis_v2insurance` (or your preferred name)
   - **Database User**: Create a new user or use existing
   - **Password**: Set a strong password
   - **Host**: Usually `localhost` (check Hostinger docs)
5. Click **"Create"**
6. **Note down these credentials:**
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### Step 2: Configure .env for Production

In your production `.env` file, set:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=softpromis_v2insurance
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Step 3: Run Migrations on Production

After setting up `.env`, run migrations:

```bash
# On production server via SSH
cd /public_html/v2insurance
php artisan migrate --force
```

This will create all tables in your MySQL database.

### Step 4: Seed Initial Data (Optional)

```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 🔄 Migrating Data from SQLite to MySQL (Optional)

If you want to migrate your local SQLite data to MySQL:

### Option 1: Export/Import via Laravel

1. **Export from SQLite:**
   ```bash
   # Create a seeder from your SQLite data
   php artisan iseed users,students,courses,enrollments --database=sqlite
   ```

2. **Import to MySQL:**
   ```bash
   # Change .env to MySQL
   # Run the generated seeders
   php artisan db:seed
   ```

### Option 2: Manual Export/Import

1. **Export SQLite data:**
   ```bash
   sqlite3 database/database.sqlite .dump > database_export.sql
   ```

2. **Convert and import to MySQL** (requires manual SQL conversion)

### Option 3: Fresh Start (Recommended for Production)

Start fresh in production with migrations:
```bash
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
```

---

## 🔧 Switching Local to MySQL (Optional)

If you want to use MySQL locally too:

### Step 1: Install MySQL

**macOS:**
```bash
brew install mysql
brew services start mysql
```

**Or use XAMPP/MAMP**

### Step 2: Create Local Database

```bash
mysql -u root -p
CREATE DATABASE v2student_local;
CREATE USER 'v2student_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON v2student_local.* TO 'v2student_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Update Local .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=v2student_local
DB_USERNAME=v2student_user
DB_PASSWORD=your_password
```

### Step 4: Run Migrations Locally

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

---

## 📊 Database Comparison

| Feature | SQLite (Local) | MySQL (Production) |
|---------|---------------|-------------------|
| **Setup** | ✅ No setup needed | ⚙️ Requires server |
| **Performance** | Fast for small DB | ⚡ Optimized for production |
| **Concurrent Users** | Limited | ✅ Handles many users |
| **Backup** | Copy file | mysqldump |
| **Best For** | Development | Production |

---

## ✅ Production Checklist

Before deploying to production:

- [ ] MySQL database created in Hostinger
- [ ] Database credentials noted down
- [ ] `.env` configured with MySQL settings
- [ ] Test connection: `php artisan migrate:status`
- [ ] Migrations run successfully
- [ ] Admin user seeded (if needed)
- [ ] Database backup strategy in place

---

## 🛠️ Useful Commands

### Check Database Connection
```bash
php artisan migrate:status
```

### View Database Configuration
```bash
php artisan config:show database
```

### Backup MySQL Database (Production)
```bash
mysqldump -u username -p database_name > backup.sql
```

### Restore MySQL Database
```bash
mysql -u username -p database_name < backup.sql
```

### Clear Database (Development Only!)
```bash
php artisan migrate:fresh
php artisan db:seed
```

---

## 🐛 Troubleshooting

### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**
- Check `DB_HOST` in `.env` (should be `localhost` or `127.0.0.1`)
- Verify MySQL is running
- Check firewall settings

### Issue: "Access denied for user"

**Solution:**
- Verify username and password in `.env`
- Check user has proper permissions
- Ensure user is allowed to connect from host

### Issue: "Unknown database"

**Solution:**
- Verify database name in `.env` matches created database
- Check database exists in Hostinger
- Ensure user has access to database

### Issue: Migration errors

**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connection works
- Ensure all migrations are compatible with MySQL

---

## 📝 Notes

1. **SQLite is fine for development** - No need to change unless you want to
2. **MySQL is required for production** - Better performance and scalability
3. **Migrations work for both** - Same migrations work on SQLite and MySQL
4. **Test locally first** - Always test migrations locally before production

---

**For deployment, see [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

