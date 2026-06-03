<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentResult;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportsController extends Controller
{
    use ScopesByTrainingPartner;

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'payments');
        $perPage = $this->parsePerPage($request, 10);

        $courses = Course::query()
            ->where('is_active', true)
            ->visibleToTrainingPartner($this->getTrainingPartnerId())
            ->orderBy('name')
            ->get();
        $tpId = $this->getTrainingPartnerId();
        $batches = Batch::query()
            ->visibleToTrainingPartner($tpId)
            ->where('is_active', true)
            ->orderBy('batch_name')
            ->get();
        $assessments = Assessment::where('is_active', true)->orderBy('title')->get();

        $viewData = [
            'tab' => $tab,
            'courses' => $courses,
            'batches' => $batches,
            'assessments' => $assessments,
            'stats' => [],
            'payments' => null,
            'enrollments' => null,
            'students' => null,
            'results' => null,
        ];

        if ($tab === 'enrollments') {
            $query = $this->enrollmentQuery($request);
            $viewData['stats'] = $this->enrollmentStats($query);
            $viewData['enrollments'] = $query
                ->orderBy('enrollment_date', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } elseif ($tab === 'students') {
            $query = $this->studentQuery($request);
            $viewData['stats'] = $this->studentStats($query);
            $viewData['students'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } elseif ($tab === 'assessments') {
            $query = $this->assessmentResultQuery($request);
            $viewData['stats'] = $this->assessmentStats($query);
            $viewData['results'] = $query
                ->orderBy('completed_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            $query = $this->paymentQuery($request);
            $viewData['stats'] = $this->paymentStats($query);
            $viewData['payments'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        }

        return view('admin.reports.index', $viewData);
    }

    /**
     * CSV of active enrollments with fee balance (same basis as Payments → Pending Payments).
     * Honors Reports filters: search, date range (enrollment date), course, batch. Ignores payment row status.
     */
    public function exportPendingBalancesCsv(Request $request)
    {
        $filename = 'report_pending_balances_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Sl. No',
                'Name',
                'Phone',
                'Email',
                'Course',
                'Batch',
                'Enrollment No',
                'Total',
                'Paid',
                'Balance',
                'Progress %',
            ]);

            $sl = 0;
            $this->pendingBalancesQuery($request)->chunk(500, function ($rows) use ($handle, &$sl) {
                foreach ($rows as $enrollment) {
                    $sl++;
                    $total = (float) ($enrollment->total_fee ?? 0);
                    $paid = (float) ($enrollment->paid_amount ?? 0);
                    $balance = (float) ($enrollment->outstanding_amount ?? 0);
                    $progress = $total > 0 ? round(($paid / $total) * 100, 1) : 0.0;

                    fputcsv($handle, [
                        $sl,
                        $enrollment->student?->full_name,
                        $enrollment->student?->whatsapp_number,
                        $enrollment->student?->email,
                        $enrollment->display_course_name,
                        $enrollment->batch?->batch_name,
                        $enrollment->enrollment_number,
                        $total,
                        $paid,
                        $balance,
                        $progress,
                    ]);
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function export(Request $request, string $report, string $format)
    {
        $format = strtolower($format);

        if (!in_array($report, ['payments', 'enrollments', 'students', 'assessments'], true)) {
            return redirect()->route('admin.reports.index')->with('error', 'Invalid report type.');
        }

        if (!in_array($format, ['csv', 'pdf'], true)) {
            return redirect()->route('admin.reports.index', $request->query())->with('error', 'Invalid export format.');
        }

        if ($format === 'pdf' && !class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return redirect()->route('admin.reports.index', $request->query())
                ->with('error', 'PDF export is not configured. Ask to install a PDF library.');
        }

        if ($format === 'csv') {
            return $this->exportCsv($request, $report);
        }

        return $this->exportPdf($request, $report);
    }

    private function exportCsv(Request $request, string $report)
    {
        $filename = "report_{$report}_" . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($request, $report) {
            $handle = fopen('php://output', 'w');

            if ($report === 'payments') {
                fputcsv($handle, ['Receipt', 'Student', 'Email', 'Phone', 'Course', 'Batch', 'Amount', 'Status', 'Date']);
                $this->paymentQuery($request)->orderBy('created_at', 'desc')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $payment) {
                        fputcsv($handle, [
                            $payment->payment_receipt_number,
                            $payment->student?->full_name,
                            $payment->student?->email,
                            $payment->student?->whatsapp_number,
                            $payment->enrollment?->display_course_name,
                            $payment->enrollment?->batch?->batch_name,
                            $payment->amount,
                            $payment->status,
                            optional($payment->created_at)->format('Y-m-d'),
                        ]);
                    }
                });
            } elseif ($report === 'enrollments') {
                fputcsv($handle, ['Enrollment No', 'Student', 'Phone', 'Course', 'Batch', 'Total Fee', 'Paid', 'Pending', 'Status', 'Enrollment Date']);
                $this->enrollmentQuery($request)->orderBy('enrollment_date', 'desc')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $enrollment) {
                        fputcsv($handle, [
                            $enrollment->enrollment_number,
                            $enrollment->student?->full_name,
                            $enrollment->student?->whatsapp_number,
                            $enrollment->display_course_name,
                            $enrollment->batch?->batch_name,
                            $enrollment->total_fee,
                            $enrollment->paid_amount,
                            $enrollment->outstanding_amount,
                            $enrollment->status,
                            optional($enrollment->enrollment_date)->format('Y-m-d'),
                        ]);
                    }
                });
            } elseif ($report === 'students') {
                fputcsv($handle, ['Student', 'Email', 'Phone', 'Aadhar', 'Status', 'Registered']);
                $this->studentQuery($request)->orderBy('created_at', 'desc')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $student) {
                        fputcsv($handle, [
                            $student->full_name,
                            $student->email,
                            $student->whatsapp_number,
                            $student->aadhar_number,
                            $student->status,
                            optional($student->created_at)->format('Y-m-d'),
                        ]);
                    }
                });
            } else {
                fputcsv($handle, ['Student', 'Exam', 'Course', 'Batch', 'Score', 'Result', 'Completed']);
                $this->assessmentResultQuery($request)->orderBy('completed_at', 'desc')->chunk(500, function ($rows) use ($handle) {
                    foreach ($rows as $result) {
                        fputcsv($handle, [
                            $result->student?->name,
                            $result->assessment?->title,
                            $result->enrollment?->display_course_name,
                            $result->enrollment?->batch?->batch_name,
                            $result->percentage,
                            $result->is_passed ? 'Passed' : 'Failed',
                            optional($result->completed_at)->format('Y-m-d'),
                        ]);
                    }
                });
            }

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportPdf(Request $request, string $report)
    {
        $data = [
            'report' => $report,
            'generated_at' => now(),
            'rows' => [],
        ];

        if ($report === 'payments') {
            $data['rows'] = $this->paymentQuery($request)->orderBy('created_at', 'desc')->limit(1000)->get();
        } elseif ($report === 'enrollments') {
            $data['rows'] = $this->enrollmentQuery($request)->orderBy('enrollment_date', 'desc')->limit(1000)->get();
        } elseif ($report === 'students') {
            $data['rows'] = $this->studentQuery($request)->orderBy('created_at', 'desc')->limit(1000)->get();
        } else {
            $data['rows'] = $this->assessmentResultQuery($request)->orderBy('completed_at', 'desc')->limit(1000)->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', $data);
        return $pdf->download("report_{$report}_" . now()->format('Ymd_His') . '.pdf');
    }

    private function pendingBalancesQuery(Request $request): Builder
    {
        $query = $this->scopeEnrollments(
            Enrollment::query()
                ->with(['student', 'batch.course', 'legacyLinkCourse'])
                ->where('status', 'active')
                ->where('outstanding_amount', '>', 0)
        );

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('whatsapp_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('batch', function ($batchQuery) use ($search) {
                        $batchQuery->where('batch_name', 'like', "%{$search}%")
                            ->orWhereHas('course', function ($courseQuery) use ($search) {
                                $courseQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('batch', function ($batchQuery) use ($request) {
                $batchQuery->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $this->applyDateRange($query, 'enrollment_date', $request);

        return $query->orderByDesc('outstanding_amount');
    }

    private function paymentQuery(Request $request): Builder
    {
        $query = $this->scopePayments(Payment::with(['student', 'enrollment.batch.course', 'enrollment.legacyLinkCourse']));

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('enrollment.batch', function ($batchQuery) use ($request) {
                $batchQuery->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('enrollment', function ($enrollmentQuery) use ($request) {
                $enrollmentQuery->where('batch_id', $request->batch_id);
            });
        }

        $this->applyDateRange($query, 'created_at', $request);

        return $query;
    }

    private function enrollmentQuery(Request $request): Builder
    {
        $query = $this->scopeEnrollments(Enrollment::with(['student', 'batch.course', 'legacyLinkCourse']));

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_number', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('whatsapp_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('batch', function ($batchQuery) use ($request) {
                $batchQuery->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $this->applyDateRange($query, 'enrollment_date', $request);

        return $query;
    }

    private function studentQuery(Request $request): Builder
    {
        $query = $this->scopeStudents(Student::query());

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('aadhar_number', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $this->applyDateRange($query, 'created_at', $request);

        return $query;
    }

    private function assessmentResultQuery(Request $request): Builder
    {
        $query = $this->scopeAssessmentResults(AssessmentResult::with(['student', 'assessment', 'enrollment.batch.course', 'enrollment.legacyLinkCourse']));

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('full_name', 'like', "%{$search}%");
                })->orWhereHas('enrollment', function ($enrollmentQuery) use ($search) {
                    $enrollmentQuery->where('enrollment_number', 'like', "%{$search}%");
                })->orWhereHas('assessment', function ($assessmentQuery) use ($search) {
                    $assessmentQuery->where('title', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_passed', $request->status === 'passed');
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        if ($request->filled('course_id')) {
            $query->whereHas('enrollment.batch', function ($batchQuery) use ($request) {
                $batchQuery->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('enrollment', function ($enrollmentQuery) use ($request) {
                $enrollmentQuery->where('batch_id', $request->batch_id);
            });
        }

        $this->applyDateRange($query, 'completed_at', $request);

        return $query;
    }

    private function applyDateRange(Builder $query, string $column, Request $request): void
    {
        if ($request->filled('date_from')) {
            $query->whereDate($column, '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate($column, '<=', $request->date_to);
        }
    }

    private function parsePerPage(Request $request, int $default = 10): int
    {
        $perPage = (int) $request->get('per_page', $default);
        return in_array($perPage, [10, 20, 50, 100], true) ? $perPage : $default;
    }

    private function paymentStats(Builder $query): array
    {
        $stats = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END) as approved_amount")
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        return [
            'total_count' => (int) ($stats->total_count ?? 0),
            'pending_count' => (int) ($stats->pending_count ?? 0),
            'approved_amount' => (float) ($stats->approved_amount ?? 0),
            'total_amount' => (float) ($stats->total_amount ?? 0),
        ];
    }

    private function enrollmentStats(Builder $query): array
    {
        $stats = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count")
            ->selectRaw("SUM(CASE WHEN status = 'dropped' THEN 1 ELSE 0 END) as dropped_count")
            ->selectRaw('SUM(total_fee) as total_fees')
            ->selectRaw('SUM(outstanding_amount) as total_outstanding')
            ->first();

        return [
            'total_count' => (int) ($stats->total_count ?? 0),
            'active_count' => (int) ($stats->active_count ?? 0),
            'dropped_count' => (int) ($stats->dropped_count ?? 0),
            'total_fees' => (float) ($stats->total_fees ?? 0),
            'total_outstanding' => (float) ($stats->total_outstanding ?? 0),
        ];
    }

    private function studentStats(Builder $query): array
    {
        $stats = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count")
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count")
            ->first();

        return [
            'total_count' => (int) ($stats->total_count ?? 0),
            'approved_count' => (int) ($stats->approved_count ?? 0),
            'pending_count' => (int) ($stats->pending_count ?? 0),
            'rejected_count' => (int) ($stats->rejected_count ?? 0),
        ];
    }

    private function assessmentStats(Builder $query): array
    {
        $stats = (clone $query)
            ->selectRaw('COUNT(*) as total_results')
            ->selectRaw("SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as passed_results")
            ->selectRaw("SUM(CASE WHEN is_passed = 0 THEN 1 ELSE 0 END) as failed_results")
            ->selectRaw('AVG(percentage) as average_score')
            ->first();

        return [
            'total_results' => (int) ($stats->total_results ?? 0),
            'passed_results' => (int) ($stats->passed_results ?? 0),
            'failed_results' => (int) ($stats->failed_results ?? 0),
            'average_score' => (float) ($stats->average_score ?? 0),
        ];
    }
}
