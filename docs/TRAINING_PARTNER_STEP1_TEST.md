# Training Partner – Step 1: Testing Guide

## What Was Implemented (Phase 1 – Foundation)

1. **New table:** `training_partners`
   - Columns: type, name, code, logo_path, district, mandal, address, contact, wallet_balance, status
   - Seeded: **Softpro HQ** (code=HQ, type=HQ)

2. **New columns:**
   - `users.training_partner_id` (nullable, FK)
   - `students.training_partner_id` (nullable, FK)

3. **Backfill:** Existing admin/reception users and all students → assigned to HQ

4. **Models:** `TrainingPartner`, updated `User`, `Student` with relationships

5. **Logic:** Admin-created students get `training_partner_id` from logged-in user; public registration defaults to HQ

---

## How to Deploy & Test

### Step 1: Deploy to Server

```bash
# From your project root
git add .
git commit -m "Training Partner Phase 1: training_partners table, HQ seed, user/student columns"
git push

# SSH to server
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/sms  # or your actual path

git pull
php artisan migrate --force
php artisan config:clear
```

### Step 2: Verify Migration

```bash
php artisan tinker
```

In tinker:

```php
// Check HQ exists
\App\Models\TrainingPartner::where('code', 'HQ')->first();
// Should return: type=HQ, name=Softpro Skill Solutions

// Check admin user has training_partner_id
\App\Models\User::where('role', 'admin')->first()->training_partner_id;
// Should return HQ id (1)

// Check student has training_partner_id
\App\Models\Student::first()->training_partner_id;
// Should return HQ id (1)
```

### Step 3: Test Existing Flow (No Visible Change)

1. **Login as Admin**  
   - Same URL, same credentials  
   - Dashboard should load as before

2. **Students list**  
   - Should show all students (no filtering yet)

3. **Create new student (Admin)**  
   - Register a new student  
   - Verify in DB: `training_partner_id` = HQ id

4. **Public registration**  
   - Register a new student via `/register`  
   - Verify: `training_partner_id` = HQ id

5. **Certificate, payments, assessments**  
   - Should work as before (no logic changes yet)

### Step 4: Rollback (If Needed)

```bash
php artisan migrate:rollback --step=2
```

This removes `training_partner_id` from users/students and drops `training_partners`.

---

## Next Step (Phase 2)

- Add Super Admin role
- Create Super Admin area
- Training Partner CRUD
- Wallet transactions table
