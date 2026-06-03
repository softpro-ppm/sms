@extends('layouts.admin')

@section('title', 'Staff Profiles')
@section('page-title', 'Staff Profiles')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-id-badge text-[10px]"></i>
                    Staff profiles
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Register staff for face attendance.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Reception can register staff with face samples. Admin approval is required before kiosk attendance is enabled.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.staff-attendance.kiosk') }}" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                    <i class="fas fa-camera text-xs"></i>
                    Kiosk
                </a>
                <a href="{{ route('admin.staff-members.create') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-user-plus text-xs"></i>
                    Register staff
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
        @foreach([['Total', 'total'], ['Pending', 'pending'], ['Approved', 'approved'], ['Rejected', 'rejected'], ['Inactive', 'inactive']] as [$label, $key])
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($counts[$key] ?? 0) }}</p>
            </div>
        @endforeach
    </div>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.staff-members.index') }}" class="grid gap-4 border-b border-slate-200 px-6 py-5 md:grid-cols-[1fr_180px_auto] md:items-end">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Name, phone, staff ID, designation"
                       class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
                    <option value="">All</option>
                    @foreach(['pending', 'approved', 'rejected', 'inactive'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-filter text-xs"></i>
                Filter
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Staff</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Role</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Face</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                        <th class="px-5 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($staffMembers as $staff)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="text-sm font-semibold text-slate-900">{{ $staff->name }}</p>
                                <p class="text-xs text-slate-500">{{ $staff->staff_code ?: 'No staff ID' }}{{ $staff->phone ? ' · ' . $staff->phone : '' }}</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                {{ $staff->designation ?: '-' }}
                                @if($staff->department)
                                    <span class="block text-xs text-slate-400">{{ $staff->department }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    {{ count($staff->face_descriptors ?? []) }} samples
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $statusClass = match($staff->status) {
                                        'approved' => 'bg-emerald-50 text-emerald-700',
                                        'rejected' => 'bg-rose-50 text-rose-700',
                                        default => 'bg-amber-50 text-amber-700',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ !$staff->is_active ? 'bg-slate-100 text-slate-600' : $statusClass }}">{{ !$staff->is_active ? 'Inactive' : ucfirst($staff->status) }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('admin.staff-members.reenroll', $staff) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100" title="Re-enroll face">
                                    <i class="fas fa-camera-rotate"></i>
                                </a>
                                <a href="{{ route('admin.staff-members.edit', $staff) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="{{ route('admin.staff-members.show', $staff) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600 transition hover:bg-blue-100" title="Open">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">No staff profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffMembers->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">{{ $staffMembers->links() }}</div>
        @endif
    </section>
</div>
@endsection
