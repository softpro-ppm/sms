<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Mail\FullyPaidMail;
use App\Mail\PaymentApprovedMail;
use App\Models\Certificate;
use App\Models\EnrollmentDiscount;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Enrollment;
use App\Jobs\SyncPaymentToAmsJob;
use App\Services\LegacyAutoCertificationService;
use App\Services\PaymentAllocationService;
use App\Services\StudentPushNotificationService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    use ScopesByTrainingPartner;

    protected function ensurePaymentBelongsToPartner(Payment $payment): void
    {
        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null && (int) $payment->student?->training_partner_id !== $tpId) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 15;
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));

        $query = $this->scopePayments(Payment::with(['student', 'enrollment.batch.course', 'approvedBy']));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('payment_receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($studentQuery) use ($search) {
                      $studentQuery->where('full_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('whatsapp_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('enrollment.batch', function ($batchQuery) use ($search) {
                      $batchQuery->where('batch_name', 'like', "%{$search}%")
                          ->orWhereHas('course', function ($courseQuery) use ($search) {
                              $courseQuery->where('name', 'like', "%{$search}%");
                          });
                  });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        // Statistics (TP-scoped)
        $stats = [
            'total_payments' => $this->scopePayments(Payment::query())->count(),
            'pending_payments' => $this->scopePayments(Payment::where('status', 'pending'))->count(),
            'approved_payments' => $this->scopePayments(Payment::where('status', 'approved'))->count(),
            'rejected_payments' => $this->scopePayments(Payment::where('status', 'rejected'))->count(),
            'total_amount_pending' => $this->scopePayments(Payment::where('status', 'pending'))->sum('amount'),
            'total_amount_approved' => $this->scopePayments(Payment::where('status', 'approved'))->sum('amount'),
            'total_remaining_amount' => $this->calculateTotalRemainingAmount(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function pending(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $pendingQuery = $this->pendingPaymentsQuery($request);
        $pendingData = $pendingQuery
            ->orderByDesc('outstanding_amount')
            ->paginate($perPage)
            ->appends($request->query());

        $statsQuery = $this->pendingPaymentsQuery($request);

        $stats = [
            'pending_students' => (clone $statsQuery)->count(),
            'total_pending_amount' => (clone $statsQuery)->sum('outstanding_amount'),
            'average_pending' => (clone $statsQuery)->avg('outstanding_amount') ?? 0,
        ];

        return view('admin.payments.pending', [
            'pendingData' => $pendingData,
            'stats' => $stats,
        ]);
    }

    public function pendingApprovals(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        $query = $this->pendingApprovalsQuery($request);

        $payments = $query
            ->orderBy('created_at')
            ->paginate($perPage)
            ->appends($request->query());

        $statsQuery = $this->pendingApprovalsQuery($request);
        $stats = [
            'pending_count' => (clone $statsQuery)->count(),
            'pending_amount' => (clone $statsQuery)->sum('amount'),
        ];

        return view('admin.payments.pending-approvals', compact('payments', 'stats'));
    }

    public function exportPendingCsv(Request $request)
    {
        $filename = 'pending_payments_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Sl. No',
                'Name',
                'Phone Number',
                'Batch',
                'Total',
                'Paid',
                'Balance',
                'Course',
                'Email',
            ]);

            $index = 0;
            $this->pendingPaymentsQuery($request)
                ->orderByDesc('outstanding_amount')
                ->chunk(500, function ($rows) use ($handle, &$index) {
                    foreach ($rows as $enrollment) {
                        $index++;
                        fputcsv($handle, [
                            $index,
                            $enrollment->student?->full_name,
                            $enrollment->student?->whatsapp_number,
                            $enrollment->batch?->batch_name,
                            $enrollment->total_fee,
                            $enrollment->paid_amount,
                            $enrollment->outstanding_amount,
                            $enrollment->batch?->course?->name,
                            $enrollment->student?->email,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function pendingPaymentsQuery(Request $request)
    {
        $query = $this->scopeEnrollments(
            Enrollment::query()
                ->with(['student', 'batch.course'])
                ->withCount([
                    'payments as pending_payments_count' => fn ($paymentQuery) => $paymentQuery->where('status', 'pending'),
                ])
                ->withMax('payments', 'created_at')
                ->where('status', 'active')
                ->where('outstanding_amount', '>', 0)
        );

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('whatsapp_number', 'like', "%{$search}%");
                })->orWhereHas('batch', function ($batchQuery) use ($search) {
                    $batchQuery->where('batch_name', 'like', "%{$search}%")
                        ->orWhereHas('course', function ($courseQuery) use ($search) {
                            $courseQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        return $query;
    }

    private function pendingApprovalsQuery(Request $request)
    {
        $query = $this->scopePayments(
            Payment::with(['student', 'enrollment.batch.course'])
                ->where('status', 'pending')
        );

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('payment_receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('whatsapp_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('enrollment.batch', function ($batchQuery) use ($search) {
                        $batchQuery->where('batch_name', 'like', "%{$search}%")
                            ->orWhereHas('course', function ($courseQuery) use ($search) {
                                $courseQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        return $query;
    }

    public function debug()
    {
        $payments = $this->scopePayments(
            Payment::with(['student'])->orderBy('created_at', 'desc')->take(10)
        )->get();

        return view('admin.payments.debug', compact('payments'));
    }

    public function create(Request $request)
    {
        $studentId = $request->get('student_id');
        $enrollmentId = $request->get('enrollment_id');
        $student = null;
        $enrollments = collect();
        $selectedEnrollment = null;

        if ($studentId) {
            $student = Student::with(['enrollments.batch.course'])->find($studentId);
            $tpId = $this->getTrainingPartnerId();
            if ($student && $tpId !== null && (int) $student->training_partner_id !== $tpId) {
                $student = null;
            }
            if ($student) {
                $enrollments = $student->enrollments()->where('status', 'active')->get();
                
                // If enrollment_id is provided and student has this enrollment, pre-select it
                if ($enrollmentId) {
                    $selectedEnrollment = $enrollments->firstWhere('id', $enrollmentId);
                }
            }
        }

        return view('admin.payments.create', compact('student', 'enrollments', 'selectedEnrollment'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'enrollment_id' => 'required|exists:enrollments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'nullable|string',
            'payment_method' => 'required|in:cash_upi,cash,upi,online',
            'remarks' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if enrollment belongs to student
        $enrollment = Enrollment::where('id', $request->enrollment_id)
            ->where('student_id', $request->student_id)
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'Invalid enrollment for this student.')
                ->withInput();
        }

        // Ensure student belongs to TP
        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null && (int) $enrollment->student->training_partner_id !== $tpId) {
            abort(404);
        }

        // Generate unique receipt number
        $receiptNumber = $this->generateUniqueReceiptNumber();

        // Create payment record
        $payment = Payment::create([
            'student_id' => $request->student_id,
            'enrollment_id' => $request->enrollment_id,
            'payment_receipt_number' => $receiptNumber,
            'amount' => $request->amount,
            'payment_type' => 'partial', // Will be updated to 'full' on approval if fully paid
            'payment_method' => $request->payment_method,
            'status' => 'pending', // Always pending - needs admin approval
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully! Receipt #' . $receiptNumber . ' is pending approval.');
    }

    public function approve(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        if (! auth()->user()->is_admin) {
            return redirect()->back()
                ->with('error', 'Only admin can approve payments.');
        }

        $allocationService = new PaymentAllocationService;
        $approved = $this->approveSinglePendingPayment($payment, $allocationService);

        if ($approved === null) {
            return redirect()->back()
                ->with('info', 'This payment was already approved.');
        }

        $approved->load(['student', 'enrollment.batch.course']);
        $this->sendPaymentApprovedNotifications($approved);

        SyncPaymentToAmsJob::dispatch($approved->id);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved successfully! Receipt #' . $approved->payment_receipt_number);
    }

    public function reject(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        if (!auth()->user()->is_admin) {
            return redirect()->back()
                ->with('error', 'Only admin can reject payments.');
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment rejected successfully! Receipt #' . $payment->payment_receipt_number);
    }

    public function bulkApprove(Request $request)
    {
        // Only admin can bulk approve
        if (!auth()->user()->is_admin) {
            return redirect()->back()
                ->with('error', 'Only admin can approve payments.');
        }

        $paymentIds = $request->input('payment_ids', []);

        if (empty($paymentIds)) {
            return redirect()->back()
                ->with('error', 'No payments selected for approval.');
        }

        $payments = $this->scopePayments(
            Payment::whereIn('id', $paymentIds)->where('status', 'pending')
        )->get();

        $approvedCount = 0;
        $allocationService = new PaymentAllocationService;

        $fullyPaidEnrollmentIds = [];

        foreach ($payments as $payment) {
            $approvedPayment = $this->approveSinglePendingPayment($payment, $allocationService);

            if ($approvedPayment === null) {
                continue;
            }

            $approvedCount++;

            $approvedPayment->load(['student', 'enrollment.batch.course']);
            try {
                Mail::to($approvedPayment->student->email)->send(new PaymentApprovedMail($approvedPayment));
            } catch (\Exception $e) {
                \Log::error('Bulk payment email failed: ' . $e->getMessage());
            }
            try {
                app(WhatsAppNotificationService::class)->sendPaymentApproved($approvedPayment);
            } catch (\Exception $e) {
                \Log::error('Bulk payment WhatsApp failed: ' . $e->getMessage());
            }

            SyncPaymentToAmsJob::dispatch($approvedPayment->id);

            if ($approvedPayment->enrollment_id) {
                $enrollment = Enrollment::find($approvedPayment->enrollment_id);
                if ($enrollment && (float) ($enrollment->outstanding_amount ?? 0) <= 0) {
                    $fullyPaidEnrollmentIds[$enrollment->id] = $enrollment;
                }
            }
        }

        // Send fully paid emails (one per enrollment that became fully paid)
        foreach ($fullyPaidEnrollmentIds as $enrollment) {
            try {
                $enrollment->refresh();
                $enrollment->loadMissing(['batch', 'student', 'legacyLinkCourse']);
                if ($enrollment->is_legacy && $enrollment->batch?->is_legacy_batch) {
                    app(LegacyAutoCertificationService::class)->issueIfEligible($enrollment->fresh(['batch', 'student', 'legacyLinkCourse']));
                }
                $enrollment->refresh();
                $hasCert = Certificate::query()
                    ->where('enrollment_id', $enrollment->id)
                    ->where('is_issued', true)
                    ->exists();
                $isLegacyBatch = $enrollment->is_legacy && $enrollment->batch?->is_legacy_batch;
                if ($isLegacyBatch && $hasCert) {
                    continue;
                }
                $enrollment->load(['batch.course', 'student']);
                Mail::to($enrollment->student->email)->send(new FullyPaidMail($enrollment));
                try {
                    app(WhatsAppNotificationService::class)->sendFullyPaid($enrollment);
                } catch (\Exception $e) {
                    \Log::error('Fully paid WhatsApp failed: ' . $e->getMessage());
                }
            } catch (\Exception $e) {
                \Log::error('Fully paid email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.payments.index')
            ->with('success', "Bulk approved {$approvedCount} payments successfully!");
    }

    public function show(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        $payment->load(['student', 'enrollment.batch.course', 'enrollment.discounts.appliedBy', 'approvedBy']);
        
        return view('admin.payments.show', compact('payment'));
    }

    public function storeDiscount(Request $request, Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);

        if (! auth()->user()->is_admin) {
            return redirect()->back()->with('error', 'Only admin can apply special discounts.');
        }

        $enrollment = $payment->enrollment;
        if (! $enrollment || ! $enrollment->exists) {
            return redirect()->back()->with('error', 'This payment has no active enrollment for discount handling.');
        }

        $validated = $request->validate([
            'discount_amount' => 'required|numeric|min:0.01',
            'discount_reason' => 'required|string|max:255',
        ]);

        $allocationService = new PaymentAllocationService();
        $outstandingByFee = $allocationService->getOutstandingAmounts($enrollment);
        $courseOutstanding = (float) ($outstandingByFee['course_fee'] ?? 0);
        $discountAmount = round((float) $validated['discount_amount'], 2);

        if ($courseOutstanding <= 0) {
            return redirect()->back()->with('error', 'No course fee balance is available for discount.');
        }

        if ($discountAmount > $courseOutstanding) {
            return redirect()->back()->with('error', 'Discount cannot exceed the remaining course fee balance of ₹' . number_format($courseOutstanding, 2) . '.');
        }

        DB::transaction(function () use ($enrollment, $validated, $discountAmount, $allocationService): void {
            Enrollment::query()->whereKey($enrollment->id)->lockForUpdate()->first();

            EnrollmentDiscount::create([
                'enrollment_id' => $enrollment->id,
                'fee_type' => 'course_fee',
                'amount' => $discountAmount,
                'reason' => $validated['discount_reason'],
                'applied_by' => auth()->id(),
                'applied_at' => now(),
            ]);

            $freshEnrollment = Enrollment::query()->find($enrollment->id);
            if ($freshEnrollment) {
                $freshEnrollment = $allocationService->recalculateEnrollmentTotals($freshEnrollment);

                if ((float) $freshEnrollment->outstanding_amount <= 0) {
                    Payment::where('enrollment_id', $freshEnrollment->id)
                        ->where('status', 'approved')
                        ->update(['payment_type' => 'full']);
                }
            }
        });

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Special discount applied successfully.');
    }

    public function retryAmsSync(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);

        if (! auth()->user()->is_super_admin) {
            return redirect()->back()->with('error', 'Only Super Admin can retry AMS sync.');
        }

        if ($payment->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved payments can be synced to AMS.');
        }

        try {
            SyncPaymentToAmsJob::dispatchSync($payment->id, true);

            $payment->refresh();

            if ($payment->ams_sync_status === 'synced') {
                return redirect()->back()->with('success', 'AMS sync successful for receipt #'.$payment->payment_receipt_number.'.');
            }

            return redirect()->back()->with('error', 'AMS sync failed: '.($payment->ams_last_error ?? 'unknown'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'AMS sync error: '.$e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        if (!auth()->user()->is_admin) {
            return redirect()->back()
                ->with('error', 'Only admin can delete payments.');
        }

        $enrollment = $payment->enrollment;
        $wasApproved = $payment->status === 'approved';
        $payment->delete();

        if ($wasApproved && $enrollment) {
            $allocationService = new \App\Services\PaymentAllocationService();
            $enrollment = $allocationService->recalculateEnrollmentTotals($enrollment);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

    public function generateReceipt(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a5', 'landscape');

        return $pdf->stream('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    public function downloadReceiptPdf(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a5', 'landscape');
        
        return $pdf->download('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    // API Methods for AJAX requests
    public function getStudents()
    {
        $students = $this->scopeStudents(
            Student::where('status', 'approved')->select('id', 'full_name', 'email', 'aadhar_number')
        )->get();

        return response()->json($students);
    }

    public function getStudentEnrollments(Student $student)
    {
        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null && (int) $student->training_partner_id !== $tpId) {
            return response()->json([], 404);
        }
        $enrollments = $student->enrollments()
            ->with(['batch.course'])
            ->where('status', 'active')
            ->get();
        
        return response()->json($enrollments);
    }

    /**
     * Approve a single pending payment with row lock (idempotent: already-approved returns null).
     */
    private function approveSinglePendingPayment(Payment $payment, PaymentAllocationService $allocationService): ?Payment
    {
        $approvedModel = null;

        DB::transaction(function () use ($payment, $allocationService, &$approvedModel): void {
            $locked = Payment::query()
                ->whereKey($payment->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $locked) {
                return;
            }

            $locked->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            if ($locked->enrollment_id) {
                Enrollment::query()->whereKey($locked->enrollment_id)->lockForUpdate()->first();

                $allocationService->allocatePayment($locked->fresh());

                $enrollment = Enrollment::query()->find($locked->enrollment_id);
                if ($enrollment) {
                    $enrollment = $allocationService->recalculateEnrollmentTotals($enrollment);

                    if ((float) $enrollment->outstanding_amount <= 0) {
                        Payment::where('enrollment_id', $enrollment->id)
                            ->where('status', 'approved')
                            ->update(['payment_type' => 'full']);
                    }
                }
            }

            $approvedModel = $locked->fresh(['student', 'enrollment.batch.course']);
        });

        return $approvedModel;
    }

    /**
     * Payment-approved messaging plus legacy fully-paid flows when balance clears.
     */
    private function sendPaymentApprovedNotifications(Payment $payment): void
    {
        try {
            Mail::to($payment->student->email)->send(new PaymentApprovedMail($payment));
        } catch (\Exception $e) {
            \Log::error('Payment email failed: ' . $e->getMessage());
        }

        if ($payment->enrollment_id) {
            $enrollment = $payment->enrollment;
            $enrollment->refresh();
            if ((float) ($enrollment->outstanding_amount ?? 0) <= 0) {
                $enrollment->loadMissing(['batch', 'student', 'legacyLinkCourse']);
                if ($enrollment->is_legacy && $enrollment->batch?->is_legacy_batch) {
                    app(LegacyAutoCertificationService::class)->issueIfEligible($enrollment->fresh(['batch', 'student', 'legacyLinkCourse']));
                }
                $enrollment->refresh();
                $hasCert = Certificate::query()
                    ->where('enrollment_id', $enrollment->id)
                    ->where('is_issued', true)
                    ->exists();
                $isLegacyBatch = $enrollment->is_legacy && $enrollment->batch?->is_legacy_batch;
                if (! $isLegacyBatch || ! $hasCert) {
                    try {
                        $enrollment->load(['batch.course', 'student']);
                        Mail::to($payment->student->email)->send(new FullyPaidMail($enrollment));
                    } catch (\Exception $e) {
                        \Log::error('Fully paid email failed: ' . $e->getMessage());
                    }
                    try {
                        app(WhatsAppNotificationService::class)->sendFullyPaid($enrollment);
                    } catch (\Exception $e) {
                        \Log::error('Fully paid WhatsApp failed: ' . $e->getMessage());
                    }
                    try {
                        app(StudentPushNotificationService::class)->sendFullyPaid($enrollment);
                    } catch (\Exception $e) {
                        \Log::error('Fully paid PWA push failed: ' . $e->getMessage());
                    }
                }
            }
        }

        try {
            app(WhatsAppNotificationService::class)->sendPaymentApproved($payment);
        } catch (\Exception $e) {
            \Log::error('Payment approved WhatsApp failed: ' . $e->getMessage());
        }
        try {
            app(StudentPushNotificationService::class)->sendPaymentApproved($payment);
        } catch (\Exception $e) {
            \Log::error('Payment approved PWA push failed: ' . $e->getMessage());
        }
    }

    /**
     * Calculate total remaining amount to be collected from all students
     * Formula: Total Fees - Paid Amount (for all students)
     */
    private function calculateTotalRemainingAmount()
    {
        return (float) $this->scopeEnrollments(
            Enrollment::query()->where('status', 'active')
        )->sum('outstanding_amount');
    }

    /**
     * Generate a truly unique receipt number
     */
    private function generateUniqueReceiptNumber()
    {
        $year = date('Y');
        $prefix = 'RCP-' . $year . '-';
        
        // Get the highest existing receipt number for this year
        $lastReceipt = Payment::where('payment_receipt_number', 'like', $prefix . '%')
            ->orderBy('payment_receipt_number', 'desc')
            ->first();
        
        if ($lastReceipt) {
            // Extract the number from the last receipt and increment
            $lastNumber = substr($lastReceipt->payment_receipt_number, -6);
            $nextNumber = str_pad((int)$lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            // First receipt for this year
            $nextNumber = '000001';
        }
        
        $receiptNumber = $prefix . $nextNumber;
        
        // Double-check uniqueness (shouldn't be needed but good safety measure)
        if (Payment::where('payment_receipt_number', $receiptNumber)->exists()) {
            $receiptNumber = $prefix . str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        }

        return $receiptNumber;
    }

}
