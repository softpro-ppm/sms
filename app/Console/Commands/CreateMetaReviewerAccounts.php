<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateMetaReviewerAccounts extends Command
{
    protected $signature = 'meta:create-reviewer-accounts';

    protected $description = 'Create test admin and student accounts for Meta app review';

    public function handle(): int
    {
        $this->info('Creating Meta reviewer test accounts...');

        // 1. Create or update Admin
        $admin = User::updateOrCreate(
            ['email' => 'meta.reviewer@softpromis.com'],
            [
                'name' => 'Meta Reviewer',
                'password' => Hash::make('MetaReview2025!'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $this->info('Admin: meta.reviewer@softpromis.com / MetaReview2025!');

        // 2. Create Student + User
        $student = Student::firstOrCreate(
            ['email' => 'meta.student@softpromis.com'],
            [
                'aadhar_number' => '111122223333',
                'full_name' => 'Meta Test Student',
                'father_name' => 'Test Father',
                'gender' => 'male',
                'qualification' => 'Graduation',
                'phone' => '9999888877',
                'whatsapp_number' => '9999888877',
                'address' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Andhra Pradesh',
                'pincode' => '535501',
                'status' => 'approved',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'meta.student@softpromis.com'],
            [
                'name' => $student->full_name,
                'password' => Hash::make('9999888877'), // WhatsApp number = password
                'role' => 'student',
                'student_id' => $student->id,
                'is_active' => true,
            ]
        );
        $this->info('Student: meta.student@softpromis.com / 9999888877');

        $this->newLine();
        $this->info('Copy these credentials for Meta App Review:');
        $this->line('---');
        $this->line('Admin:  meta.reviewer@softpromis.com / MetaReview2025!');
        $this->line('Student: meta.student@softpromis.com / 9999888877');
        $this->line('---');

        return self::SUCCESS;
    }
}
