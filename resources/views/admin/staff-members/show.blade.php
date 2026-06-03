@extends('layouts.admin')

@section('title', 'Staff Profile')
@section('page-title', 'Staff Profile')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="{{ route('admin.staff-members.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 hover:text-primary-800">
                    <i class="fas fa-arrow-left text-xs"></i> Back to staff profiles
                </a>
                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-id-badge text-[10px]"></i>
                    Staff profile
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">{{ $staffMember->name }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $staffMember->staff_code ?: 'No staff ID' }}{{ $staffMember->designation ? ' · ' . $staffMember->designation : '' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.staff-attendance.staff-report', $staffMember) }}" class="inline-flex items-center gap-2 rounded-2xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-medium text-violet-700 transition hover:bg-violet-100">
                        <i class="fas fa-chart-line text-xs"></i>
                        Report
                    </a>
                @endif
                <a href="{{ route('admin.staff-members.reenroll', $staffMember) }}" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                    <i class="fas fa-camera-rotate text-xs"></i>
                    Re-enroll
                </a>
                <a href="{{ route('admin.staff-members.edit', $staffMember) }}" class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:bg-blue-100">
                    <i class="fas fa-pen text-xs"></i>
                    Edit
                </a>
                @if(auth()->user()->is_admin && $staffMember->status === 'approved')
                    <form method="POST" action="{{ $staffMember->is_active ? route('admin.staff-members.deactivate', $staffMember) : route('admin.staff-members.activate', $staffMember) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:bg-amber-100">
                            <i class="fas {{ $staffMember->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                            {{ $staffMember->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.staff-members.destroy', $staffMember) }}" onsubmit="return confirm('Delete this staff profile and attendance records? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-100">
                        <i class="fas fa-trash text-xs"></i>
                        Delete
                    </button>
                </form>
                @php
                    $statusClass = match($staffMember->status) {
                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                        default => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                @endphp
                <span class="inline-flex rounded-full border px-3 py-1.5 text-sm font-semibold {{ !$staffMember->is_active ? 'bg-slate-50 text-slate-600 border-slate-200' : $statusClass }}">{{ !$staffMember->is_active ? 'Inactive' : ucfirst($staffMember->status) }}</span>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Details</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Profile information</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach([
                    'Phone' => $staffMember->phone,
                    'Email' => $staffMember->email,
                    'Department' => $staffMember->department,
                    'Joining date' => $staffMember->joining_date?->format('d M Y'),
                    'Created by' => $staffMember->creator?->name,
                    'Approved by' => $staffMember->approver?->name,
                    'Attendance active' => $staffMember->is_active ? 'Yes' : 'No',
                    'Face samples' => count($staffMember->face_descriptors ?? []),
                ] as $label => $value)
                    <div class="flex items-center justify-between gap-4 px-6 py-3.5">
                        <span class="text-sm text-slate-500">{{ $label }}</span>
                        <span class="text-right text-sm font-medium text-slate-900">{{ $value ?: '-' }}</span>
                    </div>
                @endforeach
            </div>

            @if(auth()->user()->is_admin && $staffMember->status === 'pending')
                <div class="space-y-3 border-t border-slate-200 p-6">
                    <form method="POST" action="{{ route('admin.staff-members.approve', $staffMember) }}">
                        @csrf
                        @method('PATCH')
                        <textarea name="approval_notes" rows="2" placeholder="Approval note"
                                  class="mb-3 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100"></textarea>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-700">
                            <i class="fas fa-check text-xs"></i>
                            Approve for attendance
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.staff-members.reject', $staffMember) }}">
                        @csrf
                        @method('PATCH')
                        <textarea name="approval_notes" rows="2" required placeholder="Reason for rejection"
                                  class="mb-3 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100"></textarea>
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-medium text-rose-700 transition hover:bg-rose-100">
                            <i class="fas fa-xmark text-xs"></i>
                            Reject profile
                        </button>
                    </form>
                </div>
            @elseif($staffMember->approval_notes)
                <div class="border-t border-slate-200 p-6">
                    <p class="text-sm font-semibold text-slate-700">Approval notes</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $staffMember->approval_notes }}</p>
                </div>
            @endif
        </section>

        <section class="space-y-5">
            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Enrollment</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Face reference images</h3>
                </div>
                <div class="grid gap-3 p-6 sm:grid-cols-3">
                    @forelse($staffMember->face_image_paths ?? [] as $index => $path)
                        <a href="{{ route('admin.staff-members.face-image', [$staffMember, $index]) }}" target="_blank">
                            <img src="{{ route('admin.staff-members.face-image', [$staffMember, $index]) }}" alt="Face sample" class="aspect-video w-full rounded-2xl border border-slate-200 object-cover">
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No face images stored.</p>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Recent attendance</div>
                    <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Last records</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($staffMember->attendances as $record)
                        <div class="flex items-center justify-between gap-4 px-6 py-3.5">
                            <span class="text-sm font-medium text-slate-900">{{ $record->attendance_date?->format('d M Y') }}</span>
                            <span class="text-sm text-slate-600">{{ $record->check_in_at?->format('h:i A') ?? '-' }} / {{ $record->check_out_at?->format('h:i A') ?? '-' }}</span>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-sm text-slate-500">No attendance yet.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
