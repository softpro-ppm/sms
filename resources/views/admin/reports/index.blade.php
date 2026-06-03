@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
@php
    $activeTab = $tab ?? 'payments';
    $pdfAvailable = class_exists('Barryvdh\\DomPDF\\Facade\\Pdf');
@endphp

<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-chart-line text-[10px]"></i>
                    Reports Queue
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review payments, enrollments, students, and assessment activity.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Filter report data, compare current activity across modules, and export the exact view your team is working with.</p>
            </div>
            <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-500">
                <i class="fas fa-filter text-[10px]"></i>
                <span>Exports use the active filters</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'payments'])) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition {{ $activeTab === 'payments' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fas fa-credit-card mr-2"></i>Payments
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'enrollments'])) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition {{ $activeTab === 'enrollments' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fas fa-graduation-cap mr-2"></i>Enrollments
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'students'])) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition {{ $activeTab === 'students' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fas fa-users mr-2"></i>Students
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'assessments'])) }}"
               class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium transition {{ $activeTab === 'assessments' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fas fa-clipboard-check mr-2"></i>Assessments
            </a>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 px-6 py-5">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Search</label>
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search..."
                           class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">From</label>
                    <input type="date" name="date_from" data-live-filter
                           value="{{ request('date_from') }}"
                           class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">To</label>
                    <input type="date" name="date_to" data-live-filter
                           value="{{ request('date_to') }}"
                           class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Course</label>
                    <select name="course_id" data-live-filter
                            class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">All</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Batch</label>
                    <select name="batch_id" data-live-filter
                            class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">All</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @php
                $reportActionBtn = 'inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-2xl px-3 text-sm font-medium transition-colors sm:px-4';
            @endphp
            <div class="space-y-4 border-t border-slate-200 pt-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                    <div class="w-full sm:w-auto sm:min-w-[10rem]">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" data-live-filter
                                class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            <option value="">All</option>
                            @if($activeTab === 'payments')
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            @elseif($activeTab === 'enrollments')
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="dropped" {{ request('status') === 'dropped' ? 'selected' : '' }}>Dropped</option>
                            @elseif($activeTab === 'students')
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            @else
                                <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Passed</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            @endif
                        </select>
                    </div>
                    @if($activeTab === 'assessments')
                        <div class="w-full sm:w-auto sm:min-w-[12rem] sm:flex-1 sm:max-w-md">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Assessment</label>
                            <select name="assessment_id" data-live-filter
                                    class="w-full rounded-2xl border border-slate-200 px-3 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                                <option value="">All</option>
                                @foreach($assessments as $assessment)
                                    <option value="{{ $assessment->id }}" {{ request('assessment_id') == $assessment->id ? 'selected' : '' }}>
                                        {{ $assessment->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm font-medium text-slate-700 whitespace-nowrap">Rows</label>
                        <select id="per_page" name="per_page" data-live-rows
                                class="h-10 min-w-[4.5rem] rounded-2xl border border-slate-200 px-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            @foreach([10,20,50,100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:gap-2">
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-500 sm:mr-1 sm:pt-0.5">Export</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.reports.index', ['tab' => $activeTab]) }}"
                           class="{{ $reportActionBtn }} border border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50">
                            Clear
                        </a>
                        <a href="{{ route('admin.reports.export', array_merge(['report' => $activeTab, 'format' => 'csv'], request()->query())) }}"
                           class="{{ $reportActionBtn }} border border-emerald-200 bg-emerald-50 text-emerald-700 hover:border-emerald-300 hover:bg-emerald-100">
                            <i class="fas fa-file-csv text-xs opacity-90" aria-hidden="true"></i>
                            <span class="whitespace-nowrap">Export CSV</span>
                        </a>
                        @if($activeTab === 'payments')
                            <a href="{{ route('admin.reports.export.pending_balances_csv', request()->query()) }}"
                               class="{{ $reportActionBtn }} border border-amber-200 bg-amber-50 text-amber-700 hover:border-amber-300 hover:bg-amber-100"
                               title="Active enrollments with fee balance (same as Pending Payments). Uses search, course, batch, enrollment date range.">
                                <i class="fas fa-file-invoice-dollar text-xs opacity-90" aria-hidden="true"></i>
                                <span class="whitespace-nowrap">Pending balances</span>
                            </a>
                        @endif
                        @if($pdfAvailable)
                            <a href="{{ route('admin.reports.export', array_merge(['report' => $activeTab, 'format' => 'pdf'], request()->query())) }}"
                               class="{{ $reportActionBtn }} border border-violet-200 bg-violet-50 text-violet-700 hover:border-violet-300 hover:bg-violet-100">
                                <i class="fas fa-file-pdf text-xs opacity-90" aria-hidden="true"></i>
                                <span class="whitespace-nowrap">Export PDF</span>
                            </a>
                        @else
                            <span class="{{ $reportActionBtn }} cursor-default border border-violet-200 bg-violet-50 text-violet-700">
                                PDF unavailable
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </section>

    @if($activeTab === 'payments')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total payments</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Pending</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-amber-600">{{ number_format($stats['pending_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Approved amount</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-600">₹{{ number_format($stats['approved_amount'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total amount</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-violet-600">₹{{ number_format($stats['total_amount'] ?? 0) }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Payments report</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Filtered payment records</h3>
                <div class="mt-1 text-sm text-slate-500">
                    Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Receipt</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Course & Batch</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Amount</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 text-sm text-gray-900">{{ $payment->payment_receipt_number }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $payment->student?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->student?->whatsapp_number ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $payment->enrollment?->display_course_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->enrollment?->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">₹{{ number_format($payment->amount) }}</td>
                                <td class="px-5 py-3.5 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $payment->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ optional($payment->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            @endif
        </section>
    @elseif($activeTab === 'enrollments')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total enrollments</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Active</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-600">{{ number_format($stats['active_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Dropped</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-rose-600">{{ number_format($stats['dropped_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total fees</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-violet-600">₹{{ number_format($stats['total_fees'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Outstanding</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-amber-600">₹{{ number_format($stats['total_outstanding'] ?? 0) }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Enrollments report</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Filtered enrollment records</h3>
                <div class="mt-1 text-sm text-slate-500">
                    Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Enrollment</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Course & Batch</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Fees</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 text-sm text-gray-900">{{ $enrollment->enrollment_number }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $enrollment->student?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->student?->whatsapp_number ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $enrollment->display_course_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div>Total: ₹{{ number_format($enrollment->total_fee) }}</div>
                                    <div class="text-xs text-gray-500">Paid: ₹{{ number_format($enrollment->paid_amount) }}</div>
                                    <div class="text-xs text-orange-600">Pending: ₹{{ number_format($enrollment->outstanding_amount) }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $enrollment->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ optional($enrollment->enrollment_date)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500">No enrollments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($enrollments->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </section>
    @elseif($activeTab === 'students')
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total students</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Approved</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-600">{{ number_format($stats['approved_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Pending</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-amber-600">{{ number_format($stats['pending_count'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Rejected</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-rose-600">{{ number_format($stats['rejected_count'] ?? 0) }}</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Students report</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Filtered student records</h3>
                <div class="mt-1 text-sm text-slate-500">
                    Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Contact</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Status</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">Aadhar: {{ $student->aadhar_number }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div>{{ $student->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->whatsapp_number }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $student->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($student->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ optional($student->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-gray-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $students->links() }}
                </div>
            @endif
        </section>
    @else
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total results</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ number_format($stats['total_results'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Passed</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-emerald-600">{{ number_format($stats['passed_results'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Failed</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-rose-600">{{ number_format($stats['failed_results'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Avg score</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-violet-600">{{ number_format($stats['average_score'] ?? 0, 1) }}%</p>
            </div>
        </div>

        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Assessment report</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Filtered assessment results</h3>
                <div class="mt-1 text-sm text-slate-500">
                    Showing {{ $results->firstItem() ?? 0 }} to {{ $results->lastItem() ?? 0 }} of {{ $results->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Assessment</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Course & Batch</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Score</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Result</th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 text-sm text-gray-900">{{ $result->student?->name ?? 'N/A' }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">{{ $result->assessment?->title ?? 'N/A' }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">
                                    <div class="font-medium">{{ $result->enrollment?->display_course_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $result->enrollment?->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-900">{{ number_format($result->percentage, 1) }}%</td>
                                <td class="px-5 py-3.5 text-sm">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $result->is_passed ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ optional($result->completed_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-gray-500">No assessment results found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($results->hasPages())
                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @endif
        </section>
    @endif
</div>
@endsection
