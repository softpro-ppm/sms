<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesByTrainingPartner;
use App\Http\Controllers\Controller;
use App\Models\StudentDeletionRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class StudentDeletionRequestController extends Controller
{
    use ScopesByTrainingPartner;

    public function index(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        [$query, $status, $search] = $this->filteredQuery($request);

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

    public function exportCsv(Request $request)
    {
        abort_unless(auth()->user()->is_admin || auth()->user()->is_super_admin, 403);

        [$query, $status] = $this->filteredQuery($request);
        $filename = 'student_deletion_requests_'.$status.'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Requested At',
                'Student',
                'Email',
                'Reason',
                'Status',
                'Requested By',
                'Reviewed By',
                'Reviewed At',
                'Review Notes',
            ]);

            $query->latest('requested_at')->chunk(200, function ($requests) use ($handle) {
                foreach ($requests as $deletionRequest) {
                    fputcsv($handle, [
                        optional($deletionRequest->requested_at)->format('Y-m-d H:i:s'),
                        $deletionRequest->student?->full_name ?? $deletionRequest->student_name_snapshot,
                        $deletionRequest->student?->email ?? $deletionRequest->student_email_snapshot,
                        $deletionRequest->request_reason,
                        $deletionRequest->status,
                        $deletionRequest->requestedBy?->name,
                        $deletionRequest->reviewedBy?->name,
                        optional($deletionRequest->reviewed_at)->format('Y-m-d H:i:s'),
                        $deletionRequest->review_notes,
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
            $query->where(function (Builder $q) use ($search) {
                $q->where('student_name_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('student_email_snapshot', 'like', '%'.$search.'%')
                    ->orWhere('request_reason', 'like', '%'.$search.'%')
                    ->orWhereHas('student', fn ($s) => $s->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        return [$query, $status, $search];
    }
}
