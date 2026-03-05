# Deploy v2student – Steps

## Pre-deploy (local)

1. **Commit and push to GitHub**
   ```bash
   git add .
   git commit -m "Add 8 email templates, signature, deploy updates"
   git push origin main
   ```

2. **Production `.env` on server**
   - `APP_URL` = your live URL (e.g. `https://v2student.softpromis.com`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - DB credentials (MySQL)
   - Mail settings:
     ```env
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.hostinger.com
     MAIL_PORT=465
     MAIL_USERNAME=info@softpro.co.in
     MAIL_PASSWORD=your_password
     MAIL_FROM_ADDRESS=info@softpro.co.in
     MAIL_FROM_NAME="Softpro"
     MAIL_ENCRYPTION=ssl
     ```

## On server (SSH)

```bash
cd /path/to/v2student   # e.g. /domains/softpromis.com/public_html/v2student

git pull origin main

chmod +x deploy.sh
./deploy.sh
```

## After deploy

1. Visit Admin → Settings → Email Templates.
2. Send a test email to confirm delivery.
3. Verify storage link: `php artisan storage:link` (if needed).
