<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\PaymentAllocationService;
use Illuminate\Console\Command;

class FixEnrollmentTotalsCommand extends Command
{
    protected $signature = 'enrollment:fix-totals 
        {--student= : Student ID} 
        {--enrollment= : Enrollment ID} 
        {--fix : Recalculate and update enrollment totals}
        {--approve-pending : Approve pending payments for the enrollment(s)}';

    protected $description = 'Diagnose and fix enrollment paid_amount/outstanding_amount (recalculate from allocations)';

    public function handle(): int
    {
        $studentId = $this->option('student');
        $enrollmentId = $this->option('enrollment');
        $applyFix = $this->option('fix');
        $approvePending = $this->option('approve-pending');

        $query = Enrollment::with(['student', 'batch.course', 'payments']);
        if ($enrollmentId) {
            $query->where('id', $enrollmentId);
        } elseif ($studentId) {
            $query->where('student_id', $studentId);
        } else {
            $this->error('Provide --student=ID or --enrollment=ID');
            return 1;
        }

        $enrollments = $query->get();
        if ($enrollments->isEmpty()) {
            $this->error('No enrollments found.');
            return 1;
        }

        $allocationService = new PaymentAllocationService();
        $fixed = 0;

        foreach ($enrollments as $enrollment) {
            $this->line('');
            $this->info("Enrollment #{$enrollment->id} - {$enrollment->student->full_name} - {$enrollment->batch->batch_name}");
            $this->line("  Total Fee: ₹{$enrollment->total_fee}");
            $this->line("  Current paid_amount: ₹{$enrollment->paid_amount}");
            $this->line("  Current outstanding_amount: ₹{$enrollment->outstanding_amount}");

            $approvedPayments = $enrollment->payments->where('status', 'approved');
            $pendingPayments = $enrollment->payments->where('status', 'pending');
            $sumPayments = $approvedPayments->sum('amount');
            $this->line("  Sum of approved payments: ₹{$sumPayments}");
            foreach ($approvedPayments as $p) {
                $this->line("    - Payment #{$p->id}: ₹{$p->amount} ({$p->payment_receipt_number}) [approved]");
            }
            if ($pendingPayments->isNotEmpty()) {
                $this->warn("  Pending payments (need approval):");
                foreach ($pendingPayments as $p) {
                    $this->line("    - Payment #{$p->id}: ₹{$p->amount} ({$p->payment_receipt_number}) [PENDING]");
                }
                if ($approvePending) {
                    foreach ($pendingPayments as $p) {
                        $this->approvePayment($p, $allocationService);
                        $this->info("  ✓ Approved payment #{$p->id} (₹{$p->amount})");
                        $fixed++;
                    }
                    $enrollment->refresh();
                }
            }

            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalPaid = (float) $enrollment->total_fee - $totalOutstanding;

            $this->line("  Recalculated outstanding: ₹{$totalOutstanding}");
            $this->line("  Recalculated paid: ₹{$totalPaid}");

            if (abs((float) $enrollment->outstanding_amount - $totalOutstanding) > 0.01) {
                $this->warn("  MISMATCH - needs fix");
                if ($applyFix) {
                    $enrollment->update([
                        'paid_amount' => $totalPaid,
                        'outstanding_amount' => $totalOutstanding,
                        'is_eligible_for_assessment' => $totalOutstanding <= 0,
                    ]);
                    $this->info("  ✓ Fixed");
                    $fixed++;
                }
            } else {
                $this->line("  OK - no change needed");
            }
        }

        if ($fixed > 0) {
            $this->info("\nFixed {$fixed} enrollment(s).");
        } elseif (!$applyFix && !$approvePending && $enrollments->isNotEmpty()) {
            $this->warn("\nRun with --fix to recalculate totals, or --approve-pending to approve pending payments.");
        }

        return 0;
    }

    private function approvePayment(Payment $payment, PaymentAllocationService $allocationService): void
    {
        $payment->update([
            'status' => 'approved',
            'approved_by' => null,
            'approved_at' => now(),
        ]);

        if ($payment->enrollment_id) {
            $allocationService->allocatePayment($payment);
            $enrollment = $payment->enrollment;
            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalPaid = (float) $enrollment->total_fee - $totalOutstanding;

            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0,
            ]);

            if ($totalOutstanding <= 0) {
                Payment::where('enrollment_id', $enrollment->id)
                    ->where('status', 'approved')
                    ->update(['payment_type' => 'full']);
            }
        }
    }
}
