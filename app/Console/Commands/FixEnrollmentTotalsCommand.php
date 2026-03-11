<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Services\PaymentAllocationService;
use Illuminate\Console\Command;

class FixEnrollmentTotalsCommand extends Command
{
    protected $signature = 'enrollment:fix-totals 
        {--student= : Student ID} 
        {--enrollment= : Enrollment ID} 
        {--fix : Recalculate and update enrollment totals}
        {--approve-pending : Approve pending payments for the enrollment(s)}
        {--reallocate : Allocate approved payments that have no allocation records}';

    protected $description = 'Diagnose and fix enrollment paid_amount/outstanding_amount (recalculate from allocations)';

    public function handle(): int
    {
        $studentId = $this->option('student');
        $enrollmentId = $this->option('enrollment');
        $applyFix = $this->option('fix');
        $approvePending = $this->option('approve-pending');
        $reallocate = $this->option('reallocate');

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

            // Reallocate: approved payments with no allocations (e.g. approved before allocation ran)
            if ($reallocate) {
                $reallocated = 0;
                foreach ($approvedPayments as $p) {
                    $allocated = PaymentAllocation::where('payment_id', $p->id)
                        ->where('enrollment_id', $enrollment->id)
                        ->sum('allocated_amount');
                    if ($allocated < (float) $p->amount) {
                        $allocationService->allocatePayment($p);
                        $this->info("  ✓ Allocated payment #{$p->id} (₹{$p->amount})");
                        $fixed++;
                        $reallocated++;
                    }
                }
                if ($reallocated > 0) {
                    $enrollment->refresh();
                }
            }

            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalPaid = (float) $enrollment->total_fee - $totalOutstanding;

            $this->line("  Recalculated outstanding: ₹{$totalOutstanding}");
            $this->line("  Recalculated paid: ₹{$totalPaid}");

            if (abs((float) $enrollment->outstanding_amount - $totalOutstanding) > 0.01) {
                $this->warn("  MISMATCH - needs fix");
                if ($applyFix || $reallocate) {
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
                    $this->info("  ✓ Fixed totals");
                    $fixed++;
                }
            } else {
                $this->line("  OK - no change needed");
            }
        }

        if ($fixed > 0) {
            $this->info("\nFixed {$fixed} enrollment(s).");
        } elseif (!$applyFix && !$approvePending && !$reallocate && $enrollments->isNotEmpty()) {
            $this->warn("\nOptions: --fix (recalc totals), --reallocate (allocate unallocated payments), --approve-pending");
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
