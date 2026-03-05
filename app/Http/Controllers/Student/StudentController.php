<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\QuestionBank;
use App\Mail\AssessmentResultMail;
use App\Mail\CertificateIssuedMail;
use App\Services\CertificateTemplateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student; // Get the related Student model
        
        if (!$student) {
            // If no student record exists, create a basic one or handle gracefully
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }
        
        // Get student's enrollments with course and batch info
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get student's payments
        $payments = Payment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get student's assessment results
        $assessmentResults = AssessmentResult::where('student_id', $student->id)
            ->with(['assessment', 'enrollment.batch.course'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get student's certificates
        $certificates = Certificate::where('student_id', $student->id)
            ->with(['course', 'batch'])
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get();
        
        // Get available assessments based on batch end dates (optimized)
        $availableAssessments = collect();
        $pendingAssessments = collect();
        
        // Get all course IDs from enrollments
        $courseIds = $enrollments->pluck('batch.course.id')->unique();
        
        // Get all assessments for these courses in one query
        $assessments = \App\Models\Assessment::whereIn('course_id', $courseIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('course_id');
            
        // Get all existing results for this student in one query
        $existingResults = AssessmentResult::where('student_id', $student->id)
            ->where('is_passed', true) // Only consider passed assessments as completed
            ->pluck('assessment_id')
            ->toArray();
            
        // Get failed assessments for re-assessment
        $failedResults = AssessmentResult::where('student_id', $student->id)
            ->where('is_passed', false)
            ->pluck('assessment_id')
            ->toArray();
        
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->batch->course;
            $batch = $enrollment->batch;
            $assessment = $assessments->get($course->id);
            
            if (!$assessment) continue;
            
            if ($enrollment->can_take_assessment) {
                if (!in_array($assessment->id, $existingResults)) {
                    $isReassessment = in_array($assessment->id, $failedResults);
                    $availableAssessments->push([
                        'assessment' => $assessment,
                        'enrollment' => $enrollment,
                        'course' => $course,
                        'batch' => $batch,
                        'is_reassessment' => $isReassessment
                    ]);
                }
            } else {
                if ($batch->end_date && $batch->end_date > now()) {
                    $pendingAssessments->push([
                        'assessment' => $assessment,
                        'enrollment' => $enrollment,
                        'course' => $course,
                        'batch' => $batch,
                        'days_remaining' => now()->diffInDays($batch->end_date)
                    ]);
                }
            }
        }
        
        // Calculate statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'active_enrollments' => $enrollments->where('status', 'active')->count(),
            'completed_assessments' => AssessmentResult::where('student_id', $student->id)->count(),
            'certificates_earned' => Certificate::where('student_id', $student->id)->where('is_issued', true)->count(),
            'total_payments' => Payment::where('student_id', $student->id)->where('status', 'approved')->sum('amount'),
            'pending_payments' => Payment::where('student_id', $student->id)->where('status', 'pending')->count(),
            'available_assessments' => $availableAssessments->count(),
            'pending_assessments' => $pendingAssessments->count(),
        ];

        return view('student.dashboard', compact(
            'user',
            'student',
            'enrollments', 
            'payments', 
            'assessmentResults', 
            'certificates', 
            'availableAssessments',
            'pendingAssessments',
            'stats'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $student = $user->student;
        return view('student.profile', compact('user', 'student'));
    }

    // Profile update functionality removed - only admin can edit student profiles

    public function enrollments()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }
        
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.enrollments', compact('enrollments'));
    }

    public function payments()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }
        
        $payments = Payment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.payments', compact('payments'));
    }

    public function assessments()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }
        
        $assessmentResults = AssessmentResult::where('student_id', $student->id)
            ->with(['assessment', 'enrollment.batch.course'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // Get available re-assessments
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch'])
            ->get();
            
        $courseIds = $enrollments->pluck('batch.course.id')->unique();
        $assessments = \App\Models\Assessment::whereIn('course_id', $courseIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('course_id');
            
        $failedResults = AssessmentResult::where('student_id', $student->id)
            ->where('is_passed', false)
            ->pluck('assessment_id')
            ->toArray();
            
        $reassessments = collect();
        foreach ($enrollments as $enrollment) {
            $course = $enrollment->batch->course;
            $batch = $enrollment->batch;
            $assessment = $assessments->get($course->id);
            
            if ($assessment && $enrollment->can_take_assessment && in_array($assessment->id, $failedResults)) {
                $reassessments->push([
                    'assessment' => $assessment,
                    'enrollment' => $enrollment,
                    'course' => $course,
                    'batch' => $batch
                ]);
            }
        }

        return view('student.assessments', compact('assessmentResults', 'reassessments'));
    }

    public function certificates()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }
        
        $certificates = Certificate::where('student_id', $student->id)
            ->with(['course', 'batch'])
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('student.certificates', compact('certificates'));
    }

    public function downloadCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Ensure the certificate belongs to the authenticated student
        if (!$student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        // Generate certificate file if it doesn't exist or uses old "Certificate of Completion" format
        $needsRegeneration = !$certificate->certificate_file_path || !\Storage::exists($certificate->certificate_file_path);
        if (!$needsRegeneration) {
            $existingContent = \Storage::get($certificate->certificate_file_path);
            $needsRegeneration = $existingContent && str_contains($existingContent, 'CERTIFICATE OF COMPLETION');
        }
        if ($needsRegeneration) {
            $this->generateCertificateFile($certificate);
        }

        if (!\Storage::exists($certificate->certificate_file_path)) {
            return redirect()->back()
                ->with('error', 'Certificate file could not be generated.');
        }

        return \Storage::download($certificate->certificate_file_path, 
            'certificate_' . $certificate->certificate_number . '.html');
    }

    public function idCard()
    {
        $user = Auth::user();
        $student = $user->student;
        if (!$student) {
            return redirect()->route('student.profile')->with('error', 'Student profile not found.');
        }
        $student->load(['documents', 'enrollments.batch.course']);
        $pdf = Pdf::loadView('admin.students.id-card-pdf', compact('student'));
        $pdf->setPaper([0, 0, 242.65, 153.07]); // CR80: 85.6mm x 54mm
        return $pdf->stream('id-card.pdf');
    }

    public function downloadIdCard()
    {
        $user = Auth::user();
        $student = $user->student;
        if (!$student) {
            return redirect()->route('student.profile')->with('error', 'Student profile not found.');
        }
        $student->load(['documents', 'enrollments.batch.course']);
        $pdf = Pdf::loadView('admin.students.id-card-pdf', compact('student'));
        $pdf->setPaper([0, 0, 242.65, 153.07]); // CR80: 85.6mm x 54mm
        return $pdf->download('id-card-' . $student->full_name . '.pdf');
    }

    public function downloadReceipt(Payment $payment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Ensure the payment belongs to the authenticated student
        if (!$student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        // Receipt is generated on-the-fly (same as admin) - stream PDF to avoid cache
        if ($payment->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Receipt is only available for approved payments.');
        }

        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm

        return $pdf->stream('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    public function downloadReceiptPdf(Payment $payment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        if ($payment->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Receipt is only available for approved payments.');
        }

        $payment->load(['student', 'enrollment.batch.course', 'approvedBy', 'allocations']);
        
        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait'); // A4 vertical: 210mm x 297mm
        
        return $pdf->download('receipt_' . $payment->payment_receipt_number . '.pdf');
    }

    public function takeAssessment(\App\Models\Assessment $assessment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Check if student has already passed this assessment
        $passedResult = AssessmentResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('is_passed', true)
            ->first();
            
        if ($passedResult) {
            return redirect()->route('student.assessments')
                ->with('error', 'You have already passed this assessment.');
        }

        $eligibleEnrollment = $student->enrollments()
            ->whereHas('batch.course', function($query) use ($assessment) {
                $query->where('id', $assessment->course_id);
            })
            ->get()
            ->first(fn ($enrollment) => $enrollment->can_take_assessment);

        if (!$eligibleEnrollment) {
            return redirect()->route('student.assessments')
                ->with('error', 'Assessment is not available. Ensure batch is completed, fee is fully paid, and exam is within one year from batch end.');
        }

        // Check if assessment has started (session check)
        if (!session()->has('assessment_started_' . $assessment->id)) {
            // Show instructions first
            return view('student.assessments.instructions', compact('assessment'));
        }

        $questionSessionKey = 'assessment_question_ids_' . $assessment->id;
        $questionIds = session($questionSessionKey, []);

        if (empty($questionIds)) {
            $sets = $assessment->generateRandomQuestions(25, 3);
            $validSets = array_filter($sets, function ($set) {
                return $set instanceof \Illuminate\Support\Collection && $set->count() >= 25;
            });

            if (empty($validSets)) {
                return redirect()->route('student.assessments')
                    ->with('error', 'Not enough questions available for this assessment.');
            }

            $selectedSet = $validSets[array_rand($validSets)];
            $questionIds = $selectedSet->pluck('id')->all();
            session([$questionSessionKey => $questionIds]);
        }

        $questionsById = QuestionBank::whereIn('id', $questionIds)->get()->keyBy('id');
        $questions = collect($questionIds)
            ->map(fn ($id) => $questionsById->get($id))
            ->filter();

        if ($questions->count() < 25) {
            return redirect()->route('student.assessments')
                ->with('error', 'Not enough questions available for this assessment.');
        }

        return view('student.assessments.take', compact('assessment', 'questions'));
    }

    public function startAssessment(\App\Models\Assessment $assessment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Check if student has already passed this assessment
        $passedResult = AssessmentResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('is_passed', true)
            ->first();
            
        if ($passedResult) {
            return redirect()->route('student.assessments')
                ->with('error', 'You have already passed this assessment.');
        }

        $eligibleEnrollment = $student->enrollments()
            ->whereHas('batch.course', function($query) use ($assessment) {
                $query->where('id', $assessment->course_id);
            })
            ->get()
            ->first(fn ($enrollment) => $enrollment->can_take_assessment);

        if (!$eligibleEnrollment) {
            return redirect()->route('student.assessments')
                ->with('error', 'Assessment is not available. Ensure batch is completed, fee is fully paid, and exam is within one year from batch end.');
        }

        // Mark assessment as started in session
        session(['assessment_started_' . $assessment->id => true]);
        session(['assessment_start_time_' . $assessment->id => now()]);

        // Redirect to the actual assessment
        return redirect()->route('student.assessments.take', $assessment);
    }

    public function submitAssessment(Request $request, \App\Models\Assessment $assessment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Check if student has already passed this assessment
        $passedResult = AssessmentResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('is_passed', true)
            ->first();
            
        if ($passedResult) {
            return redirect()->route('student.assessments')
                ->with('error', 'You have already passed this assessment.');
        }

        $questionSessionKey = 'assessment_question_ids_' . $assessment->id;
        $questionIds = session($questionSessionKey, []);

        if (empty($questionIds)) {
            return redirect()->route('student.assessments')
                ->with('error', 'Assessment session expired. Please start the exam again.');
        }

        // Process the assessment submission
        $answers = $request->input('answers', []);
        $correctAnswers = 0;
        $totalQuestions = count($questionIds);

        $questionsById = QuestionBank::whereIn('id', $questionIds)->get()->keyBy('id');
        
        foreach ($questionIds as $questionId) {
            $question = $questionsById->get($questionId);
            $selectedAnswer = $answers[$questionId] ?? null;
            if ($question && $selectedAnswer && strtoupper($question->correct_answer) === strtoupper($selectedAnswer)) {
                $correctAnswers++;
            }
        }
        
        $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $isPassed = $percentage >= 35;
        
        // Determine grade: A+ (80%+), A (60-80%), B (35-60%), C (below 35%)
        $grade = 'C';
        if ($percentage >= 80) $grade = 'A+';
        elseif ($percentage >= 60) $grade = 'A';
        elseif ($percentage >= 35) $grade = 'B';
        
        // Calculate actual time taken - prefer client-side value (includes tab switch time)
        $timeTakenSeconds = (int) $request->input('time_taken_seconds');
        if ($timeTakenSeconds > 0) {
            $timeTakenMinutes = max(1, (int) ceil($timeTakenSeconds / 60));
        } else {
            $startTime = session('assessment_start_time_' . $assessment->id);
            $timeTakenMinutes = $startTime ? max(1, (int) ceil(now()->diffInSeconds($startTime) / 60)) : 1;
        }
        
        // Find the correct enrollment for this assessment's course
        $enrollment = $student->enrollments()
            ->whereHas('batch.course', function($query) use ($assessment) {
                $query->where('id', $assessment->course_id);
            })
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('student.assessments')
                ->with('error', 'No enrollment found for this assessment.');
        }

        // Create assessment result
        $result = AssessmentResult::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'enrollment_id' => $enrollment->id,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $totalQuestions - $correctAnswers,
            'total_marks' => $correctAnswers * 4,
            'percentage' => $percentage,
            'grade' => $grade,
            'is_passed' => $isPassed,
            'started_at' => $startTime,
            'completed_at' => now(),
            'time_taken_minutes' => $timeTakenMinutes,
            'answers' => json_encode($answers)
        ]);

        // Generate certificate if student passed
        if ($isPassed) {
            try {
                $certificate = $this->generateCertificate($result, $enrollment);
                if ($certificate) {
                    try {
                        $certificate->load(['course', 'student']);
                        Mail::to($result->student->email)->send(new CertificateIssuedMail($certificate));
                        try {
                            app(\App\Services\WhatsAppNotificationService::class)->sendCertificateIssued($certificate);
                        } catch (\Exception $e) {
                            \Log::error('Certificate WhatsApp failed: ' . $e->getMessage());
                        }
                    } catch (\Exception $e) {
                        \Log::error('Certificate email failed: ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Certificate generation failed: ' . $e->getMessage());
            }
        }

        // Send assessment result email (pass or fail)
        try {
            $result->load(['assessment.course', 'enrollment.batch.course', 'student']);
            Mail::to($result->student->email)->send(new AssessmentResultMail($result));
            try {
                app(\App\Services\WhatsAppNotificationService::class)->sendAssessmentResult($result);
            } catch (\Exception $e) {
                \Log::error('Assessment result WhatsApp failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Assessment result email failed: ' . $e->getMessage());
        }

        // Clear session data
        session()->forget([
            'assessment_started_' . $assessment->id,
            'assessment_start_time_' . $assessment->id,
            $questionSessionKey
        ]);

        return redirect()->route('student.assessments.show', $result->id)
            ->with($isPassed ? 'success' : 'info', $isPassed ? 'Congratulations! You passed the exam!' : 'Exam submitted. You did not pass. You can reattempt.');
    }

    public function showAssessmentResult(AssessmentResult $result)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Ensure the result belongs to the authenticated student
        if ($result->student_id !== $student->id) {
            abort(403, 'Unauthorized access to assessment result.');
        }

        // Load relationships
        $result->load(['assessment', 'enrollment.batch.course']);

        return view('student.assessments.show', compact('result'));
    }

    public function viewCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;

        // Ensure the certificate belongs to the authenticated student
        if (!$student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        // Generate certificate file if it doesn't exist or uses old "Certificate of Completion" format
        $needsRegeneration = !$certificate->certificate_file_path || !\Storage::exists($certificate->certificate_file_path);
        if (!$needsRegeneration) {
            $existingContent = \Storage::get($certificate->certificate_file_path);
            $needsRegeneration = $existingContent && str_contains($existingContent, 'CERTIFICATE OF COMPLETION');
        }
        if ($needsRegeneration) {
            $this->generateCertificateFile($certificate);
        }

        $certificate->load(['student', 'course', 'batch', 'assessmentResult']);

        return view('student.certificates.view', compact('certificate'));
    }

    public function previewCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        $templateService = app(CertificateTemplateService::class);
        $html = $templateService->generateHtml($certificate);

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    private function generateCertificate(AssessmentResult $result, Enrollment $enrollment)
    {
        // Check if certificate already exists for this assessment result
        $existingCertificate = Certificate::where('assessment_result_id', $result->id)->first();
        
        if ($existingCertificate) {
            return $existingCertificate;
        }

        // Generate certificate number
        $nameParts = explode(' ', $result->student->full_name);
        $firstName = $nameParts[0] ?? '';
        $lastName = end($nameParts) ?? '';
        $certificateNumber = 'CERT-' . strtoupper(substr($firstName, 0, 2)) . 
                           strtoupper(substr($lastName, 0, 2)) . 
                           '-' . $result->id . '-' . date('Y');

        // Create certificate content
        $certificateContent = $this->generateCertificateContent($result, $enrollment);

        // Create certificate record
        $certificate = Certificate::create([
            'student_id' => $result->student_id,
            'course_id' => $enrollment->batch->course_id,
            'batch_id' => $enrollment->batch_id,
            'enrollment_id' => $enrollment->id,
            'assessment_result_id' => $result->id,
            'certificate_number' => $certificateNumber,
            'issue_date' => now()->toDateString(),
            'certificate_content' => $certificateContent,
            'is_issued' => true
        ]);

        return $certificate;
    }

    private function generateCertificateContent(AssessmentResult $result, Enrollment $enrollment)
    {
        $student = $result->student;
        $course = $enrollment->batch->course;
        $batch = $enrollment->batch;
        
        // Generate certificate number for display
        $nameParts = explode(' ', $result->student->full_name);
        $firstName = $nameParts[0] ?? '';
        $lastName = end($nameParts) ?? '';
        $certificateNumber = 'CERT-' . strtoupper(substr($firstName, 0, 2)) . 
                           strtoupper(substr($lastName, 0, 2)) . 
                           '-' . $result->id . '-' . date('Y');

        $content = "
        <div style='text-align: center; font-family: Arial, sans-serif; padding: 40px; border: 3px solid #2563eb;'>
            <h1 style='color: #2563eb; font-size: 32px; margin-bottom: 20px;'>CERTIFICATE OF COMPLETION</h1>
            <p style='font-size: 18px; margin-bottom: 30px;'>This is to certify that</p>
            <h2 style='color: #1f2937; font-size: 28px; margin-bottom: 30px; text-decoration: underline;'>{$student->full_name}</h2>
            <p style='font-size: 18px; margin-bottom: 20px;'>has successfully completed the course</p>
            <h3 style='color: #2563eb; font-size: 24px; margin-bottom: 20px;'>{$course->name}</h3>
            <p style='font-size: 16px; margin-bottom: 10px;'>Batch: {$batch->batch_name}</p>
            <p style='font-size: 16px; margin-bottom: 10px;'>Score: {$result->correct_answers}/{$result->total_questions} ({$result->percentage}%)</p>
            <p style='font-size: 16px; margin-bottom: 10px;'>Grade: {$result->grade}</p>
            <p style='font-size: 16px; margin-bottom: 30px;'>Date of Completion: {$result->completed_at->format('F d, Y')}</p>
            <p style='font-size: 14px; color: #6b7280;'>Certificate Number: {$certificateNumber}</p>
        </div>
        ";

        return $content;
    }

    private function generateCertificateFile(Certificate $certificate)
    {
        $templateService = app(CertificateTemplateService::class);

        // Generate HTML content using Training Certification template
        $htmlContent = $templateService->generateHtml($certificate);
        
        // Create file path
        $fileName = 'certificate_' . $certificate->certificate_number . '.html';
        $filePath = 'certificates/' . $fileName;
        
        // Store the file
        \Storage::put($filePath, $htmlContent);
        
        // Update certificate with file path
        $certificate->update(['certificate_file_path' => $filePath]);
        
        return $filePath;
    }

}
