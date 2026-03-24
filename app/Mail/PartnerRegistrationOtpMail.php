<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Address;

class PartnerRegistrationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $expiryMinutes = '10'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@softpro.co.in', 'Softpro Skill Solutions'),
            subject: 'Partner Registration – Email Verification OTP',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.partner-registration-otp');
    }

    public function attachments(): array
    {
        return [];
    }
}
