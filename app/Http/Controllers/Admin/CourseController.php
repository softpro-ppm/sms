<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    use ScopesByTrainingPartner;

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));

        $tpId = $this->getTrainingPartnerId();
        $enrollmentFilter = $tpId !== null
            ? fn ($q) => $q->where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))
            : fn ($q) => $q->where('status', 'active');

        $query = Course::query();
        if ($tpId !== null) {
            $query->visibleToTrainingPartner($tpId);
        }

        if ($tpId !== null) {
            $query->withCount([
                'batches as batches_count' => fn ($q) => $q->visibleToTrainingPartner($tpId),
                'enrollments as enrollments_count' => $enrollmentFilter,
            ]);
        } else {
            $query->withCount([
                'batches',
                'enrollments' => $enrollmentFilter,
            ]);
        }

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
            'total_courses' => $tpId !== null
                ? Course::query()->visibleToTrainingPartner($tpId)->count()
                : Course::count(),
            'active_courses' => $tpId !== null
                ? Course::query()->visibleToTrainingPartner($tpId)->where('is_active', true)->count()
                : Course::where('is_active', true)->count(),
            'total_batches' => $tpId !== null
                ? Batch::query()->visibleToTrainingPartner($tpId)->count()
                : Batch::count(),
            'total_enrollments' => $tpId !== null
                ? Enrollment::where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))->count()
                : Enrollment::where('status', 'active')->count(),
        ];

        return view('admin.courses.index', compact('courses', 'stats'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $tpId = $this->getTrainingPartnerId();
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name')->where(function ($q) use ($tpId) {
                    if ($tpId === null) {
                        return $q->whereNull('training_partner_id');
                    }

                    return $q->where('training_partner_id', $tpId);
                }),
            ],
            'description' => 'nullable|string',
            'course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'assessment_fee' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course = Course::create([
            'training_partner_id' => $tpId,
            'name' => $request->name,
            'description' => $request->description,
            'course_fee' => $request->course_fee,
            'registration_fee' => $request->registration_fee,
            'assessment_fee' => $request->assessment_fee,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $this->ensureCourseAccessible($course);
        $course->load(['batches', 'assessments', 'enrollments.student']);

        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->ensureCourseAccessible($course);
        $this->ensureTrainingPartnerOwnsCourse($course);

        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $this->ensureCourseAccessible($course);
        $this->ensureTrainingPartnerOwnsCourse($course);
        $ownerTpId = $course->training_partner_id;
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name')->ignore($course->id)->where(function ($q) use ($ownerTpId) {
                    if ($ownerTpId === null) {
                        return $q->whereNull('training_partner_id');
                    }

                    return $q->where('training_partner_id', $ownerTpId);
                }),
            ],
            'description' => 'nullable|string',
            'course_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
            'assessment_fee' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
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
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $this->ensureCourseAccessible($course);
        $this->ensureTrainingPartnerOwnsCourse($course);
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
        $this->ensureCourseAccessible($course);
        $this->ensureTrainingPartnerOwnsCourse($course);
        $course->update(['is_active' => !$course->is_active]);

        $status = $course->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Course {$status} successfully!");
    }
}
