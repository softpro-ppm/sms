<?php

namespace Tests\Unit;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Services\PaymentAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAllocationServiceIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocate_payment_second_call_returns_existing_rows_only(): void
    {
        $tpId = (int) \DB::table('training_partners')->insertGetId([
            'type' => 'STANDARD',
            'name' => 'TP Unit',
            'code' => 'TPUNIT',
            'wallet_balance' => 10000,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course = Course::create([
            'name' => 'Unit Course',
            'course_fee' => 4800,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'training_partner_id' => $tpId,
            'batch_name' => 'Unit-B',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $student = Student::create([
            'aadhar_number' => '987654321098',
            'full_name' => 'Unit Student',
            'email' => 'unit_stu_'.uniqid('', true).'@example.test',
            'phone' => '8888888888',
            'whatsapp_number' => '8888888888',
            'status' => 'approved',
            'training_partner_id' => $tpId,
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SP'.now()->format('Y').'8888',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 5000,
            'registration_fee' => 100,
            'course_fee' => 4800,
            'assessment_fee' => 100,
            'paid_amount' => 0,
            'outstanding_amount' => 5000,
            'is_eligible_for_assessment' => false,
            'is_legacy' => false,
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'payment_receipt_number' => 'RCP-UNIT-'.uniqid(),
            'amount' => 200,
            'payment_type' => 'partial',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $svc = new PaymentAllocationService;

        $first = $svc->allocatePayment($payment->fresh());
        $second = $svc->allocatePayment($payment->fresh());

        $this->assertSame(count($first), count($second));
        $this->assertSame(
            collect($first)->pluck('id')->sort()->values()->all(),
            collect($second)->pluck('id')->sort()->values()->all()
        );
    }
}
