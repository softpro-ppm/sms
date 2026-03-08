# Franchise Model Plan for SoftPro SMS

## Overview

Plan to support multiple franchisees with separate access to `sms.softpromis.com`, each with their own:
- Student fees (different per franchise)
- Courses (different or overridden)
- Certificates (slightly different branding)
- Batches (different per franchise)

SoftPro HQ gets revenue (₹200–500 per student) and monitors franchise logins/activity.

---

## 1. Architecture: Multi-Tenant with Shared Database

Use a **single database** with `franchise_id` on all tenant-scoped tables. One codebase, one deployment, data isolated by franchise.

---

## 2. New Role: Super Admin

| Role | Scope | Access |
|------|--------|--------|
| **super_admin** | All franchises | Create/manage franchises, view all data, revenue reports, activity monitoring |
| **admin** | Single franchise | Same as today, scoped to their franchise |
| **reception** | Single franchise | Same as today, scoped to their franchise |
| **student** | Self only | Same as today |

---

## 3. Database Changes

### 3.1 New Table: `franchises`

```
franchises
├── id
├── name (e.g., "SoftPro PPM", "SoftPro XYZ Location")
├── code (e.g., "PPM", "XYZ") -- short code for enrollment numbers, certificates
├── address, city, state, pincode
├── contact_name, contact_phone, contact_email
├── softpro_revenue_per_student (200-500)
├── status (active, suspended, pending)
├── logo_path (optional - for certificates)
├── created_at, updated_at
```

### 3.2 Add `franchise_id` to Existing Tables

| Table | Notes |
|-------|-------|
| `users` | Admin/reception belong to a franchise; `franchise_id` nullable for super_admin |
| `students` | Each student belongs to a franchise |
| `courses` | Either global (SoftPro) or franchise-specific (see below) |
| `batches` | Via `course_id` → `course.franchise_id` |
| `enrollments` | Via `student_id` → `student.franchise_id` |
| `payments` | Via `student_id` |
| `certificates` | Via `student_id` |
| `assessments` | Via `course_id` |
| `question_banks` | Via `course_id` |

### 3.3 New Table: `franchise_revenue_records`

```
franchise_revenue_records
├── id
├── franchise_id
├── enrollment_id (or student_id)
├── amount (200-500)
├── status (pending, collected)
├── created_at
```

---

## 4. Course Strategy: Hybrid Model

**Option A (recommended): Global catalog + franchise overrides**

- SoftPro defines a global course catalog.
- Each franchise can:
  - Use global courses as-is.
  - Override fees per course (e.g. `course_franchise_fees` table: `franchise_id`, `course_id`, `course_fee`, `registration_fee`, `assessment_fee`).

**Option B: Fully franchise-specific courses**

- Each franchise creates its own courses.
- More flexible, but more work and less standardization.

---

## 5. Fee Handling

- **Enrollment:** When enrolling, use franchise-specific fees (from override or global course).
- **SoftPro share:** On enrollment or first payment, create a `franchise_revenue_records` row with the agreed amount (₹200–500).
- **Payment flow:** Franchise collects full fee; SoftPro share is tracked and can be invoiced/collected separately.

---

## 6. Certificates

- Add `franchise_id` to certificates (via student).
- Certificate template changes:
  - SoftPro logo (always).
  - Franchise logo (if set).
  - Franchise name/location.
  - Certificate number format: e.g. `CERT-{FRANCHISE_CODE}-{STUDENT}-{YEAR}`.

---

## 7. Batches

- Batches stay linked to courses.
- If courses are franchise-scoped, batches are automatically franchise-scoped.
- Franchise admins only see and manage batches for their courses.

---

## 8. Login & Access Control

### 8.1 Same Portal, Different Dashboards

- URL: `sms.softpromis.com`
- After login:
  - **super_admin** → `/admin/super` (or similar) with franchise list, revenue, activity.
  - **admin/reception** → `/admin/dashboard` (existing), but all queries filtered by `franchise_id`.

### 8.2 Middleware: `EnsureFranchiseScope`

```php
// For admin/reception: ensure user->franchise_id is set
// All queries: ->where('franchise_id', auth()->user()->franchise_id)
// Or use a global scope on models
```

### 8.3 Data Isolation

- Add a `BelongsToFranchise` trait or global scope on models.
- Controllers use `->where('franchise_id', auth()->user()->franchise_id)` (or equivalent) for all franchise-scoped queries.

---

## 9. Revenue & Monitoring

### 9.1 Revenue Tracking

- On enrollment: create `franchise_revenue_records` with `amount` = agreed per-student fee.
- Super admin dashboard:
  - Total revenue by franchise.
  - Revenue by month.
  - Pending vs collected.

### 9.2 Activity Monitoring

- Use `sessions` (already has `user_id`).
- Add `franchise_id` to `users` so you can:
  - See who logged in and when.
  - Optionally add `activity_logs` (user_id, franchise_id, action, timestamp).

---

## 10. Migration Strategy for Existing Data

1. Create `franchises` table.
2. Insert one franchise row for your current TC (e.g. "SoftPro PPM").
3. Add `franchise_id` to all relevant tables (nullable initially).
4. Backfill: set `franchise_id` for all existing rows to the default franchise.
5. Make `franchise_id` non-nullable where required.
6. Update `users`: set `franchise_id` for existing admin/reception; keep `franchise_id` null for super_admin.

---

## 11. Implementation Phases

| Phase | Scope |
|-------|--------|
| **Phase 1** | `franchises` table, `franchise_id` on users/students/courses/batches |
| **Phase 2** | Super admin role, franchise-scoped queries, franchise management UI |
| **Phase 3** | Course fee overrides, `franchise_revenue_records`, revenue dashboard |
| **Phase 4** | Certificate templates with franchise branding |
| **Phase 5** | Activity monitoring, login logs |

---

## 12. Files to Touch (Overview)

| Area | Files |
|------|--------|
| Migrations | New `franchises`, `franchise_revenue_records`, add `franchise_id` to existing tables |
| Models | `User`, `Student`, `Course`, `Batch`, `Enrollment`, `Payment`, `Certificate` + `Franchise` |
| Middleware | New `EnsureFranchiseScope`, `EnsureSuperAdmin` |
| Controllers | All admin controllers: add franchise scoping; new `FranchiseController`, `SuperAdminDashboardController` |
| Views | Admin layout: franchise switcher for super_admin; franchise-specific branding where needed |
| Certificate | `CertificateTemplateService`, `training-certificate.blade.php` |

---

## 13. Student Portal

- Students already see only their own data via `student_id`.
- Students belong to a franchise via `student.franchise_id`.
- No change needed for student-facing flows; franchise is implicit.

---

## 14. Registration Flow

- Public registration: you need a way to assign `franchise_id` to new students.
- Options:
  - Registration link with franchise code: `/register?franchise=PPM`
  - Or admin/reception create students (they are franchise-scoped), so `franchise_id` comes from logged-in user.

---

## 15. Summary

| Requirement | Approach |
|-------------|----------|
| Separate access per franchise | Same portal; `franchise_id` on users; scoped dashboards |
| Different fees | Course fee overrides per franchise |
| Different courses | Optional; can start with shared catalog + overrides |
| Different certificates | Franchise logo/name in template; franchise code in certificate number |
| Different batches | Automatic via franchise-scoped courses |
| Revenue ₹200–500/student | `franchise_revenue_records` on enrollment |
| Monitor logins | `franchise_id` on users + sessions/activity logs |
