<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateService
{
    public function generateHtml(Certificate $certificate): string
    {
        $certificate->load(['student', 'course', 'batch', 'assessmentResult', 'enrollment']);

        $student = $certificate->student;
        $course = $certificate->course;
        $batch = $certificate->batch;
        $result = $certificate->assessmentResult;

        // Enrollment number from enrollment or first matching enrollment
        $enrollment = $certificate->enrollment ?? $student->enrollments()
            ->where('batch_id', $batch?->id)
            ->first();
        $enrollmentNumber = $enrollment?->enrollment_number ?? 'N/A';

        // Student photo: use absolute path for DomPDF, URL for browser
        $photoDoc = $student->documents()->where('document_type', 'photo')->first();
        $studentPhotoUrl = null;
        $studentPhotoPath = null;
        if ($photoDoc && Storage::disk('public')->exists($photoDoc->file_path)) {
            $studentPhotoUrl = asset('storage/' . $photoDoc->file_path);
            $studentPhotoPath = asset('storage/' . $photoDoc->file_path);
        }

        // Logo: use data URI for reliable loading in browser, PDF, and saved HTML
        $logoPath = asset('images/logo/Logo_png.png');
        $logoFile = public_path('images/logo/Logo_png.png');
        if (file_exists($logoFile)) {
            $logoData = base64_encode(file_get_contents($logoFile));
            $logoPath = 'data:image/png;base64,' . $logoData;
        }

        // Salutation based on gender
        $salutation = match (strtolower($student->gender ?? '')) {
            'male' => 'Mr.',
            'female' => 'Ms.',
            default => 'Mr. / Ms.',
        };

        // Parent label and name (D/o = Daughter of, S/o = Son of)
        $parentLabel = match (strtolower($student->gender ?? '')) {
            'male' => 'S/o',
            'female' => 'D/o',
            default => 'D/o S/o',
        };
        $parentName = trim($student->father_name ?? '');

        // Batch dates (e.g., 01 Jan 2026 – 15 Mar 2026)
        $startDate = $batch?->start_date ? $batch->start_date->format('d M Y') : '______';
        $endDate = $batch?->end_date ? $batch->end_date->format('d M Y') : '______';

        // Grade from assessment result
        $grade = $result?->grade ?? 'N/A';

        // Issue date
        $issueDate = $certificate->issue_date ? $certificate->issue_date->format('d M Y') : now()->format('d M Y');

        // QR code URL for verification (optional, configurable)
        $qrUrl = null;
        if (config('certificate.show_qr_code', true)) {
            $verificationUrl = url('/verify') . '?cert=' . urlencode($certificate->certificate_number);
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($verificationUrl);
        }

        // Configurable ISO text and title
        $isoText = config('certificate.iso_text', 'AN ISO 9001:2015 CERTIFIED ORGANIZATION');
        $certificateTitle = config('certificate.title', 'CERTIFICATE OF COMPLETION');

        return view('certificates.training-certificate', [
            'certificate' => $certificate,
            'student' => $student,
            'course' => $course,
            'batch' => $batch,
            'enrollmentNumber' => $enrollmentNumber,
            'studentPhotoUrl' => $studentPhotoUrl,
            'studentPhotoPath' => $studentPhotoPath,
            'logoPath' => $logoPath,
            'salutation' => $salutation,
            'parentLabel' => $parentLabel,
            'parentName' => $parentName,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'grade' => $grade,
            'issueDate' => $issueDate,
            'qrUrl' => $qrUrl,
            'isoText' => $isoText,
            'certificateTitle' => $certificateTitle,
        ])->render();
    }

    /**
     * Generate sample certificate HTML for admin preview (no real data).
     */
    public function generateSampleHtml(): string
    {
        $logoPath = asset('images/logo/Logo_png.png');
        $logoFile = public_path('images/logo/Logo_png.png');
        if (file_exists($logoFile)) {
            $logoData = base64_encode(file_get_contents($logoFile));
            $logoPath = 'data:image/png;base64,' . $logoData;
        }

        $certificate = (object) [
            'certificate_number' => 'CERT' . now()->format('Ym') . '0001',
        ];

        $student = (object) [
            'full_name' => 'Tejal Gulla',
            'gender' => 'female',
            'father_name' => 'Rajesh Gulla',
        ];

        $course = (object) ['name' => 'MS Office'];
        $batch = (object) ['batch_name' => 'MSO-24-2026'];

        $verificationUrl = url('/verify') . '?cert=SAMPLE';
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($verificationUrl);

        return view('certificates.training-certificate', [
            'certificate' => $certificate,
            'student' => $student,
            'course' => $course,
            'batch' => $batch,
            'enrollmentNumber' => 'SP20260001',
            'studentPhotoUrl' => null,
            'studentPhotoPath' => null,
            'logoPath' => $logoPath,
            'salutation' => 'Ms.',
            'parentLabel' => 'D/o',
            'parentName' => 'Rajesh Gulla',
            'startDate' => '01 Jan 2026',
            'endDate' => '15 Mar 2026',
            'grade' => 'A',
            'issueDate' => now()->format('d M Y'),
            'qrUrl' => config('certificate.show_qr_code', true) ? $qrUrl : null,
            'isoText' => config('certificate.iso_text', 'AN ISO 9001:2015 CERTIFIED ORGANIZATION'),
            'certificateTitle' => config('certificate.title', 'CERTIFICATE OF COMPLETION'),
        ])->render();
    }
}
