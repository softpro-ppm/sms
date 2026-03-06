<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class WhatsAppTestCommand extends Command
{
    protected $signature = 'whatsapp:test {phone : 10-digit phone number} 
        {--template= : Use an approved template, e.g. registration_received, account_approved}';

    protected $description = 'Send a test WhatsApp message (free-form or template) to verify API';

    public function handle(WhatsAppService $whatsapp)
    {
        $phone = preg_replace('/[^0-9]/', '', $this->argument('phone'));
        if (strlen($phone) !== 10) {
            $this->error('Phone must be 10 digits. Example: php artisan whatsapp:test 9876543210');
            return 1;
        }

        if (!config('services.whatsapp.access_token')) {
            $this->error('WHATSAPP_ACCESS_TOKEN is not set in .env');
            return 1;
        }
        if (!config('services.whatsapp.phone_number_id')) {
            $this->error('WHATSAPP_PHONE_NUMBER_ID is not set in .env');
            return 1;
        }

        $template = $this->option('template');

        if ($template) {
            $this->info("Sending template '{$template}' to +91 {$phone}...");
            $params = $this->getTemplateParams($template);
            $paramNames = $this->getTemplateParamNames($template);
            $languageCode = config('services.whatsapp.template_language', 'en_US');
            $result = $whatsapp->sendTemplateMessage($phone, $template, $languageCode, $params, null, $paramNames);
        } else {
            $this->info("Sending free-form message to +91 {$phone}...");
            $msg = "Test from Softpro SMS. WhatsApp API is working.";
            $result = $whatsapp->sendMessage($phone, $msg);
        }

        if (!is_array($result)) {
            $this->error('❌ Unexpected response from WhatsApp service.');
            return 1;
        }

        if ($result['success']) {
            $this->info('✅ Message sent! Check WhatsApp on +91 ' . $phone);
            return 0;
        }

        $this->error('❌ Failed: ' . ($result['error'] ?? 'Unknown error'));
        $this->warn('Business-initiated messages require approved Meta templates. Use --template=registration_received to test templates.');
        return 1;
    }

    private function getTemplateParams(string $template): array
    {
        return match ($template) {
            'registration_received' => ['Test Student'],
            'account_approved', 'registration_complete' => ['Test Student', 'test@example.com', '9999999999', url('/login')],
            'enrollment_confirmation' => ['Test Student', 'MS Office', 'MSO-1', 'SP20260001', '1900', '1900', url('/login')],
            'payment_approved' => ['Test Student', 'RCP-TEST', '500', 'MS Office', '1400'],
            'fully_paid' => ['Test Student', 'MS Office', 'MSO-1', url('/login')],
            'assessment_result' => ['Test Student', 'MS Office', '45', '50', '90', 'Passed', url('/login')],
            'certificate_issued' => ['Test Student', 'MS Office', 'CERT-TEST', url('/student/certificates/1/view')],
            default => [],
        };
    }

    private function getTemplateParamNames(string $template): ?array
    {
        return match ($template) {
            'registration_received' => ['student_name'],
            'account_approved', 'registration_complete' => ['customer_name', 'email', 'password', 'login_url'],
            'enrollment_confirmation' => ['customer_name', 'course', 'batch', 'enrollment_number', 'total_fee', 'outstanding', 'login_url'],
            'payment_approved' => ['customer_name', 'receipt_number', 'amount', 'course', 'outstanding'],
            'fully_paid' => ['customer_name', 'course', 'batch', 'login_url'],
            'assessment_result' => ['customer_name', 'course', 'correct', 'total', 'percentage', 'status', 'login_url'],
            'certificate_issued' => ['customer_name', 'course', 'certificate_number', 'view_url'],
            default => null,
        };
    }
}
