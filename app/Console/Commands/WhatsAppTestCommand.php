<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class WhatsAppTestCommand extends Command
{
    protected $signature = 'whatsapp:test {phone : 10-digit phone number} 
        {--template= : Use an approved template, e.g. registration_received, account_approved}
        {--debug : Dump the template payload (no send)}';

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
            $params = $this->getTemplateParams($template);
            $paramNames = $this->getTemplateParamNames($template);
            $buttonParams = $this->getTemplateButtonParams($template);
            $headerParams = $this->getTemplateHeaderParams($template);
            $languageCode = config('services.whatsapp.template_language', 'en_US');

            if ($this->option('debug')) {
                $payload = $this->buildTemplatePayload($template, $languageCode, $params, $paramNames, $buttonParams, $headerParams);
                $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                return 0;
            }

            $this->info("Sending template '{$template}' to +91 {$phone}...");
            $result = $whatsapp->sendTemplateMessage($phone, $template, $languageCode, $params, $buttonParams, $paramNames, $headerParams);
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
        $loginUrl = url('/login');
        return match ($template) {
            'registration_received' => ['Test Student'],
            'account_approved' => ['Test Student', 'test@example.com', '9550755039'],
            'registration_complete' => [],
            'enrollment_confirmation' => ['Test Student', 'MS Office', 'MSO-1', 'SP20260001', '1900', '1900', $loginUrl],
            'payment_approved' => ['Test Student', 'RCP-TEST', '500', 'MS Office', '1400'],
            'fully_paid' => ['Test Student', 'MS Office', 'MSO-1', $loginUrl],
            'assessment_result' => ['Test Student', 'MS Office', '45', '50', '90', 'Passed', $loginUrl],
            'certificate_issued' => ['Test Student', 'MS Office', 'CERT-TEST', url('/student/certificates/1/view')],
            default => [],
        };
    }

    private function getTemplateParamNames(string $template): ?array
    {
        return match ($template) {
            'registration_received' => ['student_name'],
            'account_approved' => ['customer_name', 'email', 'phone_number'],
            'registration_complete' => ['header' => ['customer_name'], 'body' => []],
            'enrollment_confirmation' => ['student_name', 'course_name', 'batch_name', 'enrollment_number', 'total_fee', 'outstanding_amount', 'login_url'],
            'payment_approved' => ['student_name', 'receipt_number', 'amount', 'course_name', 'outstanding_amount'],
            'fully_paid' => ['student_name', 'course_name', 'batch_name', 'login_url'],
            'assessment_result' => ['student_name', 'course_name', 'correct_answers', 'total_questions', 'percentage', 'status', 'login_url'],
            'certificate_issued' => ['student_name', 'course_name', 'certificate_number', 'view_url'],
            default => null,
        };
    }

    private function getTemplateHeaderParams(string $template): array
    {
        return match ($template) {
            'registration_complete' => ['Test Student'],
            default => [],
        };
    }

    private function buildTemplatePayload(string $template, string $languageCode, array $params, ?array $paramNames, ?array $buttonParams, array $headerParams): array
    {
        $payload = [
            'name' => $template,
            'language' => ['code' => $languageCode],
        ];
        $components = [];

        if (!empty($headerParams)) {
            $headerNames = $paramNames['header'] ?? null;
            $parameters = [];
            foreach ($headerParams as $i => $param) {
                $p = ['type' => 'text', 'text' => (string) $param];
                if ($headerNames && isset($headerNames[$i])) {
                    $p['parameter_name'] = $headerNames[$i];
                }
                $parameters[] = $p;
            }
            $components[] = ['type' => 'header', 'parameters' => $parameters];
        }

        if (!empty($params)) {
            $bodyNames = is_array($paramNames) && isset($paramNames['body']) ? $paramNames['body'] : $paramNames;
            $parameters = [];
            foreach ($params as $i => $param) {
                $p = ['type' => 'text', 'text' => (string) $param];
                if ($bodyNames && isset($bodyNames[$i])) {
                    $p['parameter_name'] = $bodyNames[$i];
                }
                $parameters[] = $p;
            }
            $components[] = ['type' => 'body', 'parameters' => $parameters];
        }

        if ($buttonParams && isset($buttonParams['url'])) {
            $components[] = [
                'type' => 'button',
                'sub_type' => 'url',
                'index' => 0,
                'parameters' => [['type' => 'text', 'text' => $buttonParams['url']]],
            ];
        }

        if (!empty($components)) {
            $payload['components'] = $components;
        }

        return $payload;
    }

    private function getTemplateButtonParams(string $template): ?array
    {
        $baseUrl = url('/login');
        $emptySuffix = config('services.whatsapp.button_url_empty_suffix', true);
        $url = $emptySuffix ? '?' : $baseUrl;
        return match ($template) {
            'account_approved', 'registration_complete' => ['url' => $url],
            default => null,
        };
    }
}
