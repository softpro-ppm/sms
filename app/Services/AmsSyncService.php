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
        if ($payment->status !== 'approved') {
            Log::error('AMS sync skipped: payment not approved', ['payment_id' => $payment->id]);
            return false;
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
    protected function sendIncome(array $payload): bool
    {
        $url = config('services.ams.api_url');
        $key = config('services.ams.api_key');

        if (empty($url) || empty($key)) {
            Log::error('AMS sync skipped: API URL or key not configured');
            return false;
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
                Log::error('AMS income synced OK', ['reference' => $body['reference'] ?? null, 'payment_id' => $body['meta']['sms_payment_id'] ?? null]);
                return true;
            }

            Log::error('AMS sync failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'reference' => $body['reference'] ?? null,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('AMS sync error', [
                'error' => $e->getMessage(),
                'reference' => $body['reference'] ?? null,
            ]);
            return false;
        }
    }
}
