<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Mail\EnrollmentConfirmationMail;
use App\Services\EnrollmentNumberService;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));

        $query = Batch::with(['course'])
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('batch_name', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');
        $batches = $query
            ->orderByRaw("
                CASE
                    WHEN DATE(start_date) > ? THEN 0
                    WHEN DATE(start_date) <= ? AND DATE(end_date) >= ? THEN 1
                    WHEN DATE(end_date) < ? THEN 2
                    ELSE 3
                END
            ", [$todayStr, $todayStr, $todayStr, $todayStr])
            ->orderByDesc('start_date')
            ->paginate($perPage)
            ->appends($request->query());
        $stats = [
            'total_batches' => Batch::count(),
            'active_batches' => Batch::where('is_active', true)->count(),
            'running_batches' => Batch::whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'total_students' => Enrollment::where('status', 'active')->count(),
        ];

        return view('admin.batches.index', compact('batches', 'stats'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        return response()
            ->view('admin.batches.create', compact('courses'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if batch name already exists for this course
        $existingBatch = Batch::where('course_id', $request->course_id)
            ->where('batch_name', $request->batch_name)
            ->first();

        if ($existingBatch) {
            // Get course name for better error message
            $course = Course::find($request->course_id);
            
            // Suggest alternative batch names
            $existingBatches = Batch::where('course_id', $request->course_id)
                ->pluck('batch_name')
                ->toArray();
            
            $suggestions = [];
            $baseName = $request->batch_name;
            $counter = 1;
            
            while (count($suggestions) < 3) {
                $suggestion = $baseName . '-' . $counter;
                if (!in_array($suggestion, $existingBatches)) {
                    $suggestions[] = $suggestion;
                }
                $counter++;
            }
            
            $errorMessage = "Batch name '{$request->batch_name}' already exists for {$course->name} course. ";
            if (!empty($suggestions)) {
                $errorMessage .= "Suggested alternatives: " . implode(', ', $suggestions);
            }
            
            return redirect()->back()
                ->withErrors(['batch_name' => $errorMessage])
                ->withInput();
        }

        $batch = Batch::create([
            'course_id' => $request->course_id,
            'batch_name' => $request->batch_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_students' => $request->max_students ?: 20,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully!');
    }

    public function show(Batch $batch)
    {
        $batch->load(['course', 'enrollments.student']);
        
        return view('admin.batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $courses = Course::where('is_active', true)->orderBy('name')->get();
        return response()
            ->view('admin.batches.edit', compact('batch', 'courses'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function update(Request $request, Batch $batch)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if batch name already exists for this course (excluding current batch)
        $existingBatch = Batch::where('course_id', $request->course_id)
            ->where('batch_name', $request->batch_name)
            ->where('id', '!=', $batch->id)
            ->first();

        if ($existingBatch) {
            // Get course name for better error message
            $course = Course::find($request->course_id);
            
            // Suggest alternative batch names
            $existingBatches = Batch::where('course_id', $request->course_id)
                ->where('id', '!=', $batch->id)
                ->pluck('batch_name')
                ->toArray();
            
            $suggestions = [];
            $baseName = $request->batch_name;
            $counter = 1;
            
            while (count($suggestions) < 3) {
                $suggestion = $baseName . '-' . $counter;
                if (!in_array($suggestion, $existingBatches)) {
                    $suggestions[] = $suggestion;
                }
                $counter++;
            }
            
            $errorMessage = "Batch name '{$request->batch_name}' already exists for {$course->name} course. ";
            if (!empty($suggestions)) {
                $errorMessage .= "Suggested alternatives: " . implode(', ', $suggestions);
            }
            
            return redirect()->back()
                ->withErrors(['batch_name' => $errorMessage])
                ->withInput();
        }

        $batch->update([
            'course_id' => $request->course_id,
            'batch_name' => $request->batch_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_students' => $request->max_students,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch updated successfully!');
    }

    public function destroy(Batch $batch)
    {
        // Check if batch has any enrollments
        if ($batch->enrollments()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete batch with existing enrollments. Please handle enrollments first.');
        }

        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully!');
    }

    public function toggleStatus(Batch $batch)
    {
        $batch->update(['is_active' => !$batch->is_active]);
        
        $status = $batch->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Batch {$status} successfully!");
    }

    /**
     * Show page to enroll students in this batch.
     * Only shows approved students who have NO enrollments in any batch.
     */
    public function enrollStudents(Request $request, Batch $batch)
    {
        $batch->load('course');

        // Check batch capacity
        $activeCount = $batch->enrollments()->where('status', 'active')->count();
        if ($batch->max_students && $activeCount >= $batch->max_students) {
            return redirect()->route('admin.batches.show', $batch)
                ->with('error', 'Batch is full. Cannot enroll more students.');
        }

        $search = trim((string) $request->get('search', ''));
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;

        // Eligible: approved students with ZERO enrollments (not enrolled in any batch)
        $query = Student::where('status', 'approved')
            ->whereDoesntHave('enrollments');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('aadhar_number', 'like', '%' . $search . '%')
                    ->orWhere('whatsapp_number', 'like', '%' . $search . '%');
            });
        }

        $students = $query->orderBy('full_name')
            ->paginate($perPage)
            ->appends($request->query());

        $course = $batch->course;
        $registrationFee = (float) ($course->registration_fee ?? 100);
        $assessmentFee = (float) ($course->assessment_fee ?? 100);
        $courseFee = (float) ($course->course_fee ?? 0);
        $totalFee = $registrationFee + $courseFee + $assessmentFee;

        return view('admin.batches.enroll', compact('batch', 'students', 'course', 'registrationFee', 'assessmentFee', 'courseFee', 'totalFee'));
    }

    /**
     * Store enrollments for selected students in this batch.
     */
    public function storeEnrollments(Request $request, Batch $batch)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|exists:students,id',
            'enrollment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $batch->load('course');
        $course = $batch->course;
        $registrationFee = (float) ($course->registration_fee ?? 100);
        $assessmentFee = (float) ($course->assessment_fee ?? 100);
        $courseFee = (float) ($course->course_fee ?? 0);
        $totalFees = $registrationFee + $courseFee + $assessmentFee;

        $studentIds = array_unique($request->student_ids);
        $enrolled = 0;
        $errors = [];

        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (!$student) {
                continue;
            }

            // Re-check: must be approved and have no enrollments
            if ($student->status !== 'approved') {
                $errors[] = "{$student->full_name}: Not approved.";
                continue;
            }
            if ($student->enrollments()->exists()) {
                $errors[] = "{$student->full_name}: Already enrolled in a batch.";
                continue;
            }

            // Check batch capacity
            $activeCount = $batch->enrollments()->where('status', 'active')->count();
            if ($batch->max_students && $activeCount >= $batch->max_students) {
                $errors[] = "Batch is full. Stopped after enrolling {$enrolled} student(s).";
                break;
            }

            $creditToApply = min(
                (float) ($student->credit_balance ?? 0),
                $totalFees
            );

            $enrollmentNumber = EnrollmentNumberService::generateEnrollmentNumber();

            $enrollment = Enrollment::create([
                'enrollment_number' => $enrollmentNumber,
                'student_id' => $student->id,
                'batch_id' => $batch->id,
                'enrollment_date' => $request->enrollment_date,
                'status' => 'active',
                'total_fee' => $totalFees,
                'paid_amount' => 0,
                'outstanding_amount' => $totalFees,
                'is_eligible_for_assessment' => false,
                'registration_fee' => $registrationFee,
                'course_fee' => $courseFee,
                'assessment_fee' => $assessmentFee,
            ]);

            if ($creditToApply > 0) {
                try {
                    $creditService = new \App\Services\StudentCreditService();
                    $creditService->applyCreditToEnrollment($enrollment, $creditToApply);
                } catch (\Exception $e) {
                    \Log::error('Credit apply failed: ' . $e->getMessage());
                }
            }

            try {
                $enrollment->load(['batch.course', 'student']);
                Mail::to($student->email)->send(new EnrollmentConfirmationMail($enrollment));
            } catch (\Exception $e) {
                \Log::error('Enrollment confirmation email failed: ' . $e->getMessage());
            }
            try {
                app(WhatsAppNotificationService::class)->sendEnrollmentConfirmation($enrollment);
            } catch (\Exception $e) {
                \Log::error('Enrollment WhatsApp failed: ' . $e->getMessage());
            }

            $enrolled++;
        }

        if (!empty($errors)) {
            return redirect()->route('admin.batches.show', $batch)
                ->with('warning', $enrolled > 0
                    ? "Enrolled {$enrolled} student(s). " . implode(' ', $errors)
                    : implode(' ', $errors));
        }

        return redirect()->route('admin.batches.show', $batch)
            ->with('success', "Successfully enrolled {$enrolled} student(s) in {$batch->batch_name}!");
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
