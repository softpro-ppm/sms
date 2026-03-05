<?php

namespace App\Mail;

use App\Models\Student;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SelfRegistrationAcknowledgementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student];
        $subject = $service->hasCustomTemplate('self-registration-ack') ? $service->getSubject('self-registration-ack', $data) : 'Softpro - Registration Received';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student];
        if ($service->hasCustomTemplate('self-registration-ack')) {
            return new Content(htmlString: $service->getHtml('self-registration-ack', $data));
        }
        return new Content(view: 'emails.self-registration-ack', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
