<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EnrollmentExamEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_remains_available_after_batch_end_without_one_year_expiry(): void
    {
        Carbon::setTestNow('2026-07-24 10:00:00');

        $student = Student::create([
            'aadhar_number' => '123456789012',
            'full_name' => 'Tally Student',
            'email' => 'tally.student@example.com',
            'phone' => '9876543210',
            'whatsapp_number' => '9876543210',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $course = Course::create([
            'name' => 'TALLY',
            'description' => 'Tally course',
            'course_fee' => 1800,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'duration_days' => 45,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'batch_name' => 'TALLY-01-2025',
            'start_date' => '2025-01-10',
            'end_date' => '2025-03-10',
            'max_students' => 30,
            'is_active' => true,
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SPTEST1001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => '2025-01-10',
            'status' => 'active',
            'total_fee' => 2000,
            'paid_amount' => 2000,
            'outstanding_amount' => 0,
            'is_eligible_for_assessment' => true,
        ]);

        $this->assertTrue($enrollment->fresh()->can_take_assessment);
        $this->assertTrue($enrollment->fresh()->exam_eligibility_checklist['within_exam_window']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
