<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionDashboardWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reception_user_sees_reception_workspace_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'reception',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Reception Dashboard');
        $response->assertSeeText('Manage student intake and payment entries.');
        $response->assertSeeText('Document Completion Queue');
        $response->assertSeeText('Register student');
        $response->assertSee('queue=admissions_today');
        $response->assertSee('queue=missing_documents');
        $response->assertSee('queue=pending_approval');
        $response->assertSee('date_filter=today');
    }

    public function test_admissions_today_card_target_filters_today_students(): void
    {
        $user = User::factory()->create([
            'role' => 'reception',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        Student::create([
            'full_name' => 'Today Admission',
            'email' => 'today.admission@example.test',
            'phone' => '9000000011',
            'whatsapp_number' => '9000000011',
            'aadhar_number' => '111122220011',
            'status' => 'approved',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $oldAdmission = Student::create([
            'full_name' => 'Old Admission',
            'email' => 'old.admission@example.test',
            'phone' => '9000000012',
            'whatsapp_number' => '9000000012',
            'aadhar_number' => '111122220012',
            'status' => 'approved',
            'is_active' => true,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);
        $oldAdmission->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        $this->actingAs($user)
            ->get(route('admin.students.index', ['queue' => 'admissions_today']))
            ->assertOk()
            ->assertSeeText('Today Admission')
            ->assertDontSeeText('Old Admission');
    }

    public function test_payments_today_card_target_filters_today_payments(): void
    {
        $user = User::factory()->create([
            'role' => 'reception',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $todayStudent = Student::create([
            'full_name' => 'Today Payment Student',
            'email' => 'today.payment.student@example.test',
            'phone' => '9000000013',
            'whatsapp_number' => '9000000013',
            'aadhar_number' => '111122220013',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $oldStudent = Student::create([
            'full_name' => 'Old Payment Student',
            'email' => 'old.payment.student@example.test',
            'phone' => '9000000014',
            'whatsapp_number' => '9000000014',
            'aadhar_number' => '111122220014',
            'status' => 'approved',
            'is_active' => true,
        ]);

        Payment::create([
            'student_id' => $todayStudent->id,
            'payment_receipt_number' => 'RCP-TODAY-TEST',
            'amount' => 100,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $oldPayment = Payment::create([
            'student_id' => $oldStudent->id,
            'payment_receipt_number' => 'RCP-OLD-TEST',
            'amount' => 100,
            'payment_type' => 'partial',
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);
        $oldPayment->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        $this->actingAs($user)
            ->get(route('admin.payments.index', ['date_filter' => 'today']))
            ->assertOk()
            ->assertSeeText('Today Payment Student')
            ->assertDontSeeText('Old Payment Student');
    }

    public function test_admin_user_keeps_existing_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Admin Dashboard');
        $response->assertSeeText('Manage approvals, enrollments, and daily operations.');
        $response->assertSeeText('Action Queues');
        $response->assertDontSeeText('Reception Workspace V3.0');
    }
}
