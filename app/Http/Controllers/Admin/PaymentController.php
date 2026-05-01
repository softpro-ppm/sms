<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Mail\FullyPaidMail;
use App\Mail\PaymentApprovedMail;
use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AmsSyncService;
use App\Services\LegacyAutoCertificationService;
use App\Services\PaymentAllocationService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
        $pendingCollection = $this->buildPendingPaymentsCollection($request);

        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedPendingData = new LengthAwarePaginator(
            $pendingCollection->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $pendingCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'pending_students' => $pendingCollection->count(),
            'total_pending_amount' => $pendingCollection->sum('pending_amount'),
            'average_pending' => $pendingCollection->count() > 0 ? $pendingCollection->avg('pending_amount') : 0,
        ];

        return view('admin.payments.pending', [
            'pendingData' => $paginatedPendingData,
            'stats' => $stats,
        ]);
    }

    public function exportPendingCsv(Request $request)
    {
        $filename = 'pending_payments_' . now()->format('Ymd_His') . '.csv';
        $pendingCollection = $this->buildPendingPaymentsCollection($request)->values();

        return response()->streamDownload(function () use ($pendingCollection) {
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

            foreach ($pendingCollection as $index => $data) {
                fputcsv($handle, [
                    $index + 1,
                    $data['student']?->full_name,
                    $data['student']?->whatsapp_number,
                    $data['batch']?->batch_name,
                    $data['course_fee'],
                    $data['approved_amount'],
                    $data['pending_amount'],
                    $data['course']?->name,
                    $data['student']?->email,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildPendingPaymentsCollection(Request $request)
    {
        $enrollments = $this->scopeEnrollments(
            \App\Models\Enrollment::with(['student', 'batch.course', 'payments'])
                ->where('status', 'active')
        )->get()
            ->filter(function ($enrollment) {
                $outstanding = (float) ($enrollment->outstanding_amount ?? 0);
                return $outstanding > 0;
            });

        $pendingData = [];

        foreach ($enrollments as $enrollment) {
            $courseFee = (float) ($enrollment->total_fee ?? 0);
            $paidAmount = (float) ($enrollment->paid_amount ?? 0);
            $pendingAmount = (float) ($enrollment->outstanding_amount ?? 0);
            $pendingPayments = $enrollment->payments->where('status', 'pending');

            if ($pendingAmount > 0) {
                $pendingData[] = [
                    'enrollment' => $enrollment,
                    'student' => $enrollment->student,
                    'course' => $enrollment->batch->course,
                    'batch' => $enrollment->batch,
                    'course_fee' => $courseFee,
                    'approved_amount' => $paidAmount,
                    'pending_amount' => $pendingAmount,
                    'pending_payments' => $pendingPayments,
                    'payment_progress' => $courseFee > 0 ? round(($paidAmount / $courseFee) * 100, 1) : 0,
                    'last_payment_date' => $enrollment->payments->max('created_at'),
                ];
            }
        }

        usort($pendingData, function ($a, $b) {
            return $b['pending_amount'] <=> $a['pending_amount'];
        });

        $pendingCollection = collect($pendingData);

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $pendingCollection = $pendingCollection->filter(function ($item) use ($search) {
                $student = $item['student'];
                $course = $item['course'];
                $batch = $item['batch'];

                return str_contains(strtolower($student->full_name ?? ''), strtolower($search)) ||
                    str_contains(strtolower($student->email ?? ''), strtolower($search)) ||
                    str_contains(strtolower($student->whatsapp_number ?? ''), strtolower($search)) ||
                    str_contains(strtolower($course->name ?? ''), strtolower($search)) ||
                    str_contains(strtolower($batch->batch_name ?? ''), strtolower($search));
            })->values();
        }

        return $pendingCollection;
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
            'status' => 'pending', // Always pending - needs admin approval
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully! Receipt #' . $receiptNumber . ' is pending approval.');
    }

    public function approve(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        if (!auth()->user()->is_admin) {
            return redirect()->back()
                ->with('error', 'Only admin can approve payments.');
        }

        $payment->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // Allocate payment to fee types and update enrollment
        if ($payment->enrollment_id) {
            $allocationService = new PaymentAllocationService();
            
            // Create payment allocations
            $allocationService->allocatePayment($payment);
            
            // Update enrollment totals
            $enrollment = $payment->enrollment;
            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalFee = (float) $enrollment->total_fee;
            $totalPaid = $totalOutstanding <= 0 ? $totalFee : round($totalFee - $totalOutstanding, 2);

            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0,
            ]);

            // Update payment_type: if fully paid, mark all payments as 'full'
            if ($totalOutstanding <= 0) {
                Payment::where('enrollment_id', $enrollment->id)
                    ->where('status', 'approved')
                    ->update(['payment_type' => 'full']);
            }
        }

        // Send payment approved email and WhatsApp (decoupled - WhatsApp sent even if email fails)
        $payment->load(['student', 'enrollment.batch.course']);
        try {
            Mail::to($payment->student->email)->send(new PaymentApprovedMail($payment));
        } catch (\Exception $e) {
            \Log::error('Payment email failed: ' . $e->getMessage());
        }
        if ($payment->enrollment_id) {
            $enrollment = $payment->enrollment;
            $enrollment->refresh();
            if ($enrollment->outstanding_amount <= 0) {
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
                }
            }
        }
        try {
            app(WhatsAppNotificationService::class)->sendPaymentApproved($payment);
        } catch (\Exception $e) {
            \Log::error('Payment approved WhatsApp failed: ' . $e->getMessage());
        }

        // Sync income to AMS (Option A: sync only on approve)
        try {
            $payment->forceFill([
                'ams_sync_status' => 'pending',
                'ams_last_attempt_at' => now(),
                'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
            ])->save();

            $result = app(AmsSyncService::class)->syncPaymentWithResult($payment);
            if (! empty($result['ok'])) {
                $payment->forceFill([
                    'ams_sync_status' => 'synced',
                    'ams_synced_at' => now(),
                    'ams_last_error' => null,
                    'ams_transaction_id' => $result['transaction_id'] ?? null,
                ])->save();
            } else {
                $payment->forceFill([
                    'ams_sync_status' => 'failed',
                    'ams_last_error' => (string) ($result['error'] ?? 'unknown_error'),
                ])->save();
            }
        } catch (\Throwable $e) {
            $payment->forceFill([
                'ams_sync_status' => 'failed',
                'ams_last_attempt_at' => now(),
                'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
                'ams_last_error' => $e->getMessage(),
            ])->save();
            \Log::error('AMS sync failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved successfully! Receipt #' . $payment->payment_receipt_number);
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
        $allocationService = new PaymentAllocationService();
        
        $fullyPaidEnrollmentIds = [];

        foreach ($payments as $payment) {
            $payment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

            // Allocate payment to fee types and update enrollment
            if ($payment->enrollment_id) {
                // Create payment allocations
                $allocationService->allocatePayment($payment);
                
                // Update enrollment totals
                $enrollment = $payment->enrollment;
                $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
                $totalFee = (float) $enrollment->total_fee;
                $totalPaid = $totalOutstanding <= 0 ? $totalFee : round($totalFee - $totalOutstanding, 2);

                $enrollment->update([
                    'paid_amount' => $totalPaid,
                    'outstanding_amount' => $totalOutstanding,
                    'is_eligible_for_assessment' => $totalOutstanding <= 0,
                ]);

                if ($totalOutstanding <= 0) {
                    $fullyPaidEnrollmentIds[$enrollment->id] = $enrollment;
                }
            }

            // Send payment approved email and WhatsApp (decoupled)
            $payment->load(['student', 'enrollment.batch.course']);
            try {
                Mail::to($payment->student->email)->send(new PaymentApprovedMail($payment));
            } catch (\Exception $e) {
                \Log::error('Bulk payment email failed: ' . $e->getMessage());
            }
            try {
                app(WhatsAppNotificationService::class)->sendPaymentApproved($payment);
            } catch (\Exception $e) {
                \Log::error('Bulk payment WhatsApp failed: ' . $e->getMessage());
            }

            // Sync income to AMS (Option A: sync only on approve)
            try {
                $payment->forceFill([
                    'ams_sync_status' => 'pending',
                    'ams_last_attempt_at' => now(),
                    'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
                ])->save();

                $result = app(AmsSyncService::class)->syncPaymentWithResult($payment);
                if (! empty($result['ok'])) {
                    $payment->forceFill([
                        'ams_sync_status' => 'synced',
                        'ams_synced_at' => now(),
                        'ams_last_error' => null,
                        'ams_transaction_id' => $result['transaction_id'] ?? null,
                    ])->save();
                } else {
                    $payment->forceFill([
                        'ams_sync_status' => 'failed',
                        'ams_last_error' => (string) ($result['error'] ?? 'unknown_error'),
                    ])->save();
                }
            } catch (\Throwable $e) {
                $payment->forceFill([
                    'ams_sync_status' => 'failed',
                    'ams_last_attempt_at' => now(),
                    'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
                    'ams_last_error' => $e->getMessage(),
                ])->save();
                \Log::error('AMS sync failed: ' . $e->getMessage());
            }

            $approvedCount++;
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
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy']);
        
        return view('admin.payments.show', compact('payment'));
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
            $payment->forceFill([
                'ams_sync_status' => 'pending',
                'ams_last_attempt_at' => now(),
                'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
            ])->save();

            $result = app(AmsSyncService::class)->syncPaymentWithResult($payment);
            if (! empty($result['ok'])) {
                $payment->forceFill([
                    'ams_sync_status' => 'synced',
                    'ams_synced_at' => now(),
                    'ams_last_error' => null,
                    'ams_transaction_id' => $result['transaction_id'] ?? null,
                ])->save();

                return redirect()->back()->with('success', 'AMS sync successful for receipt #'.$payment->payment_receipt_number.'.');
            }

            $payment->forceFill([
                'ams_sync_status' => 'failed',
                'ams_last_error' => (string) ($result['error'] ?? 'unknown_error'),
            ])->save();

            return redirect()->back()->with('error', 'AMS sync failed: '.$payment->ams_last_error);
        } catch (\Throwable $e) {
            $payment->forceFill([
                'ams_sync_status' => 'failed',
                'ams_last_attempt_at' => now(),
                'ams_attempt_count' => (int) ($payment->ams_attempt_count ?? 0) + 1,
                'ams_last_error' => $e->getMessage(),
            ])->save();

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
            $totalOutstanding = $allocationService->getTotalOutstanding($enrollment);
            $totalFee = (float) $enrollment->total_fee;
            $totalPaid = $totalOutstanding <= 0 ? $totalFee : round($totalFee - $totalOutstanding, 2);
            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0,
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

    public function generateReceipt(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm

        return $pdf->stream('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    public function downloadReceiptPdf(Payment $payment)
    {
        $this->ensurePaymentBelongsToPartner($payment);
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm
        
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
     * Calculate total remaining amount to be collected from all students
     * Formula: Total Fees - Paid Amount (for all students)
     */
    private function calculateTotalRemainingAmount()
    {
        $enrollments = $this->scopeEnrollments(
            Enrollment::with(['payments'])->where('status', 'active')
        )->get();

        $totalFees = 0;
        $totalPaid = 0;
        
        foreach ($enrollments as $enrollment) {
            $courseFee = $enrollment->total_fee ?? 0;
            $approvedPayments = $enrollment->payments->where('status', 'approved')->sum('amount');
            
            $totalFees += $courseFee;
            $totalPaid += $approvedPayments;
        }
        
        return $totalFees - $totalPaid;
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