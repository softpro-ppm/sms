<?php

namespace App\Mail;

use App\Models\Enrollment;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['enrollment' => $this->enrollment];
        $subject = $service->hasCustomTemplate('enrollment-confirmation') ? $service->getSubject('enrollment-confirmation', $data) : 'Softpro - Enrollment Confirmation';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['enrollment' => $this->enrollment];
        if ($service->hasCustomTemplate('enrollment-confirmation')) {
            return new Content(htmlString: $service->getHtml('enrollment-confirmation', $data));
        }
        return new Content(view: 'emails.enrollment-confirmation', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
