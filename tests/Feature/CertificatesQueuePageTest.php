<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CertificatesQueuePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_certificates_queue_copy(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.certificates.index'));

        $response->assertOk();
        $response->assertSeeText('Certificates Queue');
        $response->assertSeeText('Issue, review, and track certificate records.');
        $response->assertSeeText('Queue Filters');
        $response->assertSeeText('Review pending and issued certificates');
    }

    public function test_generated_certificate_number_uses_training_partner_code(): void
    {
        Mail::fake();

        [$admin, $certificate] = $this->certificateFixture('PPM');

        $this->actingAs($admin)
            ->post(route('admin.certificates.generate', $certificate))
            ->assertRedirect(route('admin.certificates.show', $certificate));

        $this->assertMatchesRegularExpression(
            '/^CERT-PPM-'.now()->format('Ym').'-0001$/',
            (string) $certificate->fresh()->certificate_number
        );
    }

    public function test_certificate_preview_shows_training_partner_identity(): void
    {
        [$admin, $certificate, $partner] = $this->certificateFixture('VSP');

        $certificate->forceFill([
            'certificate_number' => 'CERT-VSP-'.now()->format('Ym').'-0001',
            'is_issued' => true,
            'issue_date' => now()->toDateString(),
        ])->save();

        $this->actingAs($admin)
            ->get(route('admin.certificates.preview', $certificate))
            ->assertOk()
            ->assertSeeText($partner->name)
            ->assertSeeText('Centre Code: '.$partner->code)
            ->assertSeeText($certificate->fresh()->certificate_number);
    }

    private function certificateFixture(string $partnerCode): array
    {
        $partner = TrainingPartner::create([
            'type' => 'STANDARD',
            'name' => $partnerCode.' Training Centre',
            'code' => $partnerCode,
            'wallet_balance' => 1000,
            'status' => 'active',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
            'training_partner_id' => $partner->id,
        ]);

        $student = Student::create([
            'training_partner_id' => $partner->id,
            'full_name' => 'Certificate Student',
            'email' => strtolower($partnerCode).'certificate@example.test',
            'phone' => '9000000201',
            'whatsapp_number' => '9000000201',
            'aadhar_number' => '111122220201',
            'gender' => 'female',
            'father_name' => 'Certificate Father',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $course = Course::create([
            'name' => 'Certificate Course',
            'course_fee' => 1000,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $batch = Batch::create([
            'course_id' => $course->id,
            'training_partner_id' => $partner->id,
            'batch_name' => $partnerCode.'-CERT-B1',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->toDateString(),
            'course_fee' => 1000,
            'registration_fee' => 100,
            'assessment_fee' => 100,
            'is_active' => true,
        ]);

        $certificate = Certificate::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'batch_id' => $batch->id,
            'is_issued' => false,
        ]);

        return [$admin, $certificate, $partner];
    }
}
