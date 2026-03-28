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

        if ($mode === 'subscribe' && $expected && is_string($token) && hash_equals($expected, $token)) {
            return response((string) $challenge, 200);
        }

        Log::warning('WhatsApp webhook verify failed', [
            'mode' => $mode,
        ]);

        return response('Forbidden', 403);
    }

    public function handle(Request $request, WhatsAppWebhookService $webhookService)
    {
        try {
            $payload = $request->all();
            if (is_array($payload)) {
                $webhookService->processWebhookPayload($payload);
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook handle error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
