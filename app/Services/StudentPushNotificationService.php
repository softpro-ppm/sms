<?php

namespace App\Services;

use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\NotificationPreference;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentPushNotificationLog;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class StudentPushNotificationService
{
    public function isConfigured(): bool
    {
        return filled(config('services.webpush.public_key'))
            && filled(config('services.webpush.private_key'))
            && filled(config('services.webpush.subject'));
    }

    public function publicKey(): ?string
    {
        return config('services.webpush.public_key');
    }

    public function sendEnrollmentConfirmation(Enrollment $enrollment): void
    {
        $student = $enrollment->student;
        $this->sendToStudent(
            $student,
            'enrollment_confirmation',
            'Enrollment confirmed',
            'Your enrollment for '.$enrollment->display_course_name.' is confirmed.',
            [
                'url' => route('student.enrollments'),
                'tag' => 'enrollment-confirmation-'.$enrollment->id,
                'data' => ['enrollment_id' => $enrollment->id],
            ]
        );
    }

    public function sendPaymentApproved(Payment $payment): void
    {
        $student = $payment->student;
        $this->sendToStudent(
            $student,
            'payment_confirmation',
            'Payment approved',
            'Your payment of ₹'.number_format((float) $payment->amount, 0).' has been approved.',
            [
                'url' => route('student.payments'),
                'tag' => 'payment-approved-'.$payment->id,
                'data' => ['payment_id' => $payment->id],
            ]
        );
    }

    public function sendFullyPaid(Enrollment $enrollment): void
    {
        $this->sendToStudent(
            $enrollment->student,
            'payment_confirmation',
            'Course fully paid',
            'Your balance for '.$enrollment->display_course_name.' is now fully cleared.',
            [
                'url' => route('student.payments'),
                'tag' => 'fully-paid-'.$enrollment->id,
                'data' => ['enrollment_id' => $enrollment->id],
            ]
        );
    }

    public function sendAssessmentResult(AssessmentResult $result): void
    {
        $status = $result->is_passed ? 'passed' : 'is available';
        $this->sendToStudent(
            $result->student,
            'assessment_result',
            'Exam result ready',
            'Your exam result '.$status.' for '.$result->assessment->title.'.',
            [
                'url' => route('student.assessments.show', $result),
                'tag' => 'assessment-result-'.$result->id,
                'data' => ['result_id' => $result->id],
            ]
        );
    }

    public function sendCertificateIssued(Certificate $certificate): void
    {
        $this->sendToStudent(
            $certificate->student,
            'certificate_issued',
            'Certificate issued',
            'Your certificate for '.$certificate->course->name.' is ready.',
            [
                'url' => route('student.certificates.view', $certificate),
                'tag' => 'certificate-issued-'.$certificate->id,
                'data' => ['certificate_id' => $certificate->id],
            ]
        );
    }

    public function sendFeeDueReminder(Enrollment $enrollment): void
    {
        $outstanding = (float) $enrollment->outstanding_amount;
        $key = 'fee-due:'.$enrollment->id.':'.now()->toDateString();

        if ($this->alreadySent($enrollment->student_id, $key)) {
            return;
        }

        $this->sendToStudent(
            $enrollment->student,
            'payment_due',
            'Fee reminder',
            '₹'.number_format($outstanding, 0).' is still pending for '.$enrollment->display_course_name.'.',
            [
                'url' => route('student.payments'),
                'tag' => 'fee-due-'.$enrollment->id,
                'data' => ['enrollment_id' => $enrollment->id],
            ],
            $key
        );
    }

    public function sendExamReadyReminder(Enrollment $enrollment): void
    {
        $key = 'exam-ready:'.$enrollment->id.':'.now()->toDateString();

        if ($this->alreadySent($enrollment->student_id, $key)) {
            return;
        }

        $this->sendToStudent(
            $enrollment->student,
            'exam_ready',
            'Exam available',
            'You can now take the final exam for '.$enrollment->display_course_name.'.',
            [
                'url' => route('student.assessments'),
                'tag' => 'exam-ready-'.$enrollment->id,
                'data' => ['enrollment_id' => $enrollment->id],
            ],
            $key
        );
    }

    public function sendToStudent(
        Student $student,
        string $type,
        string $title,
        string $message,
        array $options = [],
        ?string $dedupeKey = null
    ): void {
        if (! $this->isConfigured()) {
            return;
        }

        if (! $this->pushAllowedForStudent($student, $type)) {
            return;
        }

        $subscriptions = $student->pushSubscriptions()->enabled()->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = [
            'title' => $title,
            'body' => $message,
            'icon' => '/icons/icon-192.png',
            'badge' => '/icons/icon-192.png',
            'url' => $options['url'] ?? route('student.dashboard'),
            'tag' => $options['tag'] ?? $type,
            'data' => $options['data'] ?? [],
        ];

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding,
                ]),
                json_encode($payload)
            );
        }

        $sent = false;

        foreach ($webPush->flush() as $report) {
            $sent = $sent || $report->isSuccess();
            if ($report->isSubscriptionExpired()) {
                $subscriptions
                    ->firstWhere('endpoint', $report->getRequest()->getUri()->__toString())
                    ?->delete();
            }
        }

        StudentPushNotificationLog::firstOrCreate(
            [
                'student_id' => $student->id,
                'dedupe_key' => $dedupeKey ?? uniqid($type.'-', true),
            ],
            [
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'status' => $sent ? 'sent' : 'attempted',
                'data' => $options['data'] ?? [],
                'sent_at' => now(),
                'sent_on' => now()->toDateString(),
            ]
        );
    }

    private function alreadySent(int $studentId, string $dedupeKey): bool
    {
        return StudentPushNotificationLog::query()
            ->where('student_id', $studentId)
            ->where('dedupe_key', $dedupeKey)
            ->exists();
    }

    private function pushAllowedForStudent(Student $student, string $type): bool
    {
        $user = $student->user;
        if (! $user) {
            return false;
        }

        $prefType = match ($type) {
            'payment_confirmation', 'fully_paid' => 'payment_confirmation',
            'payment_due' => 'payment_due',
            'assessment_result', 'exam_ready' => 'assessment_result',
            'certificate_issued' => 'certificate_issued',
            'enrollment_confirmation' => 'batch_start',
            default => $type,
        };

        $preference = NotificationPreference::query()
            ->where('user_id', $user->id)
            ->where('type', $prefType)
            ->first();

        return $preference ? (bool) $preference->push_enabled : true;
    }
}
