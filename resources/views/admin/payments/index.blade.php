@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payments Management</h1>
                <p class="text-gray-600 mt-2">Manage student payments, approvals, and receipts</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.payments.create') }}" 
                   class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Record Payment
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payments</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_payments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Remaining Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Remaining Amount</p>
                    <p class="text-3xl font-bold text-red-600">₹{{ number_format($stats['total_remaining_amount']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-3xl font-bold text-purple-600">₹{{ number_format($stats['total_amount_approved']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Amount Card - Only show if there are pending payments -->
    @if($stats['total_amount_pending'] > 0)
    <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-orange-800">Pending Amount</h3>
                    <p class="text-2xl font-bold text-orange-900">₹{{ number_format($stats['total_amount_pending']) }}</p>
                    <p class="text-sm text-orange-700">Awaiting admin approval</p>
                </div>
            </div>
            @if($stats['pending_payments'] > 0 && auth()->user()->is_admin)
                <div class="flex items-center space-x-4">
                    <button id="selectAllPending" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
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
                                class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors duration-200 flex items-center"
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
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative">
                <input type="text"
                       name="search"
                       data-live-search
                       value="{{ request('search') }}"
                       placeholder="Search student, receipt, course..."
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-80">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <select name="status" data-live-filter class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Rows</label>
                <select id="per_page" name="per_page" data-live-rows
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach([10,20,50,100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('admin.payments.index') }}"
               class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200">
                <i class="fas fa-times mr-1"></i>
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Enhanced Payments Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">All Payments</h3>
        <div class="text-sm text-gray-500 mt-1">
            Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }} results
        </div>
    </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        @if(auth()->user()->is_admin)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course & Batch
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount & Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}
                        </td>
                        @if(auth()->user()->is_admin)
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status === 'pending')
                                <input type="checkbox" 
                                       class="payment-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                       value="{{ $payment->id }}"
                                       data-amount="{{ $payment->amount }}">
                            @endif
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</div>
                                <div class="text-gray-500">{{ $payment->student ? $payment->student->email : 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $payment->student ? $payment->student->whatsapp_number : 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->course)
                                    <div class="font-medium">{{ $payment->enrollment->batch->course->name }}</div>
                                    <div class="text-gray-500">{{ $payment->enrollment->batch->batch_name }}</div>
                                    <div class="text-xs text-gray-400">Batch #{{ $payment->enrollment->batch->id }}</div>
                                @else
                                    <div class="font-medium text-gray-400">No Course Assigned</div>
                                    <div class="text-gray-500">Registration Fee</div>
                                    <div class="text-xs text-gray-400">Standalone Payment</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium text-lg">₹{{ number_format($payment->amount) }}</div>
                                <div class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $payment->payment_type) }}</div>
                                @if($payment->remarks)
                                    <div class="text-xs text-gray-400 mt-1">{{ Str::limit($payment->remarks, 30) }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payment->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Approved
                                </span>
                                @if($payment->approvedBy)
                                    <div class="text-xs text-gray-500 mt-1">by {{ $payment->approvedBy->name }}</div>
                                @endif
                            @elseif($payment->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($payment->status === 'pending' && auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                title="Approve Payment"
                                                onclick="return confirm('Are you sure you want to approve this payment?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                title="Reject Payment"
                                                onclick="return confirm('Are you sure you want to reject this payment?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                                
                                @if($payment->status === 'approved')
                                    <a href="{{ route('admin.payments.receipt.pdf', $payment) }}" 
                                       class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                       title="Download PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.payments.receipt', $payment) }}" 
                                       target="_blank"
                                       class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                                       title="View Receipt">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                                
                                @if(auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 transition-colors duration-200"
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
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
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
