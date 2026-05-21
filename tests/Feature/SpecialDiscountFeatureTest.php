<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EnrollmentDiscount;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Services\PaymentAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpecialDiscountFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function seedFixture(): array
    {
        $tpId = (int) \DB::table('training_partners')->insertGetId([
            'type' => 'STANDARD',
            'name' => 'TP Discount',
            'code' => 'TPDISC',
            'wallet_balance' => 10000,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course = Course::create([
            'name' => 'MS Office Advanced',
            'course_fee' => 1800,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'training_partner_id' => $tpId,
            'batch_name' => 'MSO-26-2026',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $student = Student::create([
            'aadhar_number' => '123456789012',
            'full_name' => 'Discount Student',
            'email' => 'discount_student_'.uniqid('', true).'@example.test',
            'phone' => '9999999999',
            'whatsapp_number' => '9999999999',
            'status' => 'approved',
            'training_partner_id' => $tpId,
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SP'.now()->format('Y').'7777',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 2000,
            'registration_fee' => 100,
            'course_fee' => 1800,
            'assessment_fee' => 100,
            'paid_amount' => 0,
            'discount_amount' => 0,
            'outstanding_amount' => 2000,
            'is_eligible_for_assessment' => false,
            'is_legacy' => false,
        ]);

        $admin = User::create([
            'name' => 'Discount Admin',
            'email' => 'discount_admin_'.uniqid('', true).'@example.test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'training_partner_id' => null,
            'must_change_password' => false,
        ]);

        return [$admin, $enrollment, $student];
    }

    public function test_admin_discount_reduces_balance_and_keeps_student_incomplete_until_remaining_paid(): void
    {
        [$admin, $enrollment, $student] = $this->seedFixture();
        $service = new PaymentAllocationService();

        $payment1 = Payment::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-D1',
            'amount' => 500,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'approved',
        ]);
        $service->allocatePayment($payment1);

        $payment2 = Payment::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-D2',
            'amount' => 1000,
            'payment_type' => 'partial',
            'payment_method' => 'upi',
            'status' => 'approved',
        ]);
        $service->allocatePayment($payment2);
        $service->recalculateEnrollmentTotals($enrollment->fresh());

        $this->assertSame(500.0, (float) $enrollment->fresh()->outstanding_amount);

        $this->actingAs($admin)
            ->post(route('admin.payments.discounts.store', $payment2), [
                'discount_amount' => 300,
                'discount_reason' => 'Special approval',
            ])
            ->assertRedirect(route('admin.payments.show', $payment2));

        $enrollment->refresh();

        $this->assertDatabaseHas('enrollment_discounts', [
            'enrollment_id' => $enrollment->id,
            'amount' => 300.00,
            'fee_type' => 'course_fee',
        ]);
        $this->assertSame(300.0, (float) $enrollment->discount_amount);
        $this->assertSame(200.0, (float) $enrollment->outstanding_amount);
        $this->assertFalse((bool) $enrollment->is_eligible_for_assessment);

        $payment3 = Payment::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-D3',
            'amount' => 200,
            'payment_type' => 'partial',
            'payment_method' => 'online',
            'status' => 'approved',
        ]);
        $service->allocatePayment($payment3);
        $service->recalculateEnrollmentTotals($enrollment->fresh());

        $enrollment->refresh();

        $this->assertSame(0.0, (float) $enrollment->outstanding_amount);
        $this->assertTrue((bool) $enrollment->is_eligible_for_assessment);
        $this->assertCount(1, EnrollmentDiscount::where('enrollment_id', $enrollment->id)->get());
    }
}
