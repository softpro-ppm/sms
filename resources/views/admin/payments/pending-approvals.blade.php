@extends('layouts.admin')

@section('title', 'Pending Approvals')
@section('page-title', 'Pending Approvals')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-amber-50 px-6 py-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">
                        <i class="fas fa-user-check text-[10px]"></i>
                        Payment approvals
                    </div>
                    <h2 class="mt-3 text-xl font-semibold text-slate-900 md:text-2xl">Pending payment approvals</h2>
                    <p class="mt-2 max-w-2xl text-sm text-slate-600">Review recorded payments that are waiting for centre admin approval.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.payments.pending') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-money-check-alt"></i>
                        Pending payments
                    </a>
                    <a href="{{ route('admin.payments.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-list"></i>
                        All payments
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pending approvals</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_count']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pending approval amount</p>
                <p class="mt-3 text-2xl font-semibold text-slate-900">₹{{ number_format($stats['pending_amount'], 2) }}</p>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.payments.pending-approvals') }}" class="grid gap-3 md:grid-cols-[minmax(0,1fr)_120px_auto]">
            <div class="relative">
                <input type="text"
                       name="search"
                       data-live-search
                       value="{{ request('search') }}"
                       placeholder="Search student, receipt, course, batch"
                       class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select id="per_page" name="per_page" data-live-rows class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                @foreach([10,20,50,100] as $size)
                    <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.payments.pending-approvals') }}"
               class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                Clear
            </a>
        </form>
    </section>

    <section class="overflow-hidden rounded-[20px] border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Approval Queue</p>
                <h3 class="mt-1 text-base font-semibold text-gray-900">Payments waiting for admin review</h3>
            </div>
            <div class="text-sm text-gray-500">
                Showing {{ $payments->firstItem() ?? 0 }}-{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Student</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Course & Receipt</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Amount</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Recorded</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($payments as $payment)
                    <tr class="align-top transition hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-900">{{ $payment->student?->full_name ?? 'Student removed' }}</p>
                            <p class="mt-0.5 text-sm text-gray-600">{{ $payment->student?->email ?? 'No email' }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ $payment->student?->whatsapp_number ?? 'No WhatsApp' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $payment->enrollment?->batch?->course?->name ?? $payment->enrollment?->display_course_name ?? 'Standalone payment' }}
                            </p>
                            <p class="mt-0.5 text-sm text-gray-600">{{ $payment->enrollment?->batch?->batch_name ?? 'No batch' }}</p>
                            <p class="mt-0.5 font-mono text-xs text-gray-500">{{ $payment->payment_receipt_number }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-base font-semibold text-slate-900">₹{{ number_format((float) $payment->amount, 2) }}</p>
                            <p class="mt-0.5 text-xs capitalize text-slate-500">{{ str_replace('_', ' ', $payment->payment_method_label) }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">
                            <p class="font-medium text-gray-900">{{ $payment->created_at->format('M d, Y') }}</p>
                            <p>{{ $payment->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <a href="{{ route('admin.payments.show', $payment) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                   title="View details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                @if(auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                                                title="Approve payment"
                                                onclick="return confirm('Are you sure you want to approve this payment?')">
                                            <i class="fas fa-check text-xs"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                                title="Reject payment"
                                                onclick="return confirm('Are you sure you want to reject this payment?')">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-check-circle mb-4 text-4xl text-emerald-500"></i>
                                <p class="text-lg font-medium">No pending approvals</p>
                                <p class="text-sm">All recorded payments have been reviewed.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">
                {{ $payments->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
