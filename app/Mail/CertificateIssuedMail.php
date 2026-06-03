<?php

namespace App\Mail;

use App\Models\Certificate;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateIssuedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Certificate $certificate) {}

    public function envelope(): Envelope
    {
        $this->certificate->loadMissing(['course', 'enrollment.legacyLinkCourse']);
        $service = app(EmailTemplateService::class);
        $data = ['certificate' => $this->certificate];
        $courseName = $this->certificate->enrollment?->display_course_name ?? $this->certificate->course?->name ?? 'Course';
        $subject = $service->hasCustomTemplate('certificate-issued') ? $service->getSubject('certificate-issued', $data) : 'Softpro - Certificate Ready: ' . $courseName;
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $this->certificate->loadMissing(['course', 'enrollment.legacyLinkCourse']);
        $service = app(EmailTemplateService::class);
        $data = ['certificate' => $this->certificate];
        if ($service->hasCustomTemplate('certificate-issued')) {
            return new Content(htmlString: $service->getHtml('certificate-issued', $data));
        }
        return new Content(view: 'emails.certificate-issued', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
