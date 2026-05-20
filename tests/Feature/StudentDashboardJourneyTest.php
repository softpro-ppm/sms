<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_surfaces_payment_due_as_primary_next_action(): void
    {
        [$user, $student] = $this->makeStudentUser();

        $course = Course::create([
            'name' => 'MS Office',
            'description' => 'Office productivity course',
            'course_fee' => 1000,
            'registration_fee' => 200,
            'assessment_fee' => 100,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'batch_name' => 'MSO-1',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(25)->toDateString(),
            'max_students' => 30,
            'is_active' => true,
        ]);

        Enrollment::create([
            'enrollment_number' => 'SP20260001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 1300,
            'paid_amount' => 300,
            'outstanding_amount' => 1000,
            'is_eligible_for_assessment' => false,
        ]);

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Payment due');
        $response->assertSeeText('Clear your pending fee to keep everything moving');
        $response->assertSeeText('View payments');
        $response->assertSeeText('Course Progress');
        $response->assertSeeText('MS Office');
    }

    public function test_student_dashboard_shows_awaiting_enrollment_for_approved_student_without_courses(): void
    {
        [$user] = $this->makeStudentUser();

        $response = $this->actingAs($user)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSeeText('Awaiting enrollment');
        $response->assertSeeText('You are approved and waiting for your first course');
        $response->assertSeeText('Review profile');
    }

    private function makeStudentUser(): array
    {
        $student = Student::create([
            'aadhar_number' => fake()->unique()->numerify('############'),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('##########'),
            'whatsapp_number' => fake()->numerify('##########'),
            'status' => 'approved',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'name' => $student->full_name,
            'email' => $student->email,
            'role' => 'student',
            'student_id' => $student->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        return [$user, $student];
    }
}
