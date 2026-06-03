<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\LegacyAutoCertificationService;
use App\Services\LegacyEnrollmentService;
use App\Services\PaymentAllocationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegacyStudentController extends Controller
{
    use ScopesByTrainingPartner;

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
}
