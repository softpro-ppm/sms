<?php

namespace App\Services;

use App\Models\CreditAllocation;
use App\Models\Enrollment;
use App\Models\EnrollmentDiscount;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Illuminate\Support\Carbon;

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

        // Idempotent approval: allocations are created once per payment (unique payment_id + fee_type).
        $existing = PaymentAllocation::where('payment_id', $payment->id)->orderBy('id')->get();
        if ($existing->isNotEmpty()) {
            return $existing->all();
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
                $paymentAmount = round($paymentAmount - $allocatedAmount, 2);
            }
        }

        return $allocations;
    }

    /**
     * Get outstanding amounts for each fee type (payments + credit allocations)
     */
    public function getOutstandingAmounts(Enrollment $enrollment, ?Carbon $asOf = null): array
    {
        $outstandingAmounts = [
            'registration' => $enrollment->registration_fee,
            'course_fee' => $enrollment->course_fee,
            'assessment_fee' => $enrollment->assessment_fee
        ];

        // Subtract already paid amounts for each fee type
        $paidAllocations = PaymentAllocation::where('enrollment_id', $enrollment->id)
            ->whereHas('payment', function($query) use ($asOf) {
                $query->where('status', 'approved');
                if ($asOf) {
                    $query->where('created_at', '<=', $asOf);
                }
            })
            ->get();

        foreach ($paidAllocations as $allocation) {
            $outstandingAmounts[$allocation->fee_type] = round(
                (float) $outstandingAmounts[$allocation->fee_type] - (float) $allocation->allocated_amount,
                2
            );
        }

        // Subtract credit allocations
        $creditAllocations = CreditAllocation::where('enrollment_id', $enrollment->id)
            ->when($asOf, fn ($query) => $query->where('created_at', '<=', $asOf))
            ->get();
        foreach ($creditAllocations as $allocation) {
            $outstandingAmounts[$allocation->fee_type] = round(
                (float) $outstandingAmounts[$allocation->fee_type] - (float) $allocation->allocated_amount,
                2
            );
        }

        $discountAllocations = EnrollmentDiscount::where('enrollment_id', $enrollment->id)
            ->when($asOf, fn ($query) => $query->where('applied_at', '<=', $asOf))
            ->get();

        foreach ($discountAllocations as $allocation) {
            $outstandingAmounts[$allocation->fee_type] = round(
                (float) $outstandingAmounts[$allocation->fee_type] - (float) $allocation->amount,
                2
            );
        }

        // Ensure no negative amounts (2 dp avoids float noise showing as ₹5199.99 etc.)
        foreach ($outstandingAmounts as $key => $amount) {
            $outstandingAmounts[$key] = max(0, round((float) $amount, 2));
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
        $sum = round(array_sum($outstandingAmounts), 2);

        return $sum <= 0.004 ? 0.0 : $sum;
    }

    public function getTotalOutstandingAt(Enrollment $enrollment, Carbon $asOf): float
    {
        $outstandingAmounts = $this->getOutstandingAmounts($enrollment, $asOf);
        $sum = round(array_sum($outstandingAmounts), 2);

        return $sum <= 0.004 ? 0.0 : $sum;
    }

    public function getTotalDiscount(Enrollment $enrollment, ?Carbon $asOf = null): float
    {
        return round((float) EnrollmentDiscount::where('enrollment_id', $enrollment->id)
            ->when($asOf, fn ($query) => $query->where('applied_at', '<=', $asOf))
            ->sum('amount'), 2);
    }

    public function getApprovedPaymentTotal(Enrollment $enrollment, ?Carbon $asOf = null): float
    {
        return round((float) Payment::where('enrollment_id', $enrollment->id)
            ->where('status', 'approved')
            ->when($asOf, fn ($query) => $query->where('created_at', '<=', $asOf))
            ->sum('amount'), 2);
    }

    public function recalculateEnrollmentTotals(Enrollment $enrollment): Enrollment
    {
        $totalOutstanding = $this->getTotalOutstanding($enrollment);
        $totalFee = (float) $enrollment->total_fee;
        $totalDiscount = $this->getTotalDiscount($enrollment);
        $coveredAmount = $totalOutstanding <= 0 ? $totalFee : round($totalFee - $totalOutstanding, 2);

        $enrollment->update([
            'paid_amount' => $coveredAmount,
            'discount_amount' => $totalDiscount,
            'outstanding_amount' => $totalOutstanding,
            'is_eligible_for_assessment' => $totalOutstanding <= 0,
        ]);

        return $enrollment->fresh();
    }
}
