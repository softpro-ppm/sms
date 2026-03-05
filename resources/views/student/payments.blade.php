@extends('layouts.student')

@section('title', 'Payment History')
@section('page-title', 'Payments')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Payment History</h2>
                <p class="text-gray-600 mt-1">View all your payment transactions and receipts.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: {{ $payments->total() }} payments</span>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-green-500 to-green-600 flex items-center justify-center">
                                                <i class="fas fa-credit-card text-white"></i>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₹{{ number_format($payment->amount) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ ucfirst($payment->payment_type) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        <i class="fas fa-{{ $payment->status === 'approved' ? 'check-circle' : 
                                                           ($payment->status === 'pending' ? 'clock' : 'times-circle') }} mr-1"></i>
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($payment->status === 'approved')
                                        <a href="{{ route('student.payments.receipt.pdf', $payment) }}" 
                                           class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-file-pdf mr-1"></i>
                                            Download PDF
                                        </a>
                                        <a href="{{ route('student.payments.receipt', $payment) }}" 
                                           target="_blank"
                                           class="text-primary-600 hover:text-primary-900 mr-3">
                                            <i class="fas fa-receipt mr-1"></i>
                                            View
                                        </a>
                                    @endif
                                    @if($payment->receipt_file_path)
                                        <a href="{{ Storage::url($payment->receipt_file_path) }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-download mr-1"></i>
                                            Download
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
                <div class="px-6 py-4 border-t border-gray-200">
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
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Payment Summary -->
    @if($payments->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 rounded-lg p-4">
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
                
                <div class="bg-yellow-50 rounded-lg p-4">
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
                
                <div class="bg-blue-50 rounded-lg p-4">
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
        </div>
    @endif
</div>
@endsection
