<?php

namespace App\Mail;

use App\Models\TrainingPartner;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartnerApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array{email: string, password: string}|null  $loginCredentials  Admin credentials (when auto-created)
     */
    public function __construct(
        public TrainingPartner $trainingPartner,
        public ?array $loginCredentials = null
    ) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['trainingPartner' => $this->trainingPartner, 'loginCredentials' => $this->loginCredentials];
        $subject = $service->hasCustomTemplate('partner-approved')
            ? $service->getSubject('partner-approved', $data)
            : 'Softpro - Your Training Partner Account is Approved!';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['trainingPartner' => $this->trainingPartner, 'loginCredentials' => $this->loginCredentials];
        if ($service->hasCustomTemplate('partner-approved')) {
            return new Content(htmlString: $service->getHtml('partner-approved', $data));
        }

        return new Content(view: 'emails.partner-approved', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
