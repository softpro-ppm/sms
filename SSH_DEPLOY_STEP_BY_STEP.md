# SSH Deploy - Step by Step Guide

## Step 1: Open Terminal

- **Mac:** Open **Terminal** (Applications → Utilities → Terminal)
- **Windows:** Open **Command Prompt** or **PowerShell**

---

## Step 2: Connect via SSH

Type this command and press **Enter**:

```bash
ssh -p 65002 u820431346@145.14.146.15
```

When prompted for **password**, enter your SSH password (you won't see characters as you type – that's normal).

---

## Step 3: Go to Project Folder

After you're connected, you'll see a prompt like `u820431346@server:~$`.

Type:

```bash
cd ~/public_html/v2student
```

Press **Enter**.

---

## Step 4: Check You're in the Right Place

Type:

```bash
ls -la
```

You should see files like `artisan`, `composer.json`, `.env`, etc.

---

## Step 5: Pull Latest Code from GitHub

Type:

```bash
git pull origin main
```

Enter your GitHub credentials if asked.

---

## Step 6: Run Deployment Script

Type:

```bash
chmod +x deploy.sh
./deploy.sh
```

Wait for it to finish. It will:
- Install Composer packages
- Install Node packages
- Build assets
- Run migrations
- Seed email templates
- Create storage link
- Clear and cache config

---

## Step 7: Verify (Optional)

Check migration status:

```bash
php artisan migrate:status
```

---

## Step 8: Exit SSH

Type:

```bash
exit
```

---

## Quick One-Liner (after you're connected)

If you're already in the project folder:

```bash
cd ~/public_html/v2student && git pull origin main && ./deploy.sh
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Connection refused" | Check port 65002; try without `-p` |
| "Permission denied" | Wrong password; reset in Hostinger hPanel |
| "No such file or directory" | Try `cd /home/u820431346/public_html/v2student` |
| Git asks for credentials | Use GitHub Personal Access Token instead of password |
| `deploy.sh` fails | Run steps manually (see deploy.sh contents) |

---

## Your Details

- **SSH Host:** 145.14.146.15
- **Port:** 65002
- **Username:** u820431346
- **Project path:** `~/public_html/v2student` or `/home/u820431346/public_html/v2student`
