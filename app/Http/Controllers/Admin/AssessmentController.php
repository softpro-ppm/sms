<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Student;
use App\Models\AssessmentResult;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 15;
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));
        $courseId = $request->get('course_id');

        $query = Assessment::with(['course']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== '') {
            $query->where('is_active', $status === '1');
        }

        if (!empty($courseId)) {
            $query->where('course_id', $courseId);
        }

        $assessments = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $stats = [
            'total_assessments' => Assessment::count(),
            'active_assessments' => Assessment::where('is_active', true)->count(),
            'inactive_assessments' => Assessment::where('is_active', false)->count(),
            'total_students_assessed' => AssessmentResult::distinct('student_id')->count(),
        ];

        return view('admin.assessments.index', compact('assessments', 'stats'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.assessments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1|max:300',
            'total_questions' => 'required|integer|min:1|max:100',
            'passing_percentage' => 'required|numeric|min:1|max:100',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessment = Assessment::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'time_limit_minutes' => $request->time_limit_minutes,
            'total_questions' => $request->total_questions,
            'passing_percentage' => $request->passing_percentage,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('admin.assessments.index')
            ->with('success', 'Assessment created successfully!');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load(['course', 'assessmentResults.student']);
        
        $questionBankCount = QuestionBank::where('course_id', $assessment->course_id)->where('is_active', true)->count();
        $subjectCount = QuestionBank::where('course_id', $assessment->course_id)->where('is_active', true)->distinct('subject')->count('subject');
        
        $stats = [
            'total_questions_in_bank' => $questionBankCount,
            'total_subjects' => $subjectCount,
            'students_attempted' => $assessment->assessmentResults()->distinct('student_id')->count(),
            'students_passed' => $assessment->assessmentResults()->where('is_passed', true)->count(),
            'students_failed' => $assessment->assessmentResults()->where('is_passed', false)->count(),
        ];

        return view('admin.assessments.show', compact('assessment', 'stats'));
    }

    public function edit(Assessment $assessment)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.assessments.edit', compact('assessment', 'courses'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'time_limit_minutes' => 'required|integer|min:1|max:300',
            'total_questions' => 'required|integer|min:1|max:100',
            'passing_percentage' => 'required|numeric|min:1|max:100',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessment->update([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'time_limit_minutes' => $request->time_limit_minutes,
            'total_questions' => $request->total_questions,
            'passing_percentage' => $request->passing_percentage,
            'is_active' => (bool) $request->is_active,
        ]);

        return redirect()->route('admin.assessments.index')
            ->with('success', 'Assessment updated successfully!');
    }

    public function destroy(Assessment $assessment)
    {
        // Check if assessment has results
        if ($assessment->results()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete assessment with existing results. Please handle results first.');
        }

        $assessment->delete();

        return redirect()->route('admin.assessments.index')
            ->with('success', 'Assessment deleted successfully!');
    }

    public function toggleStatus(Assessment $assessment)
    {
        $newStatus = !$assessment->is_active;
        $assessment->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Assessment {$statusText} successfully!");
    }

    public function getBatchesByCourse(Request $request)
    {
        $courseId = $request->get('course_id');
        
        if (!$courseId) {
            return response()->json([]);
        }

        $batches = Batch::where('course_id', $courseId)
            ->where('is_active', true)
            ->where(function ($query) {
                $today = Carbon::today();
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $today);
            })
            ->orderBy('batch_name')
            ->get(['id', 'batch_name', 'start_date', 'end_date']);

        return response()->json($batches);
    }
}
