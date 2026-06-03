@extends('layouts.admin')

@section('title', 'Pending Payments Queue')
@section('page-title', 'Pending Payments')

@section('content')
<div class="space-y-8">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-rose-50 px-6 py-5 text-slate-900">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-rose-700">
                        <i class="fas fa-money-check-alt text-[10px]"></i>
                        Pending Payments Queue
                    </div>
                    <h2 class="mt-3 text-xl font-semibold leading-tight md:text-2xl">Review outstanding balances and clear payment approvals.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Track students with pending amounts, confirm queued payments, and move enrollments back into good standing from one finance queue.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 transition hover:bg-slate-50">
                        <i class="fas fa-list"></i>
                        All payments
                    </a>
                    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus-circle"></i>
                        Record payment
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pending Students</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_students']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Enrollments with outstanding fees</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pending Amount</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">₹{{ number_format($stats['total_pending_amount']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Total outstanding across queue</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Average Outstanding</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">₹{{ number_format($stats['average_pending']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Average pending per enrollment</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
        <div class="border-b border-gray-100 pb-3">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Queue Filters</p>
            <h3 class="mt-1 text-base font-semibold text-gray-900">Outstanding payment records</h3>
            <p class="mt-1 text-sm text-gray-600">Search by student, course, or batch and export the same filtered result if needed.</p>
        </div>

        <div class="mt-4 flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('admin.payments.pending') }}" class="grid gap-3 sm:grid-cols-[260px_120px]">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               data-live-search
                               value="{{ request('search') }}"
                               placeholder="Search student, course, batch"
                               class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <select id="per_page" name="per_page" data-live-rows class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        @foreach([10,20,50,100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }} rows</option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.payments.pending.export-csv', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-medium text-emerald-800 transition hover:bg-emerald-100">
                    <i class="fas fa-download"></i>
                    Download CSV
                </a>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Pending Finance Queue</p>
                <h3 class="mt-1 text-base font-semibold text-gray-900">Students with outstanding balances</h3>
            </div>
            <div class="text-sm text-gray-500">
                Showing {{ $pendingData->firstItem() ?? 0 }}-{{ $pendingData->lastItem() ?? 0 }} of {{ $pendingData->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Student</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Course & Batch</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Fee Status</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Progress</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Last Payment</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($pendingData as $enrollment)
                        @php
                            $courseFee = (float) $enrollment->total_fee;
                            $paidAmount = (float) $enrollment->paid_amount;
                            $paymentProgress = $courseFee > 0 ? round(($paidAmount / $courseFee) * 100, 1) : 0;
                        @endphp
                        <tr class="align-top transition hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700">
                                        {{ strtoupper(substr($enrollment->student->full_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold leading-5 text-gray-900">{{ $enrollment->student->full_name }}</p>
                                        <p class="mt-0.5 text-sm leading-5 text-gray-600">{{ $enrollment->student->email ?: 'No email recorded' }}</p>
                                        <p class="mt-0.5 text-sm leading-5 text-gray-500">{{ $enrollment->student->whatsapp_number ?: 'No WhatsApp recorded' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="space-y-0.5">
                                    <p class="text-sm font-semibold leading-5 text-gray-900">{{ $enrollment->display_course_name }}</p>
                                    <p class="text-sm leading-5 text-gray-600">{{ $enrollment->batch->batch_name }}</p>
                                    <p class="text-xs text-slate-500">Batch #{{ $enrollment->batch->id }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="space-y-1.5">
                                    <p class="text-base font-semibold leading-6 text-slate-900">₹{{ number_format((float) $enrollment->paid_amount) }} / ₹{{ number_format((float) $enrollment->total_fee) }}</p>
                                    <p class="text-sm font-semibold leading-5 text-rose-700">Outstanding: ₹{{ number_format((float) $enrollment->outstanding_amount) }}</p>
                                    @if($enrollment->pending_payments_count > 0)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                            {{ $enrollment->pending_payments_count }} payment(s) waiting for approval
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-200">
                                            <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, $paymentProgress) }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ $paymentProgress }}%</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Paid versus total fee</p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($enrollment->payments_max_created_at)
                                    <div class="space-y-0.5 text-sm text-gray-600">
                                        <p class="font-medium text-gray-900">{{ \Illuminate\Support\Carbon::parse($enrollment->payments_max_created_at)->format('M d, Y') }}</p>
                                        <p>{{ \Illuminate\Support\Carbon::parse($enrollment->payments_max_created_at)->format('h:i A') }}</p>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">No payment recorded yet</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                                    <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                       title="Open student"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-primary-700 transition hover:border-primary-300 hover:bg-primary-50">
                                        <i class="fas fa-user text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.create', ['student_id' => $enrollment->student->id, 'enrollment_id' => $enrollment->id]) }}"
                                       title="Add payment"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white transition hover:bg-emerald-700">
                                        <i class="fas fa-plus text-xs"></i>
                                    </a>
                                    @if($enrollment->pending_payments_count > 0)
                                        <a href="{{ route('admin.payments.index') }}?student={{ $enrollment->student->id }}"
                                           title="Review pending entries"
                                           class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-800 transition hover:bg-amber-200">
                                            <i class="fas fa-clock text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-check-circle text-4xl mb-4 text-emerald-500"></i>
                                    <p class="text-lg font-medium">No pending payments</p>
                                    <p class="text-sm">All active enrollments are currently clear or fully paid.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                <i class="fas fa-arrow-left text-xs"></i>
                Back to all payments
            </a>
            @if($pendingData->hasPages())
                {{ $pendingData->links() }}
            @endif
        </div>
    </section>
</div>
@endsection
