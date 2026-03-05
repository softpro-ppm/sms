<?php

namespace App\Mail;

use App\Models\Enrollment;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FullyPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['enrollment' => $this->enrollment];
        $subject = $service->hasCustomTemplate('fully-paid') ? $service->getSubject('fully-paid', $data) : "Softpro - Fees Fully Paid! You're Eligible for Assessment";
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['enrollment' => $this->enrollment];
        if ($service->hasCustomTemplate('fully-paid')) {
            return new Content(htmlString: $service->getHtml('fully-paid', $data));
        }
        return new Content(view: 'emails.fully-paid', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
