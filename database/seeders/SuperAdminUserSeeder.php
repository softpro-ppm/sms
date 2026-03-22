<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'superadmin@edumanage.com')->exists()) {
            $this->command->info('Super Admin user already exists.');
            return;
        }

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@edumanage.com',
            'password' => Hash::make('superadmin123'),
            'role' => 'super_admin',
            'training_partner_id' => null,
            'is_active' => true,
        ]);

        $this->command->info('Super Admin user created.');
        $this->command->info('Login: superadmin@edumanage.com / superadmin123');
        $this->command->info('Use Reception/Admin login section.');
    }
}
