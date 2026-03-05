<?php

namespace App\Mail;

use App\Models\AssessmentResult;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AssessmentResult $result) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['result' => $this->result];
        $defaultSubject = "Softpro - Assessment " . ($this->result->is_passed ? 'Passed' : 'Completed') . ": " . ($this->result->assessment?->title ?? $this->result->assessment?->course?->name ?? 'Exam');
        $subject = $service->hasCustomTemplate('assessment-result') ? $service->getSubject('assessment-result', $data) : $defaultSubject;
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['result' => $this->result];
        if ($service->hasCustomTemplate('assessment-result')) {
            return new Content(htmlString: $service->getHtml('assessment-result', $data));
        }
        return new Content(view: 'emails.assessment-result', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
