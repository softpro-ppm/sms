<?php

namespace App\Mail;

use App\Models\Student;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student, 'loginCredentials' => ['email' => $this->student->email, 'password' => $this->student->whatsapp_number]];
        $subject = $service->hasCustomTemplate('account-approved') ? $service->getSubject('account-approved', $data) : 'Softpro - Your Account is Approved!';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student, 'loginCredentials' => ['email' => $this->student->email, 'password' => $this->student->whatsapp_number]];
        if ($service->hasCustomTemplate('account-approved')) {
            return new Content(htmlString: $service->getHtml('account-approved', $data));
        }
        return new Content(view: 'emails.account-approved', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
