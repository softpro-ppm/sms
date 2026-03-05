@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.payments.index') }}" 
               class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment Details</h1>
                <p class="text-gray-600 mt-2">Receipt #{{ $payment->payment_receipt_number }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <!-- Payment Status Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 {{ $payment->status === 'approved' ? 'bg-green-600' : ($payment->status === 'pending' ? 'bg-orange-600' : 'bg-red-600') }} text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            @if($payment->status === 'approved')
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            @elseif($payment->status === 'pending')
                                <i class="fas fa-clock text-white text-xl"></i>
                            @else
                                <i class="fas fa-times-circle text-white text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold capitalize">{{ $payment->status }} Payment</h2>
                            <p class="text-opacity-90">
                                @if($payment->status === 'approved')
                                    Payment approved and processed
                                @elseif($payment->status === 'pending')
                                    Awaiting admin approval
                                @else
                                    Payment rejected
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">₹{{ number_format($payment->amount) }}</div>
                        <div class="text-sm opacity-90">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Payment Details -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-receipt text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">Payment Information</h2>
                            <p class="text-blue-100 text-sm">Transaction details and receipt information</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Receipt Number</span>
                            <span class="text-gray-900 font-semibold">#{{ $payment->payment_receipt_number }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Payment ID</span>
                            <span class="text-gray-900 font-semibold">#{{ $payment->id }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Amount</span>
                            <span class="text-gray-900 font-semibold text-lg">₹{{ number_format($payment->amount) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Payment Type</span>
                            <span class="text-gray-900 font-semibold">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Status</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800') }}">
                                <i class="fas {{ $payment->status === 'approved' ? 'fa-check-circle' : ($payment->status === 'pending' ? 'fa-clock' : 'fa-times-circle') }} mr-1"></i>
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Created Date</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        
                        @if($payment->approved_at)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Approved Date</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->approved_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                        
                        @if($payment->remarks)
                        <div class="py-2">
                            <span class="text-gray-600 font-medium block mb-2">Remarks</span>
                            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $payment->remarks }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold">Student Information</h2>
                            <p class="text-green-100 text-sm">Student and enrollment details</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Student Name</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Email</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->student ? $payment->student->email : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">WhatsApp</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->student ? $payment->student->whatsapp_number : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Aadhar Number</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->student ? $payment->student->aadhar_number : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Course</span>
                            <span class="text-gray-900 font-semibold">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->course)
                                    {{ $payment->enrollment->batch->course->name }}
                                @else
                                    No Course Assigned
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Batch</span>
                            <span class="text-gray-900 font-semibold">
                                @if($payment->enrollment && $payment->enrollment->batch)
                                    {{ $payment->enrollment->batch->batch_name }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Enrollment Date</span>
                            <span class="text-gray-900 font-semibold">
                                @if($payment->enrollment && $payment->enrollment->enrollment_date)
                                    {{ $payment->enrollment->enrollment_date->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        
                        @if($payment->approvedBy)
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-600 font-medium">Approved By</span>
                            <span class="text-gray-900 font-semibold">{{ $payment->approvedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-calculator text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Financial Summary</h2>
                        <p class="text-purple-100 text-sm">Course fees and payment breakdown</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">₹{{ number_format($payment->enrollment->batch->course->course_fee) }}</div>
                        <div class="text-sm text-gray-600">Course Fee</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900">₹{{ number_format($payment->enrollment->paid_amount) }}</div>
                        <div class="text-sm text-gray-600">Total Paid</div>
                    </div>
                    
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-2xl font-bold {{ $payment->enrollment->outstanding_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₹{{ number_format($payment->enrollment->outstanding_amount) }}
                        </div>
                        <div class="text-sm text-gray-600">Outstanding</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.payments.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Payments
                </a>
                
                @if($payment->status === 'pending' && auth()->user()->is_admin)
                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to approve this payment?')">
                            <i class="fas fa-check mr-2"></i>
                            Approve Payment
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to reject this payment?')">
                            <i class="fas fa-times mr-2"></i>
                            Reject Payment
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="flex items-center space-x-4">
                @if($payment->status === 'approved')
                    <a href="{{ route('admin.payments.receipt', $payment) }}" 
                       target="_blank"
                       class="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <i class="fas fa-file-invoice mr-2"></i>
                        View Receipt
                    </a>
                    <a href="{{ route('admin.payments.receipt.pdf', $payment) }}" 
                       class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Download PDF
                    </a>
                @endif
                
                @if(auth()->user()->is_admin)
                    <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Payment
                        </button>
                    </form>
                @endif
            </div>
        </div>
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
