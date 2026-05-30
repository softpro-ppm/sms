@extends('layouts.admin')

@section('title', 'Student Deletion Requests')
@section('page-title', 'Deletion Requests')

@section('content')
<div class="space-y-6">
    <section class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Admin review</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Student deletion requests</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Review deletion requests submitted by reception before student records are removed.</p>
            </div>
            <a href="{{ route('admin.students.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 transition hover:bg-slate-50">
                <i class="fas fa-users mr-2"></i>Student queue
            </a>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            <a href="{{ route('admin.student-deletion-requests.index') }}" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-amber-700">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-amber-900">{{ number_format($stats['pending']) }}</p>
            </a>
            <a href="{{ route('admin.student-deletion-requests.index', ['status' => 'approved']) }}" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Approved</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ number_format($stats['approved']) }}</p>
            </a>
            <a href="{{ route('admin.student-deletion-requests.index', ['status' => 'rejected']) }}" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-rose-700">Rejected</p>
                <p class="mt-2 text-2xl font-semibold text-rose-900">{{ number_format($stats['rejected']) }}</p>
            </a>
        </div>
    </section>

    <section class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_180px_auto]">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search student, email, reason"
                       class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
            </select>
            <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">Apply</button>
        </form>
    </section>

    <section class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Student</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Reason</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Requested</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($requests as $deletionRequest)
                    <tr class="align-top hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $deletionRequest->student?->full_name ?? $deletionRequest->student_name_snapshot }}</p>
                            <p class="mt-0.5 text-slate-500">{{ $deletionRequest->student?->email ?? $deletionRequest->student_email_snapshot ?? 'No email' }}</p>
                        </td>
                        <td class="px-5 py-4 max-w-md text-slate-700">{{ $deletionRequest->request_reason }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            <p>{{ $deletionRequest->requestedBy?->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-500">{{ optional($deletionRequest->requested_at)->format('d M Y, h:i A') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $deletionRequest->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($deletionRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($deletionRequest->status) }}
                            </span>
                            @if($deletionRequest->reviewed_at)
                                <p class="mt-2 text-xs text-slate-500">Reviewed by {{ $deletionRequest->reviewedBy?->name ?? 'admin' }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if($deletionRequest->student)
                                <a href="{{ route('admin.students.show', $deletionRequest->student) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-primary-700 hover:bg-primary-50">
                                    Open student
                                </a>
                                @endif
                                @if($deletionRequest->status === 'pending' && $deletionRequest->student)
                                <form method="POST" action="{{ route('admin.student-deletion-requests.approve', $deletionRequest) }}" onsubmit="return confirm('Approve this deletion request and delete the student?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">Approve delete</button>
                                </form>
                                <form method="POST" action="{{ route('admin.student-deletion-requests.reject', $deletionRequest) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-lg bg-amber-100 px-3 py-1.5 text-xs font-medium text-amber-900 hover:bg-amber-200">Reject</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">No deletion requests match this view.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $requests->links() }}</div>
        @endif
    </section>
</div>
@endsection
