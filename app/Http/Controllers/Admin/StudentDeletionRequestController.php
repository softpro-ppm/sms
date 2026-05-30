<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\StudentDeletionRequest;
use Illuminate\Http\Request;

class StudentDeletionRequestController extends Controller
{
    use ScopesByTrainingPartner;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        $status = (string) $request->get('status', StudentDeletionRequest::STATUS_PENDING);
        $search = trim((string) $request->get('search', ''));

        $query = StudentDeletionRequest::query()
            ->with(['student', 'requestedBy', 'reviewedBy'])
            ->when(
                $this->getTrainingPartnerId() !== null,
                fn ($q) => $q->whereHas('student', fn ($s) => $s->where('training_partner_id', $this->getTrainingPartnerId()))
            );

        if (in_array($status, [
            StudentDeletionRequest::STATUS_PENDING,
            StudentDeletionRequest::STATUS_APPROVED,
            StudentDeletionRequest::STATUS_REJECTED,
        ], true)) {
            $query->where('status', $status);
        } elseif ($status !== 'all') {
            $status = StudentDeletionRequest::STATUS_PENDING;
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('student_name_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('student_email_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('request_reason', 'like', '%'.$search.'%')
                    ->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        $requests = $query
            ->latest('requested_at')
            ->paginate(20)
            ->appends($request->query());

        $baseStats = StudentDeletionRequest::query()
            ->when(
                $this->getTrainingPartnerId() !== null,
                fn ($q) => $q->whereHas('student', fn ($s) => $s->where('training_partner_id', $this->getTrainingPartnerId()))
            );

        $stats = [
            'pending' => (clone $baseStats)->where('status', StudentDeletionRequest::STATUS_PENDING)->count(),
            'approved' => (clone $baseStats)->where('status', StudentDeletionRequest::STATUS_APPROVED)->count(),
            'rejected' => (clone $baseStats)->where('status', StudentDeletionRequest::STATUS_REJECTED)->count(),
        ];

        return view('admin.students.deletion-requests', compact('requests', 'stats', 'status', 'search'));
    }
}
