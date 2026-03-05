<?php

namespace App\Services;

use App\Models\CreditAllocation;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\PaymentAllocation;

class PaymentAllocationService
{
    /**
     * Allocate payment to fee types in order: Registration -> Course Fee -> Assessment Fee
     */
    public function allocatePayment(Payment $payment): array
    {
        if (!$payment->enrollment_id) {
            return [];
        }

        $enrollment = $payment->enrollment;
        $paymentAmount = $payment->amount;
        $allocations = [];

        // Get current outstanding amounts for each fee type
        $outstandingAmounts = $this->getOutstandingAmounts($enrollment);

        // Allocation order: Registration -> Course Fee -> Assessment Fee
        $feeTypes = ['registration', 'course_fee', 'assessment_fee'];

        foreach ($feeTypes as $feeType) {
            if ($paymentAmount <= 0) {
                break;
            }

            $outstandingForFeeType = $outstandingAmounts[$feeType];
            
            if ($outstandingForFeeType > 0) {
                $allocatedAmount = min($paymentAmount, $outstandingForFeeType);
                $remainingAfterAllocation = $outstandingForFeeType - $allocatedAmount;

                $allocation = PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'enrollment_id' => $enrollment->id,
                    'fee_type' => $feeType,
                    'allocated_amount' => $allocatedAmount,
                    'remaining_fee' => $remainingAfterAllocation
                ]);

                $allocations[] = $allocation;
                $paymentAmount -= $allocatedAmount;
            }
        }

        return $allocations;
    }

    /**
     * Get outstanding amounts for each fee type (payments + credit allocations)
     */
    public function getOutstandingAmounts(Enrollment $enrollment): array
    {
        $outstandingAmounts = [
            'registration' => $enrollment->registration_fee,
            'course_fee' => $enrollment->course_fee,
            'assessment_fee' => $enrollment->assessment_fee
        ];

        // Subtract already paid amounts for each fee type
        $paidAllocations = PaymentAllocation::where('enrollment_id', $enrollment->id)
            ->whereHas('payment', function($query) {
                $query->where('status', 'approved');
            })
            ->get();

        foreach ($paidAllocations as $allocation) {
            $outstandingAmounts[$allocation->fee_type] -= $allocation->allocated_amount;
        }

        // Subtract credit allocations
        $creditAllocations = CreditAllocation::where('enrollment_id', $enrollment->id)->get();
        foreach ($creditAllocations as $allocation) {
            $outstandingAmounts[$allocation->fee_type] -= $allocation->allocated_amount;
        }

        // Ensure no negative amounts
        foreach ($outstandingAmounts as $key => $amount) {
            $outstandingAmounts[$key] = max(0, $amount);
        }

        return $outstandingAmounts;
    }

    /**
     * Get payment summary for receipt display
     */
    public function getPaymentSummary(Enrollment $enrollment): array
    {
        $allocations = PaymentAllocation::where('enrollment_id', $enrollment->id)
            ->whereHas('payment', function($query) {
                $query->where('status', 'approved');
            })
            ->with(['payment'])
            ->orderBy('created_at')
            ->get();

        $summary = [];
        $index = 1;

        foreach ($allocations as $allocation) {
            $summary[] = [
                'sl_no' => $index++,
                'date' => $allocation->payment->created_at->format('d-m-Y'),
                'fee_type' => $allocation->fee_type_display,
                'amount' => $allocation->allocated_amount,
                'status' => 'Paid'
            ];
        }

        // Include credit allocations
        $creditAllocations = \App\Models\CreditAllocation::where('enrollment_id', $enrollment->id)
            ->with('studentCreditTransaction')
            ->orderBy('created_at')
            ->get();

        foreach ($creditAllocations as $allocation) {
            $summary[] = [
                'sl_no' => $index++,
                'date' => $allocation->studentCreditTransaction->created_at->format('d-m-Y'),
                'fee_type' => $allocation->fee_type_display . ' (Credit)',
                'amount' => $allocation->allocated_amount,
                'status' => 'Paid'
            ];
        }

        return $summary;
    }

    /**
     * Calculate total outstanding amount
     */
    public function getTotalOutstanding(Enrollment $enrollment): float
    {
        $outstandingAmounts = $this->getOutstandingAmounts($enrollment);
        return array_sum($outstandingAmounts);
    }
}