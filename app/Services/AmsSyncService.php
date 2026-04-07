<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AmsSyncService
{
    // Softpro HO, Student (Income), Student Fees - override via config
    protected const DEFAULT_PROJECT_ID = 1;
    protected const DEFAULT_CATEGORY_ID = 13;   // Student income
    protected const DEFAULT_SUBCATEGORY_ID = 62; // Student Fees
    protected const DEFAULT_USER_ID = 2;

    /**
     * Sync an approved payment's income to AMS.
     * Call after payment is approved (Option A: sync only on create/approval, not on edit or delete).
     */
    public function syncPayment(Payment $payment): bool
    {
        return $this->syncPaymentWithResult($payment)['ok'] ?? false;
    }

    /**
     * Sync an approved payment's income to AMS and return a structured result.
     *
     * @return array{ok: bool, http_status: int|null, transaction_id: string|null, error: string|null}
     */
    public function syncPaymentWithResult(Payment $payment): array
    {
        if ($payment->status !== 'approved') {
            Log::error('AMS sync skipped: payment not approved', ['payment_id' => $payment->id]);

            return [
                'ok' => false,
                'http_status' => null,
                'transaction_id' => null,
                'error' => 'payment_not_approved',
            ];
        }

        $studentName = $payment->student?->full_name ?? 'Unknown';

        return $this->sendIncome([
            'amount' => (float) $payment->amount,
            'transaction_date' => $payment->approved_at?->format('Y-m-d') ?? $payment->created_at->format('Y-m-d'),
            'subcategory_id' => (int) config('services.ams.subcategory_id', self::DEFAULT_SUBCATEGORY_ID),
            'reference' => $studentName,
            'description' => "SMS-Fee-{$payment->id}",
            'phone_number' => $payment->student?->whatsapp_number ?? null,
            'meta' => ['sms_payment_id' => $payment->id],
        ]);
    }

    /**
     * Send income payload to AMS API.
     */
    protected function sendIncome(array $payload): array
    {
        $url = config('services.ams.api_url');
        $key = config('services.ams.api_key');

        if (empty($url) || empty($key)) {
            Log::error('AMS sync skipped: API URL or key not configured');
            return [
                'ok' => false,
                'http_status' => null,
                'transaction_id' => null,
                'error' => 'api_not_configured',
            ];
        }

        $body = array_merge([
            'type' => 'income',
            'paid_amount' => $payload['amount'],
            'balance' => 0,
            'category_id' => (int) config('services.ams.category_id', self::DEFAULT_CATEGORY_ID),
            'project_id' => (int) config('services.ams.project_id', self::DEFAULT_PROJECT_ID),
            'user_id' => (int) config('services.ams.user_id', self::DEFAULT_USER_ID),
        ], $payload);

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $key,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, $body);

            if ($response->successful()) {
                $json = $response->json() ?? [];
                $transactionId = null;
                if (is_array($json)) {
                    $transactionId = $json['transaction_id'] ?? ($json['id'] ?? null);
                }

                Log::error('AMS income synced OK', [
                    'reference' => $body['reference'] ?? null,
                    'payment_id' => $body['meta']['sms_payment_id'] ?? null,
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'ok' => true,
                    'http_status' => $response->status(),
                    'transaction_id' => $transactionId ? (string) $transactionId : null,
                    'error' => null,
                ];
            }

            Log::error('AMS sync failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'reference' => $body['reference'] ?? null,
            ]);
            return [
                'ok' => false,
                'http_status' => $response->status(),
                'transaction_id' => null,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('AMS sync error', [
                'error' => $e->getMessage(),
                'reference' => $body['reference'] ?? null,
            ]);
            return [
                'ok' => false,
                'http_status' => null,
                'transaction_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
