<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
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
    use ScopesByTrainingPartner;

    protected function ensureBatchAccessible(Batch $batch): void
    {
        $tpId = $this->getTrainingPartnerId();
        if ($tpId === null) {
            return;
        }
        $ok = Batch::query()->whereKey($batch->id)->visibleToTrainingPartner($tpId)->exists();
        if (!$ok) {
            abort(404);
        }
    }

    /**
     * Batch names are unique per (course, owner): platform rows use null training_partner_id.
     * On update, pass the row's owner id (not the editor's) so super admins validate correctly.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function batchesForNameUniquenessScope(int $courseId, ?int $excludeBatchId, ?int $ownerTrainingPartnerId)
    {
        $q = Batch::where('course_id', $courseId);
        if ($ownerTrainingPartnerId === null) {
            $q->whereNull('training_partner_id');
        } else {
            $q->where('training_partner_id', $ownerTrainingPartnerId);
        }
        if ($excludeBatchId !== null) {
            $q->where('id', '!=', $excludeBatchId);
        }

        return $q;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : 10;
        $search = trim((string) $request->get('search', ''));

        $tpId = $this->getTrainingPartnerId();
        $enrollmentCountFilter = $tpId !== null
            ? fn ($q) => $q->where('status', 'active')->whereHas('student', fn ($sq) => $sq->where('training_partner_id', $tpId))
            : fn ($q) => $q->where('status', 'active');

        $query = Batch::query()
            ->visibleToTrainingPartner($tpId)
            ->with(['course'])
            ->withCount(['enrollments' => $enrollmentCountFilter]);

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

        $statsBase = fn () => Batch::query()->visibleToTrainingPartner($tpId);
        $stats = [
            'total_batches' => $statsBase()->count(),
            'active_batches' => $statsBase()->where('is_active', true)->count(),
            'running_batches' => $statsBase()
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'total_students' => $this->scopeEnrollments(Enrollment::where('status', 'active'))->count(),
        ];

        return view('admin.batches.index', compact('batches', 'stats'));
    }

    public function create()
    {
        $tpId = $this->getTrainingPartnerId();
        $courses = Course::query()
            ->where('is_active', true)
            ->visibleToTrainingPartner($tpId)
            ->orderBy('name')
            ->get();
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

        $course = Course::query()->where('is_active', true)->whereKey((int) $request->course_id)->first();
        if (! $course) {
            return redirect()->back()
                ->withErrors(['course_id' => 'Invalid or inactive course.'])
                ->withInput();
        }

        $existingBatch = $this->batchesForNameUniquenessScope(
            (int) $request->course_id,
            null,
            $this->getTrainingPartnerId()
        )
            ->where('batch_name', $request->batch_name)
            ->first();

        if ($existingBatch) {
            // Get course name for better error message
            $course = Course::find($request->course_id);
            
            // Suggest alternative batch names
            $existingBatches = $this->batchesForNameUniquenessScope(
                (int) $request->course_id,
                null,
                $this->getTrainingPartnerId()
            )
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
            'training_partner_id' => $this->getTrainingPartnerId(),
            'batch_name' => $request->batch_name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_students' => $request->max_students ?: 20,
            'course_fee' => $request->course_fee,
            'registration_fee' => $request->registration_fee,
            'assessment_fee' => $request->assessment_fee,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully!');
    }

    public function show(Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
        $batch->load(['course', 'enrollments.student']);
        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null) {
            $batch->setRelation('enrollments', $batch->enrollments->filter(fn ($e) => (int) ($e->student->training_partner_id ?? 0) === $tpId));
        }

        return view('admin.batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
        $batch->load('course');
        $tpId = $this->getTrainingPartnerId();
        $courses = Course::query()
            ->where('is_active', true)
            ->visibleToTrainingPartner($tpId)
            ->orderBy('name')
            ->get();
        return response()
            ->view('admin.batches.edit', compact('batch', 'courses'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function update(Request $request, Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'batch_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
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

        $course = Course::query()->where('is_active', true)->whereKey((int) $request->course_id)->first();
        if (! $course) {
            return redirect()->back()
                ->withErrors(['course_id' => 'Invalid or inactive course.'])
                ->withInput();
        }

        $batchOwnerTpId = $batch->training_partner_id !== null
            ? (int) $batch->training_partner_id
            : null;
        $existingBatch = $this->batchesForNameUniquenessScope((int) $request->course_id, $batch->id, $batchOwnerTpId)
            ->where('batch_name', $request->batch_name)
            ->first();

        if ($existingBatch) {
            // Get course name for better error message
            $course = Course::find($request->course_id);
            
            // Suggest alternative batch names
            $existingBatches = $this->batchesForNameUniquenessScope((int) $request->course_id, $batch->id, $batchOwnerTpId)
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
            'course_fee' => $request->course_fee,
            'registration_fee' => $request->registration_fee,
            'assessment_fee' => $request->assessment_fee,
            'duration_days' => $request->duration_days,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch updated successfully!');
    }

    public function destroy(Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
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
        $this->ensureBatchAccessible($batch);
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
        $this->ensureBatchAccessible($batch);
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

        // Eligible: approved students with ZERO enrollments (TP-scoped)
        $query = $this->scopeStudents(
            Student::where('status', 'approved')->whereDoesntHave('enrollments')
        );

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
        $registrationFee = $batch->resolved_registration_fee;
        $assessmentFee = $batch->resolved_assessment_fee;
        $courseFee = $batch->resolved_course_fee;
        $totalFee = $batch->resolved_total_fee;

        return view('admin.batches.enroll', compact('batch', 'students', 'course', 'registrationFee', 'assessmentFee', 'courseFee', 'totalFee'));
    }

    /**
     * Store enrollments for selected students in this batch.
     */
    public function storeEnrollments(Request $request, Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
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
        $registrationFee = $batch->resolved_registration_fee;
        $assessmentFee = $batch->resolved_assessment_fee;
        $courseFee = $batch->resolved_course_fee;
        $totalFees = round($batch->resolved_total_fee, 2);

        $studentIds = array_unique($request->student_ids);
        $enrolled = 0;
        $errors = [];

        $tpId = $this->getTrainingPartnerId();
        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (!$student) {
                continue;
            }
            if ($tpId !== null && (int) $student->training_partner_id !== $tpId) {
                continue; // Skip students from other TPs
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

        $tpId = $this->getTrainingPartnerId();
        $batches = Batch::query()
            ->visibleToTrainingPartner($tpId)
            ->where('course_id', $courseId)
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

    /**
     * Resolved fee/duration for this batch (API for enrollment forms).
     */
    public function feeDetails(Batch $batch)
    {
        $this->ensureBatchAccessible($batch);
        $batch->load('course');

        return response()->json([
            'success' => true,
            'data' => [
                'course_fee' => $batch->resolved_course_fee,
                'registration_fee' => $batch->resolved_registration_fee,
                'assessment_fee' => $batch->resolved_assessment_fee,
                'total_fee' => $batch->resolved_total_fee,
                'duration_days' => $batch->resolved_duration_days,
            ],
        ]);
    }
}
