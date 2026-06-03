<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Assessment;
use App\Models\QuestionBank;
use App\Models\Certificate;
use App\Models\WhatsAppConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ScopesByTrainingPartner;

    private const DASHBOARD_CACHE_TTL_SECONDS = 120;

    public function index()
    {
        $tpId = $this->getTrainingPartnerId();
        $user = auth()->user();
        $isReception = $user->is_reception;

        $stats = Cache::remember(
            $this->dashboardCacheKey($user, 'stats'),
            self::DASHBOARD_CACHE_TTL_SECONDS,
            fn () => $this->loadDashboardStats($tpId)
        );

        $recentActivities = Cache::remember(
            $this->dashboardCacheKey($user, 'recent'),
            self::DASHBOARD_CACHE_TTL_SECONDS,
            fn () => $this->loadRecentActivities()
        );

        $chartData = Cache::remember(
            $this->dashboardCacheKey($user, 'charts'),
            self::DASHBOARD_CACHE_TTL_SECONDS,
            fn () => $this->loadChartData()
        );

        // Extract recent activities for easier access in view
        $recentStudents = $recentActivities['recent_students'];
        $recentPayments = $recentActivities['recent_payments'];
        $recentAssessments = $recentActivities['recent_assessments'];

        $onboarding = null;
        if ($tpId !== null && ! auth()->user()->is_super_admin && auth()->user()->is_admin) {
            $courseDone = Course::query()->visibleToTrainingPartner($tpId)->exists();
            $batchDone = Batch::query()->visibleToTrainingPartner($tpId)->exists();
            $questionBankDone = QuestionBank::query()
                ->whereHas('course', fn ($q) => $q->visibleToTrainingPartner($tpId))
                ->exists();
            $examDone = Assessment::query()
                ->whereHas('course', fn ($q) => $q->visibleToTrainingPartner($tpId))
                ->exists();
            $studentDone = $this->scopeStudents(Student::where('status', 'approved'))->exists();
            $enrollmentDone = $this->scopeEnrollments(Enrollment::where('status', 'active'))->exists();

            $allRequiredComplete = $courseDone && $batchDone && $questionBankDone && $examDone && $studentDone && $enrollmentDone;

            $onboarding = [
                'course_done' => $courseDone,
                'batch_done' => $batchDone,
                'question_bank_done' => $questionBankDone,
                'exam_done' => $examDone,
                'student_done' => $studentDone,
                'enrollment_done' => $enrollmentDone,
                'payment_done' => $this->scopePayments(Payment::where('status', 'approved'))->exists(),
                'all_required_complete' => $allRequiredComplete,
                'show_modal' => ! auth()->user()->dismiss_catalog_onboarding && ! $allRequiredComplete,
            ];
        }

        $receptionWorkspace = $isReception
            ? Cache::remember(
                $this->dashboardCacheKey($user, 'reception'),
                self::DASHBOARD_CACHE_TTL_SECONDS,
                fn () => $this->loadReceptionWorkspace()
            )
            : null;

        $adminWorkspace = ! $isReception
            ? Cache::remember(
                $this->dashboardCacheKey($user, 'admin-workspace'),
                self::DASHBOARD_CACHE_TTL_SECONDS,
                fn () => $this->loadAdminWorkspace()
            )
            : null;

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'chartData',
            'recentStudents',
            'recentPayments',
            'recentAssessments',
            'onboarding',
            'receptionWorkspace',
            'adminWorkspace'
        ));
    }

    private function loadDashboardStats(?int $tpId): array
    {
        return [
            'total_students' => $this->scopeStudents(Student::where('status', 'approved'))->count(),
            'pending_students' => $this->scopeStudents(Student::where('status', 'pending'))->count(),
            'total_courses' => $tpId !== null
                ? Course::query()->visibleToTrainingPartner($tpId)->where('is_active', true)->count()
                : Course::where('is_active', true)->count(),
            'active_batches' => Batch::query()
                ->visibleToTrainingPartner($tpId)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'total_enrollments' => $this->scopeEnrollments(Enrollment::where('status', 'active'))->count(),
            'pending_payments' => $this->scopePayments(Payment::where('status', 'pending'))->count(),
            'total_payments' => $this->scopePayments(Payment::where('status', 'approved'))->sum('amount'),
            'total_revenue' => $this->scopePayments(Payment::where('status', 'approved'))->sum('amount'),
            'certificates_issued' => $this->scopeCertificates(Certificate::where('is_issued', true))->count(),
        ];
    }

    private function loadRecentActivities(): array
    {
        return [
            'recent_students' => $this->scopeStudents(
                Student::select('id', 'full_name', 'email', 'status', 'created_at')
                    ->where('status', 'approved')
                    ->latest('created_at')
                    ->limit(5)
            )->get(),
            'recent_payments' => $this->scopePayments(
                Payment::select('id', 'student_id', 'amount', 'status', 'created_at')
                    ->with(['student:id,full_name'])
                    ->where('status', 'approved')
                    ->latest('created_at')
                    ->limit(5)
            )->get(),
            'recent_assessments' => $this->scopeAssessmentResults(
                AssessmentResult::select('id', 'student_id', 'assessment_id', 'total_marks', 'percentage', 'is_passed', 'created_at')
                    ->with(['student:id,full_name', 'assessment:id,course_id'])
                    ->latest('created_at')
                    ->limit(5)
            )->get(),
        ];
    }

    private function loadChartData(): array
    {
        return [
            'monthly_enrollments' => $this->getMonthlyEnrollments(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'course_popularity' => $this->getCoursePopularity(),
            'batch_performance' => $this->getBatchPerformance(),
        ];
    }

    /**
     * Plain-language process guide for staff (Phase 1 — operational clarity).
     */
    public function help()
    {
        return view('admin.help');
    }

    /**
     * Persist “don’t show catalogue setup popup again” for this admin (TP centre).
     */
    public function dismissCatalogOnboarding(Request $request)
    {
        $user = auth()->user();
        if (! $user->is_admin || $user->is_super_admin || $user->training_partner_id === null) {
            abort(403);
        }

        $permanent = $request->boolean('dismiss_permanently');
        if ($permanent) {
            $user->forceFill(['dismiss_catalog_onboarding' => true])->save();
        }

        return response()->json(['ok' => true, 'dismissed_permanently' => $permanent]);
    }

    private function getMonthlyEnrollments()
    {
        $driver = DB::connection()->getDriverName();
        $query = $this->scopeEnrollments(Enrollment::query())
            ->where('enrollment_date', '>=', now()->subMonths(12));

        if ($driver === 'sqlite') {
            return $query->select(
                    DB::raw('CAST(strftime("%m", enrollment_date) AS INTEGER) as month'),
                    DB::raw('CAST(strftime("%Y", enrollment_date) AS INTEGER) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }

        return $query->select(
                DB::raw('MONTH(enrollment_date) as month'),
                DB::raw('YEAR(enrollment_date) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    private function getMonthlyRevenue()
    {
        $driver = DB::connection()->getDriverName();
        $query = $this->scopePayments(Payment::where('status', 'approved')->where('created_at', '>=', now()->subMonths(12)));

        if ($driver === 'sqlite') {
            return $query->select(
                    DB::raw('CAST(strftime("%m", created_at) AS INTEGER) as month'),
                    DB::raw('CAST(strftime("%Y", created_at) AS INTEGER) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }

        return $query->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
    }

    private function getCoursePopularity()
    {
        $tpId = $this->getTrainingPartnerId();
        $enrollmentFilter = $tpId !== null
            ? fn ($q) => $q->where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))
            : fn ($q) => $q->where('status', 'active');

        $courseQuery = Course::query();
        if ($tpId !== null) {
            $courseQuery->visibleToTrainingPartner($tpId);
        }

        return $courseQuery->withCount(['enrollments' => $enrollmentFilter])
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getBatchPerformance()
    {
        $tpId = $this->getTrainingPartnerId();
        $enrollmentFilter = $tpId !== null
            ? fn ($q) => $q->where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))
            : fn ($q) => $q->where('status', 'active');

        return Batch::query()
            ->visibleToTrainingPartner($tpId)
            ->with(['course', 'enrollments'])
            ->withCount(['enrollments' => $enrollmentFilter])
            ->where('end_date', '<=', now())
            ->orderBy('enrollments_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function loadReceptionWorkspace(): array
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $admissionsToday = $this->scopeStudents(
            Student::whereBetween('created_at', [$todayStart, $todayEnd])
        )->count();

        $paymentsToday = $this->scopePayments(
            Payment::whereBetween('created_at', [$todayStart, $todayEnd])
        )->count();

        $pendingApprovals = $this->scopeStudents(
            Student::where('status', 'pending')
        )->count();

        $missingDocumentsCount = $this->scopeStudents(
            Student::where(function ($query) {
                $query->whereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'photo'))
                    ->orWhereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'aadhar'))
                    ->orWhereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'qualification_certificate'));
            })
        )->count();

        $recentAdmissions = $this->scopeStudents(
            Student::select('id', 'full_name', 'status', 'created_at')
                ->latest('created_at')
                ->limit(5)
        )->get();

        $missingDocumentStudents = $this->scopeStudents(
            Student::with('documents:id,student_id,document_type')
                ->where(function ($query) {
                    $query->whereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'photo'))
                        ->orWhereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'aadhar'))
                        ->orWhereDoesntHave('documents', fn ($doc) => $doc->where('document_type', 'qualification_certificate'));
                })
                ->latest('created_at')
                ->limit(5)
        )->get()->map(function (Student $student) {
            $types = $student->documents->pluck('document_type')->all();
            $missing = collect(['photo', 'aadhar', 'qualification_certificate'])
                ->reject(fn ($type) => in_array($type, $types, true))
                ->map(fn ($type) => match ($type) {
                    'photo' => 'Photo',
                    'aadhar' => 'Aadhar',
                    'qualification_certificate' => 'Qualification',
                    default => ucfirst($type),
                })
                ->values();

            return [
                'student' => $student,
                'missing' => $missing,
            ];
        });

        $recentPayments = $this->scopePayments(
            Payment::select('id', 'student_id', 'amount', 'status', 'created_at')
                ->with(['student:id,full_name'])
                ->latest('created_at')
                ->limit(5)
        )->get();

        return [
            'stats' => [
                'admissions_today' => $admissionsToday,
                'missing_documents' => $missingDocumentsCount,
                'pending_approvals' => $pendingApprovals,
                'payments_today' => $paymentsToday,
            ],
            'recent_admissions' => $recentAdmissions,
            'missing_document_students' => $missingDocumentStudents,
            'recent_payments' => $recentPayments,
        ];
    }

    private function loadAdminWorkspace(): array
    {
        $tpId = $this->getTrainingPartnerId();
        $today = now()->startOfDay();
        $examWindowStart = now()->subYear()->startOfDay();

        $pendingStudents = $this->scopeStudents(
            Student::select('id', 'full_name', 'email', 'created_at', 'status')
                ->where('status', 'pending')
                ->latest('created_at')
                ->limit(5)
        )->get();

        $pendingPayments = $this->scopePayments(
            Payment::select('id', 'student_id', 'amount', 'created_at', 'status')
                ->with('student:id,full_name')
                ->where('status', 'pending')
                ->latest('created_at')
                ->limit(5)
        )->get();

        $readyForEnrollmentCount = $this->scopeStudents(
            Student::where('status', 'approved')
                ->whereDoesntHave('enrollments', fn ($query) => $query->where('status', 'active'))
        )->count();

        $assessmentReadyQuery = $this->scopeEnrollments(
            Enrollment::query()
                ->where('status', 'active')
                ->where('is_eligible_for_assessment', true)
                ->where('outstanding_amount', '<=', 0)
                ->where('is_legacy', false)
                ->whereHas('batch', function ($query) use ($today, $examWindowStart) {
                    $query->whereDate('end_date', '<=', $today)
                        ->whereDate('end_date', '>=', $examWindowStart);
                })
                ->whereDoesntHave('assessmentResults', fn ($query) => $query->where('is_passed', true))
        );

        $assessmentReadyCount = (clone $assessmentReadyQuery)->count();
        $assessmentReady = (clone $assessmentReadyQuery)
            ->with(['student:id,full_name', 'batch:id,batch_name,course_id,end_date', 'batch.course:id,name', 'legacyLinkCourse:id,name'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $pendingCertificatesCount = $this->scopeCertificates(
            Certificate::where('is_issued', false)
        )->count();

        $pendingCertificates = $this->scopeCertificates(
            Certificate::select('id', 'student_id', 'course_id', 'batch_id', 'enrollment_id', 'issue_date', 'is_issued', 'created_at')
                ->with(['student:id,full_name', 'course:id,name', 'batch:id,batch_name', 'enrollment.legacyLinkCourse:id,name'])
                ->where('is_issued', false)
                ->latest('created_at')
                ->limit(5)
        )->get();

        $enrollmentCountFilter = $tpId !== null
            ? fn ($query) => $query->where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))
            : fn ($query) => $query->where('status', 'active');

        $batchHealth = Batch::query()
            ->visibleToTrainingPartner($tpId)
            ->with('course:id,name')
            ->withCount(['enrollments' => $enrollmentCountFilter])
            ->where('is_active', true)
            ->orderByRaw("
                CASE
                    WHEN DATE(start_date) > ? THEN 0
                    WHEN DATE(start_date) <= ? AND DATE(end_date) >= ? THEN 1
                    ELSE 2
                END
            ", [$today->toDateString(), $today->toDateString(), $today->toDateString()])
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(function (Batch $batch) use ($today) {
                $maxStudents = max(1, (int) ($batch->max_students ?? 0));
                $fillRate = $batch->max_students
                    ? (int) round(($batch->enrollments_count / $maxStudents) * 100)
                    : null;

                $status = 'Ended';
                if ($batch->start_date && $batch->start_date->startOfDay()->gt($today)) {
                    $status = 'Upcoming';
                } elseif ($batch->end_date && $batch->end_date->endOfDay()->gte($today)) {
                    $status = 'Running';
                }

                return [
                    'batch' => $batch,
                    'fill_rate' => $fillRate,
                    'status' => $status,
                ];
            });

        $lowFillCount = $batchHealth
            ->filter(fn ($item) => $item['status'] !== 'Ended' && $item['fill_rate'] !== null && $item['fill_rate'] < 50)
            ->count();

        $whatsAppBase = $tpId !== null
            ? WhatsAppConversation::query()->forTrainingPartner($tpId)
            : WhatsAppConversation::query();

        $unreadWhatsAppCount = (clone $whatsAppBase)
            ->where('unread_count', '>', 0)
            ->count();

        $whatsAppConversations = (clone $whatsAppBase)
            ->with(['student:id,full_name', 'lastMessage'])
            ->where('unread_count', '>', 0)
            ->orderByDesc('last_message_at')
            ->limit(5)
            ->get();

        return [
            'queue_counts' => [
                'pending_students' => $this->scopeStudents(Student::where('status', 'pending'))->count(),
                'pending_payments' => $this->scopePayments(Payment::where('status', 'pending'))->count(),
                'ready_for_enrollment' => $readyForEnrollmentCount,
                'assessment_ready' => $assessmentReadyCount,
                'pending_certificates' => $pendingCertificatesCount,
                'unread_whatsapp' => $unreadWhatsAppCount,
                'low_fill_batches' => $lowFillCount,
            ],
            'pending_students' => $pendingStudents,
            'pending_payments' => $pendingPayments,
            'assessment_ready' => $assessmentReady,
            'pending_certificates' => $pendingCertificates,
            'batch_health' => $batchHealth,
            'whatsapp_conversations' => $whatsAppConversations,
        ];
    }

    private function dashboardCacheKey($user, string $suffix): string
    {
        $scope = $user->is_super_admin ? 'super' : 'tp_'.($user->training_partner_id ?? 'null');

        return 'dashboard.'.$scope.'.'.$suffix;
    }

}
