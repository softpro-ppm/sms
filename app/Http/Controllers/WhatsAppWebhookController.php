<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $expected = config('services.whatsapp.webhook_verify_token');
        $mode = $request->query('hub.mode')
            ?? $request->query('hub_mode');
        $token = $request->query('hub.verify_token')
            ?? $request->query('hub_verify_token');
        $challenge = $request->query('hub.challenge')
            ?? $request->query('hub_challenge');

        // Plain browser GET has no Meta params — endpoint is fine; 403 looked like a misconfiguration.
        if ($mode === null && $token === null && $challenge === null) {
            return response(
                'WhatsApp webhook URL is reachable. Meta verification sends GET with hub.mode, hub.verify_token, and hub.challenge.',
                200,
                ['Content-Type' => 'text/plain; charset=UTF-8']
            );
        }

        if ($mode === 'subscribe' && $expected && is_string($token) && hash_equals($expected, $token)) {
            return response((string) $challenge, 200);
        }

        if ($mode === 'subscribe' && !$expected) {
            Log::warning('WhatsApp webhook verify failed: WHATSAPP_WEBHOOK_VERIFY_TOKEN is not set');
        } else {
            Log::warning('WhatsApp webhook verify failed', [
                'mode' => $mode,
            ]);
        }

        return response('Forbidden', 403);
    }

    public function handle(Request $request, WhatsAppWebhookService $webhookService)
    {
        try {
            $payload = $request->all();
            if (is_array($payload)) {
                Log::info('WhatsApp webhook POST', [
                    'object' => $payload['object'] ?? null,
                    'entry_count' => isset($payload['entry']) ? count($payload['entry']) : 0,
                ]);
                $stored = $webhookService->processWebhookPayload($payload);
                if ($stored === 0 && ($payload['object'] ?? '') === 'whatsapp_business_account') {
                    Log::info('WhatsApp webhook: no inbound messages stored (may be status-only or non-message payload)');
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook handle error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
