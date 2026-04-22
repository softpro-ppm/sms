<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    private const EMAIL = 'superadmin@softpromis.com';

    private const LEGACY_EMAIL = 'superadmin@edumanage.com';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('Kalpana@123');

        $user = User::query()
            ->where('email', self::EMAIL)
            ->first();

        if (! $user) {
            $user = User::query()
                ->where('email', self::LEGACY_EMAIL)
                ->where('role', 'super_admin')
                ->first();
        }

        if ($user) {
            $user->update([
                'name' => 'Super Admin',
                'email' => self::EMAIL,
                'password' => $password,
                'role' => 'super_admin',
                'training_partner_id' => null,
                'is_active' => true,
            ]);
            $this->command->info('Super Admin user updated.');
            $this->command->info('Login: '.self::EMAIL.' / Kalpana@123');
            $this->command->info('Use Reception/Admin login section.');

            return;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => self::EMAIL,
            'password' => $password,
            'role' => 'super_admin',
            'training_partner_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('Super Admin user created.');
        $this->command->info('Login: '.self::EMAIL.' / Kalpana@123');
        $this->command->info('Use Reception/Admin login section.');
    }
}
