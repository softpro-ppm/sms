@extends('layouts.admin')

@section('title', 'Payments Management')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-credit-card text-[10px] text-primary-600"></i>
                        Payments Queue
                    </div>
                    <h2 class="mt-3 text-xl font-semibold leading-tight text-slate-900 md:text-2xl">Manage recorded payments, approval status, and receipts.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review payment records, approve pending entries, and track collected and outstanding amounts in one place.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.payments.pending') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-clock"></i>
                        Pending queue
                    </a>
                    <a href="{{ route('admin.payments.create') }}" 
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus"></i>
                        Record payment
                    </a>
                </div>
            </div>
        </div>

    @if(auth()->user()->is_reception)
    <div class="mx-6 mt-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        <i class="fas fa-info-circle mr-2" aria-hidden="true"></i>
        <strong>Reception:</strong> You can record payments and view details. <strong>Approving or rejecting</strong> pending payments is done by a <strong>centre admin</strong> only.
    </div>
    @endif

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total Payments</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_payments']) }}</p>
                    <p class="mt-1 text-sm text-slate-600">Recorded payment entries</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                    <i class="fas fa-credit-card"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">Pending Approval</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_payments']) }}</p>
                    <p class="mt-1 text-sm text-slate-600">Waiting for admin review</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-rose-700">Remaining Amount</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">₹{{ number_format($stats['total_remaining_amount']) }}</p>
                    <p class="mt-1 text-sm text-slate-600">Outstanding balance still due</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-700">Approved Amount</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">₹{{ number_format($stats['total_amount_approved']) }}</p>
                    <p class="mt-1 text-sm text-slate-600">Approved payment value</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <i class="fas fa-rupee-sign"></i>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Pending Amount Card - Only show if there are pending payments -->
    @if($stats['total_amount_pending'] > 0)
    <div class="rounded-[20px] border border-orange-200 bg-orange-50 p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500 text-white">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-orange-800">Pending Amount</h3>
                    <p class="text-2xl font-semibold text-orange-900">₹{{ number_format($stats['total_amount_pending']) }}</p>
                    <p class="text-sm text-orange-700">Awaiting admin approval</p>
                </div>
            </div>
            @if($stats['pending_payments'] > 0 && auth()->user()->is_admin)
                <div class="flex items-center space-x-4">
                    <button id="selectAllPending" 
                            class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700">
                        <i class="fas fa-check-square mr-2"></i>
                        Select All Pending
                    </button>
                    <form method="POST" action="{{ route('admin.payments.bulk-approve') }}" class="inline" id="bulkApproveForm">
                        @csrf
                        <div id="selectedPayments" class="hidden">
                            <!-- Selected payment IDs will be added here -->
                        </div>
                        <button type="submit" 
                                id="bulkApproveBtn"
                                class="inline-flex items-center rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-orange-700"
                                disabled>
                            <i class="fas fa-check-double mr-2"></i>
                            <span id="bulkApproveText">Approve Selected (<span id="selectedCount">0</span>)</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    @endif

