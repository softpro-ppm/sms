<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    public function __construct(
        private WhatsAppService $whatsapp
    ) {}

    /**
     * Send template and log. Returns true if sent successfully.
     */
    private function sendTemplate(
        ?int $studentId,
        string $phone,
        string $templateName,
        string $type,
        array $bodyParams,
        ?array $buttonParams = null,
        ?array $parameterNames = null,
        array $headerParams = []
    ): bool {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) !== 10) {
            return false;
        }

        try {
            $languageCode = config('services.whatsapp.template_language', 'en_US');
            $result = $this->whatsapp->sendTemplateMessage(
                $phone,
                $templateName,
                $languageCode,
                $bodyParams,
                $buttonParams,
                $parameterNames,
                $headerParams
            );

            $this->logWhatsApp($studentId, $templateName, $type, $phone, $result['success'] ? 'sent' : 'failed', $result['message_id'] ?? null, $result['error'] ?? null, $result['success'] ? null : ['error' => $result['error']]);

            return $result['success'];
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            $this->logWhatsApp($studentId, $templateName, $type, $phone, 'failed', null, $e->getMessage(), null);
            return false;
        }
    }

    private function logWhatsApp(?int $studentId, string $templateName, string $type, string $phone, string $status, ?string $metaMessageId, ?string $errorMessage, ?array $metadata): void
    {
        try {
            WhatsAppLog::create([
                'student_id' => $studentId,
                'template_name' => $templateName,
                'type' => $type,
                'phone' => $phone,
                'status' => $status,
                'meta_message_id' => $metaMessageId,
                'error_message' => $errorMessage,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $e) {
            Log::warning('WhatsAppLog create failed (table may not exist): ' . $e->getMessage());
        }
    }

    private function loginUrl(): string
    {
        return url('/login');
    }

    /**
     * For dynamic URL buttons: if template has base URL + {{1}} suffix,
     * pass "?" to avoid duplication (results in base + ?). Otherwise pass full URL.
     */
    private function loginButtonParam(): string
    {
        if (config('services.whatsapp.button_url_empty_suffix', true)) {
            return '?';
        }
        return $this->loginUrl();
    }

    public function sendRegistration(Student $student, array $loginCredentials): bool
    {
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        // registration_complete: header (Hello {{customer_name}},) + body (no vars) + button
        return $this->sendTemplate(
            $student->id,
            $phone,
            'registration_complete',
            'registration_complete',
            [],
            ['url' => $this->loginButtonParam()],
            ['header' => ['customer_name'], 'body' => []],
            [$student->full_name]
        );
    }

    public function sendSelfRegistrationAck(Student $student): bool
    {
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) {
            Log::info('WhatsApp skip: no phone for student', ['student_id' => $student->id]);
            return false;
        }

        return $this->sendTemplate(
            $student->id,
            $phone,
            'registration_received',
            'registration_received',
            [$student->full_name],
            null,
            ['student_name']
        );
    }

    public function sendAccountApproved(Student $student, array $loginCredentials): bool
    {
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $displayPhone = $student->whatsapp_number ?? $student->phone ?? 'N/A';

        return $this->sendTemplate(
            $student->id,
            $phone,
            'account_approved',
            'account_approved',
            [
                $student->full_name,
                $loginCredentials['email'],
                $displayPhone,
            ],
            ['url' => $this->loginButtonParam()],
            ['customer_name', 'email', 'phone_number']
        );
    }

    public function sendEnrollmentConfirmation(Enrollment $enrollment): bool
    {
        $student = $enrollment->student;
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $course = $enrollment->batch->course;
        $batch = $enrollment->batch;

        return $this->sendTemplate(
            $student->id,
            $phone,
            'enrollment_confirmation',
            'enrollment_confirmation',
            [
                $student->full_name,
                $course->name,
                $batch->batch_name,
                $enrollment->enrollment_number,
                (string) (int) $enrollment->total_fee,
                (string) (int) $enrollment->outstanding_amount,
                $this->loginUrl(),
            ]
        );
    }

    public function sendPaymentApproved(Payment $payment): bool
    {
        $student = $payment->student;
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $enrollment = $payment->enrollment;
        $course = $enrollment?->batch?->course?->name ?? 'N/A';

        return $this->sendTemplate(
            $student->id,
            $phone,
            'payment_approved',
            'payment_approved',
            [
                $student->full_name,
                $payment->payment_receipt_number ?? 'N/A',
                (string) (int) $payment->amount,
                $course,
                (string) (int) ($enrollment?->outstanding_amount ?? 0),
            ]
        );
    }

    public function sendFullyPaid(Enrollment $enrollment): bool
    {
        $student = $enrollment->student;
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $course = $enrollment->batch->course;

        return $this->sendTemplate(
            $student->id,
            $phone,
            'fully_paid',
            'fully_paid',
            [
                $student->full_name,
                $course->name,
                $enrollment->batch->batch_name,
                $this->loginUrl(),
            ]
        );
    }

    public function sendAssessmentResult(AssessmentResult $result): bool
    {
        $student = $result->student;
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $status = $result->is_passed ? 'Passed' : 'Not passed';
        $course = $result->assessment?->course?->name ?? $result->enrollment?->batch?->course?->name ?? 'Course';

        return $this->sendTemplate(
            $student->id,
            $phone,
            'assessment_result',
            'assessment_result',
            [
                $student->full_name,
                $course,
                (string) $result->correct_answers,
                (string) $result->total_questions,
                (string) $result->percentage,
                $status,
                $this->loginUrl(),
            ]
        );
    }

    public function sendCertificateIssued(Certificate $certificate): bool
    {
        $student = $certificate->student;
        $phone = $student->whatsapp_number ?? $student->phone ?? null;
        if (!$phone) return false;

        $viewUrl = url('/student/certificates/' . $certificate->id . '/view');

        return $this->sendTemplate(
            $student->id,
            $phone,
            'certificate_issued',
            'certificate_issued',
            [
                $student->full_name,
                $certificate->course->name,
                $certificate->certificate_number ?? 'N/A',
                $viewUrl,
            ]
        );
    }
}
