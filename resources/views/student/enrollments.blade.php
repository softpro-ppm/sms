@extends('layouts.student')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">My Courses</h2>
                <p class="text-gray-600 mt-1">View all your enrolled courses and their progress.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: {{ $enrollments->total() }} courses</span>
            </div>
        </div>
    </div>

    <!-- Enrollments List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Batch
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Enrollment Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fees
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                                                <i class="fas fa-book text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $enrollment->batch->course->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Enrollment #{{ $enrollment->enrollment_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $enrollment->batch->batch_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $enrollment->batch->start_date->format('M d, Y') }} - 
                                        {{ $enrollment->batch->end_date->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $enrollment->enrollment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : 
                                           ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>Total: ₹{{ number_format($enrollment->total_fee) }}</div>
                                        <div class="text-green-600">Paid: ₹{{ number_format($enrollment->paid_amount) }}</div>
                                        @if($enrollment->outstanding_amount > 0)
                                            <div class="text-red-600">Due: ₹{{ number_format($enrollment->outstanding_amount) }}</div>
                                        @else
                                            <div class="text-green-600">Fully Paid</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($enrollments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300">
                    <i class="fas fa-book text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No courses enrolled</h3>
                <p class="mt-2 text-gray-500">You haven't enrolled in any courses yet.</p>
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
</div>
@endsection
