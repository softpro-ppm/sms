<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\LegacyEnrollmentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

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
}
