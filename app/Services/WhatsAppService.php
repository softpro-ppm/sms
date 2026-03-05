<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $accessToken;
    private $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v17.0');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Send a message. Returns ['success' => bool, 'message_id' => ?string, 'error' => ?string].
     */
    public function sendMessage($to, $message, $type = 'text'): array
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => $type,
            ];

            if ($type === 'text') {
                $payload['text'] = ['body' => $message];
            } elseif ($type === 'template') {
                $payload['template'] = $message;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $messageId = $data['messages'][0]['id'] ?? null;
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'message_id' => $messageId,
                ]);
                return ['success' => true, 'message_id' => $messageId, 'error' => null];
            } else {
                $body = $response->body();
                $decoded = $response->json();
                $error = $decoded['error']['message'] ?? $body;
                Log::error('WhatsApp message failed', ['to' => $to, 'error' => $body]);
                return ['success' => false, 'message_id' => null, 'error' => $error];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage());
            return ['success' => false, 'message_id' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a template message.
     * @param array $bodyParameters - Body variables in order (e.g. ['Name', 'value2'])
     * @param array|null $buttonParameters - For URL button: ['url' => 'https://...', 'display_text' => 'Login']
     */
    public function sendTemplateMessage(
        $to,
        $templateName,
        $languageCode = 'en',
        array $bodyParameters = [],
        ?array $buttonParameters = null
    ): array {
        $template = [
            'name' => $templateName,
            'language' => ['code' => $languageCode],
        ];

        $components = [];

        if (!empty($bodyParameters)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn($param) => ['type' => 'text', 'text' => (string) $param], $bodyParameters),
            ];
        }

        if ($buttonParameters && isset($buttonParameters['url'])) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => 0,
                'parameters' => [
                    ['type' => 'text', 'text' => $buttonParameters['url']],
                ],
            ];
        }

        if (!empty($components)) {
            $template['components'] = $components;
        }

        return $this->sendMessage($to, $template, 'template');
    }

    public function sendPaymentConfirmation($user, $payment)
    {
        $message = "🎉 Payment Confirmed!\n\n";
        $message .= "Hello {$user->name},\n\n";
        $message .= "Your payment of ₹{$payment->amount} has been confirmed successfully.\n";
        $message .= "Payment ID: {$payment->payment_number}\n";
        $message .= "Date: " . $payment->created_at->format('M d, Y') . "\n\n";
        $message .= "Thank you for your payment!\n\n";
        $message .= "SoftPro Education";

        return $this->sendMessage($user->phone, $message);
    }

    public function sendAssessmentResult($user, $result)
    {
        $status = $result->is_passed ? '✅ Passed' : '❌ Failed';
        $message = "📊 Assessment Result\n\n";
        $message .= "Hello {$user->name},\n\n";
        $message .= "Your assessment result is now available:\n";
        $message .= "Course: {$result->enrollment->batch->course->name}\n";
        $message .= "Score: {$result->percentage}%\n";
        $message .= "Grade: {$result->grade}\n";
        $message .= "Status: {$status}\n\n";
        $message .= "Keep up the great work!\n\n";
        $message .= "SoftPro Education";

        return $this->sendMessage($user->phone, $message);
    }

    public function sendCertificateIssued($user, $certificate)
    {
        $message = "🏆 Certificate Issued!\n\n";
        $message .= "Hello {$user->name},\n\n";
        $message .= "Congratulations! Your certificate has been issued:\n";
        $message .= "Course: {$certificate->course->name}\n";
        $message .= "Certificate No: {$certificate->certificate_number}\n";
        $message .= "Issue Date: " . $certificate->issue_date->format('M d, Y') . "\n\n";
        $message .= "You can download it from your student portal.\n\n";
        $message .= "SoftPro Education";

        return $this->sendMessage($user->phone, $message);
    }

    public function sendCourseReminder($user, $course, $daysLeft)
    {
        $message = "⏰ Course Reminder\n\n";
        $message .= "Hello {$user->name},\n\n";
        $message .= "Reminder: Your course is ending soon!\n";
        $message .= "Course: {$course->name}\n";
        $message .= "Days Left: {$daysLeft}\n\n";
        $message .= "Make sure to complete all assignments and assessments.\n\n";
        $message .= "SoftPro Education";

        return $this->sendMessage($user->phone, $message);
    }

    public function sendBatchStartNotification($user, $batch)
    {
        $message = "🚀 Batch Starting Soon!\n\n";
        $message .= "Hello {$user->name},\n\n";
        $message .= "Your batch is starting soon:\n";
        $message .= "Course: {$batch->course->name}\n";
        $message .= "Batch: {$batch->batch_name}\n";
        $message .= "Start Date: " . $batch->start_date->format('M d, Y') . "\n\n";
        $message .= "Get ready for an amazing learning journey!\n\n";
        $message .= "SoftPro Education";

        return $this->sendMessage($user->phone, $message);
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add country code if not present (assuming India +91)
        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '91' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    public function sendBulkMessage($recipients, $message)
    {
        $results = [];
        foreach ($recipients as $recipient) {
            $result = $this->sendMessage($recipient, $message);
            $results[] = [
                'phone' => $recipient,
                'success' => $result['success'],
            ];
        }
        return $results;
    }
}
