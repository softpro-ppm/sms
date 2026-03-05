<?php

namespace App\Mail;

use App\Models\Payment;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        $service = app(EmailTemplateService::class);
        $data = ['payment' => $this->payment];
        $subject = $service->hasCustomTemplate('payment-approved') ? $service->getSubject('payment-approved', $data) : 'Softpro - Payment Approved (Receipt #' . $this->payment->payment_receipt_number . ')';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $service = app(EmailTemplateService::class);
        $data = ['payment' => $this->payment];
        if ($service->hasCustomTemplate('payment-approved')) {
            return new Content(htmlString: $service->getHtml('payment-approved', $data));
        }
        return new Content(view: 'emails.payment-approved', with: $data);
    }

    public function attachments(): array
    {
        return [];
    }
}
