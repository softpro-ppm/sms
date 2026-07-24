<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\TrainingPartner;
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

    public function test_tally_advanced_enrollment_can_fallback_to_shared_tally_assessment_catalog(): void
    {
        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => 'SoftPro Partner',
            'code' => 'SPT001',
            'contact_name' => 'Admin',
            'contact_phone' => '9876543212',
            'contact_email' => 'partner@example.com',
            'status' => 'approved',
        ]);

        $basicCourse = Course::create([
            'name' => 'TALLY',
            'description' => 'Shared tally exam catalog',
            'course_fee' => 1800,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'duration_days' => 45,
            'is_active' => true,
        ]);

        $advancedCourse = Course::create([
            'training_partner_id' => $partner->id,
            'name' => 'Tally ERP 9 Advanced',
            'description' => 'Advanced tally course',
            'course_fee' => 2300,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'duration_days' => 45,
            'is_active' => true,
        ]);

        \App\Models\Assessment::create([
            'course_id' => $basicCourse->id,
            'title' => 'Tally Final Exam',
            'description' => 'Shared tally exam',
            'time_limit_minutes' => 30,
            'total_questions' => 25,
            'passing_percentage' => 35,
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 25; $i++) {
            \App\Models\QuestionBank::create([
                'course_id' => $basicCourse->id,
                'subject' => 'Subject '.ceil($i / 5),
                'question_text' => 'Question '.$i,
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_answer' => 'A',
                'difficulty_level' => 'easy',
                'is_active' => true,
            ]);
        }

        $student = Student::create([
            'aadhar_number' => '123456789013',
            'full_name' => 'Advanced Tally Student',
            'email' => 'advanced.tally@example.com',
            'phone' => '9876543211',
            'whatsapp_number' => '9876543211',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $advancedCourse->id,
            'batch_name' => 'TALLY-ADV-01',
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-10',
            'max_students' => 30,
            'is_active' => true,
        ]);

        $enrollment = Enrollment::create([
            'enrollment_number' => 'SPTEST1002',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => '2026-06-01',
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 2500,
            'outstanding_amount' => 0,
            'is_eligible_for_assessment' => true,
        ]);

        $this->assertSame($basicCourse->id, $enrollment->fresh()->assessment_course_id);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
