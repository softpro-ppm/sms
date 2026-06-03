<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentCreditTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_drop_then_remove_enrollment_does_not_credit_paid_amount_twice(): void
    {
        [$admin, $enrollment, $student] = $this->makePaidEnrollment();

        $this->actingAs($admin)
            ->patch(route('admin.enrollments.drop', $enrollment))
            ->assertRedirect(route('admin.students.show', $student));

        $this->assertSame(1000.0, (float) $student->fresh()->credit_balance);

        $this->actingAs($admin)
            ->delete(route('admin.enrollments.remove', $enrollment->fresh()))
            ->assertRedirect();

        $this->assertSame(1000.0, (float) $student->fresh()->credit_balance);
        $this->assertSame(1, StudentCreditTransaction::query()
            ->where('student_id', $student->id)
            ->where('reference_enrollment_id', $enrollment->id)
            ->where('amount', '>', 0)
            ->whereIn('type', ['enrollment_drop', 'enrollment_remove'])
            ->count());
    }

    private function makePaidEnrollment(): array
    {
        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'Credit Test TP',
            'code' => 'CREDITTP',
            'wallet_balance' => 10000,
            'student_approval_deduction' => 200,
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Credit Admin',
            'email' => 'credit.admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $course = Course::create([
            'name' => 'MS Office Advanced',
            'course_fee' => 4000,
            'registration_fee' => 500,
            'assessment_fee' => 500,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'training_partner_id' => $partner->id,
            'batch_name' => 'MS Office Advanced Batch',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '444455556666',
            'full_name' => 'Credit Bug Student',
            'email' => 'credit.bug@example.test',
            'phone' => '9000000201',
            'whatsapp_number' => '9000000201',
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SP'.now()->format('Y').'7777',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 5000,
            'registration_fee' => 500,
            'course_fee' => 4000,
            'assessment_fee' => 500,
            'paid_amount' => 1000,
            'outstanding_amount' => 4000,
            'is_eligible_for_assessment' => false,
            'is_legacy' => false,
        ]);

        return [$admin, $enrollment, $student];
    }
}
