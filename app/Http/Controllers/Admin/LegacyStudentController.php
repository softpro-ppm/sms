<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\LegacyEnrollmentService;
use Illuminate\Http\Request;

class LegacyStudentController extends Controller
{
    use ScopesByTrainingPartner;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));

        $query = $this->scopeEnrollments(
            Enrollment::query()
                ->where('is_legacy', true)
                ->with(['student', 'batch', 'legacyLinkCourse', 'payments'])
        );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
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
}
