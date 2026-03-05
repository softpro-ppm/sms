# ✅ Pre-Deployment Checklist

Before deploying to production, complete these steps:

## 🧹 Clean Up Files

### Backup Files to Remove (Optional but Recommended)

The following backup files were found in the project. Consider removing them before committing:

```
app/Http/Controllers/Admin/StudentController.php.backup
app/Services/DocumentUploadService.php.backup
resources/views/admin/dashboard.blade.php.backup
resources/views/admin/students/create.blade.php.backup
resources/views/admin/students/create.blade.php.backup2
resources/views/admin/students/show.blade.php.backup
```

**To remove them:**
```bash
# On macOS/Linux
find . -name "*.backup" -type f -delete
find . -name "*.backup2" -type f -delete

# Or manually delete each file
```

### Files Already Ignored by .gitignore

These files are already excluded from Git:
- `.env` and `.env.*`
- `vendor/`
- `node_modules/`
- `database.sqlite`
- `storage/logs/*.log`

## 📝 Pre-Commit Checklist

Before committing to GitHub:

- [ ] **Review .gitignore** - Ensure sensitive files are excluded
- [ ] **Remove backup files** (optional but recommended)
- [ ] **Verify .env is NOT staged** - Check GitHub Desktop
- [ ] **Verify vendor/ is NOT staged** - Should be ignored
- [ ] **Verify node_modules/ is NOT staged** - Should be ignored
- [ ] **Check for sensitive data** - No passwords/keys in code
- [ ] **Test locally** - Ensure application runs
- [ ] **Build assets** - Run `npm run build` to verify

## 🔍 Verify Git Status

Before committing, check what will be committed:

```bash
git status
```

**Should NOT see:**
- `.env`
- `vendor/`
- `node_modules/`
- `database.sqlite`
- `*.backup` files (if you removed them)

**Should see:**
- All PHP files
- Configuration files (except .env)
- Views, routes, migrations
- Documentation files
- `package.json`, `composer.json`

## 🚀 Ready to Deploy?

Once checklist is complete:

1. ✅ All files cleaned up
2. ✅ Git status verified
3. ✅ Ready to commit and push

**Next Steps:**
- Follow [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)
- Or use [QUICK_DEPLOY.md](./QUICK_DEPLOY.md) for quick reference

## 📋 Environment Variables to Set

Make sure you have these ready for production `.env`:

- [ ] Database credentials (Hostinger MySQL)
- [ ] Mail server settings (Hostinger SMTP)
- [ ] APP_URL (https://v2insurance.softpromis.com)
- [ ] APP_KEY (will be generated)
- [ ] Any API keys (WhatsApp, etc.)

## 🔐 Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] SSL certificate configured
- [ ] File permissions set correctly
- [ ] `.env` file secured (not in Git)
- [ ] Default admin password changed

---

**Ready? Proceed to [DEPLOYMENT_GUIDE.md](./DEPLOYMENT_GUIDE.md)**

