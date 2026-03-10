# Deployment Guide

## Quick Deploy

From the project root, run:

```bash
npm run deploy
```

Or with a custom commit message:

```bash
./scripts/deploy.sh "Fix WhatsApp templates"
```

**What it does:**
1. Builds frontend (Vite)
2. Git add, commit (if changes), push
3. SSHs to server and runs: `git pull` + `php artisan config:clear`

**SSH:** `ssh -p 65002 u820431346@145.14.146.15`  
**Server path:** `~/public_html/sms`

**Tip:** Set up SSH key auth for passwordless deploy:
```bash
ssh-copy-id -p 65002 u820431346@145.14.146.15
```

---

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

---

## 419 Page Expired (Registration / Login)

If users get "419 Page Expired" when submitting the registration or login form:

1. **No-cache headers** – Auth pages now send `Cache-Control: no-store` so browsers don't serve cached pages with stale CSRF tokens.

2. **Session storage** – Ensure `storage/framework/sessions` is writable on the server:
   ```bash
   chmod -R 775 storage/framework/sessions
   ```

3. **Session config** – In `.env`, ensure:
   - `SESSION_SECURE_COOKIE=true` (for HTTPS)
   - `APP_URL=https://sms.softpromis.com` (exact match, no trailing slash)

4. **Quick fix** – Ask the user to refresh the page and try again (gets a fresh CSRF token).
