# Sprint 1 tickets TICKET-04 → TICKET-08 (implementation notes)

## TICKET-04 — Payment approval idempotency

- **`approve`** / **`bulkApprove`** use **`approveSinglePendingPayment()`**: `DB::transaction`, **`Payment::where(... pending)->lockForUpdate()`**, skip if not pending; then **`allocatePayment`** + enrollment totals + **`payment_type = full`** when balanced.
- **`PaymentAllocationService::allocatePayment`**: returns existing rows if allocations already exist for `payment_id`.
- **Migration** `2026_05_16_120000_add_unique_payment_id_fee_type_to_payment_allocations.php`: **unique (`payment_id`, `fee_type`)**. If migration fails, dedupe duplicates manually then rerun.

## TICKET-05 — AMS queue

- **`SyncPaymentToAmsJob`** (`ShouldQueue`, **`$tries = 1`**) performs **`AmsSyncService::syncPaymentWithResult`** and updates **`ams_*`** on `payments`.
- **Approve / bulk approve**: **`SyncPaymentToAmsJob::dispatch($id)`** after successful transition (no synchronous HTTP in controller).
- **Retry** (`retryAmsSync`): **`SyncPaymentToAmsJob::dispatchSync($payment->id, true)`** so UI waits once; **`forceResync`** bypasses “already synced” short-circuit.
- **Production**: **`QUEUE_CONNECTION`** must run workers (`php artisan queue:work`); monitor **`payments.ams_sync_status`** / **`ams_last_error`** (not only `failed_jobs`).

## TICKET-06 — Verification rate limits

- **`RateLimiter::for('verify-search', …)`** in **`AppServiceProvider`**: **`VERIFY_SEARCH_RATE_LIMIT_PER_MINUTE`** (default **20**), keyed by **`$request->ip()`**.
- **`routes/web.php`**: **`POST /verify`** and **`POST /public/student-verification/search`** under **`throttle:verify-search`**.

## TICKET-07 — Verification payload reduction

- **Public result view** shows only: **photo (if any), full name, enrollment blocks** (course, batch, enrollment number, status, enrollment date, training period), compact verification sidebar.
- **Removed from public UI**: father’s name, DOB, gender, qualification, masked contacts/Aadhar, **exam marks table**.
- **Controller**: **`verifyByEnrollment`**, **`showResult`**, **`search`** eager-load **`enrollments.batch.course`** (+ **`documents`** where needed for photo); dropped **`assessmentResults`** chains for public paths.

## TICKET-08 — Admin layout counter cache

- **`AdminLayoutScopes::pendingStudentsCountCached`** / **`pendingPaymentsCountCached`**: **`Cache::remember`**, TTL **`PENDING_COUNTS_TTL_SECONDS` (60)**, key **`admin_pending.u{id}.{super|tp_X}.{students|payments}`**.
- **`ViewComposerServiceProvider`** sidebar counts use cached helpers.
- **`AppServiceProvider`** top bar **`notificationCount`** uses same cached counts (still loads latest 5 rows separately).
