<?php

namespace App\Mail;

use App\Models\Student;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student, 'loginCredentials' => ['email' => $this->student->email, 'password' => $this->student->whatsapp_number]];
        $subject = $service->hasCustomTemplate('student-registration') ? $service->getSubject('student-registration', $data) : 'Welcome to Softpro - Registration Confirmation';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['student' => $this->student, 'loginCredentials' => ['email' => $this->student->email, 'password' => $this->student->whatsapp_number]];
        if ($service->hasCustomTemplate('student-registration')) {
            return new Content(htmlString: $service->getHtml('student-registration', $data));
        }
        return new Content(view: 'emails.student-registration', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}