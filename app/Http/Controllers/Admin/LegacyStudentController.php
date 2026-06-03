<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\TrainingPartner;
use App\Models\User;
use App\Services\EnrollmentNumberService;
use App\Services\LegacyAutoCertificationService;
use App\Services\LegacyEnrollmentService;
use App\Services\PaymentAllocationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LegacyStudentController extends Controller
{
    use ScopesByTrainingPartner;

    private const IMPORT_HEADERS = [
        'full_name',
        'email',
        'whatsapp_number',
        'aadhar_number',
        'gender',
        'qualification',
        'father_name',
        'legacy_course_name',
        'legacy_start_date',
        'legacy_end_date',
        'enrollment_date',
        'registration_fee',
        'course_fee',
        'assessment_fee',
        'legacy_link_course_name',
        'status',
    ];

    public function index(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        [$query, $search, $status] = $this->filteredQuery($request);

        $legacyEnrollments = $query
            ->latest('enrollment_date')
            ->paginate(20)
            ->appends($request->query());

        $statsQuery = $this->scopeEnrollments(Enrollment::query()->where('is_legacy', true));
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'active' => (clone $statsQuery)->where('status', 'active')->count(),
            'paid' => (clone $statsQuery)->where('outstanding_amount', '<=', 0)->count(),
            'outstanding' => (float) (clone $statsQuery)->sum('outstanding_amount'),
        ];

        $legacyConfigured = LegacyEnrollmentService::isConfigured();

        return view('admin.legacy-students.index', compact(
            'legacyEnrollments',
            'stats',
            'search',
            'status',
            'legacyConfigured'
        ));
    }

    public function exportCsv(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        [$query] = $this->filteredQuery($request);
        $filename = 'legacy_students_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Enrollment No',
                'Student',
                'Email',
                'WhatsApp',
                'Course',
                'Linked LMS Course',
                'Start Date',
                'End Date',
                'Total Fee',
                'Paid Amount',
                'Outstanding Amount',
                'Status',
                'Enrollment Date',
            ]);

            $query->latest('enrollment_date')->chunk(200, function ($enrollments) use ($handle) {
                foreach ($enrollments as $enrollment) {
                    fputcsv($handle, [
                        $enrollment->enrollment_number,
                        $enrollment->student?->full_name,
                        $enrollment->student?->email,
                        $enrollment->student?->whatsapp_number,
                        $enrollment->display_course_name,
                        $enrollment->legacyLinkCourse?->name,
                        $enrollment->effective_start_date?->format('Y-m-d'),
                        $enrollment->effective_end_date?->format('Y-m-d'),
                        $enrollment->total_fee,
                        $enrollment->paid_amount,
                        $enrollment->outstanding_amount,
                        $enrollment->status,
                        $enrollment->enrollment_date?->format('Y-m-d'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importTemplate()
    {
        $this->ensureCanManageLegacyImports();

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::IMPORT_HEADERS);
            fputcsv($handle, [
                'Sample Legacy Student',
                'sample.legacy@example.test',
                '9000000000',
                '123412341234',
                'Other',
                'Intermediate',
                'Sample Father',
                'Historical MS Office',
                '2019-01-01',
                '2019-03-31',
                now()->toDateString(),
                '500',
                '2000',
                '0',
                '',
                'active',
            ]);
            fclose($handle);
        }, 'legacy_students_import_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function importCsv(Request $request)
    {
        $partner = $this->ensureCanManageLegacyImports();

        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $summary = [
            'created' => 0,
            'students_created' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $handle = fopen($validated['csv_file']->getRealPath(), 'r');
        if ($handle === false) {
            return back()->with('error', 'Unable to read uploaded CSV file.');
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers)) {
            fclose($handle);

            return back()->with('error', 'CSV file is empty.');
        }

        $headers = $this->normalizeHeaders($headers);
        $missingHeaders = array_diff($this->requiredImportHeaders(), $headers);
        if ($missingHeaders !== []) {
            fclose($handle);

            return back()->with('error', 'CSV is missing required columns: '.implode(', ', $missingHeaders));
        }

        $rowNumber = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if ($this->isEmptyCsvRow($values)) {
                continue;
            }

            $row = $this->mapCsvRow($headers, $values);
            $result = $this->importLegacyRow($row, $partner);

            if ($result['status'] === 'created') {
                $summary['created']++;
                $summary['students_created'] += $result['student_created'] ? 1 : 0;
            } elseif ($result['status'] === 'skipped') {
                $summary['skipped']++;
                $summary['errors'][] = ['row' => $rowNumber, 'message' => $result['message']];
            } else {
                $summary['errors'][] = ['row' => $rowNumber, 'message' => $result['message']];
            }
        }

        fclose($handle);

        return redirect()
            ->route('admin.legacy-students.index')
            ->with('success', "Legacy import finished. Created {$summary['created']} enrollment(s).")
            ->with('legacy_import_summary', $summary);
    }

    public function edit(Enrollment $enrollment)
    {
        $this->ensureEditableLegacyEnrollment($enrollment);

        $legacyCourseId = LegacyEnrollmentService::legacyCourseId();
        $linkCourses = Course::query()
            ->where('is_active', true)
            ->when($legacyCourseId, fn ($q) => $q->where('id', '!=', $legacyCourseId))
            ->orderBy('name')
            ->get();

        $enrollment->load(['student', 'batch', 'legacyLinkCourse']);

        return view('admin.legacy-students.edit', compact('enrollment', 'linkCourses'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $this->ensureEditableLegacyEnrollment($enrollment);

        $validated = $request->validate([
            'legacy_course_name' => ['required', 'string', 'max:255'],
            'legacy_start_date' => ['required', 'date'],
            'legacy_end_date' => ['required', 'date', 'after_or_equal:legacy_start_date'],
            'enrollment_date' => ['required', 'date'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'course_fee' => ['required', 'numeric', 'min:0'],
            'assessment_fee' => ['required', 'numeric', 'min:0'],
            'legacy_link_course_id' => ['nullable', 'exists:courses,id'],
            'status' => ['required', 'in:active,completed,dropped'],
        ]);

        if (! empty($validated['legacy_link_course_id'])) {
            $this->ensureCourseAccessible(Course::findOrFail((int) $validated['legacy_link_course_id']));
        }

        $enrollment = DB::transaction(function () use ($enrollment, $validated) {
            $registrationFee = round((float) $validated['registration_fee'], 2);
            $courseFee = round((float) $validated['course_fee'], 2);
            $assessmentFee = round((float) $validated['assessment_fee'], 2);
            $totalFee = round($registrationFee + $courseFee + $assessmentFee, 2);

            $enrollment->update([
                'legacy_course_name' => $validated['legacy_course_name'],
                'legacy_start_date' => $validated['legacy_start_date'],
                'legacy_end_date' => $validated['legacy_end_date'],
                'legacy_link_course_id' => $validated['legacy_link_course_id'] ?: null,
                'enrollment_date' => $validated['enrollment_date'],
                'registration_fee' => $registrationFee,
                'course_fee' => $courseFee,
                'assessment_fee' => $assessmentFee,
                'total_fee' => $totalFee,
                'status' => $validated['status'],
            ]);

            return app(PaymentAllocationService::class)
                ->recalculateEnrollmentTotals($enrollment->fresh());
        });

        if ($enrollment->is_legacy && $enrollment->batch?->is_legacy_batch && $enrollment->is_fully_paid) {
            app(LegacyAutoCertificationService::class)->issueIfEligible(
                $enrollment->fresh(['batch', 'student', 'legacyLinkCourse'])
            );
        }

        return redirect()
            ->route('admin.legacy-students.index')
            ->with('success', 'Legacy enrollment updated successfully.');
    }

    private function filteredQuery(Request $request): array
    {
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));

        $query = $this->scopeEnrollments(
            Enrollment::query()
                ->where('is_legacy', true)
                ->with(['student', 'batch', 'legacyLinkCourse', 'payments'])
        );

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('enrollment_number', 'like', '%'.$search.'%')
                    ->orWhere('legacy_course_name', 'like', '%'.$search.'%')
                    ->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('whatsapp_number', 'like', '%'.$search.'%'));
            });
        }

        if (in_array($status, ['active', 'completed', 'dropped'], true)) {
            $query->where('status', $status);
        }

        return [$query, $search, $status];
    }

    private function ensureEditableLegacyEnrollment(Enrollment $enrollment): void
    {
        $enrollment->loadMissing(['student.trainingPartner', 'batch']);

        abort_unless($enrollment->is_legacy && $enrollment->batch?->is_legacy_batch, 404);

        $tpId = $this->getTrainingPartnerId();
        if ($tpId !== null) {
            abort_unless((int) $enrollment->student?->training_partner_id === $tpId, 404);
            abort_unless((bool) $enrollment->student?->trainingPartner?->is_hq, 403);
        }
    }

    private function ensureCanManageLegacyImports(): TrainingPartner
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        $partner = auth()->user()->is_super_admin
            ? LegacyEnrollmentService::hqPartner()
            : auth()->user()->trainingPartner;

        abort_unless($partner?->is_hq, 403);
        abort_unless(LegacyEnrollmentService::legacyBatch() !== null, 422);

        return $partner;
    }

    private function requiredImportHeaders(): array
    {
        return [
            'full_name',
            'email',
            'whatsapp_number',
            'aadhar_number',
            'legacy_course_name',
            'legacy_start_date',
            'legacy_end_date',
            'enrollment_date',
            'registration_fee',
            'course_fee',
            'assessment_fee',
        ];
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower(trim((string) $header));
            $header = preg_replace('/[^a-z0-9]+/', '_', $header);

            return trim((string) $header, '_');
        }, $headers);
    }

    private function mapCsvRow(array $headers, array $values): array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return $row;
    }

    private function isEmptyCsvRow(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function importLegacyRow(array $row, TrainingPartner $partner): array
    {
        foreach (['gender', 'qualification', 'father_name', 'legacy_link_course_name', 'status'] as $optionalField) {
            if (array_key_exists($optionalField, $row) && $row[$optionalField] === '') {
                $row[$optionalField] = null;
            }
        }

        $validator = Validator::make($row, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'aadhar_number' => ['required', 'digits:12'],
            'gender' => ['nullable', 'string', 'max:50'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'legacy_course_name' => ['required', 'string', 'max:255'],
            'legacy_start_date' => ['required', 'date'],
            'legacy_end_date' => ['required', 'date', 'after_or_equal:legacy_start_date'],
            'enrollment_date' => ['required', 'date'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'course_fee' => ['required', 'numeric', 'min:0'],
            'assessment_fee' => ['required', 'numeric', 'min:0'],
            'legacy_link_course_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'dropped'])],
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'student_created' => false,
            ];
        }

        $data = $validator->validated();

        try {
            return DB::transaction(function () use ($data, $partner) {
                $linkCourseId = null;
                if (($data['legacy_link_course_name'] ?? '') !== '') {
                    $linkCourse = Course::query()
                        ->where('is_active', true)
                        ->where('name', $data['legacy_link_course_name'])
                        ->first();

                    if (! $linkCourse) {
                        return [
                            'status' => 'error',
                            'message' => 'Linked LMS course was not found or is inactive.',
                            'student_created' => false,
                        ];
                    }

                    $linkCourseId = $linkCourse->id;
                }

                $student = $this->resolveImportStudent($data, $partner);
                $studentCreated = false;

                if (! $student) {
                    $student = Student::create([
                        'training_partner_id' => $partner->id,
                        'aadhar_number' => $data['aadhar_number'],
                        'full_name' => $data['full_name'],
                        'father_name' => $data['father_name'] ?? null,
                        'gender' => $data['gender'] ?? null,
                        'qualification' => $data['qualification'] ?? null,
                        'email' => $data['email'],
                        'phone' => $data['whatsapp_number'],
                        'whatsapp_number' => $data['whatsapp_number'],
                        'status' => 'approved',
                        'is_active' => true,
                        'approved_at' => now(),
                    ]);
                    $studentCreated = true;
                }

                if ((int) $student->training_partner_id !== (int) $partner->id) {
                    return [
                        'status' => 'error',
                        'message' => 'Student belongs to another training partner.',
                        'student_created' => false,
                    ];
                }

                if (! $student->is_approved) {
                    $student->update([
                        'status' => 'approved',
                        'approved_at' => $student->approved_at ?: now(),
                        'is_active' => true,
                    ]);
                }

                $batch = LegacyEnrollmentService::legacyBatch();
                if ($student->enrollments()->where('batch_id', $batch->id)->exists()) {
                    return [
                        'status' => 'skipped',
                        'message' => 'Student already has a legacy enrollment.',
                        'student_created' => $studentCreated,
                    ];
                }

                $this->ensureStudentUser($student, $data);

                $registrationFee = round((float) $data['registration_fee'], 2);
                $courseFee = round((float) $data['course_fee'], 2);
                $assessmentFee = round((float) $data['assessment_fee'], 2);
                $totalFee = round($registrationFee + $courseFee + $assessmentFee, 2);

                $enrollment = Enrollment::create([
                    'enrollment_number' => EnrollmentNumberService::generateEnrollmentNumber(),
                    'student_id' => $student->id,
                    'batch_id' => $batch->id,
                    'enrollment_date' => $data['enrollment_date'],
                    'status' => $data['status'] ?? 'active',
                    'total_fee' => $totalFee,
                    'paid_amount' => 0,
                    'discount_amount' => 0,
                    'outstanding_amount' => $totalFee,
                    'is_eligible_for_assessment' => $totalFee <= 0,
                    'registration_fee' => $registrationFee,
                    'course_fee' => $courseFee,
                    'assessment_fee' => $assessmentFee,
                    'is_legacy' => true,
                    'legacy_course_name' => $data['legacy_course_name'],
                    'legacy_start_date' => $data['legacy_start_date'],
                    'legacy_end_date' => $data['legacy_end_date'],
                    'legacy_link_course_id' => $linkCourseId,
                ]);

                $enrollment = app(PaymentAllocationService::class)
                    ->recalculateEnrollmentTotals($enrollment);

                if ($enrollment->is_fully_paid) {
                    app(LegacyAutoCertificationService::class)->issueIfEligible(
                        $enrollment->fresh(['batch', 'student', 'legacyLinkCourse'])
                    );
                }

                return [
                    'status' => 'created',
                    'message' => 'Imported successfully.',
                    'student_created' => $studentCreated,
                ];
            });
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'student_created' => false,
            ];
        }
    }

    private function resolveImportStudent(array $data, TrainingPartner $partner): ?Student
    {
        $byEmail = Student::query()->where('email', $data['email'])->first();
        $byAadhar = Student::query()->where('aadhar_number', $data['aadhar_number'])->first();

        if ($byEmail && $byAadhar && $byEmail->id !== $byAadhar->id) {
            throw new \RuntimeException('Email and Aadhar belong to different students.');
        }

        $student = $byEmail ?: $byAadhar;
        if ($student && (int) $student->training_partner_id !== (int) $partner->id) {
            throw new \RuntimeException('Student belongs to another training partner.');
        }

        return $student;
    }

    private function ensureStudentUser(Student $student, array $data): void
    {
        if ($student->user()->exists()) {
            return;
        }

        if (User::query()->where('email', $student->email)->exists()) {
            throw new \RuntimeException('A user account with this email already exists.');
        }

        User::create([
            'name' => $student->full_name,
            'email' => $student->email,
            'password' => Hash::make($data['whatsapp_number']),
            'role' => 'student',
            'student_id' => $student->id,
            'training_partner_id' => $student->training_partner_id,
            'is_active' => true,
            'must_change_password' => true,
        ]);
    }
}
