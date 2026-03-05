@extends('layouts.admin')

@section('title', 'Pending Payments')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pending Payments</h1>
            <p class="text-gray-600 mt-2">Manage students with pending course payments</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.payments.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to All Payments
            </a>
        </div>
        @if($pendingData->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pendingData->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Pending Students -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Students</p>
                    <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['pending_students']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pending Amount -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pending Amount</p>
                    <p class="text-3xl font-bold text-red-600">₹{{ number_format($stats['total_pending_amount']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Pending -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">₹{{ number_format($stats['average_pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Students with Pending Payments</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student Info
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Course & Batch
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Payment Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Payment
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingData as $index => $data)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($pendingData->currentPage() - 1) * $pendingData->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr($data['student']->full_name, 0, 1) }}{{ substr($data['student']->full_name, strpos($data['student']->full_name, ' ') + 1, 1) ?: '' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $data['student']->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $data['student']->email }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $data['student']->whatsapp_number }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">{{ $data['course']->name }}</div>
                                <div class="text-gray-500">{{ $data['batch']->batch_name }}</div>
                                <div class="text-xs text-gray-400">Batch #{{ $data['batch']->id }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium text-lg">₹{{ number_format($data['approved_amount']) }} / ₹{{ number_format($data['course_fee']) }}</div>
                                <div class="text-xs text-red-600 font-medium">Pending: ₹{{ number_format($data['pending_amount']) }}</div>
                                @if($data['pending_payments']->count() > 0)
                                    <div class="text-xs text-orange-600">{{ $data['pending_payments']->count() }} payment(s) awaiting approval</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $data['payment_progress'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600">{{ $data['payment_progress'] }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($data['last_payment_date'])
                                <div>{{ $data['last_payment_date']->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $data['last_payment_date']->format('h:i A') }}</div>
                            @else
                                <span class="text-gray-400">No payments</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.students.show', $data['student']) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="View Student">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a href="{{ route('admin.payments.create', ['student_id' => $data['student']->id, 'enrollment_id' => $data['enrollment']->id]) }}" 
                                   class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                   title="Add Payment">
                                    <i class="fas fa-plus"></i>
                                </a>
                                @if($data['pending_payments']->count() > 0)
                                    <a href="{{ route('admin.payments.index') }}?student={{ $data['student']->id }}" 
                                       class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                       title="View Pending Payments">
                                        <i class="fas fa-clock"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-4 text-green-500"></i>
                                <p class="text-lg font-medium">No Pending Payments</p>
                                <p class="text-sm">All students have completed their course payments.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <form method="GET" action="{{ route('admin.payments.pending') }}" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search student, course, batch..."
                           class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm text-gray-600">Rows</label>
                    <select id="per_page" name="per_page"
                            data-live-rows
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @foreach([10,20,50,100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
            @if($pendingData->hasPages())
            <div>
                {{ $pendingData->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
