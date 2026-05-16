# TICKET-01 — Baseline measurement

## Purpose

Capture **before** numbers for Sprint 1 hotspots so performance work is evidence-driven. Measure on **staging or production** with **representative row counts** (idle/local DB baselines are misleading).

---

## Routes ↔ controller methods (repo map)

| # | HTTP | Path | Route name | Controller::method | Notes |
|---|------|------|------------|---------------------|--------|
| 1 | GET | `/admin/payments/pending` | `admin.payments.pending` | `PaymentController::pending` → `buildPendingPaymentsCollection` | Loads scoped active `Enrollment` with `student`, `batch.course`, **`payments`**, filters in PHP |
| 2 | GET | `/admin/reports` | `admin.reports.index` | `ReportsController::index` | Tabbed; payments tab uses `paymentQuery` + repeated `(clone $query)` stats |
| 3 | GET | `/admin/dashboard` | `admin.dashboard` | `DashboardController::index` | Multiple aggregates + chart helpers (`getMonthlyEnrollments`, `getMonthlyRevenue`, `getCoursePopularity`, `getBatchPerformance`) |
| 4 | POST | `/verify` | `verify.search` | `StudentVerificationController::search` | Wide `OR` + leading `LIKE`, `get()` without hard limit, heavy `with([...])` |
| 5 | POST | `/public/student-verification/search` | `public.student-verification.search` | `StudentVerificationController::search` | Same handler as row 4 |
| 6 | — | Admin chrome | *(any view extending `layouts.admin`)* | `ViewComposerServiceProvider` → `AdminLayoutScopes::pendingStudentsQuery`, `pendingPaymentsQuery` | Two extra queries per full admin page render |

**Auth:** Rows 1–3 and admin chrome require `auth`, `role:admin,reception,super_admin`, `password.force`. Rows 4–5 are public (CSRF on POST).

---

## Scripted navigations / calls (3–5 scenarios)

Run each **5–20 times** after warmup; record **p50 / p95 / p99** server-side latency (APM, ingress, or PHP-FPM slow log) **and** either total **SQL count** per request or **MySQL slow query log** excerpts.

### Scenario A — Pending payments (primary hotspot)

1. Log in as TP-scoped admin (and once as super admin if behavior differs).
2. GET `/admin/payments/pending` (default query string).
3. Optional: change `per_page` if used in URL.

### Scenario B — Reports (payments tab)

1. GET `/admin/reports?tab=payments` (add typical filters if staff use them).
2. Repeat with `tab=enrollments` if time permits (same ticket: reports index).

### Scenario C — Dashboard

1. GET `/admin/dashboard`.

### Scenario D — Public verification search

1. POST `/verify` with `_token` + `search_term` (realistic 3+ char term that returns matches).
2. Repeat POST `/public/student-verification/search` only if still in use.

### Scenario E — Admin layout overhead (composer)

1. GET `/admin/dashboard` **and** GET `/admin/profile` (both use `layouts.admin` per repo).
2. Compare **SQL count** difference attributable to composer queries (`students` pending count + `payments` pending with `whereHas('student', …)`).

---

## Metrics to capture

| Metric | How |
|--------|-----|
| Latency p95/p99 | APM (Datadog/New Relic/etc.), or reverse-proxy access logs + upstream timing |
| Query count / request | Laravel Telescope (staging only), Debugbar (local/staging only), or temporary logging policy approved by team |
| DB | MySQL `slow_query_log`, `long_query_time`, note `Rows_examined` for worst queries |
| Load | Approximate row counts: active enrollments, payments, students |

---

## Baseline results *(fill in staging/prod)*

| Scenario | Env | Approx. data scale (rows) | p95 (ms) | p99 (ms) | Queries/request (if measured) | Worst SQL / notes |
|----------|-----|---------------------------|----------|----------|--------------------------------|-------------------|
| A Pending | | | | | | |
| B Reports payments | | | | | | |
| C Dashboard | | | | | | |
| D POST /verify | | | | | | |
| E Layout overhead | | | | | | |

**Collected in this workspace:** Runtime p95/p99 **were not** captured here (no staging/production access, no representative dataset in CI).

---

## Code inspection summary *(evidence without runtime)*

This complements empty cells above until staging fills them.

- **`PaymentController::buildPendingPaymentsCollection`** (`routes`: `admin.payments.pending`): Eager-loads **all** scoped **active** enrollments with **`payments`** then filters outstanding in PHP → **high CPU/memory and query volume risk** as enrollments grow.
- **`ReportsController::index`**: Stats use **multiple clones** of the same scoped builder → redundant scans per tab load.
- **`DashboardController::index`**: Several aggregate queries per visit; **`getBatchPerformance`** eager-loads **`enrollments`** on batches → large hydration risk.
- **`StudentVerificationController::search`**: Leading-wildcard `LIKE`, OR across columns/subqueries, **`get()` without LIMIT** → worst-case **full-table style** work + large response hydration.
- **`ViewComposerServiceProvider`**: Two count queries on **every** `layouts.admin` render; pending payments count uses **`whereHas('student', …)`** → predictable per-request overhead.

---

## `/admin/payments/pending` — release-blocking?

| Question | Answer |
|----------|--------|
| **Functional / correctness blocker for Sprint 1 ship?** | **No** — page works; Sprint 1 explicitly defers pending-SQL rewrite unless baseline proves critical. |
| **Performance blocker?** | **TBD** — fill the baseline table on staging/prod. |
| **Escalation rule** | If **Scenario A p95** exceeds the team’s agreed admin-page SLO (e.g. **> 3 s** or **> N queries** / timeouts under load), treat as **critical for Sprint 2 prioritization** and notify PM/architect; **does not by itself block** Sprint 1 security queue items unless ops declares hard SLO breach. |

---

## Recommended next performance fix *(evidence-based)*

1. **Locked for Sprint 1 (per brief):** short-TTL cache for **`AdminLayoutScopes`** counts in **`ViewComposerServiceProvider`** — improves **every** admin page without touching payment correctness.
2. **After baseline row is filled:** If **Scenario A** dominates latency/query count vs **Scenario E**, escalate **`buildPendingPaymentsCollection` SQL rewrite** to Sprint 2 top item; if composer overhead is a large fraction of cheaper pages, the locked cache fix is validated.

---

## Acceptance checklist

- [ ] Routes above mapped to controller methods (done in this doc).
- [ ] At least **3 scenarios** (A–C minimum) measured on **non-empty** staging with notes on data scale.
- [ ] Explicit **blocker / not blocker** statement for pending page (see table — default **not Sprint 1 ship blocker** pending metrics).
- [ ] Worst query or query-count snapshot attached (slow log or APM).

---

## References (files)

- `routes/web.php` — route definitions
- `app/Http/Controllers/Admin/PaymentController.php` — `pending`, `buildPendingPaymentsCollection`
- `app/Http/Controllers/Admin/ReportsController.php` — `index`
- `app/Http/Controllers/Admin/DashboardController.php` — `index`
- `app/Http/Controllers/Public/StudentVerificationController.php` — `search`
- `app/Providers/ViewComposerServiceProvider.php` — `layouts.admin`
- `app/Support/AdminLayoutScopes.php` — pending queries
