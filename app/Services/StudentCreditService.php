<?php

namespace App\Services;

use App\Models\CreditAllocation;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\StudentCreditTransaction;
use Illuminate\Support\Facades\DB;

class StudentCreditService
{
    /**
     * Add credit when student drops or is removed from enrollment.
     * The paid amount from the enrollment is transferred to student credit.
     */
    public function addCreditFromEnrollment(Enrollment $enrollment, string $type = 'enrollment_drop'): StudentCreditTransaction
    {
        $paidAmount = (float) $enrollment->paid_amount;
        if ($paidAmount <= 0) {
            throw new \InvalidArgumentException('Enrollment has no paid amount to transfer to credit.');
        }

        $student = $enrollment->student;

        return DB::transaction(function () use ($student, $enrollment, $paidAmount, $type) {
            $transaction = StudentCreditTransaction::create([
                'student_id' => $student->id,
                'amount' => $paidAmount,
                'type' => $type,
                'notes' => 'Transfer from dropped enrollment #' . $enrollment->enrollment_number . ' (' . ($enrollment->batch?->course?->name ?? 'N/A') . ')',
                'reference_enrollment_id' => $enrollment->id,
            ]);

            $student->increment('credit_balance', $paidAmount);

            return $transaction;
        });
    }

    /**
     * Apply student credit to a new enrollment.
     * Creates credit allocations and updates enrollment.
     */
    public function applyCreditToEnrollment(Enrollment $enrollment, float $amount): StudentCreditTransaction
    {
        $student = $enrollment->student;
        $availableCredit = (float) $student->credit_balance;
        $totalFee = (float) $enrollment->total_fee;

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be positive.');
        }
        if ($amount > $availableCredit) {
            throw new \InvalidArgumentException("Insufficient credit. Available: ₹{$availableCredit}");
        }
        if ($amount > $totalFee) {
            throw new \InvalidArgumentException("Credit cannot exceed total fee (₹{$totalFee}).");
        }

        return DB::transaction(function () use ($student, $enrollment, $amount) {
            $transaction = StudentCreditTransaction::create([
                'student_id' => $student->id,
                'amount' => -$amount,
                'type' => 'enrollment_transfer',
                'notes' => 'Applied to enrollment #' . $enrollment->enrollment_number . ' (' . ($enrollment->batch?->course?->name ?? 'N/A') . ')',
                'reference_enrollment_id' => $enrollment->id,
            ]);

            // Allocate to fee types in order: registration -> course_fee -> assessment_fee
            $allocationService = new PaymentAllocationService();
            $outstandingAmounts = $allocationService->getOutstandingAmounts($enrollment);

            $remaining = $amount;
            foreach (['registration', 'course_fee', 'assessment_fee'] as $feeType) {
                if ($remaining <= 0) {
                    break;
                }
                $outstanding = $outstandingAmounts[$feeType] ?? 0;
                if ($outstanding > 0) {
                    $allocated = min($remaining, $outstanding);
                    CreditAllocation::create([
                        'enrollment_id' => $enrollment->id,
                        'student_credit_transaction_id' => $transaction->id,
                        'fee_type' => $feeType,
                        'allocated_amount' => $allocated,
                        'remaining_fee' => $outstanding - $allocated,
                    ]);
                    $remaining -= $allocated;
                }
            }

            $student->decrement('credit_balance', $amount);

            // Recalculate enrollment totals including credit
            $allocationService = new PaymentAllocationService();
            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalPaid = $enrollment->total_fee - $totalOutstanding;

            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0,
            ]);

            return $transaction;
        });
    }
}
