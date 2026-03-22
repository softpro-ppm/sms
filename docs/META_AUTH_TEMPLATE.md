# Meta Authentication – Implementation Template

> **Purpose:** Blueprint for implementing Meta (Facebook) Login in SOFTPRO SMS. Use this when ready to add "Sign in with Facebook" for students or staff.

---

## Overview

| Item | Details |
|------|---------|
| **Provider** | Meta Login (Facebook Login) |
| **Laravel Package** | `laravel/socialite` + `league/oauth1-client` (optional) |
| **Use Case** | Students can sign in with Facebook; optionally link/link Facebook to existing accounts |
| **Scope** | Student portal initially; admin/reception can be added later |

---

## 1. Prerequisites (Meta Developer Console)

1. Go to [developers.facebook.com](https://developers.facebook.com)
2. Create or select an **App** (use existing WhatsApp Business app if applicable)
3. Add **Facebook Login** product
4. **Settings → Basic:** Note `App ID` and `App Secret`
5. **Facebook Login → Settings:**
   - Valid OAuth Redirect URI: `https://YOUR_DOMAIN/auth/facebook/callback`
   - For local: `http://localhost:8000/auth/facebook/callback`

---

## 2. Install Dependencies

```bash
composer require laravel/socialite
```

---

## 3. Configuration

### 3.1 `config/services.php`

Add inside the `return` array:

```php
'facebook' => [
    'client_id'     => env('FACEBOOK_CLIENT_ID'),
    'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
    'redirect'      => env('FACEBOOK_REDIRECT_URI', env('APP_URL') . '/auth/facebook/callback'),
],
```

### 3.2 `.env` (add these variables)

```env
# Meta (Facebook) Login – for future implementation
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT_URI="${APP_URL}/auth/facebook/callback"
```

---

## 4. Database Changes

### 4.1 Migration: Add `facebook_id` to users

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_id')->nullable()->unique()->after('email');
            $table->string('avatar')->nullable()->after('facebook_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['facebook_id', 'avatar']);
        });
    }
};
```

### 4.2 Optional: Social accounts table (for multiple providers later)

```php
Schema::create('social_accounts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('provider');       // facebook, google, etc.
    $table->string('provider_id');
    $table->timestamps();
    $table->unique(['provider', 'provider_id']);
});
```

---

## 5. Model Updates

### `app/Models/User.php`

Add to `$fillable`:

```php
'facebook_id',
'avatar',
```

---

## 6. Controller: `app/Http/Controllers/Auth/FacebookAuthController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class FacebookAuthController extends Controller
{
    /**
     * Redirect to Facebook for authentication.
     */
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    /**
     * Handle Facebook callback.
     */
    public function callback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Facebook login failed. Please try again.');
        }

        $user = User::where('facebook_id', $fbUser->getId())->first();

        if ($user) {
            Auth::login($user, true);
            return $this->redirectAfterLogin($user);
        }

        // Check if email exists (link account)
        $user = User::where('email', $fbUser->getEmail())->first();
        if ($user) {
            $user->update([
                'facebook_id' => $fbUser->getId(),
                'avatar'      => $fbUser->getAvatar(),
            ]);
            Auth::login($user, true);
            return $this->redirectAfterLogin($user);
        }

        // New user – create student + user (or restrict to staff only)
        // Option A: Allow new signups via Facebook
        $user = $this->createUserFromFacebook($fbUser);

        // Option B: Disallow – redirect with message
        // return redirect()->route('login')->with('error', 'No account found. Please register first.');

        Auth::login($user, true);
        return $this->redirectAfterLogin($user);
    }

    protected function createUserFromFacebook($fbUser): User
    {
        $student = Student::create([
            'email'          => $fbUser->getEmail() ?: $fbUser->getId() . '@facebook.com',
            'full_name'      => $fbUser->getName(),
            'status'         => 'pending',
            'is_active'      => false,
        ]);

        return User::create([
            'name'        => $fbUser->getName(),
            'email'       => $student->email,
            'password'    => Hash::make($fbUser->getId()),
            'role'        => 'student',
            'student_id'  => $student->id,
            'facebook_id' => $fbUser->getId(),
            'avatar'      => $fbUser->getAvatar(),
        ]);
    }

    protected function redirectAfterLogin(User $user)
    {
        if ($user->is_student) {
            return redirect()->intended(route('student.dashboard'));
        }
        return redirect()->intended(route('admin.dashboard'));
    }
}
```

---

## 7. Routes

Add to `routes/web.php` (inside guest middleware if desired):

```php
// Meta (Facebook) Login – for future implementation
Route::get('/auth/facebook', [FacebookAuthController::class, 'redirect'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookAuthController::class, 'callback'])->name('facebook.callback');
```

---

## 8. Login View – Add Facebook Button

In `resources/views/auth/login.blade.php` or split login view:

```html
@env('FACEBOOK_CLIENT_ID')
<a href="{{ route('facebook.redirect') }}"
   class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-[#1877F2] text-white hover:bg-[#166FE5] transition-colors">
    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
    Continue with Facebook
</a>
@endenv
```

---

## 9. Implementation Checklist

- [ ] Create Meta app + add Facebook Login product
- [ ] Add redirect URI in Meta dashboard
- [ ] `composer require laravel/socialite`
- [ ] Add config + .env variables
- [ ] Run migration for `facebook_id`, `avatar`
- [ ] Create `FacebookAuthController`
- [ ] Add routes
- [ ] Add "Continue with Facebook" button to login view
- [ ] Decide: allow new signups via Facebook or only link existing accounts?
- [ ] Test on staging before production

---

## 10. Security Notes

1. **HTTPS required** in production for OAuth callback.
2. **App Secret** must stay in `.env`; never commit it.
3. **Redirect URI** must match exactly (including trailing slash rules).
4. For **Meta App Review**, you may need to provide demo accounts (`meta:create-reviewer-accounts`).

---

## 11. Optional: Restrict to Students Only

If Facebook login is only for students:

```php
// In callback(), after creating/linking user:
if (!$user->is_student) {
    Auth::logout();
    return redirect()->route('login')->with('error', 'Facebook login is for students only.');
}
```

---

## References

- [Laravel Socialite](https://laravel.com/docs/socialite)
- [Meta Login for the Web](https://developers.facebook.com/docs/facebook-login/web)
- [Meta App Review](https://developers.facebook.com/docs/app-review)
