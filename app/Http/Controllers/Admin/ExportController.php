<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AssessmentResult;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    public function exportStudents(Request $request)
    {
        $query = Student::with(['user', 'enrollments.batch.course']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('course_id')) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->whereHas('batch', function ($batchQuery) use ($request) {
                    $batchQuery->where('course_id', $request->course_id);
                });
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $students = $query->get();

        $filename = 'students_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Aadhar Number', 'Status', 
                'Enrollment Number', 'Course', 'Batch', 'Enrollment Date', 'Created At'
            ]);

            foreach ($students as $student) {
                foreach ($student->enrollments as $enrollment) {
                    fputcsv($file, [
                        $student->id,
                        $student->user->name,
                        $student->user->email,
                        $student->phone,
                        $student->aadhar_number,
                        $student->status,
                        $enrollment->enrollment_number,
                        $enrollment->batch->course->name,
                        $enrollment->batch->batch_name,
                        $enrollment->enrollment_date->format('Y-m-d'),
                        $student->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPayments(Request $request)
    {
        $query = Payment::with(['student.user', 'enrollment.batch.course']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->get();

        $filename = 'payments_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Student Name', 'Email', 'Amount', 'Payment Type', 'Payment Method', 
                'Status', 'Payment Number', 'Course', 'Batch', 'Created At'
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->student->user->name,
                    $payment->student->user->email,
                    $payment->amount,
                    $payment->payment_type,
                    $payment->payment_method,
                    $payment->status,
                    $payment->payment_number,
                    $payment->enrollment->batch->course->name,
                    $payment->enrollment->batch->batch_name,
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAssessmentResults(Request $request)
    {
        $query = AssessmentResult::with(['student.user', 'assessment', 'enrollment.batch.course']);

        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->whereHas('batch', function ($batchQuery) use ($request) {
                    $batchQuery->where('course_id', $request->course_id);
                });
            });
        }
        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }
        if ($request->filled('is_passed')) {
            $query->where('is_passed', $request->is_passed);
        }

        $results = $query->get();

        $filename = 'assessment_results_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Student Name', 'Email', 'Assessment', 'Course', 'Batch',
                'Total Questions', 'Correct Answers', 'Wrong Answers', 'Percentage', 
                'Grade', 'Status', 'Completed At'
            ]);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->id,
                    $result->student->user->name,
                    $result->student->user->email,
                    $result->assessment->title,
                    $result->enrollment->batch->course->name,
                    $result->enrollment->batch->batch_name,
                    $result->total_questions,
                    $result->correct_answers,
                    $result->wrong_answers,
                    $result->percentage,
                    $result->grade,
                    $result->is_passed ? 'Passed' : 'Failed',
                    $result->completed_at ? $result->completed_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCertificates(Request $request)
    {
        $query = Certificate::with(['student.user', 'course', 'batch']);

        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('is_issued')) {
            $query->where('is_issued', $request->is_issued);
        }

        $certificates = $query->get();

        $filename = 'certificates_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($certificates) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Student Name', 'Email', 'Course', 'Batch', 'Certificate Number',
                'Issue Date', 'Status', 'Created At'
            ]);

            foreach ($certificates as $certificate) {
                fputcsv($file, [
                    $certificate->id,
                    $certificate->student->user->name,
                    $certificate->student->user->email,
                    $certificate->course->name,
                    $certificate->batch ? $certificate->batch->batch_name : 'N/A',
                    $certificate->certificate_number,
                    $certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : 'N/A',
                    $certificate->is_issued ? 'Issued' : 'Pending',
                    $certificate->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportFinancialReport(Request $request)
    {
        $query = Payment::with(['student.user', 'enrollment.batch.course']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->get();

        $filename = 'financial_report_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Date', 'Student Name', 'Course', 'Payment Type', 'Amount', 'Status', 'Payment Method'
            ]);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->created_at->format('Y-m-d'),
                    $payment->student->user->name,
                    $payment->enrollment->batch->course->name,
                    $payment->payment_type,
                    $payment->amount,
                    $payment->status,
                    $payment->payment_method
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCourseAnalytics(Request $request)
    {
        $courses = Course::with(['batches.enrollments', 'assessments.results'])->get();

        $filename = 'course_analytics_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($courses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Course ID', 'Course Name', 'Duration', 'Total Batches', 'Total Enrollments',
                'Active Enrollments', 'Completed Enrollments', 'Total Assessments', 
                'Average Score', 'Pass Rate'
            ]);

            foreach ($courses as $course) {
                $totalEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->count();
                });
                
                $activeEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->where('status', 'active')->count();
                });
                
                $completedEnrollments = $course->batches->sum(function ($batch) {
                    return $batch->enrollments->where('status', 'completed')->count();
                });

                $totalAssessments = $course->assessments->count();
                $averageScore = $course->assessments->avg(function ($assessment) {
                    return $assessment->results->avg('percentage');
                });
                
                $passRate = $course->assessments->avg(function ($assessment) {
                    $totalResults = $assessment->results->count();
                    if ($totalResults === 0) return 0;
                    $passedResults = $assessment->results->where('is_passed', true)->count();
                    return ($passedResults / $totalResults) * 100;
                });

                fputcsv($file, [
                    $course->id,
                    $course->name,
                    $course->duration,
                    $course->batches->count(),
                    $totalEnrollments,
                    $activeEnrollments,
                    $completedEnrollments,
                    $totalAssessments,
                    round($averageScore, 2),
                    round($passRate, 2)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
