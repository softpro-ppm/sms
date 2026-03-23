# Training Partner – Phase 4: Public TP Registration + Logo

## What Was Implemented

1. **Public TP Registration** (`/register/partner`)
   - Form: name, code, logo (optional), address, district, mandal, city, state, pincode, contact (name, phone, email)
   - New TPs created with `type=STANDARD`, `status=pending`
   - Success page after submission
   - "Become a Partner" link in main header (Verify Student, Register, Become a Partner)

2. **Super Admin: Approve/Reject Pending TPs**
   - On TP show page, when status is `pending`: Approve and Reject buttons
   - **Approve:** Modal to enter Student Approval Deduction (₹); sets status to `active`
   - **Reject:** Sets status to `rejected`
   - Filter by status: active, suspended, pending, rejected
   - Pending count in stats

3. **Logo Upload**
   - Public registration: optional logo (JPEG/PNG, max 2MB)
   - Super Admin create/edit: optional logo
   - Stored in `storage/app/public/partner-logos/`
   - Run `php artisan storage:link` if not already done

---

## Deploy Steps

```bash
git add .
git commit -m "Phase 4: Public TP registration, approve/reject, logo upload"
git push

# On server
php artisan storage:link   # if not exists
php artisan config:clear
```

---

## Test Checklist

### 1. Public Registration
- Visit `/register/partner` or click "Become a Partner"
- Fill form, optionally upload logo
- Submit → success page
- Check DB: new row in `training_partners` with status=pending

### 2. Super Admin Approve
- Login as super admin
- Go to Training Partners, filter by Pending
- Open a pending partner
- Click Approve → enter deduction amount (e.g. 200) → Approve
- Status should become Active, deduction saved

### 3. Super Admin Reject
- Open another pending partner
- Click Reject → confirm
- Status should become Rejected

### 4. Logo
- Create TP with logo (public or Super Admin)
- View TP show page → logo visible
- Edit TP, upload new logo → replaces old
