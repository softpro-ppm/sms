# SMS → AMS Integration – Setup Note

This integration syncs fee/tuition income from SMS to AMS when payments are **approved** (collected).

---

## What Was Built

- **SMS**: When a payment is approved, `AmsSyncService` POSTs the income to AMS
- **AMS**: Receives the POST at `/api/income/from-sms`, validates API key, creates income transaction
- **Deduplication**: Uses `meta.sms_payment_id` – duplicate requests return 200 with `duplicate: true`

---

## SMS Side

### Files Created/Modified

- `app/Services/AmsSyncService.php` – syncs approved payment to AMS
- `config/services.php` – `ams` block (api_url, api_key, subcategory_id)
- `app/Http/Controllers/Admin/PaymentController.php` – calls sync after approve/bulkApprove
- `.env.example` – AMS_API_URL, AMS_API_KEY

### .env (SMS)

```
AMS_API_URL=https://ams.softpromis.com/api/income/from-sms
AMS_API_KEY=ims-ams-2025-x7Kp9mNq2

# AMS mapping: Softpro HO, Student (Income), Student Fees (defaults below)
# AMS_PROJECT_ID=1
# AMS_CATEGORY_ID=2
# AMS_SUBCATEGORY_ID=13
# AMS_USER_ID=2
```

### Field Mapping

| AMS Field        | SMS Source                    |
|------------------|-------------------------------|
| reference        | Student full_name             |
| description      | SMS-Fee-{payment_id}          |
| amount           | payment.amount                |
| transaction_date | approved_at or created_at     |
| meta             | sms_payment_id (for dedup)     |

### Sync Trigger

- **Option A**: Sync only when payment is **approved** (single or bulk)
- No sync on edit or delete

---

## AMS Side

### Files Created/Modified

- `app/Http/Controllers/Api/IncomeFromSmsController.php`
- `app/Http/Middleware/ValidateSmsApiKey.php`
- `app/Http/Requests/Income/IncomeFromSmsRequest.php`
- `routes/api.php` – POST /api/income/from-sms
- `config/services.php` – `sms` block
- `bootstrap/app.php` – sms.api_key middleware alias
- `.env.example` – SMS_API_KEY

### .env (AMS)

```
SMS_API_KEY=your-shared-secret
```

Use the **same value** as `AMS_API_KEY` in SMS.

---

## Shared Secret

- **SMS**: `AMS_API_KEY=xxx`
- **AMS**: `SMS_API_KEY=xxx`

Both must be identical.
