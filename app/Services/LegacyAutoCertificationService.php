<?php

namespace App\Services;

use App\Mail\AssessmentResultMail;
use App\Mail\CertificateIssuedMail;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LegacyAutoCertificationService
{
    public const LEGACY_GRADE = 'A+';

    public const LEGACY_TOTAL_QUESTIONS = 25;

    public const LEGACY_CORRECT = 25;

    public const LEGACY_PERCENTAGE = 100.0;

    public function issueIfEligible(Enrollment $enrollment): ?Certificate
    {
        $enrollment->loadMissing(['batch', 'student', 'legacyLinkCourse']);

        if (! $enrollment->is_legacy || ! $enrollment->batch?->is_legacy_batch) {
            return null;
        }

        if (! $enrollment->is_fully_paid || ! $enrollment->is_eligible_for_assessment) {
            return null;
        }

        $assessment = LegacyEnrollmentService::legacyCompletionAssessment();
        if (! $assessment) {
            Log::warning('Legacy auto-certification skipped: legacy completion assessment not found. Run migrations.');

            return null;
        }

        if (Certificate::query()
            ->where('enrollment_id', $enrollment->id)
            ->where('is_issued', true)
            ->exists()) {
            return Certificate::query()
                ->where('enrollment_id', $enrollment->id)
                ->where('is_issued', true)
                ->orderByDesc('id')
                ->first();
        }

        $existingResult = AssessmentResult::query()
            ->where('enrollment_id', $enrollment->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        $issuance = new CertificateIssuanceService();

        if ($existingResult) {
            $hadCert = Certificate::query()->where('assessment_result_id', $existingResult->id)->exists();
            $certificate = $issuance->createForPassedResult($existingResult, $enrollment);
            if (! $hadCert) {
                $this->sendNotifications($existingResult, $certificate);
            }

            return $certificate;
        }

        [$result, $certificate] = DB::transaction(function () use ($enrollment, $assessment, $issuance) {
            $now = now();

            $result = AssessmentResult::create([
                'student_id' => $enrollment->student_id,
                'assessment_id' => $assessment->id,
                'enrollment_id' => $enrollment->id,
                'attempt_number' => 1,
                'total_questions' => self::LEGACY_TOTAL_QUESTIONS,
                'correct_answers' => self::LEGACY_CORRECT,
                'wrong_answers' => 0,
                'total_marks' => self::LEGACY_CORRECT * 4,
                'percentage' => self::LEGACY_PERCENTAGE,
                'grade' => self::LEGACY_GRADE,
                'is_passed' => true,
                'started_at' => $now,
                'completed_at' => $now,
                'time_taken_minutes' => 1,
                'answers' => [],
            ]);

            $certificate = $issuance->createForPassedResult($result, $enrollment);

            return [$result, $certificate];
        });

        $this->sendNotifications($result, $certificate);

        return $certificate;
    }

    private function sendNotifications(AssessmentResult $result, Certificate $certificate): void
    {
        try {
            $certificate->load(['course', 'student']);
            Mail::to($result->student->email)->send(new CertificateIssuedMail($certificate));
            try {
                app(WhatsAppNotificationService::class)->sendCertificateIssued($certificate);
            } catch (\Exception $e) {
                Log::error('Legacy certificate WhatsApp failed: ' . $e->getMessage());
            }
            try {
                app(StudentPushNotificationService::class)->sendCertificateIssued($certificate);
            } catch (\Exception $e) {
                Log::error('Legacy certificate PWA push failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Legacy certificate email failed: ' . $e->getMessage());
        }

        try {
            $result->load(['assessment.course', 'enrollment.batch.course', 'student']);
            Mail::to($result->student->email)->send(new AssessmentResultMail($result));
            try {
                app(WhatsAppNotificationService::class)->sendAssessmentResult($result);
            } catch (\Exception $e) {
                Log::error('Legacy assessment result WhatsApp failed: ' . $e->getMessage());
            }
            try {
                app(StudentPushNotificationService::class)->sendAssessmentResult($result);
            } catch (\Exception $e) {
                Log::error('Legacy assessment result PWA push failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Legacy assessment result email failed: ' . $e->getMessage());
        }
    }
}
