<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\AssessmentResultMail;
use App\Mail\CertificateIssuedMail;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Services\CertificateIssuanceService;
use App\Services\CertificatePdfService;
use App\Services\CertificateTemplateService;
use App\Services\StudentPushNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class StudentController extends Controller
{
    private function findEligibleEnrollmentForAssessmentCourse(Student $student, int $assessmentCourseId): ?Enrollment
    {
        return $student->enrollments()
            ->with(['batch.course', 'legacyLinkCourse'])
            ->get()
            ->first(fn (Enrollment $e) => $e->assessment_course_id === $assessmentCourseId && $e->can_take_assessment);
    }

    private function findEnrollmentForAssessmentCourse(Student $student, int $assessmentCourseId): ?Enrollment
    {
        return $student->enrollments()
            ->with(['batch.course', 'legacyLinkCourse'])
            ->get()
            ->first(fn (Enrollment $e) => $e->assessment_course_id === $assessmentCourseId);
    }

    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student; // Get the related Student model

        if (! $student) {
            // If no student record exists, create a basic one or handle gracefully
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Get student's enrollments with course and batch info
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch', 'legacyLinkCourse', 'lastAccessedLesson'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get student's payments
        $payments = Payment::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get student's assessment results
        $assessmentResults = AssessmentResult::where('student_id', $student->id)
            ->with(['assessment', 'enrollment.batch.course', 'enrollment.legacyLinkCourse'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Get student's certificates
        $certificates = Certificate::where('student_id', $student->id)
            ->with(['course', 'batch', 'enrollment.legacyLinkCourse'])
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get();

        // Get available assessments based on batch end dates (optimized)
        $availableAssessments = collect();
        $pendingAssessments = collect();

        // Course IDs that have an active assessment mapping (batch course or legacy link)
        $courseIds = $enrollments->map(fn (Enrollment $e) => $e->assessment_course_id)->filter()->unique()->values();

        // Get all assessments for these courses in one query
        $assessments = $courseIds->isEmpty()
            ? collect()
            : \App\Models\Assessment::whereIn('course_id', $courseIds)
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
            $batch = $enrollment->batch;
            $assessmentCourseId = $enrollment->assessment_course_id;
            if (! $assessmentCourseId) {
                continue;
            }

            $assessment = $assessments->get($assessmentCourseId);
            if (! $assessment) {
                continue;
            }

            $displayCourseName = $enrollment->display_course_name;
            $periodEnd = $enrollment->effective_end_date;

            if ($enrollment->can_take_assessment) {
                if (! in_array($assessment->id, $existingResults)) {
                    $isReassessment = in_array($assessment->id, $failedResults);
                    $availableAssessments->push([
                        'assessment' => $assessment,
                        'enrollment' => $enrollment,
                        'display_course_name' => $displayCourseName,
                        'batch' => $batch,
                        'is_reassessment' => $isReassessment,
                    ]);
                }
            } else {
                if ($periodEnd && $periodEnd > now()) {
                    $pendingAssessments->push([
                        'assessment' => $assessment,
                        'enrollment' => $enrollment,
                        'display_course_name' => $displayCourseName,
                        'batch' => $batch,
                        'days_remaining' => now()->diffInDays($periodEnd),
                        'period_end' => $periodEnd,
                    ]);
                }
            }
        }

        $approvedPaymentsTotal = (float) Payment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->sum('amount');
        $pendingPaymentsTotal = (float) Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->sum('amount');
        $pendingPaymentsCount = Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();
        $passedAssessmentsCount = AssessmentResult::where('student_id', $student->id)
            ->where('is_passed', true)
            ->count();
        $issuedCertificatesCount = Certificate::where('student_id', $student->id)
            ->where('is_issued', true)
            ->count();
        $pendingCertificateCount = max(0, $passedAssessmentsCount - $issuedCertificatesCount);
        $outstandingAmount = (float) $enrollments->sum('outstanding_amount');

        $courseJourneyCards = $this->buildDashboardCourseCards($enrollments, $availableAssessments);
        $journey = $this->buildStudentJourney(
            $student,
            $enrollments,
            $courseJourneyCards,
            $availableAssessments,
            $outstandingAmount,
            $pendingCertificateCount,
            $issuedCertificatesCount
        );
        $journeyTimeline = $this->buildStudentJourneyTimeline(
            $student,
            $enrollments,
            $courseJourneyCards,
            $availableAssessments,
            $outstandingAmount,
            $passedAssessmentsCount,
            $pendingCertificateCount,
            $issuedCertificatesCount
        );

        // Calculate statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'active_enrollments' => $enrollments->where('status', 'active')->count(),
            'completed_assessments' => AssessmentResult::where('student_id', $student->id)->count(),
            'passed_assessments' => $passedAssessmentsCount,
            'certificates_earned' => $issuedCertificatesCount,
            'pending_certificates' => $pendingCertificateCount,
            'total_payments' => $approvedPaymentsTotal,
            'pending_payments' => $pendingPaymentsCount,
            'pending_payment_amount' => $pendingPaymentsTotal,
            'outstanding_amount' => $outstandingAmount,
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
            'courseJourneyCards',
            'journey',
            'journeyTimeline',
            'stats'
        ));
    }

    private function buildDashboardCourseCards(Collection $enrollments, Collection $availableAssessments): Collection
    {
        return $enrollments
            ->where('status', 'active')
            ->values()
            ->map(function (Enrollment $enrollment) use ($availableAssessments) {
                $course = $enrollment->course;
                $progress = $course ? $enrollment->lmsProgressForCourse($course) : null;
                $hasLessons = $course ? $course->lmsHostHasActiveLessons() : false;
                $availableExam = $availableAssessments->first(
                    fn (array $item) => (int) $item['enrollment']->id === (int) $enrollment->id
                );

                $nextAction = [
                    'label' => 'View course',
                    'url' => route('student.enrollments'),
                    'icon' => 'fa-book-open',
                ];
                $state = 'On track';
                $tone = 'emerald';
                $note = 'Your enrollment is active.';

                if ($enrollment->outstanding_amount > 0) {
                    $state = 'Payment due';
                    $tone = 'amber';
                    $note = 'Clear your pending fee to unlock the full journey.';
                    $nextAction = [
                        'label' => 'View payments',
                        'url' => route('student.payments'),
                        'icon' => 'fa-credit-card',
                    ];
                } elseif ($availableExam) {
                    $state = ($availableExam['is_reassessment'] ?? false) ? 'Re-exam ready' : 'Exam ready';
                    $tone = 'violet';
                    $note = 'You can take your assessment now.';
                    $nextAction = [
                        'label' => ($availableExam['is_reassessment'] ?? false) ? 'Retake exam' : 'Take exam',
                        'url' => route('student.assessments.take', $availableExam['assessment']->id),
                        'icon' => 'fa-clipboard-check',
                    ];
                } elseif ($hasLessons) {
                    $completed = (int) ($progress['completed'] ?? 0);
                    $total = (int) ($progress['total'] ?? 0);

                    if ($total > 0 && $completed < $total) {
                        $state = 'Learning in progress';
                        $tone = 'sky';
                        $note = sprintf('%d of %d lessons completed.', $completed, $total);
                    } else {
                        $state = 'Lessons complete';
                        $tone = 'teal';
                        $note = 'You have finished the online lessons for this course.';
                    }

                    $nextAction = [
                        'label' => $enrollment->lastAccessedLesson ? 'Resume lessons' : 'Start lessons',
                        'url' => Route::has('student.learn.resume')
                            ? route('student.learn.resume', $enrollment)
                            : route('student.learn.outline', $enrollment),
                        'icon' => 'fa-book-reader',
                    ];
                }

                return [
                    'enrollment' => $enrollment,
                    'title' => $enrollment->display_course_name,
                    'batch' => $enrollment->batch?->batch_name,
                    'status' => ucfirst($enrollment->status),
                    'state' => $state,
                    'tone' => $tone,
                    'note' => $note,
                    'progress' => $progress,
                    'has_lessons' => $hasLessons,
                    'date_range' => trim(collect([
                        $enrollment->effective_start_date?->format('d M Y'),
                        $enrollment->effective_end_date?->format('d M Y'),
                    ])->filter()->implode(' - ')),
                    'outstanding_amount' => (float) $enrollment->outstanding_amount,
                    'next_action' => $nextAction,
                ];
            });
    }

    private function buildStudentJourney(
        Student $student,
        Collection $enrollments,
        Collection $courseJourneyCards,
        Collection $availableAssessments,
        float $outstandingAmount,
        int $pendingCertificateCount,
        int $issuedCertificatesCount
    ): array {
        $activeCards = $courseJourneyCards->values();
        $learningCard = $activeCards->first(
            fn (array $card) => $card['has_lessons'] && (($card['progress']['percent'] ?? 0) < 100)
        );
        $hero = [
            'badge' => 'Ready',
            'title' => 'Stay on track',
            'description' => 'Keep moving through your course journey from lessons to assessments and certificates.',
            'theme' => 'from-sky-600 via-primary-700 to-primary-900',
            'meta' => 'Your student dashboard is now organized around the next best action.',
            'action' => [
                'label' => 'Open my courses',
                'url' => route('student.enrollments'),
                'icon' => 'fa-book-open',
            ],
            'secondary_action' => [
                'label' => 'View payments',
                'url' => route('student.payments'),
                'icon' => 'fa-credit-card',
            ],
        ];

        if (! $student->is_approved) {
            return [
                'badge' => 'Pending approval',
                'title' => 'Your profile is under review',
                'description' => 'Your institute will approve your student account before the course journey begins.',
                'theme' => 'from-amber-500 via-orange-500 to-rose-500',
                'meta' => 'You do not need to do anything right now unless the institute asks for more details.',
                'action' => [
                    'label' => 'View profile',
                    'url' => route('student.profile'),
                    'icon' => 'fa-user',
                ],
                'secondary_action' => null,
            ];
        }

        if ($enrollments->isEmpty()) {
            return [
                'badge' => 'Awaiting enrollment',
                'title' => 'You are approved and waiting for your first course',
                'description' => 'Once the institute enrolls you into a batch, your learning journey will appear here.',
                'theme' => 'from-slate-600 via-slate-700 to-primary-900',
                'meta' => 'Keep your contact information up to date so your centre can notify you quickly.',
                'action' => [
                    'label' => 'Review profile',
                    'url' => route('student.profile'),
                    'icon' => 'fa-address-card',
                ],
                'secondary_action' => null,
            ];
        }

        if ($outstandingAmount > 0) {
            return [
                'badge' => 'Payment due',
                'title' => 'Clear your pending fee to keep everything moving',
                'description' => 'Your course journey has an outstanding balance. Paying it keeps assessments and certificates on track.',
                'theme' => 'from-amber-500 via-orange-500 to-rose-500',
                'meta' => 'Some assessments stay locked until your fee is fully paid.',
                'action' => [
                    'label' => 'View payments',
                    'url' => route('student.payments'),
                    'icon' => 'fa-credit-card',
                ],
                'secondary_action' => [
                    'label' => 'See my courses',
                    'url' => route('student.enrollments'),
                    'icon' => 'fa-book',
                ],
            ];
        }

        if ($learningCard) {
            return [
                'badge' => 'Learning in progress',
                'title' => 'Continue your current course',
                'description' => $learningCard['note'],
                'theme' => 'from-primary-600 via-sky-600 to-cyan-600',
                'meta' => 'Finishing your lessons is the fastest path to exam eligibility.',
                'action' => $learningCard['next_action'],
                'secondary_action' => [
                    'label' => 'View all courses',
                    'url' => route('student.enrollments'),
                    'icon' => 'fa-book-open',
                ],
            ];
        }

        if ($availableAssessments->isNotEmpty()) {
            $exam = $availableAssessments->first();

            return [
                'badge' => ($exam['is_reassessment'] ?? false) ? 'Re-assessment ready' : 'Eligible for exam',
                'title' => ($exam['is_reassessment'] ?? false)
                    ? 'You can retake your assessment now'
                    : 'Your next milestone is ready',
                'description' => 'Your course journey has reached the exam stage for '.$exam['display_course_name'].'.',
                'theme' => 'from-violet-600 via-purple-700 to-primary-900',
                'meta' => 'Take the exam while your eligibility window is open.',
                'action' => [
                    'label' => ($exam['is_reassessment'] ?? false) ? 'Retake exam' : 'Take exam',
                    'url' => route('student.assessments.take', $exam['assessment']->id),
                    'icon' => 'fa-clipboard-check',
                ],
                'secondary_action' => [
                    'label' => 'Review exam status',
                    'url' => route('student.assessments'),
                    'icon' => 'fa-chart-line',
                ],
            ];
        }

        if ($pendingCertificateCount > 0) {
            return [
                'badge' => 'Certificate processing',
                'title' => 'You have completed a course milestone',
                'description' => 'Your result is complete and your certificate should appear once the institute issues it.',
                'theme' => 'from-emerald-600 via-teal-600 to-primary-900',
                'meta' => 'Keep an eye on your certificates section for the issued document.',
                'action' => [
                    'label' => 'Open certificates',
                    'url' => route('student.certificates'),
                    'icon' => 'fa-certificate',
                ],
                'secondary_action' => [
                    'label' => 'View results',
                    'url' => route('student.assessments'),
                    'icon' => 'fa-award',
                ],
            ];
        }

        if ($issuedCertificatesCount > 0) {
            return [
                'badge' => 'Certificate ready',
                'title' => 'Your certificate is available',
                'description' => 'You have completed at least one course journey successfully.',
                'theme' => 'from-emerald-600 via-green-600 to-lime-600',
                'meta' => 'Download your certificate any time from the student portal.',
                'action' => [
                    'label' => 'View certificates',
                    'url' => route('student.certificates'),
                    'icon' => 'fa-download',
                ],
                'secondary_action' => [
                    'label' => 'Back to dashboard',
                    'url' => route('student.dashboard'),
                    'icon' => 'fa-house',
                ],
            ];
        }

        return $hero;
    }

    private function buildStudentJourneyTimeline(
        Student $student,
        Collection $enrollments,
        Collection $courseJourneyCards,
        Collection $availableAssessments,
        float $outstandingAmount,
        int $passedAssessmentsCount,
        int $pendingCertificateCount,
        int $issuedCertificatesCount
    ): array {
        $allLessonsComplete = $courseJourneyCards->isNotEmpty()
            && $courseJourneyCards
                ->where('has_lessons', true)
                ->every(fn (array $card) => ($card['progress']['percent'] ?? 100) >= 100);

        return [
            [
                'label' => 'Registered',
                'note' => 'Your student account exists in the system.',
                'state' => 'complete',
                'icon' => 'fa-user-plus',
            ],
            [
                'label' => 'Approved',
                'note' => 'Institute approval unlocks the journey.',
                'state' => $student->is_approved ? 'complete' : 'current',
                'icon' => 'fa-user-check',
            ],
            [
                'label' => 'Enrolled',
                'note' => 'Your batch and course are assigned.',
                'state' => $enrollments->isNotEmpty() ? 'complete' : ($student->is_approved ? 'current' : 'upcoming'),
                'icon' => 'fa-book-open-reader',
            ],
            [
                'label' => 'Fees cleared',
                'note' => 'Payments must be approved to remove blockers.',
                'state' => $enrollments->isEmpty() ? 'upcoming' : ($outstandingAmount <= 0 ? 'complete' : 'current'),
                'icon' => 'fa-wallet',
            ],
            [
                'label' => 'Lessons complete',
                'note' => 'Finish the online content where lessons apply.',
                'state' => $courseJourneyCards->isEmpty()
                    ? 'upcoming'
                    : ($allLessonsComplete ? 'complete' : 'current'),
                'icon' => 'fa-book-reader',
            ],
            [
                'label' => 'Exam stage',
                'note' => 'Take or retake the assessment during the valid window.',
                'state' => $passedAssessmentsCount > 0
                    ? 'complete'
                    : ($availableAssessments->isNotEmpty() ? 'current' : 'upcoming'),
                'icon' => 'fa-clipboard-check',
            ],
            [
                'label' => 'Certificate',
                'note' => 'Issued once the institute completes certification.',
                'state' => $issuedCertificatesCount > 0
                    ? 'complete'
                    : ($pendingCertificateCount > 0 ? 'current' : 'upcoming'),
                'icon' => 'fa-certificate',
            ],
        ];
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

        if (! $student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with([
                'batch',
                'batch.course.learningModules',
                'legacyLinkCourse.learningModules',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.enrollments', compact('enrollments'));
    }

    public function payments()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
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

        if (! $student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        $assessmentResults = AssessmentResult::where('student_id', $student->id)
            ->with(['assessment', 'enrollment.batch.course', 'enrollment.legacyLinkCourse'])
            ->orderBy('completed_at', 'desc')
            ->paginate(10);

        // Get available re-assessments
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['batch.course', 'batch', 'legacyLinkCourse'])
            ->get();

        $courseIds = $enrollments->map(fn (Enrollment $e) => $e->assessment_course_id)->filter()->unique()->values();
        $assessments = $courseIds->isEmpty()
            ? collect()
            : \App\Models\Assessment::whereIn('course_id', $courseIds)
                ->where('is_active', true)
                ->get()
                ->keyBy('course_id');

        $failedResults = AssessmentResult::where('student_id', $student->id)
            ->where('is_passed', false)
            ->pluck('assessment_id')
            ->toArray();

        $reassessments = collect();
        foreach ($enrollments as $enrollment) {
            $batch = $enrollment->batch;
            $assessmentCourseId = $enrollment->assessment_course_id;
            if (! $assessmentCourseId) {
                continue;
            }
            $assessment = $assessments->get($assessmentCourseId);

            if ($assessment && $enrollment->can_take_assessment && in_array($assessment->id, $failedResults)) {
                $reassessments->push([
                    'assessment' => $assessment,
                    'enrollment' => $enrollment,
                    'display_course_name' => $enrollment->display_course_name,
                    'batch' => $batch,
                ]);
            }
        }

        $examStatusEnrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['batch.course', 'legacyLinkCourse'])
            ->get()
            ->filter(fn (Enrollment $e) => $e->assessment_course_id !== null);

        return view('student.assessments', compact('assessmentResults', 'reassessments', 'examStatusEnrollments'));
    }

    public function certificates()
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        $certificates = Certificate::where('student_id', $student->id)
            ->with(['course', 'batch', 'enrollment.legacyLinkCourse'])
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('student.certificates', compact('certificates'));
    }

    public function downloadCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        if (! $certificate->is_issued || ! $certificate->certificate_number) {
            return redirect()->back()
                ->with('error', 'Certificate is not yet issued.');
        }

        $templateService = app(CertificateTemplateService::class);
        $html = $templateService->generateHtml($certificate);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->getOptions()->setDefaultMediaType('print'); // Apply @media print rules (no body padding)
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');

        $pdfContent = $pdf->output();
        $pdfContent = CertificatePdfService::keepFirstPageOnly($pdfContent);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="certificate_'.$certificate->certificate_number.'.pdf"',
            'Content-Length' => strlen($pdfContent),
        ]);
    }

    public function idCard()
    {
        $user = Auth::user();
        $student = $user->student;
        if (! $student) {
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
        if (! $student) {
            return redirect()->route('student.profile')->with('error', 'Student profile not found.');
        }
        $student->load(['documents', 'enrollments.batch.course']);
        $pdf = Pdf::loadView('admin.students.id-card-pdf', compact('student'));
        $pdf->setPaper([0, 0, 242.65, 153.07]); // CR80: 85.6mm x 54mm

        return $pdf->download('id-card-'.$student->full_name.'.pdf');
    }

    public function downloadReceipt(Payment $payment)
    {
        $user = Auth::user();
        $student = $user->student;

        // Ensure the payment belongs to the authenticated student
        if (! $student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        // Receipt is generated on-the-fly (same as admin) - stream PDF to avoid cache
        if ($payment->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Receipt is only available for approved payments.');
        }

        $payment->load(['student', 'enrollment.batch.course', 'enrollment.legacyLinkCourse', 'approvedBy', 'allocations']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('receipt_'.$payment->payment_receipt_number.'.pdf');
    }

    public function downloadReceiptPdf(Payment $payment)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student || $payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment receipt.');
        }

        if ($payment->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Receipt is only available for approved payments.');
        }

        $payment->load(['student', 'enrollment.batch.course', 'enrollment.legacyLinkCourse', 'approvedBy', 'allocations']);

        $pdf = Pdf::loadView('admin.payments.receipt-pdf', compact('payment'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('receipt_'.$payment->payment_receipt_number.'.pdf');
    }

    public function takeAssessment(\App\Models\Assessment $assessment)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
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

        $eligibleEnrollment = $this->findEligibleEnrollmentForAssessmentCourse($student, (int) $assessment->course_id);

        if (! $eligibleEnrollment) {
            return redirect()->route('student.assessments')
                ->with('error', 'Exam is not available yet. Finish all online lessons for this course, wait until after your batch end date, clear all fees, and ensure your institute has marked you eligible for the exam.');
        }

        // Check if assessment has started (session check)
        if (! session()->has('assessment_started_'.$assessment->id)) {
            // Show instructions first
            return view('student.assessments.instructions', compact('assessment'));
        }

        $questionSessionKey = 'assessment_question_ids_'.$assessment->id;
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

        if (! $student) {
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

        $eligibleEnrollment = $this->findEligibleEnrollmentForAssessmentCourse($student, (int) $assessment->course_id);

        if (! $eligibleEnrollment) {
            return redirect()->route('student.assessments')
                ->with('error', 'Exam is not available yet. Finish all online lessons for this course, wait until after your batch end date, clear all fees, and ensure your institute has marked you eligible for the exam.');
        }

        // Mark assessment as started in session
        session(['assessment_started_'.$assessment->id => true]);
        session(['assessment_start_time_'.$assessment->id => now()]);

        // Redirect to the actual assessment
        return redirect()->route('student.assessments.take', $assessment);
    }

    public function submitAssessment(Request $request, \App\Models\Assessment $assessment)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
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

        $questionSessionKey = 'assessment_question_ids_'.$assessment->id;
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
        if ($percentage >= 80) {
            $grade = 'A+';
        } elseif ($percentage >= 60) {
            $grade = 'A';
        } elseif ($percentage >= 35) {
            $grade = 'B';
        }

        // Calculate actual time taken - prefer client-side value (includes tab switch time)
        $startTime = session('assessment_start_time_'.$assessment->id);
        $timeTakenSeconds = (int) $request->input('time_taken_seconds');
        if ($timeTakenSeconds > 0) {
            $timeTakenMinutes = max(1, (int) ceil($timeTakenSeconds / 60));
        } else {
            $timeTakenMinutes = $startTime ? max(1, (int) ceil(now()->diffInSeconds($startTime) / 60)) : 1;
        }

        $enrollment = $this->findEligibleEnrollmentForAssessmentCourse($student, (int) $assessment->course_id)
            ?? $this->findEnrollmentForAssessmentCourse($student, (int) $assessment->course_id);

        if (! $enrollment) {
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
            'answers' => json_encode($answers),
        ]);

        // Generate certificate if student passed
        if ($isPassed) {
            try {
                $certificate = app(CertificateIssuanceService::class)->createForPassedResult($result, $enrollment);
                if ($certificate) {
                    try {
                        $certificate->load(['course', 'student']);
                        Mail::to($result->student->email)->send(new CertificateIssuedMail($certificate));
                        try {
                            app(\App\Services\WhatsAppNotificationService::class)->sendCertificateIssued($certificate);
                        } catch (\Exception $e) {
                            \Log::error('Certificate WhatsApp failed: '.$e->getMessage());
                        }
                        try {
                            app(StudentPushNotificationService::class)->sendCertificateIssued($certificate);
                        } catch (\Exception $e) {
                            \Log::error('Certificate PWA push failed: '.$e->getMessage());
                        }
                    } catch (\Exception $e) {
                        \Log::error('Certificate email failed: '.$e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Certificate generation failed: '.$e->getMessage());
            }
        }

        // Send assessment result email (pass or fail)
        try {
            $result->load(['assessment.course', 'enrollment.batch.course', 'enrollment.legacyLinkCourse', 'student']);
            Mail::to($result->student->email)->send(new AssessmentResultMail($result));
            try {
                app(\App\Services\WhatsAppNotificationService::class)->sendAssessmentResult($result);
            } catch (\Exception $e) {
                \Log::error('Assessment result WhatsApp failed: '.$e->getMessage());
            }
            try {
                app(StudentPushNotificationService::class)->sendAssessmentResult($result);
            } catch (\Exception $e) {
                \Log::error('Assessment result PWA push failed: '.$e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Assessment result email failed: '.$e->getMessage());
        }

        // Clear session data
        session()->forget([
            'assessment_started_'.$assessment->id,
            'assessment_start_time_'.$assessment->id,
            $questionSessionKey,
        ]);

        return redirect()->route('student.assessments.show', $result->id)
            ->with($isPassed ? 'success' : 'info', $isPassed ? 'Congratulations! You passed the exam!' : 'Exam submitted. You did not pass. You can reattempt.');
    }

    public function showAssessmentResult(AssessmentResult $result)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student) {
            return redirect()->route('student.profile')
                ->with('error', 'Please complete your student profile first.');
        }

        // Ensure the result belongs to the authenticated student
        if ($result->student_id !== $student->id) {
            abort(403, 'Unauthorized access to assessment result.');
        }

        // Load relationships
        $result->load(['assessment', 'enrollment.batch.course', 'enrollment.legacyLinkCourse']);

        return view('student.assessments.show', compact('result', 'student'));
    }

    public function viewCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;

        // Ensure the certificate belongs to the authenticated student
        if (! $student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        $certificate->load(['student', 'course', 'batch', 'assessmentResult']);

        return view('student.certificates.view', compact('certificate'));
    }

    public function previewCertificate(Certificate $certificate)
    {
        $user = Auth::user();
        $student = $user->student;

        if (! $student || $certificate->student_id !== $student->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        $templateService = app(CertificateTemplateService::class);
        $html = $templateService->generateHtml($certificate);

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    private function generateCertificateFile(Certificate $certificate)
    {
        $templateService = app(CertificateTemplateService::class);

        // Generate HTML content using Training Certification template
        $htmlContent = $templateService->generateHtml($certificate);

        // Create file path
        $fileName = 'certificate_'.$certificate->certificate_number.'.html';
        $filePath = 'certificates/'.$fileName;

        // Store the file
        \Storage::put($filePath, $htmlContent);

        // Update certificate with file path
        $certificate->update(['certificate_file_path' => $filePath]);

        return $filePath;
    }
}
