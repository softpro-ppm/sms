<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Meta WhatsApp Cloud API: validates POST payload authenticity via X-Hub-Signature-256.
 *
 * @see https://developers.facebook.com/docs/graph-api/webhooks/getting-started#verification-requests
 */
class VerifyWhatsAppWebhookSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.whatsapp.app_secret');

        if ($secret === null || $secret === '') {
            Log::warning('WhatsApp webhook POST accepted without signature verification: WHATSAPP_APP_SECRET is not set. Set this in production.');

            return $next($request);
        }

        $signature = $request->header('X-Hub-Signature-256');
        if (! is_string($signature) || ! str_starts_with($signature, 'sha256=')) {
            Log::warning('WhatsApp webhook POST rejected: missing or invalid X-Hub-Signature-256 header');

            return response('Forbidden', 403, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        $payload = $request->getContent();
        $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expected, $signature)) {
            Log::warning('WhatsApp webhook POST rejected: signature mismatch');

            return response('Forbidden', 403, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return $next($request);
    }
}
