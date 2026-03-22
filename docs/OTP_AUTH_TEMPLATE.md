# OTP Authentication – Implementation Template

> **Purpose:** Blueprint for implementing OTP-based authentication in SOFTPRO SMS. Use this when ready to add OTP verification for admin login (2FA) and password reset.

---

## Overview

| Use Case | Flow | OTP Delivery |
|----------|------|---------------|
| **Admin Login (2FA)** | Email/Password → Send OTP → Verify OTP → Login | Email, WhatsApp, or SMS |
| **Password Reset** | Request reset → Send OTP → Verify OTP → Set new password | Email, WhatsApp, or SMS |

---

## 1. Use Case A: Admin Login OTP (2FA)

### Flow

```
Admin enters email + password
    → Credentials validated
    → Generate 6-digit OTP (valid 5–10 min)
    → Send OTP to admin's email / WhatsApp
    → Redirect to OTP verification page (user_id in session, not logged in yet)
    → Admin enters OTP
    → Verify OTP
    → Complete login, redirect to dashboard
```

### Scope

- **Only for admin and reception** (staff). Students skip OTP.
- Optional: configurable per-user or global enable/disable.

---

## 2. Use Case B: Password Reset OTP

### Flow (replace or augment current reset-link flow)

```
Staff requests password reset (enters email)
    → Find user (admin/reception only)
    → Generate 6-digit OTP (valid 10–15 min)
    → Send OTP to email / WhatsApp
    → Redirect to "Enter OTP" page (email in session)
    → Staff enters OTP
    → Verify OTP
    → Redirect to "Set new password" form (token/session)
    → Submit new password → Login → Done
```

### Current vs OTP

| Current | OTP Alternative |
|---------|-----------------|
| Laravel sends reset link via email | Send OTP instead |
| User clicks link, sets password | User enters OTP, then sets password |
| Link expires in 60 min | OTP expires in 10–15 min |

---

## 3. OTP Storage

### Option A: Cache (recommended)

```php
// Store: cache key = "otp:{$identifier}:{$purpose}"
Cache::put("otp:{$user->id}:login", [
    'code' => $otp,      // e.g. '847291'
    'attempts' => 0,
], now()->addMinutes(10));

// Retrieve & verify
$stored = Cache::get("otp:{$userId}:login");
if ($stored && $stored['code'] === $inputOtp) { /* valid */ }
```

### Option B: Database table

```php
Schema::create('otp_codes', function (Blueprint $table) {
    $table->id();
    $table->string('identifier');       // email or user_id
    $table->string('purpose');          // 'login', 'password_reset'
    $table->string('code', 6);
    $table->tinyInteger('attempts')->default(0);
    $table->timestamp('expires_at');
    $table->timestamps();
    $table->index(['identifier', 'purpose']);
});
```

---

## 4. OTP Generation

```php
// 6-digit numeric OTP
$otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
```

---

## 5. Delivery Channels

### 5.1 Email (Laravel Mail)

- Use existing Mail setup.
- Create `OtpMail` mailable with a simple "Your OTP is: 847291" message.
- Fast to implement.

### 5.2 WhatsApp (existing WhatsAppService)

- Use `WhatsAppService::sendMessage()` for free-form text:
  ```php
  $msg = "Your Softpro SMS login OTP is: {$otp}. Valid for 10 minutes.";
  app(WhatsAppService::class)->sendMessage($user->phone, $msg);
  ```
- Or create an OTP template in Meta Business Suite and use `sendTemplateMessage()`.
- Admin/reception need a `phone` or `whatsapp_number` field. Users table may need `phone` if only staff use OTP.

### 5.3 SMS (future)

- Use provider: Twilio, MSG91, Fast2SMS, etc.
- Add config + service wrapper similar to WhatsAppService.

---

## 6. Configuration

### 6.1 `config/otp.php` (create)

```php
<?php

return [
    'enabled' => env('OTP_ENABLED', false),
    'login_otp_enabled' => env('OTP_LOGIN_ENABLED', false),
    'password_reset_otp_enabled' => env('OTP_PASSWORD_RESET_ENABLED', false),
    'length' => 6,
    'expiry_minutes' => [
        'login' => 10,
        'password_reset' => 15,
    ],
    'max_attempts' => 5,
    'channel' => env('OTP_CHANNEL', 'email'), // email, whatsapp, sms
];
```

### 6.2 `.env` (add)

```env
# OTP Authentication – for future implementation
OTP_ENABLED=false
OTP_LOGIN_ENABLED=false
OTP_PASSWORD_RESET_ENABLED=false
OTP_CHANNEL=email
OTP_EXPIRY_MINUTES=10
```

---

