<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));

        $query = Course::withCount(['batches', 'enrollments']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        $stats = [
            'total_courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'total_batches' => Batch::count(),
            'total_enrollments' => Enrollment::where('status', 'active')->count(),
        ];

        return view('admin.courses.index', compact('courses', 'stats'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:courses,name',
            'description' => 'nullable|string',
            'course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'assessment_fee' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course = Course::create([
            'name' => $request->name,
            'description' => $request->description,
            'course_fee' => $request->course_fee,
            'registration_fee' => $request->registration_fee,
            'assessment_fee' => $request->assessment_fee,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $course->load(['batches', 'assessments', 'enrollments.student']);
        
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:courses,name,' . $course->id,
            'description' => 'nullable|string',
            'course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'assessment_fee' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course->update([
            'name' => $request->name,
            'description' => $request->description,
            'course_fee' => $request->course_fee,
            'registration_fee' => $request->registration_fee,
            'assessment_fee' => $request->assessment_fee,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        // Check if course has any batches or enrollments
        if ($course->batches()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete course with existing batches. Please delete batches first.');
        }

        if ($course->enrollments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete course with existing enrollments. Please handle enrollments first.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully!');
    }

    public function toggleStatus(Course $course)
    {
        $course->update(['is_active' => !$course->is_active]);
        
        $status = $course->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Course {$status} successfully!");
    }
}
