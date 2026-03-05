<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@edumanage.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Reception User
        User::create([
            'name' => 'Reception User',
            'email' => 'reception@edumanage.com',
            'password' => Hash::make('reception123'),
            'role' => 'reception',
            'is_active' => true,
        ]);

        $this->command->info('Admin and Reception users created successfully!');
        $this->command->info('Admin Login: admin@edumanage.com / admin123');
        $this->command->info('Reception Login: reception@edumanage.com / reception123');
    }
}
