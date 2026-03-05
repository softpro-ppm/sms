<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentResult;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 20;

        $query = AssessmentResult::with(['student', 'assessment', 'enrollment.batch.course']);

        // Filter by course (enrollment -> batch -> course_id)
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment.batch', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        // Filter by assessment
        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        // Filter by student
        if ($request->filled('student_search')) {
            $search = $request->student_search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', fn($sq) => $sq->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('enrollment', fn($eq) => $eq->where('enrollment_number', 'like', "%{$search}%"));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'passed') {
                $query->where('is_passed', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_passed', false);
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        $results = $query->orderBy('completed_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());
        
        // Get filter options
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        $assessments = Assessment::where('is_active', true)->orderBy('title')->get();

        // Get statistics
        $stats = [
            'total_results' => AssessmentResult::count(),
            'passed_results' => AssessmentResult::where('is_passed', true)->count(),
            'failed_results' => AssessmentResult::where('is_passed', false)->count(),
            'average_percentage' => AssessmentResult::avg('percentage') ?? 0,
            'total_students' => AssessmentResult::distinct('student_id')->count(),
        ];

        return view('admin.results.index', compact('results', 'courses', 'assessments', 'stats'));
    }

    public function show(AssessmentResult $result)
    {
        $result->load(['student', 'assessment', 'enrollment.batch.course', 'attempt.attemptQuestions.question']);
        
        return view('admin.results.show', compact('result'));
    }

    public function export(Request $request)
    {
        $query = AssessmentResult::with(['student', 'assessment', 'enrollment.batch.course']);

        // Apply same filters as index
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment.batch', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'passed') {
                $query->where('is_passed', true);
            } elseif ($request->status === 'failed') {
                $query->where('is_passed', false);
            }
        }

        $results = $query->orderBy('completed_at', 'desc')->get();

        $filename = 'assessment_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Student Name',
                'Enrollment Number',
                'Course',
                'Assessment',
                'Total Questions',
                'Correct Answers',
                'Wrong Answers',
                'Total Marks',
                'Percentage',
                'Grade',
                'Status',
                'Completed Date',
                'Time Taken (Minutes)'
            ]);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->student->full_name ?? '',
                    $result->enrollment->enrollment_number ?? '',
                    $result->enrollment->course->name,
                    $result->assessment->title,
                    $result->total_questions,
                    $result->correct_answers,
                    $result->wrong_answers,
                    $result->total_marks,
                    $result->percentage,
                    $result->grade,
                    $result->passing_status,
                    $result->completed_at ? $result->completed_at->format('Y-m-d H:i:s') : '',
                    $result->time_taken_minutes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getStats(Request $request)
    {
        $query = AssessmentResult::query();

        if ($request->filled('course_id')) {
            $query->whereHas('enrollment.batch', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('assessment_id')) {
            $query->where('assessment_id', $request->assessment_id);
        }

        $stats = [
            'total_results' => $query->count(),
            'passed_results' => $query->clone()->where('is_passed', true)->count(),
            'failed_results' => $query->clone()->where('is_passed', false)->count(),
            'average_percentage' => round($query->clone()->avg('percentage') ?? 0, 2),
        ];

        return response()->json($stats);
    }
}
