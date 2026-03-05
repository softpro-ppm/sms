<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Enrollment;
use App\Models\User;

class ResetStudentPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:student-payment-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all student and payment data for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Resetting student and payment data...');

        // Delete payment allocations first (due to foreign key constraints)
        PaymentAllocation::truncate();
        $this->info('✅ Payment allocations cleared');

        // Delete payments
        Payment::truncate();
        $this->info('✅ Payments cleared');

        // Delete enrollments
        Enrollment::truncate();
        $this->info('✅ Enrollments cleared');

        // Delete student users (but keep admin users)
        User::where('role', 'student')->delete();
        $this->info('✅ Student users cleared');

        // Delete students
        Student::truncate();
        $this->info('✅ Students cleared');

        // Reset auto-increment counters (SQLite syntax)
        if (\DB::getDriverName() === 'sqlite') {
            \DB::statement('DELETE FROM sqlite_sequence WHERE name IN ("students", "payments", "enrollments", "payment_allocations", "users")');
            $this->info('✅ Auto-increment counters reset (SQLite)');
        } else {
            \DB::statement('ALTER TABLE students AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE payments AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE enrollments AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE payment_allocations AUTO_INCREMENT = 1');
            \DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
            $this->info('✅ Auto-increment counters reset (MySQL)');
        }

        $this->info('');
        $this->info('📋 Enrollment numbers will start from SP' . date('Y') . '3000');

        $this->info('');
        $this->info('🎉 Student and payment data reset complete!');
        $this->info('');
        $this->info('You can now test the complete flow:');
        $this->info('1. Register a new student');
        $this->info('2. Enroll them in a course');
        $this->info('3. Record payments');
        $this->info('4. Approve payments');
        $this->info('5. Generate receipts');
        $this->info('');
        $this->info('Admin users and other data remain intact.');
    }
}