<?php

namespace App\Services;

use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\Enrollment;

class CertificateIssuanceService
{
    public function createForPassedResult(AssessmentResult $result, Enrollment $enrollment): Certificate
    {
        $existingCertificate = Certificate::query()->where('assessment_result_id', $result->id)->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        $student = $result->student;
        $nameParts = explode(' ', $student->full_name);
        $firstName = $nameParts[0] ?? '';
        $lastName = end($nameParts) ?? '';
        $certificateNumber = 'CERT-' . strtoupper(substr($firstName, 0, 2))
            . strtoupper(substr($lastName, 0, 2))
            . '-' . $result->id . '-' . date('Y');

        $certificateContent = $this->buildCertificateHtmlContent($result, $enrollment);

        $courseIdForCert = $enrollment->is_legacy && $enrollment->legacy_link_course_id
            ? $enrollment->legacy_link_course_id
            : $enrollment->batch->course_id;

        return Certificate::create([
            'student_id' => $result->student_id,
            'course_id' => $courseIdForCert,
            'batch_id' => $enrollment->batch_id,
            'enrollment_id' => $enrollment->id,
            'assessment_result_id' => $result->id,
            'certificate_number' => $certificateNumber,
            'issue_date' => now()->toDateString(),
            'certificate_content' => $certificateContent,
            'is_issued' => true,
        ]);
    }

    public function buildCertificateHtmlContent(AssessmentResult $result, Enrollment $enrollment): string
    {
        $student = $result->student;
        $courseName = $enrollment->display_course_name;
        $batch = $enrollment->batch;
        $batchName = $batch?->batch_name ?? 'N/A';

        $nameParts = explode(' ', $result->student->full_name);
        $firstName = $nameParts[0] ?? '';
        $lastName = end($nameParts) ?? '';
        $certificateNumber = 'CERT-' . strtoupper(substr($firstName, 0, 2))
            . strtoupper(substr($lastName, 0, 2))
            . '-' . $result->id . '-' . date('Y');

        $completedAt = $result->completed_at?->format('F d, Y') ?? now()->format('F d, Y');

        return "
        <div style='text-align: center; font-family: Arial, sans-serif; padding: 40px; border: 3px solid #2563eb;'>
            <h1 style='color: #2563eb; font-size: 32px; margin-bottom: 20px;'>CERTIFICATE OF COMPLETION</h1>
            <p style='font-size: 18px; margin-bottom: 30px;'>This is to certify that</p>
            <h2 style='color: #1f2937; font-size: 28px; margin-bottom: 30px; text-decoration: underline;'>{$student->full_name}</h2>
            <p style='font-size: 18px; margin-bottom: 20px;'>has successfully completed the course</p>
            <h3 style='color: #2563eb; font-size: 24px; margin-bottom: 20px;'>{$courseName}</h3>
            <p style='font-size: 16px; margin-bottom: 10px;'>Batch: {$batchName}</p>
            <p style='font-size: 16px; margin-bottom: 10px;'>Score: {$result->correct_answers}/{$result->total_questions} ({$result->percentage}%)</p>
            <p style='font-size: 16px; margin-bottom: 10px;'>Grade: {$result->grade}</p>
            <p style='font-size: 16px; margin-bottom: 30px;'>Date of Completion: {$completedAt}</p>
            <p style='font-size: 14px; color: #6b7280;'>Certificate Number: {$certificateNumber}</p>
        </div>
        ";
    }
}
