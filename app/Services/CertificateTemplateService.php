<?php

namespace App\Services;

use App\Models\Certificate;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateService
{
    public function generateHtml(Certificate $certificate): string
    {
        $certificate->load([
            'student.trainingPartner',
            'course',
            'batch',
            'assessmentResult',
            'enrollment',
        ]);

        $student = $certificate->student;
        $course = $certificate->course;
        $batch = $certificate->batch;
        $result = $certificate->assessmentResult;

        // Enrollment number from enrollment or first matching enrollment
        $enrollment = $certificate->enrollment ?? $student->enrollments()
            ->where('batch_id', $batch?->id)
            ->first();
        $enrollmentNumber = $enrollment?->enrollment_number ?? 'N/A';

        // Training partner logo (top-right, mirrors SoftPro logo). Omit if missing — space stays blank.
        $trainingPartnerLogoPath = null;
        $partner = $student->trainingPartner;
        if ($partner && ! empty($partner->logo_path) && Storage::disk('public')->exists($partner->logo_path)) {
            $tpLogoFile = Storage::disk('public')->path($partner->logo_path);
            if (file_exists($tpLogoFile) && is_readable($tpLogoFile)) {
                $tpLogoData = base64_encode(file_get_contents($tpLogoFile));
                $mime = mime_content_type($tpLogoFile) ?: 'image/png';
                $trainingPartnerLogoPath = 'data:' . $mime . ';base64,' . $tpLogoData;
            }
        }

        // SoftPro logo: use data URI for reliable loading in browser, PDF, and saved HTML
        $logoPath = asset('images/logo/Logo_png.png');
        $logoFile = public_path('images/logo/Logo_png.png');
        if (file_exists($logoFile)) {
            $logoData = base64_encode(file_get_contents($logoFile));
            $logoPath = 'data:image/png;base64,' . $logoData;
        }

        // Director signature: base64 for DomPDF
        $directorSignaturePath = null;
        $directorSigFile = public_path('images/signatures/director-signature.png');
        if (file_exists($directorSigFile)) {
            $sigData = base64_encode(file_get_contents($directorSigFile));
            $directorSignaturePath = 'data:image/png;base64,' . $sigData;
        }

        // Salutation based on gender
        $salutation = match (strtolower($student->gender ?? '')) {
            'male' => 'Mr.',
            'female' => 'Ms.',
            default => 'Mr. / Ms.',
        };

        // Parent label and name (F/o = Father of, D/o = Daughter of)
        $parentLabel = match (strtolower($student->gender ?? '')) {
            'male' => 'F/o',
            'female' => 'D/o',
            default => 'F/o D/o',
        };
        $parentName = trim($student->father_name ?? '');

        // Batch dates (e.g., 01 Jan 2026 – 15 Mar 2026)
        $startDate = $batch?->start_date ? $batch->start_date->format('d M Y') : '______';
        $endDate = $batch?->end_date ? $batch->end_date->format('d M Y') : '______';

        // Grade from assessment result
        $grade = $result?->grade ?? 'N/A';

        // Issue date
        $issueDate = $certificate->issue_date ? $certificate->issue_date->format('d M Y') : now()->format('d M Y');

        // QR code as base64 PNG (DomPDF has poor SVG support; PNG via GD works reliably)
        // Links directly to student verification page - no need to enter enrollment/name/email/phone
        $qrUrl = null;
        if (config('certificate.show_qr_code', true) && $enrollmentNumber !== 'N/A') {
            $verificationUrl = url('/verify/' . urlencode($enrollmentNumber));
            $qrUrl = $this->generateQrCodePng($verificationUrl);
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
            'trainingPartnerLogoPath' => $trainingPartnerLogoPath,
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
            'directorSignaturePath' => $directorSignaturePath,
        ])->render();
    }

    /**
     * Generate QR code as base64 PNG data URI (DomPDF-compatible, uses GD).
     */
    private function generateQrCodePng(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 8,
            'outputBase64' => true,
        ]);
        return (new QRCode($options))->render($url);
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

        $qrUrl = null;
        if (config('certificate.show_qr_code', true)) {
            $verificationUrl = url('/verify/SP20260001');
            $qrUrl = $this->generateQrCodePng($verificationUrl);
        }

        return view('certificates.training-certificate', [
            'certificate' => $certificate,
            'student' => $student,
            'course' => $course,
            'batch' => $batch,
            'enrollmentNumber' => 'SP20260001',
            'trainingPartnerLogoPath' => null,
            'logoPath' => $logoPath,
            'salutation' => 'Ms.',
            'parentLabel' => 'D/o',
            'parentName' => 'Rajesh Gulla',
            'startDate' => '01 Jan 2026',
            'endDate' => '15 Mar 2026',
            'grade' => 'A',
            'issueDate' => now()->format('d M Y'),
            'qrUrl' => $qrUrl,
            'isoText' => config('certificate.iso_text', 'AN ISO 9001:2015 CERTIFIED ORGANIZATION'),
            'certificateTitle' => config('certificate.title', 'CERTIFICATE OF COMPLETION'),
            'directorSignaturePath' => file_exists(public_path('images/signatures/director-signature.png'))
                ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/signatures/director-signature.png')))
                : null,
        ])->render();
    }
}
