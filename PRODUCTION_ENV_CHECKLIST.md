# Production .env Checklist

Update these values in your **server's** `.env` file (the one on Hostinger at `~/domains/softpromis.com/public_html/sms/.env`).

---

## Required Changes

| Variable | Local/Example | Production Value |
|---------|---------------|------------------|
| **APP_NAME** | Laravel | `Softpro Student` or your app name |
| **APP_ENV** | local | `production` |
| **APP_DEBUG** | true | `false` |
| **APP_URL** | http://localhost | `https://sms.softpromis.com` |

---

## Database (Hostinger MySQL)

| Variable | Local | Production |
|----------|-------|------------|
| **DB_CONNECTION** | mysql | `mysql` |
| **DB_HOST** | 127.0.0.1 | `localhost` (or value from Hostinger) |
| **DB_PORT** | 3306 | `3306` |
| **DB_DATABASE** | v2student | Your Hostinger DB name (e.g. `u820431346_sms`) |
| **DB_USERNAME** | v2local | Your Hostinger DB username |
| **DB_PASSWORD** | Metx@12345 | Your Hostinger DB password |

---

## Mail (Hostinger)

| Variable | Production Value |
|----------|------------------|
| **MAIL_MAILER** | `smtp` |
| **MAIL_HOST** | `smtp.hostinger.com` |
| **MAIL_PORT** | `465` |
| **MAIL_USERNAME** | `info@softpro.co.in` |
| **MAIL_PASSWORD** | Your Hostinger email password |
| **MAIL_FROM_ADDRESS** | `info@softpro.co.in` |
| **MAIL_FROM_NAME** | `Softpro` |
| **MAIL_ENCRYPTION** | `ssl` |

---

## Logging

| Variable | Local | Production |
|----------|-------|------------|
| **LOG_LEVEL** | debug | `error` or `warning` |

---

## Session (fixes 419 / form POST issues on HTTPS)

| Variable | Production Value |
|----------|------------------|
| **SESSION_SECURE_COOKIE** | `true` |
| **SESSION_SAME_SITE** | `lax` |
| **SESSION_DOMAIN** | `null` (or `.softpromis.com` if using multiple subdomains) |

## Optional (Keep Defaults Usually)

- **SESSION_DRIVER** – `database` (fine for production)
- **CACHE_STORE** – `database` (fine for shared hosting)
- **QUEUE_CONNECTION** – `database`

---

## Sample Production .env Block

```env
APP_NAME="Softpro Student"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://sms.softpromis.com

LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=u820431346_sms
DB_USERNAME=u820431346_v2student
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=info@softpro.co.in
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=info@softpro.co.in
MAIL_FROM_NAME="Softpro"
MAIL_ENCRYPTION=ssl
```

---

## How to Update on Server

**Option 1 – SSH + nano**
```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/domains/softpromis.com/public_html/sms
nano .env
# Edit, then Ctrl+X, Y, Enter to save
php artisan config:clear
```

**Option 2 – Hostinger File Manager**
1. hPanel → File Manager
2. Go to `domains/softpromis.com/public_html/sms`
3. Edit `.env`

---

**Get DB credentials:** hPanel → Databases → MySQL Databases