<!-- Search and Filters -->
<div class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative">
                <input type="text"
                       name="search"
                       data-live-search
                       value="{{ request('search') }}"
                       placeholder="Search student, receipt, course..."
                       class="w-80 rounded-xl border border-gray-300 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute left-3.5 top-3 text-gray-400"></i>
            </div>
            <select name="status" data-live-filter class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="date_filter" data-live-filter class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Dates</option>
                <option value="today" {{ ($dateFilter ?? request('date_filter')) === 'today' ? 'selected' : '' }}>Today</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Rows</label>
                <select id="per_page" name="per_page" data-live-rows
                        class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach([10,20,50,100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('admin.payments.index') }}"
               class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-800">
                <i class="fas fa-times mr-1"></i>
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Enhanced Payments Table -->
<div class="rounded-[20px] border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200">
        <h3 class="text-base font-semibold text-gray-900">All Payments</h3>
        <div class="text-sm text-gray-500 mt-1">
            Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} results
        </div>
    </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            #
                        </th>
                        @if(auth()->user()->is_admin)
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        @endif
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Student Info
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Course & Batch
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Amount & Type
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Status
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Date & Time
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $index => $payment)
                    <tr class="payment-row hover:bg-gray-50 transition-colors duration-200" 
                        data-student-name="{{ $payment->student ? strtolower($payment->student->full_name) : 'n/a' }}"
                        data-student-email="{{ $payment->student ? strtolower($payment->student->email) : 'n/a' }}"
                        data-receipt-number="{{ strtolower($payment->payment_receipt_number) }}"
                        data-payment-status="{{ $payment->status }}">
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-500">
                            {{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}
                        </td>
                        @if(auth()->user()->is_admin)
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($payment->status === 'pending')
                                <input type="checkbox" 
                                       class="payment-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                       value="{{ $payment->id }}"
                                       data-amount="{{ $payment->amount }}">
                            @endif
                        </td>
                        @endif
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</div>
                                <div class="mt-0.5 text-gray-500">{{ $payment->student ? $payment->student->email : 'N/A' }}</div>
                                <div class="mt-0.5 text-xs text-gray-400">{{ $payment->student ? $payment->student->whatsapp_number : 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->course)
                                    <div class="font-medium">{{ $payment->enrollment->batch->course->name }}</div>
                                    <div class="mt-0.5 text-gray-500">{{ $payment->enrollment->batch->batch_name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-400">Batch #{{ $payment->enrollment->batch->id }}</div>
                                @else
                                    <div class="font-medium text-gray-400">No Course Assigned</div>
                                    <div class="mt-0.5 text-gray-500">Registration Fee</div>
                                    <div class="mt-0.5 text-xs text-gray-400">Standalone Payment</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium text-base">₹{{ number_format($payment->amount) }}</div>
                                <div class="mt-0.5 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $payment->payment_type) }}</div>
                                @if($payment->remarks)
                                    <div class="text-xs text-gray-400 mt-1">{{ Str::limit($payment->remarks, 30) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($payment->status === 'approved')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Approved
                                </span>
                                @if(auth()->user()->is_super_admin)
                                    @php
                                        $amsStatus = $payment->ams_sync_status ?: 'not_tracked';
                                        $amsClasses = [
                                            'synced' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'failed' => 'bg-red-50 text-red-700 border-red-200',
                                            'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'not_tracked' => 'bg-slate-50 text-slate-600 border-slate-200',
                                        ];
                                        $amsLabel = [
                                            'synced' => 'AMS: Synced',
                                            'failed' => 'AMS: Failed',
                                            'pending' => 'AMS: Pending',
                                            'not_tracked' => 'AMS: —',
                                        ];
                                    @endphp
                                    <div class="mt-2">
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $amsClasses[$amsStatus] ?? $amsClasses['not_tracked'] }}"
                                              title="{{ $payment->ams_last_error ? Str::limit($payment->ams_last_error, 160) : '' }}">
                                            <i class="fas {{ $amsStatus === 'synced' ? 'fa-cloud-arrow-up' : ($amsStatus === 'failed' ? 'fa-triangle-exclamation' : ($amsStatus === 'pending' ? 'fa-clock' : 'fa-minus')) }} text-[10px]"></i>
                                            {{ $amsLabel[$amsStatus] ?? $amsLabel['not_tracked'] }}
                                        </span>
                                    </div>
                                @endif
                                @if($payment->approvedBy)
                                    <div class="text-xs text-gray-500 mt-1">by {{ $payment->approvedBy->name }}</div>
                                @endif
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</div>
                                @if($payment->approved_at)
                                    <div class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-check mr-1"></i>Approved {{ $payment->approved_at->format('M d') }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.payments.show', $payment) }}" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($payment->status === 'pending' && auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                                                title="Approve Payment"
                                                onclick="return confirm('Are you sure you want to approve this payment?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                                title="Reject Payment"
                                                onclick="return confirm('Are you sure you want to reject this payment?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($payment->status === 'approved')
                                    <a href="{{ route('admin.payments.receipt.pdf', $payment) }}" 
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                                       title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" 
                                       target="_blank"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-700 transition hover:bg-violet-100"
                                       title="View Receipt">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    @if(auth()->user()->is_super_admin && ($payment->ams_sync_status === 'failed'))
                                        <form method="POST" action="{{ route('admin.payments.ams.retry', $payment) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 transition hover:bg-amber-100"
                                                    title="Retry AMS sync"
                                                    onclick="return confirm('Retry AMS sync for receipt #{{ $payment->payment_receipt_number }}?')">
                                                <i class="fas fa-rotate-right"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if(auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                                title="Delete Payment"
                                                onclick="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->is_admin ? '7' : '6' }}" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-credit-card text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No payments found</p>
                                <p class="text-sm">Start by recording a payment for a student</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- Enhanced Pagination -->
    <div class="px-5 py-4 border-t border-gray-200 bg-gray-50">
        @if($payments->hasPages())
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} results
                </div>
                <div class="flex items-center space-x-2">
                    {{ $payments->links() }}
                </div>
            </div>
        @else
            <div class="text-center text-sm text-gray-500">
                {{ $payments->count() }} payments found
            </div>
        @endif
    </div>
</div>

<!-- Auto-dismissing notifications -->
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const paymentCheckboxes = document.querySelectorAll('.payment-checkbox');
    const selectAllPendingBtn = document.getElementById('selectAllPending');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectedPaymentsDiv = document.getElementById('selectedPayments');
    const bulkApproveForm = document.getElementById('bulkApproveForm');

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            paymentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkApproveButton();
        });
    }

    // Individual checkbox change
    paymentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkApproveButton();
            updateSelectAllState();
        });
    });

    // Select All Pending button
    if (selectAllPendingBtn) {
        selectAllPendingBtn.addEventListener('click', function() {
            paymentCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
            }
            updateBulkApproveButton();
        });
    }

    // Update bulk approve button state
    function updateBulkApproveButton() {
        const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        
        if (count > 0) {
            if (bulkApproveBtn) {
                bulkApproveBtn.disabled = false;
                bulkApproveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                bulkApproveBtn.classList.add('hover:bg-orange-700');
            }
        } else {
            if (bulkApproveBtn) {
                bulkApproveBtn.disabled = true;
                bulkApproveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                bulkApproveBtn.classList.remove('hover:bg-orange-700');
            }
        }

        // Update hidden inputs for form submission
        if (selectedPaymentsDiv) {
            selectedPaymentsDiv.innerHTML = '';
            checkedBoxes.forEach(checkbox => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'payment_ids[]';
                hiddenInput.value = checkbox.value;
                selectedPaymentsDiv.appendChild(hiddenInput);
            });
        }
    }

    // Update select all checkbox state
    function updateSelectAllState() {
        if (!selectAllCheckbox) return;
        
        const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
        const totalBoxes = paymentCheckboxes.length;
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Form submission confirmation
    if (bulkApproveForm) {
        bulkApproveForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one payment to approve.');
                return false;
            }
            
            const count = checkedBoxes.length;
            const confirmed = confirm(`Are you sure you want to approve ${count} payment${count > 1 ? 's' : ''}?`);
            
            if (!confirmed) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Initialize button state
    updateBulkApproveButton();
});
</script>
@endsection
