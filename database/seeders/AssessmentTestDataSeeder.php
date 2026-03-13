<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\User;
use App\Services\EnrollmentNumberService;
use App\Services\PaymentAllocationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AssessmentTestDataSeeder extends Seeder
{
    /**
     * Seed fully paid, batch-completed students for local assessment testing.
     */
    public function run(): void
    {
        // Ensure admin exists for payment approval
        if (!User::where('role', 'admin')->exists()) {
            $this->call(AdminUserSeeder::class);
        }

        // 1. Course (MS Office)
        $course = Course::firstOrCreate(
            ['name' => 'MS Office'],
            [
                'description' => 'Microsoft Office Suite - Word, Excel, PowerPoint',
                'course_fee' => 3000,
                'registration_fee' => 200,
                'assessment_fee' => 100,
                'duration_days' => 30,
                'is_active' => true,
            ]
        );

        // 2. Batch - ended 2 weeks ago (completed, within 1 year)
        $batchEndDate = now()->subWeeks(2);
        $batchStartDate = $batchEndDate->copy()->subDays(30);

        $batch = Batch::firstOrCreate(
            [
                'course_id' => $course->id,
                'batch_name' => 'MSO-24-TEST',
            ],
            [
                'start_date' => $batchStartDate,
                'end_date' => $batchEndDate,
                'max_students' => 50,
                'is_active' => true,
            ]
        );

        // 3. Assessment
        $assessment = Assessment::firstOrCreate(
            [
                'course_id' => $course->id,
                'title' => 'MS Office Certification Exam',
            ],
            [
                'description' => 'Final assessment for MS Office course completion',
                'time_limit_minutes' => 30,
                'total_questions' => 25,
                'passing_percentage' => 35,
                'is_active' => true,
            ]
        );

        // 4. Ensure question bank has enough questions
        $questionCount = QuestionBank::where('course_id', $course->id)->count();
        if ($questionCount < 25) {
            $this->call(MSOfficeQuestionBankSeeder::class);
        }

        // 5. Test students
        $studentsData = [
            ['full_name' => 'Test Student One', 'email' => 'student1@test.com', 'father_name' => 'Father One', 'aadhar' => '123456789001'],
            ['full_name' => 'Test Student Two', 'email' => 'student2@test.com', 'father_name' => 'Father Two', 'aadhar' => '123456789002'],
            ['full_name' => 'Test Student Three', 'email' => 'student3@test.com', 'father_name' => 'Father Three', 'aadhar' => '123456789003'],
        ];

        $adminUser = User::where('role', 'admin')->first();

        foreach ($studentsData as $data) {
            // Student
            $student = Student::firstOrCreate(
                ['email' => $data['email']],
                [
                    'aadhar_number' => $data['aadhar'],
                    'full_name' => $data['full_name'],
                    'father_name' => $data['father_name'],
                    'gender' => 'male',
                    'phone' => '9876543210',
                    'status' => 'approved',
                    'is_active' => true,
                    'approved_at' => now(),
                ]
            );

            // User for student login
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['full_name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'student_id' => $student->id,
                    'is_active' => true,
                ]
            );
            if (!$user->student_id) {
                $user->update(['student_id' => $student->id]);
            }

            // Enrollment - skip if already enrolled in this batch
            $existingEnrollment = Enrollment::where('student_id', $student->id)
                ->where('batch_id', $batch->id)
                ->first();

            if ($existingEnrollment) {
                // Ensure fully paid
                $existingEnrollment->update([
                    'paid_amount' => $existingEnrollment->total_fee,
                    'outstanding_amount' => 0,
                    'is_eligible_for_assessment' => true,
                ]);
                $this->command->info("  Updated enrollment for {$data['full_name']}");
                continue;
            }

            $totalFee = $course->course_fee + $course->registration_fee + $course->assessment_fee;
            $enrollmentNumber = EnrollmentNumberService::generateEnrollmentNumber();

            $enrollment = Enrollment::create([
                'enrollment_number' => $enrollmentNumber,
                'student_id' => $student->id,
                'batch_id' => $batch->id,
                'enrollment_date' => $batchStartDate,
                'status' => 'active',
                'total_fee' => $totalFee,
                'paid_amount' => 0,
                'outstanding_amount' => $totalFee,
                'is_eligible_for_assessment' => false,
                'registration_fee' => $course->registration_fee,
                'course_fee' => $course->course_fee,
                'assessment_fee' => $course->assessment_fee,
            ]);

            // Payment - full amount, approved
            $receiptNumber = 'RCP' . now()->format('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            while (Payment::where('payment_receipt_number', $receiptNumber)->exists()) {
                $receiptNumber = 'RCP' . now()->format('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            $payment = Payment::create([
                'student_id' => $student->id,
                'enrollment_id' => $enrollment->id,
                'payment_receipt_number' => $receiptNumber,
                'amount' => $totalFee,
                'payment_type' => 'partial',
                'status' => 'approved',
                'approved_by' => $adminUser?->id,
                'approved_at' => now(),
            ]);

            // Allocate payment and update enrollment
            $allocationService = new PaymentAllocationService();
            $allocationService->allocatePayment($payment);

            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalPaid = $enrollment->total_fee - $totalOutstanding;

            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0,
            ]);

            if ($totalOutstanding <= 0) {
                $payment->update(['payment_type' => 'full']);
            }

            $this->command->info("  Created {$data['full_name']} - Enrol: {$enrollmentNumber}");
        }

        $this->command->info('');
        $this->command->info('Assessment test data seeded successfully!');
        $this->command->info('');
        $this->command->info('Student logins (password: password):');
        $this->command->info('  - student1@test.com');
        $this->command->info('  - student2@test.com');
        $this->command->info('  - student3@test.com');
        $this->command->info('');
        $this->command->info('Go to /login, sign in as student, then /student/assessments');
    }
}
