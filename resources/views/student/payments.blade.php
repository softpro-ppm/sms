@extends('layouts.student')

@section('title', 'Payment History')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-credit-card text-[10px]"></i>
                    Payments
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review your submitted payments and receipt status.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Track approved and pending payments, download receipts, and review your payment history in one place.</p>
            </div>
            <div class="text-sm text-slate-500">{{ $payments->total() }} payments</div>
        </div>
    </section>

    <!-- Payments List -->
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Payment Details
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Amount
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Type
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Date
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                Payment #{{ $payment->payment_number }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $payment->payment_method }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₹{{ number_format($payment->amount) }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ ucfirst($payment->payment_type) }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                        {{ $payment->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 
                                           ($payment->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                        <i class="fas fa-{{ $payment->status === 'approved' ? 'check-circle' : 
                                                           ($payment->status === 'pending' ? 'clock' : 'times-circle') }} mr-1"></i>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                                    @if($payment->status === 'approved')
                                        <a href="{{ route('student.payments.receipt.pdf', $payment) }}" 
                                           class="mr-2 inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">
                                            <i class="fas fa-file-pdf text-xs"></i>
                                            PDF
                                        </a>
                                        <a href="{{ route('student.payments.receipt', $payment) }}" 
                                           target="_blank"
                                           class="mr-2 inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                                            <i class="fas fa-receipt text-xs"></i>
                                            View
                                        </a>
                                    @endif
                                    @if($payment->receipt_file_path)
                                        <a href="{{ Storage::url($payment->receipt_file_path) }}" 
                                           target="_blank"
                                           class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                                            <i class="fas fa-download text-xs"></i>
                                            Receipt
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300">
                    <i class="fas fa-credit-card text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No payments found</h3>
                <p class="mt-2 text-gray-500">You haven't made any payments yet.</p>
                <div class="mt-6">
                    <a href="{{ route('student.dashboard') }}" 
                       class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Payment Summary -->
    @if($payments->count() > 0)
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Summary</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Payment summary</h3>
            </div>
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-3">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Total Paid</p>
                            <p class="text-2xl font-bold text-green-900">
                                ₹{{ number_format($payments->where('status', 'approved')->sum('amount')) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Pending</p>
                            <p class="text-2xl font-bold text-yellow-900">
                                ₹{{ number_format($payments->where('status', 'pending')->sum('amount')) }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-receipt text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Total Payments</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $payments->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