## 7. Service: `app/Services/OtpService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generate(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function store(string $identifier, string $purpose, string $otp, int $minutes = 10): void
    {
        $key = "otp:{$identifier}:{$purpose}";
        Cache::put($key, [
            'code' => $otp,
            'attempts' => 0,
        ], now()->addMinutes($minutes));
    }

    public function verify(string $identifier, string $purpose, string $input): bool
    {
        $key = "otp:{$identifier}:{$purpose}";
        $stored = Cache::get($key);
        if (!$stored) return false;
        if ($stored['attempts'] >= config('otp.max_attempts', 5)) {
            Cache::forget($key);
            return false;
        }
        $valid = $stored['code'] === $input;
        if (!$valid) {
            $stored['attempts']++;
            Cache::put($key, $stored, Cache::get("{$key}_ttl") ?? now()->addMinutes(5));
        } else {
            Cache::forget($key);
        }
        return $valid;
    }

    public function send($user, string $otp, string $purpose): bool
    {
        $channel = config('otp.channel', 'email');
        if ($channel === 'email') {
            Mail::to($user->email)->send(new \App\Mail\OtpMail($otp, $purpose));
            return true;
        }
        if ($channel === 'whatsapp' && $user->phone) {
            $msg = "Your Softpro SMS OTP is: {$otp}. Valid for 10 minutes.";
            $result = app(WhatsAppService::class)->sendMessage($user->phone, $msg);
            return $result['success'] ?? false;
        }
        return false;
    }
}
```

---

## 8. Login Flow with OTP (modified LoginController)

```php
// In login() after credentials pass, for admin/reception:
if (config('otp.login_otp_enabled') && in_array($user->role, ['admin', 'reception'])) {
    $otp = app(OtpService::class)->generate();
    app(OtpService::class)->store($user->id, 'login', $otp, 10);
    app(OtpService::class)->send($user, $otp, 'login');
    session(['otp_pending_user_id' => $user->id]);
    return redirect()->route('login.otp.verify')->with('status', 'OTP sent to your email.');
}

// Normal login for students or when OTP disabled
Auth::login($user, $remember);
// ... redirect
```

---

## 9. New Routes

```php
// OTP verification (login 2FA)
Route::get('/login/otp', [OtpVerifyController::class, 'show'])->name('login.otp.verify');
Route::post('/login/otp', [OtpVerifyController::class, 'verify'])->name('login.otp.submit');

// Password reset OTP (replace or add)
Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password/otp', [ResetPasswordController::class, 'showResetFormOtp'])->name('password.reset.otp');
Route::post('/reset-password/otp', [ResetPasswordController::class, 'resetOtp'])->name('password.reset.otp.submit');
```

---

## 10. Controller: `OtpVerifyController` (login 2FA)

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerifyController extends Controller
{
    public function show()
    {
        if (!session('otp_pending_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $userId = session('otp_pending_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        $request->validate(['otp' => 'required|string|size:6']);

        $valid = app(OtpService::class)->verify($userId, 'login', $request->otp);
        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $user = User::find($userId);
        session()->forget('otp_pending_user_id');
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
```

---

## 11. View: `auth/verify-otp.blade.php`

```html
<form method="POST" action="{{ route('login.otp.submit') }}">
    @csrf
    <label>Enter 6-digit OTP sent to your email/phone</label>
    <input type="text" name="otp" maxlength="6" pattern="[0-9]*" inputmode="numeric" required>
    <button type="submit">Verify</button>
    <a href="{{ route('login') }}">Back to login</a>
</form>
```

---

## 12. Mailable: `app/Mail/OtpMail.php`

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $purpose = 'login'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->purpose === 'login' ? 'Your Login OTP' : 'Your Password Reset OTP';
        return new Envelope(subject: "Softpro SMS - {$subject}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.otp');
    }
}
```

---

## 13. Meta WhatsApp OTP Template (optional)

If using WhatsApp templates for OTP:

| Field | Value |
|-------|-------|
| Name | `otp_verification` |
| Category | Authentication |
| Language | English |
| Body | `Your Softpro SMS verification code is: {{1}}. Valid for {{2}} minutes. Do not share.` |
| Variables | `{{1}}` = OTP, `{{2}}` = 10 |

---

## 14. Implementation Checklist

### Admin login OTP (2FA)

- [ ] Add `config/otp.php` and `.env` variables
- [ ] Create `OtpService`
- [ ] Create `OtpMail` + `emails.otp` view
- [ ] Create `OtpVerifyController` + `auth/verify-otp.blade.php`
- [ ] Modify `LoginController` to send OTP for admin/reception when enabled
- [ ] Add routes
- [ ] Ensure staff users have `phone` or email for OTP delivery
- [ ] Set `OTP_LOGIN_ENABLED=true` when ready

### Password reset OTP

- [ ] Add OTP flow to `ForgotPasswordController` (or new controller)
- [ ] Create `password/otp-request`, `password/otp-verify`, `password/reset-otp` views
- [ ] Modify reset flow: request → OTP → verify → set password
- [ ] Set `OTP_PASSWORD_RESET_ENABLED=true` when ready

### Optional

- [ ] Add `phone` to `users` table if not present (for WhatsApp OTP)
- [ ] Create Meta WhatsApp OTP template
- [ ] Add "Resend OTP" with rate limiting (e.g. 1 per minute)

---

## 15. Security Notes

1. **Rate limiting:** Limit OTP requests per email/phone (e.g. 3 per 15 min)
2. **Attempt limit:** Max 5 wrong OTP attempts, then invalidate
3. **Expiry:** 10–15 minutes recommended
4. **HTTPS:** Required in production for all auth flows
5. **Logging:** Log OTP send/verify failures for monitoring

---

## References

- [Laravel Cache](https://laravel.com/docs/cache)
- [Laravel Mail](https://laravel.com/docs/mail)
- [WhatsApp Cloud API – Messages](https://developers.facebook.com/docs/whatsapp/cloud-api/reference/messages)
