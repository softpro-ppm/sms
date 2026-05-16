<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\AmsSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Pushes approved payment income to AMS asynchronously (see AmsSyncService).
 *
 * Failures are persisted on payments (ams_sync_status / ams_last_error). Monitor those columns;
 * queue failed_jobs only captures unexpected handler crashes.
 */
class SyncPaymentToAmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    /**
     * @param  bool  $forceResync  Super-admin retry: submit again even if previously synced.
     */
    public function __construct(
        public int $paymentId,
        public bool $forceResync = false,
    ) {}

    public function handle(AmsSyncService $ams): void
    {
        $payment = Payment::with(['student'])->find($this->paymentId);
        if (! $payment || $payment->status !== 'approved') {
            return;
        }

        if (
            ! $this->forceResync
            && $payment->ams_sync_status === 'synced'
            && filled($payment->ams_transaction_id)
        ) {
            return;
        }

        $payment->forceFill([
            'ams_sync_status' => 'pending',
            'ams_last_attempt_at' => now(),
            'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
        ])->save();

        try {
            $result = $ams->syncPaymentWithResult($payment->fresh(['student']));
            if (! empty($result['ok'])) {
                $payment->fresh()->forceFill([
                    'ams_sync_status' => 'synced',
                    'ams_synced_at' => now(),
                    'ams_last_error' => null,
                    'ams_transaction_id' => $result['transaction_id'] ?? null,
                ])->save();

                return;
            }

            $payment->fresh()->forceFill([
                'ams_sync_status' => 'failed',
                'ams_last_error' => (string) ($result['error'] ?? 'unknown_error'),
            ])->save();
        } catch (\Throwable $e) {
            $payment->fresh()->forceFill([
                'ams_sync_status' => 'failed',
                'ams_last_attempt_at' => now(),
                'ams_last_error' => $e->getMessage(),
            ])->save();
            Log::error('SyncPaymentToAmsJob exception', [
                'payment_id' => $this->paymentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
