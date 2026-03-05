# 🔐 Create Admin User on Production

The admin user doesn't exist yet. Run these commands on Hostinger to create it.

---

## Option 1: Run the Seeder (Easiest)

Connect to Hostinger via SSH and run:

```bash
ssh -p 65002 u820431346@145.14.146.15
cd ~/public_html/v2student
php artisan db:seed --class=AdminUserSeeder
```

This will create:
- **Admin:** admin@edumanage.com / admin123
- **Reception:** reception@edumanage.com / reception123

---

## Option 2: Create Admin Manually via Tinker

If the seeder doesn't work, create manually:

```bash
cd ~/public_html/v2student
php artisan tinker
```

Then paste this code:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin User',
    'email' => 'admin@edumanage.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'is_active' => true,
]);

exit
```

---

## Option 3: Check Existing Users

First, check if any users exist:

```bash
php artisan tinker
```

Then:
```php
use App\Models\User;
User::all();
exit
```

---

## Quick Copy-Paste Command

Run this entire command in Hostinger SSH:

```bash
cd ~/public_html/v2student && php artisan db:seed --class=AdminUserSeeder && echo "✅ Admin user created!"
```

---

**After running, try logging in again with:**
- Email: `admin@edumanage.com`
- Password: `admin123`

