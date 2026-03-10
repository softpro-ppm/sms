<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\WhatsAppLog;
use Illuminate\Console\Command;

class WhatsAppDebugCommand extends Command
{
    protected $signature = 'whatsapp:debug {--payment= : Payment ID to check}';

    protected $description = 'Debug WhatsApp delivery - check student phone and recent logs';

    public function handle(): int
    {
        $paymentId = $this->option('payment');

        if ($paymentId) {
            $payment = Payment::with('student')->find($paymentId);
            if (!$payment) {
                $this->error("Payment {$paymentId} not found.");
                return 1;
            }
            $student = $payment->student;
            $this->table(
                ['Field', 'Value'],
                [
                    ['Student ID', $student->id],
                    ['Student Name', $student->full_name],
                    ['whatsapp_number', $student->whatsapp_number ?? '(null)'],
                    ['phone', $student->phone ?? '(null)'],
                    ['Phone for WhatsApp', ($student->whatsapp_number ?? $student->phone) ?: 'NONE - would skip'],
                ]
            );
            return 0;
        }

        $this->info('Recent payment_approved WhatsApp logs:');
        $logs = WhatsAppLog::where('type', 'payment_approved')->latest()->take(10)->get();
        if ($logs->isEmpty()) {
            $this->warn('No payment_approved logs found. Either no payments approved yet, or students had no phone.');
            $this->line('Run: php artisan migrate (if whatsapp_logs table missing)');
        } else {
            $this->table(
                ['student_id', 'phone', 'status', 'error_message', 'created_at'],
                $logs->map(fn ($l) => [$l->student_id, $l->phone, $l->status, $l->error_message ?? '-', $l->created_at->format('Y-m-d H:i')])->toArray()
            );
        }

        return 0;
    }
}
