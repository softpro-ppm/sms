# Legacy Students Feature – Plan

## Overview

Support students who completed courses years ago (offline) and now need:
- Online enrollment verification
- Online certificate
- Student portal login
- Same flow: registration → approval → enrollment → payment → exam → certificate
- All 8 points of mail and WhatsApp service mandatory

**Key constraint:** One single "Legacy Batch" – no hundreds of batches. All legacy students under one batch with per-enrollment overrides.

---

## Core Design

- **One batch only:** "Legacy Batch" (or "Legacy")
- **All legacy students** enrolled in this single batch
- **Per-enrollment overrides:** course, dates, fees stored on enrollment
- **Main flow untouched:** Batches page stays clean; legacy is isolated

---

## Data Model Changes

### 1. Single "Legacy" Batch (Create Once)

- **Course:** "Legacy" (or "Legacy / Archive") – dummy container
- **Batch:** "Legacy Batch"
- **Dates:** e.g. 2010-01-01 to 2030-12-31 (wide range)
- Created via migration or seed

### 2. Enrollment Overrides

Add to `enrollments` table:

| Column | Type | Purpose |
|--------|------|---------|
| `is_legacy` | boolean | Flag for legacy enrollment |
| `course_id` | FK, nullable | Override: actual course (Tally, MSO, etc.) |
| `start_date` | date, nullable | Override: student's batch start date |
| `end_date` | date, nullable | Override: student's batch end date |

**Legacy enrollment:**
- `batch_id` → Legacy Batch
- `is_legacy` = true
- `course_id` = actual course
- `start_date`, `end_date` = student's dates
- `registration_fee`, `course_fee`, `assessment_fee` = custom fees

**Regular enrollment:**
- `is_legacy` = false
- `course_id`, `start_date`, `end_date` = null (use batch's)

---

## Logic Changes

### Display / Certificate / Verification

Use overrides when present:

```php
// Effective course
$course = $enrollment->course_id ? $enrollment->course : $enrollment->batch->course;

// Effective dates
$startDate = $enrollment->start_date ?? $enrollment->batch->start_date;
$endDate   = $enrollment->end_date   ?? $enrollment->batch->end_date;
```

Apply in: Certificate generation, verification page, student portal, receipts, mail, WhatsApp.

### Assessment Eligibility

For legacy enrollments:
- Use `enrollment.course_id` to find assessment
- Allow anytime when fully paid (no 1-year-from-end restriction)

### Batches Page

- Legacy Batch appears as one row
- Optional: hide from main list or show "Legacy" badge
- No hundreds of legacy batches

---

## Legacy Students Page

- **Route:** `/admin/legacy-students`
- **Data:** Enrollments where `is_legacy = true`
- **Columns:** Student, Course, Start date, End date, Fee, Paid, Outstanding, Status, etc.
- **Menu:** Separate "Legacy Students" link
- **Isolated** from main Students/Batches pages

---

## Legacy Enrollment Flow

1. Admin opens student (create or existing)
2. Clicks "Enroll (Legacy)"
3. Form: Course (dropdown), Start date, End date (past), Registration fee, Course fee, Assessment fee
4. Submit → creates enrollment with Legacy Batch, `is_legacy` = true, overrides
5. Rest same: payment, assessment, certificate, mail, WhatsApp

---

## Isolation from Main Flow

| Area | Change |
|------|--------|
| Batch creation | None |
| Regular enrollment | None |
| Batches index | Optional badge/filter for Legacy Batch |
| Students index | None |
| Legacy students | Separate page + controller |
| Certificate | Use enrollment overrides when `is_legacy` |
| Verification | Same |
| Mail / WhatsApp | Same; ensure all 8 points use effective course/dates |

---

## Files to Touch

| File | Change |
|------|--------|
| Migration | Add `is_legacy`, `course_id`, `start_date`, `end_date` to enrollments |
| Migration/Seed | Create Legacy course + Legacy Batch |
| `Enrollment` model | New fields, accessors for effective course/dates |
| `LegacyStudentsController` | New controller |
| Legacy enrollment form | New view/section |
| Certificate / verification / receipts | Use effective course/dates when `is_legacy` |
| Student dashboard | Use effective course/dates for legacy enrollments |
| Assessment eligibility | Special case for legacy |
| Routes | Add legacy students route |
| Sidebar | Add "Legacy Students" link |

---

## Summary

- One batch: "Legacy Batch"
- Per-enrollment: course, dates, fees
- Main flow unchanged
- Dedicated Legacy Students page
- Certificate, verification, mail, WhatsApp use overrides for legacy
