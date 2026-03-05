# Deployment Checklist

Use this every time you deploy changes.

---

## 1. Build (if assets changed)

```bash
cd /Users/rajesh/Documents/projects/sms

# Only if you changed JS/CSS/views that use Vite
npm run build
```

---

## 2. Local Deploy (commit & push)

```bash
cd /Users/rajesh/Documents/projects/sms

git add -A
git status
git commit -m "Your commit message"
git push origin main
```

---

## 3. Production Deploy (on server)

**SSH to server:**
```bash
ssh -p 65002 u820431346@145.14.146.15
```

**Then run:**
```bash
cd /home/u820431346/domains/softpromis.com/public_html/sms

git pull origin main

php artisan view:clear
./deploy.sh
```

**If you updated .env on server:**
```bash
php artisan config:clear
php artisan config:cache
```

---

## Quick Reference

| Step | Command / Action |
|------|------------------|
| Build | `npm run build` |
| Commit | `git add -A && git commit -m "msg"` |
| Push | `git push origin main` |
| SSH | `ssh -p 65002 u820431346@145.14.146.15` |
| Server path | `/home/u820431346/domains/softpromis.com/public_html/sms` |
| Pull + Deploy | `git pull origin main` then `./deploy.sh` |
