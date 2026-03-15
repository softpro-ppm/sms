<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FullyPaidMail;
use App\Mail\PaymentApprovedMail;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AmsSyncService;
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
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 15;
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));

        $query = Payment::with(['student', 'enrollment.batch.course', 'approvedBy']);

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

        // Statistics
        $stats = [
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'approved_payments' => Payment::where('status', 'approved')->count(),
            'rejected_payments' => Payment::where('status', 'rejected')->count(),
            'total_amount_pending' => Payment::where('status', 'pending')->sum('amount'),
            'total_amount_approved' => Payment::where('status', 'approved')->sum('amount'),
            'total_remaining_amount' => $this->calculateTotalRemainingAmount(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function pending(Request $request)
    {
        // Get all enrollments with pending payments
        // Use paid_amount/outstanding_amount (includes credit allocations) - NOT sum of payments
        $enrollments = \App\Models\Enrollment::with(['student', 'batch.course', 'payments'])
            ->where('status', 'active')
            ->get()
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

        // Sort by pending amount (highest first)
        usort($pendingData, function($a, $b) {
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

    public function debug()
    {
        $payments = Payment::with(['student'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

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
        // Only admin can approve payments
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
            $totalPaid = $enrollment->total_fee - $totalOutstanding;
            
            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0
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
            if ($enrollment->outstanding_amount <= 0) {
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
        try {
            app(WhatsAppNotificationService::class)->sendPaymentApproved($payment);
        } catch (\Exception $e) {
            \Log::error('Payment approved WhatsApp failed: ' . $e->getMessage());
        }

        // Sync income to AMS (Option A: sync only on approve)
        try {
            app(AmsSyncService::class)->syncPayment($payment);
        } catch (\Exception $e) {
            \Log::error('AMS sync failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved successfully! Receipt #' . $payment->payment_receipt_number);
    }

    public function reject(Payment $payment)
    {
        // Only admin can reject payments
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

        $payments = Payment::whereIn('id', $paymentIds)
            ->where('status', 'pending')
            ->get();

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
                $totalPaid = $enrollment->total_fee - $totalOutstanding;
                
                $enrollment->update([
                    'paid_amount' => $totalPaid,
                    'outstanding_amount' => $totalOutstanding,
                    'is_eligible_for_assessment' => $totalOutstanding <= 0
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
                app(AmsSyncService::class)->syncPayment($payment);
            } catch (\Exception $e) {
                \Log::error('AMS sync failed: ' . $e->getMessage());
            }

            $approvedCount++;
        }

        // Send fully paid emails (one per enrollment that became fully paid)
        foreach ($fullyPaidEnrollmentIds as $enrollment) {
            try {
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
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy']);
        
        return view('admin.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        // Only admin can delete payments
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
            $totalPaid = $enrollment->total_fee - $totalOutstanding;
            $enrollment->update([
                'paid_amount' => $totalPaid,
                'outstanding_amount' => $totalOutstanding,
                'is_eligible_for_assessment' => $totalOutstanding <= 0
            ]);
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

    public function generateReceipt(Payment $payment)
    {
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm

        return $pdf->stream('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    public function downloadReceiptPdf(Payment $payment)
    {
        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm
        
        return $pdf->download('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    // API Methods for AJAX requests
    public function getStudents()
    {
        $students = Student::where('status', 'approved')
            ->select('id', 'full_name', 'email', 'aadhar_number')
            ->get();
        
        return response()->json($students);
    }

    public function getStudentEnrollments(Student $student)
    {
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
        // Get all active enrollments
        $enrollments = Enrollment::with(['payments'])
            ->where('status', 'active')
            ->get();

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