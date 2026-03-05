<?php

namespace App\Http\Controllers\Admin;

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
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'payments');
        $perPage = $this->parsePerPage($request, 10);

        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $batches = Batch::where('is_active', true)->orderBy('batch_name')->get();
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
            $viewData['stats'] = [
                'total_count' => (clone $query)->count(),
                'active_count' => (clone $query)->where('status', 'active')->count(),
                'dropped_count' => (clone $query)->where('status', 'dropped')->count(),
                'total_fees' => (clone $query)->sum('total_fee'),
                'total_outstanding' => (clone $query)->sum('outstanding_amount'),
            ];
            $viewData['enrollments'] = $query
                ->orderBy('enrollment_date', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } elseif ($tab === 'students') {
            $query = $this->studentQuery($request);
            $viewData['stats'] = [
                'total_count' => (clone $query)->count(),
                'approved_count' => (clone $query)->where('status', 'approved')->count(),
                'pending_count' => (clone $query)->where('status', 'pending')->count(),
                'rejected_count' => (clone $query)->where('status', 'rejected')->count(),
            ];
            $viewData['students'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } elseif ($tab === 'assessments') {
            $query = $this->assessmentResultQuery($request);
            $viewData['stats'] = [
                'total_results' => (clone $query)->count(),
                'passed_results' => (clone $query)->where('is_passed', true)->count(),
                'failed_results' => (clone $query)->where('is_passed', false)->count(),
                'average_score' => (clone $query)->avg('percentage') ?? 0,
            ];
            $viewData['results'] = $query
                ->orderBy('completed_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            $query = $this->paymentQuery($request);
            $viewData['stats'] = [
                'total_count' => (clone $query)->count(),
                'pending_count' => (clone $query)->where('status', 'pending')->count(),
                'approved_amount' => (clone $query)->where('status', 'approved')->sum('amount'),
                'total_amount' => (clone $query)->sum('amount'),
            ];
            $viewData['payments'] = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        }

        return view('admin.reports.index', $viewData);
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
                            $payment->enrollment?->batch?->course?->name,
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
                            $enrollment->batch?->course?->name,
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
                            $result->enrollment?->batch?->course?->name,
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

    private function paymentQuery(Request $request): Builder
    {
        $query = Payment::with(['student', 'enrollment.batch.course']);

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
        $query = Enrollment::with(['student', 'batch.course']);

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
        $query = Student::query();

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
        $query = AssessmentResult::with(['student', 'assessment', 'enrollment.batch.course']);

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('enrollment_number', 'like', "%{$search}%");
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
}
