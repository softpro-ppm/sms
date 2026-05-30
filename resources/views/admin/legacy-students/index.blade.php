@extends('layouts.admin')

@section('title', 'Legacy Students')
@section('page-title', 'Legacy Students')

@section('content')
<div class="space-y-6">
    <section class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Archive enrollments</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Legacy students</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Track historical course completions that use the single legacy batch with per-student course, dates, and fee overrides.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.legacy-students.export-csv', request()->query()) }}"
                   class="inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-800 transition hover:bg-emerald-100">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </a>
                <a href="{{ route('admin.students.index') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-user-plus mr-2"></i>Open student queue
                </a>
            </div>
        </div>

        @unless($legacyConfigured)
            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Legacy batch is not configured yet. Run migrations and ensure an HQ training partner exists.
            </div>
        @endunless

        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">Legacy records</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Active</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ number_format($stats['active']) }}</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-blue-700">Fully paid</p>
                <p class="mt-2 text-2xl font-semibold text-blue-900">{{ number_format($stats['paid']) }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-rose-700">Outstanding</p>
                <p class="mt-2 text-2xl font-semibold text-rose-900">₹{{ number_format($stats['outstanding'], 2) }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_180px_auto]">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search student, course, enrollment number"
                       class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                <option value="">All statuses</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="dropped" {{ $status === 'dropped' ? 'selected' : '' }}>Dropped</option>
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
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Dates</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Fees</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($legacyEnrollments as $enrollment)
                    <tr class="align-top hover:bg-slate-50">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $enrollment->student?->full_name ?? 'Student removed' }}</p>
                            <p class="mt-0.5 text-slate-500">{{ $enrollment->student?->email ?? 'No email' }}</p>
                            <p class="mt-1 font-mono text-xs text-slate-500">{{ $enrollment->enrollment_number }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $enrollment->display_course_name }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">Linked LMS: {{ $enrollment->legacyLinkCourse?->name ?? 'None' }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $enrollment->effective_start_date?->format('d M Y') ?? '—' }}
                            <span class="text-slate-400">to</span>
                            {{ $enrollment->effective_end_date?->format('d M Y') ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-slate-700">
                            <p>Total: ₹{{ number_format($enrollment->total_fee, 2) }}</p>
                            <p class="text-emerald-700">Paid: ₹{{ number_format($enrollment->paid_amount, 2) }}</p>
                            <p class="{{ (float) $enrollment->outstanding_amount > 0 ? 'text-rose-700' : 'text-slate-500' }}">Due: ₹{{ number_format($enrollment->outstanding_amount, 2) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $enrollment->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($enrollment->student)
                            <a href="{{ route('admin.students.show', $enrollment->student) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-primary-700 hover:bg-primary-50">
                                Open student
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">No legacy enrollments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($legacyEnrollments->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $legacyEnrollments->links() }}</div>
        @endif
    </section>
</div>
@endsection
