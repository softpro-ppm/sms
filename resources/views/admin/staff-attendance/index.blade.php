@extends('layouts.admin')

@section('title', 'Staff Attendance Records')
@section('page-title', 'Staff Attendance')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-list-check text-[10px]"></i>
                    Staff attendance
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review staff check-ins and check-outs.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Records are scoped to your training centre and include the live capture saved at each punch.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.staff-attendance.check') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-camera text-xs"></i>
                    Open punch
                </a>
                <a href="{{ route('admin.settings.users.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fas fa-user-shield text-xs"></i>
                    Staff users
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Staff</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['staff']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Face enrolled</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['enrolled']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Checked in</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['checked_in_today']) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Checked out</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['checked_out_today']) }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <form method="GET" action="{{ route('admin.staff-attendance.index') }}" class="grid gap-4 border-b border-slate-200 px-6 py-5 md:grid-cols-[1fr_1fr_1.2fr_auto_auto] md:items-end">
            <div>
                <label for="from" class="block text-sm font-medium text-slate-700">From</label>
                <input type="date" id="from" name="from" value="{{ request('from', $from->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="to" class="block text-sm font-medium text-slate-700">To</label>
                <input type="date" id="to" name="to" value="{{ request('to', $to->toDateString()) }}" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
            </div>
            <div>
                <label for="user_id" class="block text-sm font-medium text-slate-700">Staff</label>
                <select id="user_id" name="user_id" class="mt-1 block w-full rounded-2xl border-slate-200 px-4 py-2.5 text-sm shadow-sm focus:border-primary-300 focus:ring-primary-100">
                    <option value="">All staff</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" @selected((string) request('user_id') === (string) $staff->id)>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-filter text-xs"></i>
                Filter
            </button>
            <a href="{{ route('admin.staff-attendance.export', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100">
                <i class="fas fa-file-csv text-xs"></i>
                Export
            </a>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Staff</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check in</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Check out</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Hours</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Captures</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-900">{{ $record->attendance_date?->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="text-sm font-medium text-slate-900">{{ $record->user?->name }}</p>
                                <p class="text-xs text-slate-500">{{ ucfirst($record->user?->role ?? '') }}</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $record->check_in_at?->format('h:i A') ?? '-' }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $record->check_out_at?->format('h:i A') ?? '-' }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm text-slate-600">
                                @if($record->check_in_at && $record->check_out_at)
                                    {{ number_format($record->check_in_at->diffInMinutes($record->check_out_at) / 60, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($record->check_in_image_path)
                                        <a href="{{ Storage::disk('public')->url($record->check_in_image_path) }}" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100" title="Check-in capture">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @endif
                                    @if($record->check_out_image_path)
                                        <a href="{{ Storage::disk('public')->url($record->check_out_image_path) }}" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100" title="Check-out capture">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="border-t border-slate-200 px-6 py-4">
                {{ $records->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
