@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
@php
    $activeTab = $tab ?? 'payments';
    $pdfAvailable = class_exists('Barryvdh\\DomPDF\\Facade\\Pdf');
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Reports</h2>
                <p class="text-gray-600 mt-1">Payments, enrollments, students, and assessments</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <i class="fas fa-filter"></i>
                <span>Exports use current filters</span>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'payments'])) }}"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $activeTab === 'payments' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-credit-card mr-2"></i>Payments
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'enrollments'])) }}"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $activeTab === 'enrollments' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-graduation-cap mr-2"></i>Enrollments
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'students'])) }}"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $activeTab === 'students' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-users mr-2"></i>Students
            </a>
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'assessments'])) }}"
               class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $activeTab === 'assessments' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-clipboard-check mr-2"></i>Assessments
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="date_from" data-live-filter
                           value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="date_to" data-live-filter
                           value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                    <select name="course_id" data-live-filter
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                    <select name="batch_id" data-live-filter
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" data-live-filter
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assessment</label>
                        <select name="assessment_id" data-live-filter
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All</option>
                            @foreach($assessments as $assessment)
                                <option value="{{ $assessment->id }}" {{ request('assessment_id') == $assessment->id ? 'selected' : '' }}>
                                    {{ $assessment->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="flex items-end">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-600">Rows</label>
                        <select id="per_page" name="per_page" data-live-rows
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @foreach([10,20,50,100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-end flex-wrap gap-2">
                    <a href="{{ route('admin.reports.index', ['tab' => $activeTab]) }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Clear
                    </a>
                    <a href="{{ route('admin.reports.export', array_merge(['report' => $activeTab, 'format' => 'csv'], request()->query())) }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Export CSV
                    </a>
                    @if($pdfAvailable)
                        <a href="{{ route('admin.reports.export', array_merge(['report' => $activeTab, 'format' => 'pdf'], request()->query())) }}"
                           class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Export PDF
                        </a>
                    @else
                        <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg text-sm">
                            PDF not configured
                        </span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    @if($activeTab === 'payments')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Payments</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Approved Amount</p>
                <p class="text-2xl font-bold text-green-600">₹{{ number_format($stats['approved_amount'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Amount</p>
                <p class="text-2xl font-bold text-purple-600">₹{{ number_format($stats['total_amount'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payments</h3>
                <div class="text-sm text-gray-500 mt-1">
                    Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} of {{ $payments->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course & Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_receipt_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $payment->student?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->student?->whatsapp_number ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $payment->enrollment?->batch?->course?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $payment->enrollment?->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">₹{{ number_format($payment->amount) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ optional($payment->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'enrollments')
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Enrollments</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Active</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['active_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Dropped</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['dropped_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Fees</p>
                <p class="text-2xl font-bold text-purple-600">₹{{ number_format($stats['total_fees'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Outstanding</p>
                <p class="text-2xl font-bold text-orange-600">₹{{ number_format($stats['total_outstanding'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Enrollments</h3>
                <div class="text-sm text-gray-500 mt-1">
                    Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }} of {{ $enrollments->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course & Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fees</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $enrollment->enrollment_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $enrollment->student?->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->student?->whatsapp_number ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $enrollment->batch?->course?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $enrollment->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>Total: ₹{{ number_format($enrollment->total_fee) }}</div>
                                    <div class="text-xs text-gray-500">Paid: ₹{{ number_format($enrollment->paid_amount) }}</div>
                                    <div class="text-xs text-orange-600">Pending: ₹{{ number_format($enrollment->outstanding_amount) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ optional($enrollment->enrollment_date)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">No enrollments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($enrollments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $enrollments->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'students')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['approved_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending_count'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Rejected</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['rejected_count'] ?? 0) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Students</h3>
                <div class="text-sm text-gray-500 mt-1">
                    Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">Aadhar: {{ $student->aadhar_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div>{{ $student->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->whatsapp_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->status === 'approved' ? 'bg-green-100 text-green-800' : ($student->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ optional($student->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Total Results</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_results'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Passed</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($stats['passed_results'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Failed</p>
                <p class="text-2xl font-bold text-red-600">{{ number_format($stats['failed_results'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <p class="text-sm font-medium text-gray-600">Avg Score</p>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['average_score'] ?? 0, 1) }}%</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Assessment Results</h3>
                <div class="text-sm text-gray-500 mt-1">
                    Showing {{ $results->firstItem() ?? 0 }} to {{ $results->lastItem() ?? 0 }} of {{ $results->total() ?? 0 }}
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assessment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course & Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Result</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->student?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->assessment?->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="font-medium">{{ $result->enrollment?->batch?->course?->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $result->enrollment?->batch?->batch_name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($result->percentage, 1) }}%</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ optional($result->completed_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">No assessment results found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($results->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
