<?php

namespace Tests\Feature;

use App\Services\WhatsAppWebhookService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WhatsAppWebhookSignatureTest extends TestCase
{
    private function signatureHeader(string $rawBody, string $secret): string
    {
        return 'sha256='.hash_hmac('sha256', $rawBody, $secret);
    }

    public function test_post_rejected_when_secret_configured_and_signature_missing(): void
    {
        Config::set('services.whatsapp.app_secret', 'test-secret-for-hmac');

        $body = '{"object":"whatsapp_business_account","entry":[]}';

        $response = $this->call('POST', '/webhook/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $body);

        $response->assertForbidden();
    }

    public function test_post_rejected_when_secret_configured_and_signature_invalid(): void
    {
        Config::set('services.whatsapp.app_secret', 'correct-secret');

        $body = '{"object":"whatsapp_business_account","entry":[]}';

        $response = $this->call('POST', '/webhook/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.str_repeat('00', 32),
        ], $body);

        $response->assertForbidden();
    }

    public function test_post_accepted_when_signature_valid(): void
    {
        Config::set('services.whatsapp.app_secret', 'correct-secret');

        $this->mock(WhatsAppWebhookService::class, function ($mock) {
            $mock->shouldReceive('processWebhookPayload')
                ->once()
                ->with(\Mockery::type('array'))
                ->andReturn(0);
        });

        $body = '{"object":"whatsapp_business_account","entry":[]}';
        $sig = $this->signatureHeader($body, 'correct-secret');

        $response = $this->call('POST', '/webhook/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => $sig,
        ], $body);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_post_skips_verification_when_secret_not_configured(): void
    {
        Config::set('services.whatsapp.app_secret', null);

        $this->mock(WhatsAppWebhookService::class, function ($mock) {
            $mock->shouldReceive('processWebhookPayload')
                ->once()
                ->andReturn(0);
        });

        $body = '{"object":"whatsapp_business_account","entry":[]}';

        $response = $this->call('POST', '/webhook/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $body);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_get_webhook_verify_unchanged_plain_health_check(): void
    {
        $response = $this->get('/webhook/whatsapp');

        $response->assertOk();
        $response->assertSee('reachable', false);
    }
}
