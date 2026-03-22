# Training Partner – Phase 2: Super Admin & TP Management

## What Was Implemented

1. **Super Admin role**
   - Migration adds `super_admin` to `users.role` enum
   - Super Admin users use Reception/Admin login section

2. **Super Admin area**
   - Routes: `/admin/super` (dashboard), `/admin/super/training-partners` (CRUD)
   - Middleware: `EnsureSuperAdmin` (super_admin only)
   - Sidebar: Super Admin sees "Super Dashboard" and "Training Partners" only

3. **Training Partner CRUD**
   - List with search, type filter, status filter
   - Create / Edit / Show / Delete (delete blocked for HQ and partners with staff/students)
   - Fields: type (HQ/STANDARD), name, code, status, address, district, mandal, city, state, pincode, contact info

4. **Seeder**
   - `SuperAdminUserSeeder`: creates `superadmin@edumanage.com` / `superadmin123`

---

## Deploy Steps

```bash
git add .
git commit -m "Training Partner Phase 2: Super Admin + TP CRUD"
git push

# SSH to server
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/sms

git pull
php artisan migrate --force
php artisan db:seed --class=SuperAdminUserSeeder
php artisan config:clear
```

---

## Test Checklist

### 1. Migration & Seeder
```bash
php artisan tinker
\App\Models\User::where('role', 'super_admin')->first();
# Should return Super Admin user
```

### 2. Login
- Use **Reception / Admin** login section (right side)
- Email: `superadmin@edumanage.com`
- Password: `superadmin123`
- Should redirect to `/admin/super` (Super Admin Dashboard)

### 3. Super Dashboard
- Stats: Total Partners, Active Partners, Total Students, HQ/Standard counts, Staff count
- Recent partners table with link to view

### 4. Training Partners
- **Index:** List HQ + any standard partners, filter by type/status, search
- **Create:** Add new partner (type STANDARD or HQ), status active/pending/suspended
- **Show:** Partner details, wallet, staff count, students count, contact info
- **Edit:** Update partner details
- **Delete:** Blocked for HQ; blocked if partner has staff or students

### 5. Regular Admin Unchanged
- Login as `admin@edumanage.com` → should go to regular admin dashboard
- Regular admin does NOT see Super Admin sidebar items
- Regular admin cannot access `/admin/super` (403)

---

## Routes Reference

| Route | Name |
|-------|------|
| GET /admin/super | admin.super.dashboard |
| GET /admin/super/training-partners | admin.super.training-partners.index |
| GET /admin/super/training-partners/create | admin.super.training-partners.create |
| POST /admin/super/training-partners | admin.super.training-partners.store |
| GET /admin/super/training-partners/{id} | admin.super.training-partners.show |
| GET /admin/super/training-partners/{id}/edit | admin.super.training-partners.edit |
| PUT /admin/super/training-partners/{id} | admin.super.training-partners.update |
| DELETE /admin/super/training-partners/{id} | admin.super.training-partners.destroy |
