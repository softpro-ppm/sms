<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentDeletionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReceptionStudentDeletionRequestTest extends TestCase
{
    use RefreshDatabase;

    private function seedUsersAndStudent(): array
    {
        $tpId = (int) \DB::table('training_partners')->insertGetId([
            'type' => 'STANDARD',
            'name' => 'Deletion TP',
            'code' => 'DELTP',
            'wallet_balance' => 1000,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $student = Student::create([
            'training_partner_id' => $tpId,
            'aadhar_number' => '444455556666',
            'full_name' => 'Delete Candidate',
            'email' => 'delete.candidate@example.test',
            'phone' => '9000000001',
            'whatsapp_number' => '9000000001',
            'status' => 'approved',
        ]);

        $studentUser = User::create([
            'name' => $student->full_name,
            'email' => $student->email,
            'password' => Hash::make('password'),
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $reception = User::factory()->create([
            'email' => 'reception.delete@example.test',
            'role' => 'reception',
            'training_partner_id' => $tpId,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $admin = User::factory()->create([
            'email' => 'admin.delete@example.test',
            'role' => 'admin',
            'training_partner_id' => $tpId,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        return [$student, $studentUser, $reception, $admin];
    }

    public function test_reception_submits_delete_request_and_admin_can_approve_it(): void
    {
        [$student, $studentUser, $reception, $admin] = $this->seedUsersAndStudent();

        $this->actingAs($reception)
            ->delete(route('admin.students.destroy', $student), [
                'request_reason' => 'Duplicate registration entered by mistake',
            ])
            ->assertSessionHas('success');

        $request = StudentDeletionRequest::where('student_id', $student->id)->firstOrFail();

        $this->assertSame(StudentDeletionRequest::STATUS_PENDING, $request->status);
        $this->assertDatabaseHas('students', ['id' => $student->id]);

        $this->actingAs($admin)
            ->patch(route('admin.student-deletion-requests.approve', $request))
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $studentUser->id]);
    }

    public function test_admin_gets_topbar_and_sidebar_notification_for_pending_delete_request(): void
    {
        Cache::flush();

        [$student, , $reception, $admin] = $this->seedUsersAndStudent();

        $this->actingAs($reception)
            ->delete(route('admin.students.destroy', $student), [
                'request_reason' => 'Student requested cancellation',
            ])
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Student deletion request pending');
    }

    public function test_reception_cannot_create_duplicate_pending_delete_request(): void
    {
        [$student, , $reception] = $this->seedUsersAndStudent();

        $this->actingAs($reception)
            ->delete(route('admin.students.destroy', $student), [
                'request_reason' => 'Duplicate registration entered by mistake',
            ])
            ->assertSessionHas('success');

        $this->actingAs($reception)
            ->delete(route('admin.students.destroy', $student), [
                'request_reason' => 'Second request should be blocked',
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, StudentDeletionRequest::where('student_id', $student->id)->count());
    }

    public function test_admin_direct_delete_closes_existing_pending_delete_request(): void
    {
        [$student, $studentUser, $reception, $admin] = $this->seedUsersAndStudent();

        $this->actingAs($reception)
            ->delete(route('admin.students.destroy', $student), [
                'request_reason' => 'Duplicate registration entered by mistake',
            ])
            ->assertSessionHas('success');

        $request = StudentDeletionRequest::where('student_id', $student->id)->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.students.destroy', $student))
            ->assertRedirect(route('admin.students.index'));

        $request->refresh();

        $this->assertSame(StudentDeletionRequest::STATUS_APPROVED, $request->status);
        $this->assertSame('Student deleted directly by admin.', $request->review_notes);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $studentUser->id]);
    }
}
