# Deployment Guide

## .env File (IMPORTANT)

**`.env` is NOT in git** – it must be created/edited separately on each environment (local, server). This prevents your local `.env` from overwriting the server's production `.env` when you `git pull`.

### After git pull on server (first time after this fix)

When you pull, `.env` will be removed from the repo. You must recreate it:

1. Copy from example: `cp .env.example .env`
2. Edit with your production values: `nano .env`
3. Run: `php artisan config:clear`

### Production .env template (sms.softpromis.com)

```env
APP_NAME="Student Management System"
APP_ENV=production
APP_KEY=base64:vOEWjf5MPNg0R1CRYpGw33LaGg1sH0a2PYDF2N4YNNw=
APP_DEBUG=false
APP_URL=https://sms.softpromis.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u820431346_sms
DB_USERNAME=u820431346_sms
DB_PASSWORD=your_mysql_password

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true

CACHE_STORE=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=info@softpro.co.in
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@softpro.co.in
MAIL_FROM_NAME="Softpro Skill Solutions"

WHATSAPP_ACCESS_TOKEN=your_meta_token
WHATSAPP_PHONE_NUMBER_ID=880926738448475
WHATSAPP_TEMPLATE_LANGUAGE=en
WHATSAPP_API_URL=https://graph.facebook.com/v17.0
WHATSAPP_BUTTON_URL_EMPTY_SUFFIX=true
```

Replace `your_mysql_password`, `your_mail_password`, `your_meta_token` with actual values.

### Never commit .env

The `.gitignore` excludes `.env`. Do not force-add it. Each machine keeps its own `.env`.
