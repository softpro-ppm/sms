<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Mail\AccountApprovedMail;
use App\Mail\CertificateIssuedMail;
use App\Services\NotificationService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BulkOperationsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function bulkApproveStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $students = Student::whereIn('id', $request->student_ids)->get();
        $approvedCount = 0;

        DB::transaction(function () use ($students, &$approvedCount) {
            foreach ($students as $student) {
                if ($student->status !== 'approved') {
                    $student->update([
                        'status' => 'approved',
                        'approved_at' => now()
                    ]);
                    $approvedCount++;

                    // Activate user account and send email
                    if ($student->user) {
                        $student->user->update(['is_active' => true]);
                    }
                    try {
                        Mail::to($student->email)->send(new AccountApprovedMail($student));
                        try {
                            app(WhatsAppNotificationService::class)->sendAccountApproved($student, ['email' => $student->email, 'password' => $student->whatsapp_number]);
                        } catch (\Exception $e) {
                            \Log::error('Bulk approve WhatsApp failed: ' . $e->getMessage());
                        }
                    } catch (\Exception $e) {
                        \Log::error('Bulk approve email failed: ' . $e->getMessage());
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$approvedCount} students.",
            'approved_count' => $approvedCount
        ]);
    }

    public function bulkApprovePayments(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id'
        ]);

        $payments = Payment::whereIn('id', $request->payment_ids)->get();
        $approvedCount = 0;

        DB::transaction(function () use ($payments, &$approvedCount) {
            foreach ($payments as $payment) {
                if ($payment->status !== 'approved') {
                    $payment->update(['status' => 'approved']);
                    $approvedCount++;

                    // Send notification
                    $this->notificationService->sendPaymentConfirmation(
                        $payment->student->user,
                        $payment
                    );
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$approvedCount} payments.",
            'approved_count' => $approvedCount
        ]);
    }

    public function bulkGenerateCertificates(Request $request)
    {
        $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'exists:assessment_results,id'
        ]);

        $results = AssessmentResult::whereIn('id', $request->result_ids)
            ->where('is_passed', true)
            ->with(['student', 'enrollment.batch.course'])
            ->get();

        $generatedCount = 0;

        DB::transaction(function () use ($results, &$generatedCount) {
            foreach ($results as $result) {
                // Check if certificate already exists
                $existingCertificate = Certificate::where('student_id', $result->student_id)
                    ->where('course_id', $result->enrollment->batch->course_id)
                    ->first();

                if (!$existingCertificate) {
                    $certificate = Certificate::create([
                        'student_id' => $result->student_id,
                        'course_id' => $result->enrollment->batch->course_id,
                        'batch_id' => $result->enrollment->batch_id,
                        'assessment_result_id' => $result->id,
                        'certificate_number' => 'CERT-' . str_pad($result->student_id, 4, '0', STR_PAD_LEFT) . '-' . date('Y'),
                        'issue_date' => now(),
                        'is_issued' => true,
                        'certificate_file_path' => null, // Will be generated later
                    ]);

                    $generatedCount++;

                    // Send certificate issued email
                    try {
                        $certificate->load(['course', 'student']);
                        Mail::to($certificate->student->email)->send(new CertificateIssuedMail($certificate));
                        try {
                            app(WhatsAppNotificationService::class)->sendCertificateIssued($certificate);
                        } catch (\Exception $e) {
                            \Log::error('Bulk certificate WhatsApp failed: ' . $e->getMessage());
                        }
                    } catch (\Exception $e) {
                        \Log::error('Bulk certificate email failed: ' . $e->getMessage());
                    }
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully generated {$generatedCount} certificates.",
            'generated_count' => $generatedCount
        ]);
    }

    public function bulkSendNotifications(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'delivery_methods' => 'array'
        ]);

        $deliveryMethods = $request->delivery_methods ?? ['email', 'whatsapp'];
        $result = $this->notificationService->sendBulkNotification(
            $request->user_ids,
            $request->type,
            $request->title,
            $request->message,
            [],
            $deliveryMethods
        );

        return response()->json([
            'success' => true,
            'message' => "Notifications sent successfully.",
            'result' => $result
        ]);
    }

    public function bulkUpdateEnrollments(Request $request)
    {
        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
            'status' => 'required|in:active,inactive,completed,suspended'
        ]);

        $enrollments = Enrollment::whereIn('id', $request->enrollment_ids)->get();
        $updatedCount = 0;

        DB::transaction(function () use ($enrollments, $request, &$updatedCount) {
            foreach ($enrollments as $enrollment) {
                $enrollment->update(['status' => $request->status]);
                $updatedCount++;

                // Send notification based on status
                $message = match($request->status) {
                    'active' => 'Your enrollment has been activated.',
                    'inactive' => 'Your enrollment has been deactivated.',
                    'completed' => 'Congratulations! You have completed your course.',
                    'suspended' => 'Your enrollment has been suspended.',
                    default => 'Your enrollment status has been updated.'
                };

                $this->notificationService->sendNotification(
                    $enrollment->student->user,
                    'enrollment_status_update',
                    'Enrollment Status Update',
                    $message,
                    ['enrollment_id' => $enrollment->id],
                    ['email', 'whatsapp']
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} enrollments.",
            'updated_count' => $updatedCount
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'model' => 'required|in:students,enrollments,payments,assessments,certificates',
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $model = match($request->model) {
            'students' => Student::class,
            'enrollments' => Enrollment::class,
            'payments' => Payment::class,
            'assessments' => AssessmentResult::class,
            'certificates' => Certificate::class,
        };

        $deletedCount = 0;

        DB::transaction(function () use ($model, $request, &$deletedCount) {
            $deletedCount = $model::whereIn('id', $request->ids)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} records.",
            'deleted_count' => $deletedCount
        ]);
    }
}
