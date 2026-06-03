<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PartnerWalletTransaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentDeletionRequest;
use App\Models\TrainingPartner;
use App\Models\TrainingPartnerActivityLog;
use App\Models\User;
use App\Services\WhatsAppNotificationService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class V3ContinuationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_student_deletion_request_queue(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin();

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '111122223333',
            'full_name' => 'Queue Delete Student',
            'email' => 'queue.delete@example.test',
            'phone' => '9000000001',
            'whatsapp_number' => '9000000001',
            'status' => 'approved',
        ]);

        StudentDeletionRequest::create([
            'student_id' => $student->id,
            'student_name_snapshot' => $student->full_name,
            'student_email_snapshot' => $student->email,
            'request_reason' => 'Duplicate student record',
            'status' => StudentDeletionRequest::STATUS_PENDING,
            'requested_by' => $admin->id,
            'requested_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.student-deletion-requests.index'))
            ->assertOk()
            ->assertSeeText('Student deletion requests')
            ->assertSeeText('Queue Delete Student')
            ->assertSeeText('Duplicate student record');
    }

    public function test_admin_can_export_student_deletion_requests_csv(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin();

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '111122223334',
            'full_name' => 'Export Delete Student',
            'email' => 'export.delete@example.test',
            'phone' => '9000000003',
            'whatsapp_number' => '9000000003',
            'status' => 'approved',
        ]);

        StudentDeletionRequest::create([
            'student_id' => $student->id,
            'student_name_snapshot' => $student->full_name,
            'student_email_snapshot' => $student->email,
            'request_reason' => 'Export this request',
            'status' => StudentDeletionRequest::STATUS_PENDING,
            'requested_by' => $admin->id,
            'requested_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.student-deletion-requests.export-csv'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_open_legacy_students_page(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '222233334444',
            'full_name' => 'Legacy Learner',
            'email' => 'legacy.learner@example.test',
            'phone' => '9000000002',
            'whatsapp_number' => '9000000002',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        Enrollment::create([
            'enrollment_number' => 'LEG-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 1000,
            'outstanding_amount' => 1500,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Historical Tally',
            'legacy_start_date' => '2018-01-01',
            'legacy_end_date' => '2018-03-31',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.legacy-students.index'))
            ->assertOk()
            ->assertSeeText('Legacy students')
            ->assertSeeText('Legacy Learner')
            ->assertSeeText('Historical Tally');
    }

    public function test_admin_can_export_legacy_students_csv(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '222233334445',
            'full_name' => 'Legacy Export Learner',
            'email' => 'legacy.export@example.test',
            'phone' => '9000000004',
            'whatsapp_number' => '9000000004',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        Enrollment::create([
            'enrollment_number' => 'LEG-002',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 2500,
            'outstanding_amount' => 0,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Historical MS Office',
            'legacy_start_date' => '2019-01-01',
            'legacy_end_date' => '2019-03-31',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.legacy-students.export-csv'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_hq_admin_can_edit_legacy_enrollment(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '222233334446',
            'full_name' => 'Legacy Edit Learner',
            'email' => 'legacy.edit@example.test',
            'phone' => '9000000005',
            'whatsapp_number' => '9000000005',
            'status' => 'approved',
        ]);

        $linkedCourse = Course::create([
            'name' => 'Legacy Linked Course',
            'course_fee' => 1000,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        $enrollment = Enrollment::create([
            'enrollment_number' => 'LEG-EDIT-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 1000,
            'outstanding_amount' => 1500,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Old Name',
            'legacy_start_date' => '2018-01-01',
            'legacy_end_date' => '2018-03-31',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.legacy-students.edit', $enrollment))
            ->assertOk()
            ->assertSeeText('Edit Legacy Enrollment')
            ->assertSee('Old Name');

        $this->actingAs($admin)
            ->put(route('admin.legacy-students.update', $enrollment), [
                'legacy_course_name' => 'Updated Legacy Course',
                'legacy_start_date' => '2018-02-01',
                'legacy_end_date' => '2018-04-30',
                'enrollment_date' => '2026-02-01',
                'registration_fee' => 400,
                'course_fee' => 1400,
                'assessment_fee' => 100,
                'legacy_link_course_id' => $linkedCourse->id,
                'status' => 'completed',
            ])
            ->assertRedirect(route('admin.legacy-students.index'));

        $enrollment->refresh();

        $this->assertSame('Updated Legacy Course', $enrollment->legacy_course_name);
        $this->assertSame($linkedCourse->id, $enrollment->legacy_link_course_id);
        $this->assertSame('2018-02-01', $enrollment->legacy_start_date->format('Y-m-d'));
        $this->assertSame('2018-04-30', $enrollment->legacy_end_date->format('Y-m-d'));
        $this->assertSame('completed', $enrollment->status);
        $this->assertSame(1900.0, (float) $enrollment->total_fee);
        $this->assertSame(1900.0, (float) $enrollment->outstanding_amount);
    }

    public function test_hq_admin_can_download_legacy_import_template(): void
    {
        [, $admin] = $this->makePartnerAdmin('HQ');

        $this->actingAs($admin)
            ->get(route('admin.legacy-students.import-template'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_hq_admin_can_import_legacy_students_csv(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin('HQ');

        Course::create([
            'name' => 'Import Linked Course',
            'course_fee' => 1000,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $csv = implode("\n", [
            'full_name,email,whatsapp_number,aadhar_number,gender,qualification,father_name,legacy_course_name,legacy_start_date,legacy_end_date,enrollment_date,registration_fee,course_fee,assessment_fee,legacy_link_course_name,status',
            'Imported Legacy Student,imported.legacy@example.test,9000000101,333344445555,Female,Graduate,Imported Father,Old Accounting,2018-01-01,2018-04-30,2026-03-01,500,2500,200,Import Linked Course,active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.legacy-students.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('legacy.csv', $csv),
            ])
            ->assertRedirect(route('admin.legacy-students.index'))
            ->assertSessionHas('legacy_import_summary');

        $student = Student::where('email', 'imported.legacy@example.test')->firstOrFail();

        $this->assertSame($partner->id, $student->training_partner_id);
        $this->assertTrue($student->is_approved);
        $this->assertDatabaseHas('users', [
            'email' => 'imported.legacy@example.test',
            'role' => 'student',
            'student_id' => $student->id,
        ]);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'is_legacy' => true,
            'legacy_course_name' => 'Old Accounting',
            'total_fee' => 3200,
            'outstanding_amount' => 3200,
        ]);
    }

    public function test_standard_partner_cannot_import_legacy_students_csv(): void
    {
        [, $admin] = $this->makePartnerAdmin();

        $this->actingAs($admin)
            ->post(route('admin.legacy-students.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('legacy.csv', "full_name\nBlocked"),
            ])
            ->assertForbidden();
    }

    public function test_legacy_import_reports_invalid_rows(): void
    {
        [, $admin] = $this->makePartnerAdmin('HQ');

        $csv = implode("\n", [
            'full_name,email,whatsapp_number,aadhar_number,legacy_course_name,legacy_start_date,legacy_end_date,enrollment_date,registration_fee,course_fee,assessment_fee',
            ',bad-email,9000000102,not-aadhar,Old Course,2018-01-01,2018-04-30,2026-03-01,500,2500,200',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.legacy-students.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('legacy.csv', $csv),
            ])
            ->assertRedirect(route('admin.legacy-students.index'))
            ->assertSessionHas('legacy_import_summary', function (array $summary) {
                return $summary['created'] === 0
                    && count($summary['errors']) === 1
                    && $summary['errors'][0]['row'] === 2;
            });

        $this->assertDatabaseMissing('students', [
            'email' => 'bad-email',
        ]);
    }

    public function test_standard_partner_cannot_edit_hq_legacy_enrollment(): void
    {
        [$hqPartner] = $this->makePartnerAdmin('HQ');
        [, $standardAdmin] = $this->makePartnerAdmin();

        $student = Student::create([
            'training_partner_id' => $hqPartner->id,
            'aadhar_number' => '222233334447',
            'full_name' => 'Protected Legacy Learner',
            'email' => 'legacy.protected@example.test',
            'phone' => '9000000006',
            'whatsapp_number' => '9000000006',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        $enrollment = Enrollment::create([
            'enrollment_number' => 'LEG-PROTECTED-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => '2026-01-01',
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 0,
            'outstanding_amount' => 2500,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Protected Course',
            'legacy_start_date' => '2018-01-01',
            'legacy_end_date' => '2018-03-31',
        ]);

        $this->actingAs($standardAdmin)
            ->get(route('admin.legacy-students.edit', $enrollment))
            ->assertNotFound();
    }

    public function test_public_verification_uses_legacy_course_and_dates(): void
    {
        [$partner] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '222233334448',
            'full_name' => 'Legacy Verify Learner',
            'email' => 'legacy.verify@example.test',
            'phone' => '9000000007',
            'whatsapp_number' => '9000000007',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        Enrollment::create([
            'enrollment_number' => 'LEG-VERIFY-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => '2026-01-01',
            'status' => 'completed',
            'total_fee' => 2500,
            'paid_amount' => 2500,
            'outstanding_amount' => 0,
            'is_eligible_for_assessment' => false,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Historical MS Office',
            'legacy_start_date' => '2018-02-01',
            'legacy_end_date' => '2018-04-30',
        ]);

        $this->get(route('verify.show', 'LEG-VERIFY-001'))
            ->assertOk()
            ->assertSeeText('Historical MS Office')
            ->assertSeeText('01-02-2018 to 30-04-2018')
            ->assertDontSeeText('Legacy (Archive)');
    }

    public function test_super_admin_partner_activity_shows_revenue_and_staff_activity(): void
    {
        [$partner, $staff] = $this->makePartnerAdmin();
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        PartnerWalletTransaction::create([
            'training_partner_id' => $partner->id,
            'amount' => -200,
            'type' => 'student_approval',
            'description' => 'Student approval: Revenue Student',
            'balance_after' => 800,
        ]);

        \DB::table('sessions')->insert([
            'id' => 'staff-session',
            'user_id' => $staff->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.super.training-partners.activity', $partner))
            ->assertOk()
            ->assertSeeText('Platform revenue')
            ->assertSeeText('₹200.00')
            ->assertSeeText('Staff activity')
            ->assertSeeText($staff->email);
    }

    public function test_staff_login_creates_partner_activity_log(): void
    {
        [$partner, $staff] = $this->makePartnerAdmin();

        $this->post(route('login'), [
            'email' => $staff->email,
            'password' => 'password',
            'role_scope' => 'staff',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('training_partner_activity_logs', [
            'training_partner_id' => $partner->id,
            'user_id' => $staff->id,
            'actor_user_id' => $staff->id,
            'type' => 'staff_login',
        ]);
    }

    public function test_super_admin_partner_activity_shows_audit_timeline_and_exports_csv(): void
    {
        [$partner, $staff] = $this->makePartnerAdmin();
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        TrainingPartnerActivityLog::create([
            'training_partner_id' => $partner->id,
            'user_id' => $staff->id,
            'actor_user_id' => $staff->id,
            'type' => 'staff_login',
            'description' => 'Admin signed in',
            'ip_address' => '127.0.0.1',
            'occurred_at' => now(),
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.super.training-partners.activity', $partner))
            ->assertOk()
            ->assertSeeText('Activity timeline')
            ->assertSeeText('Admin signed in');

        $this->actingAs($superAdmin)
            ->get(route('admin.super.training-partners.activity-export.csv', $partner))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_super_admin_can_export_partner_revenue_csv_and_pdf(): void
    {
        [$partner] = $this->makePartnerAdmin();
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        PartnerWalletTransaction::create([
            'training_partner_id' => $partner->id,
            'amount' => -200,
            'type' => 'student_approval',
            'description' => 'Student approval: Export Revenue Student',
            'balance_after' => 800,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.super.training-partners.revenue-export.csv', $partner))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($superAdmin)
            ->get(route('admin.super.training-partners.revenue-export.pdf', $partner))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_mark_partner_revenue_collected(): void
    {
        [$partner] = $this->makePartnerAdmin();
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $transaction = PartnerWalletTransaction::create([
            'training_partner_id' => $partner->id,
            'amount' => -200,
            'type' => 'student_approval',
            'description' => 'Student approval: Collection Student',
            'balance_after' => 800,
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('admin.super.training-partners.revenue.collect', [$partner, $transaction]), [
                'collection_notes' => 'UPI collected',
            ])
            ->assertRedirect();

        $transaction->refresh();

        $this->assertSame('collected', $transaction->collection_status);
        $this->assertSame($superAdmin->id, $transaction->collected_by);
        $this->assertSame('UPI collected', $transaction->collection_notes);
        $this->assertNotNull($transaction->collected_at);
    }

    public function test_whatsapp_payment_approved_uses_legacy_display_course_name(): void
    {
        config(['services.whatsapp.template_language' => 'en_US']);

        [$partner] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '777788889999',
            'full_name' => 'WhatsApp Legacy Learner',
            'email' => 'whatsapp.legacy@example.test',
            'phone' => '9000000008',
            'whatsapp_number' => '9000000008',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        $enrollment = Enrollment::create([
            'enrollment_number' => 'LEG-WA-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 1000,
            'outstanding_amount' => 1500,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Historical MS Office',
            'legacy_start_date' => '2019-01-01',
            'legacy_end_date' => '2019-03-31',
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'payment_receipt_number' => 'RCP-WA-001',
            'amount' => 1000,
            'payment_type' => 'course_fee',
            'payment_method' => 'cash',
            'status' => 'approved',
        ]);

        $sentBodyParams = null;

        $this->mock(WhatsAppService::class, function ($mock) use (&$sentBodyParams) {
            $mock->shouldReceive('sendTemplateMessage')
                ->once()
                ->andReturnUsing(function ($to, $templateName, $languageCode, $bodyParams) use (&$sentBodyParams) {
                    $sentBodyParams = $bodyParams;

                    $this->assertSame('9000000008', $to);
                    $this->assertSame('payment_approved2', $templateName);
                    $this->assertSame('en_US', $languageCode);

                    return ['success' => true, 'message_id' => 'wamid.test', 'error' => null];
                });
        });

        $sent = app(WhatsAppNotificationService::class)->sendPaymentApproved($payment->fresh(['student', 'enrollment.batch.course']));

        $this->assertTrue($sent);
        $this->assertSame('Historical MS Office', $sentBodyParams[3]);
        $this->assertNotSame('Legacy (Archive)', $sentBodyParams[3]);
    }

    public function test_reports_enrollment_csv_uses_legacy_display_course_name(): void
    {
        [$partner, $admin] = $this->makePartnerAdmin('HQ');

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'aadhar_number' => '888899990000',
            'full_name' => 'Report Legacy Learner',
            'email' => 'report.legacy@example.test',
            'phone' => '9000000009',
            'whatsapp_number' => '9000000009',
            'status' => 'approved',
        ]);

        $batch = Batch::where('is_legacy_batch', true)->firstOrFail();

        Enrollment::create([
            'enrollment_number' => 'LEG-REPORT-001',
            'student_id' => $student->id,
            'batch_id' => $batch->id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'active',
            'total_fee' => 2500,
            'paid_amount' => 1000,
            'outstanding_amount' => 1500,
            'registration_fee' => 500,
            'course_fee' => 2000,
            'assessment_fee' => 0,
            'is_legacy' => true,
            'legacy_course_name' => 'Historical Tally Prime',
            'legacy_start_date' => '2020-01-01',
            'legacy_end_date' => '2020-03-31',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.export', ['report' => 'enrollments', 'format' => 'csv']));

        $response->assertOk();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Historical Tally Prime', $csv);
        $this->assertStringNotContainsString('Legacy (Archive)', $csv);
    }

    private function makePartnerAdmin(string $type = 'STANDARD'): array
    {
        $partner = $type === 'HQ'
            ? TrainingPartner::where('type', 'HQ')->firstOrFail()
            : TrainingPartner::create([
                'type' => 'STANDARD',
                'name' => 'Continuation TP',
                'code' => 'CONTTP',
                'wallet_balance' => 1000,
                'student_approval_deduction' => 200,
                'status' => 'active',
            ]);

        $admin = User::create([
            'name' => $type === 'HQ' ? 'HQ Admin' : 'Continuation Admin',
            'email' => strtolower($type).'.continuation@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'training_partner_id' => $partner->id,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        return [$partner, $admin];
    }
}
