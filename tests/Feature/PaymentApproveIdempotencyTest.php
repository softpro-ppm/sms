<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentApproveIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private function seedFixture(): array
    {
        $tpId = (int) \DB::table('training_partners')->insertGetId([
            'type' => 'STANDARD',
            'name' => 'TP Test',
            'code' => 'TPTEST',
            'wallet_balance' => 10000,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $course = Course::create([
            'name' => 'Fixture Course',
            'course_fee' => 4800,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'training_partner_id' => $tpId,
            'batch_name' => 'Fix-B1',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
            'is_active' => true,
        ]);

        $student = Student::create([
            'aadhar_number' => '123456789012',
            'full_name' => 'Fixture Student',
            'email' => 'fixture_student_'.uniqid('', true).'@example.test',
            'phone' => '9999999999',
            'whatsapp_number' => '9999999999',
            'status' => 'approved',
            'training_partner_id' => $tpId,
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SP'.now()->format('Y').'9999',
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
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-Fixture'.uniqid(),
            'amount' => 500,
            'payment_type' => 'partial',
            'status' => 'pending',
        ]);

        $admin = User::create([
            'name' => 'Fixture Admin',
            'email' => 'fixture_admin_'.uniqid('', true).'@example.test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'training_partner_id' => null,
            'must_change_password' => false,
        ]);

        return [$admin, $payment];
    }

    public function test_double_approve_does_not_duplicate_allocations(): void
    {
        Mail::fake();
        Http::fake([
            '*' => Http::response(['transaction_id' => 'AMS-TX-1'], 200),
        ]);

        [$admin, $payment] = $this->seedFixture();

        $this->actingAs($admin);

        $allocBefore = \App\Models\PaymentAllocation::where('payment_id', $payment->id)->count();

        $this->patch(route('admin.payments.approve', $payment))->assertRedirect();

        $afterFirst = \App\Models\PaymentAllocation::where('payment_id', $payment->id)->count();
        $this->assertGreaterThan($allocBefore, $afterFirst);

        $this->patch(route('admin.payments.approve', $payment->fresh()))
            ->assertRedirect();

        $afterSecond = \App\Models\PaymentAllocation::where('payment_id', $payment->id)->count();
        $this->assertSame($afterFirst, $afterSecond);
    }

    public function test_pending_approvals_page_shows_only_pending_payment_approvals(): void
    {
        [$admin, $payment] = $this->seedFixture();

        Payment::create([
            'student_id' => $payment->student_id,
            'enrollment_id' => $payment->enrollment_id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-APPROVED'.uniqid(),
            'amount' => 250,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments.pending-approvals'))
            ->assertOk()
            ->assertSeeText('Pending Approvals')
            ->assertSeeText($payment->payment_receipt_number)
            ->assertDontSeeText('APPROVED');
    }

    public function test_admin_can_bulk_approve_pending_approvals(): void
    {
        Mail::fake();
        Http::fake([
            '*' => Http::response(['transaction_id' => 'AMS-TX-1'], 200),
        ]);

        [$admin, $payment] = $this->seedFixture();

        $secondPayment = Payment::create([
            'student_id' => $payment->student_id,
            'enrollment_id' => $payment->enrollment_id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-BULK'.uniqid(),
            'amount' => 250,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payments.pending-approvals'))
            ->assertOk()
            ->assertSee('pendingApprovalsBulkForm')
            ->assertSee($payment->payment_receipt_number)
            ->assertSee($secondPayment->payment_receipt_number);

        $this->actingAs($admin)
            ->post(route('admin.payments.bulk-approve'), [
                'payment_ids' => [$payment->id, $secondPayment->id],
            ])
            ->assertRedirect(route('admin.payments.index'));

        $this->assertSame('approved', $payment->fresh()->status);
        $this->assertSame('approved', $secondPayment->fresh()->status);
    }

    public function test_pending_approvals_csv_exports_only_pending_payment_approvals(): void
    {
        [$admin, $payment] = $this->seedFixture();

        Payment::create([
            'student_id' => $payment->student_id,
            'enrollment_id' => $payment->enrollment_id,
            'payment_receipt_number' => 'RCP-'.now()->format('Y').'-APPROVED'.uniqid(),
            'amount' => 250,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.payments.pending-approvals.export-csv'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $csv = $response->streamedContent();
        $this->assertStringContainsString($payment->payment_receipt_number, $csv);
        $this->assertStringNotContainsString('APPROVED', $csv);
    }
}
