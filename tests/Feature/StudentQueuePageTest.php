<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentQueuePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_student_queue_dashboard_copy(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.students.index'));

        $response->assertOk();
        $response->assertSeeText('Student Queue');
        $response->assertSeeText('Review registrations, missing records, and enrollment readiness.');
        $response->assertSeeText('Pending Approval');
        $response->assertSeeText('Ready for Enrollment');
    }

    public function test_ready_for_enrollment_queue_filters_students_without_active_enrollment(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $course = Course::create([
            'name' => 'Tally Prime',
            'description' => 'Accounting',
            'course_fee' => 5000,
            'registration_fee' => 500,
            'assessment_fee' => 500,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'batch_name' => 'Morning Batch',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(25),
            'max_students' => 30,
            'course_fee' => 5000,
            'registration_fee' => 500,
            'assessment_fee' => 500,
            'duration_days' => 30,
            'is_active' => true,
            'is_legacy_batch' => false,
        ]);

        $readyStudent = Student::create([
            'full_name' => 'Ready Student',
            'email' => 'ready@example.com',
            'phone' => '9000000001',
            'whatsapp_number' => '9000000001',
            'aadhar_number' => '111122223333',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $enrolledStudent = Student::create([
            'full_name' => 'Enrolled Student',
            'email' => 'enrolled@example.com',
            'phone' => '9000000002',
            'whatsapp_number' => '9000000002',
            'aadhar_number' => '111122223334',
            'status' => 'approved',
            'is_active' => true,
        ]);

        Enrollment::create([
            'student_id' => $enrolledStudent->id,
            'batch_id' => $batch->id,
            'enrollment_number' => 'ENR001',
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 6000,
            'paid_amount' => 0,
            'outstanding_amount' => 6000,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 5000,
            'assessment_fee' => 500,
            'is_legacy' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.students.index', ['queue' => 'ready_for_enrollment']));

        $response->assertOk();
        $response->assertSeeText('Ready Student');
        $response->assertDontSeeText('Enrolled Student');
    }
}
