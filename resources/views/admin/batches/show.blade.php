@extends('layouts.admin')

@section('title', 'Batch Details')
@section('page-title', 'Batch Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $batch->batch_name }}</h2>
            <p class="text-gray-600 mt-1">{{ $batch->course->name }} • Batch ID: #{{ $batch->id }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            @if(!$batch->is_full)
            <a href="{{ route('admin.batches.enroll', $batch) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-user-plus mr-2"></i>
                Add/Enroll Students
            </a>
            @endif
            <a href="{{ route('admin.batches.edit', $batch) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>
                Edit Batch
            </a>
            <a href="{{ route('admin.batches.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Batches
            </a>
        </div>
    </div>

    <!-- Batch Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Course Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Course Details</h3>
                    <p class="text-sm text-gray-600">Course Information</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600">Course Name</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->course->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Fee</p>
                    <p class="text-lg font-semibold text-green-600">₹{{ number_format($batch->course->total_fee) }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Course Duration</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->course->duration_days ?? 'N/A' }} days</p>
                </div>
            </div>
        </div>

        <!-- Schedule Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Schedule</h3>
                    <p class="text-sm text-gray-600">Batch Timeline</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600">Start Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">End Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->end_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Duration</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->start_date->diffInDays($batch->end_date) }} days</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Status</p>
                    @if($batch->start_date > now())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-clock mr-1"></i>Upcoming
                        </span>
                    @elseif($batch->end_date < now())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-check-circle mr-1"></i>Completed
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-play-circle mr-1"></i>Running
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Students</h3>
                    <p class="text-sm text-gray-600">Enrollment Info</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600">Enrolled Students</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $batch->enrollments->count() }}</p>
                </div>
                @if($batch->max_students)
                    <div>
                        <p class="text-sm font-medium text-gray-600">Maximum Capacity</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $batch->max_students }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available Slots</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $batch->max_students - $batch->enrollments->count() }}</p>
                    </div>
                    @if($batch->enrollments->count() >= $batch->max_students)
                        <div class="p-2 bg-red-50 rounded-lg">
                            <p class="text-sm font-medium text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Batch Full
                            </p>
                        </div>
                    @endif
                @else
                    <div>
                        <p class="text-sm font-medium text-gray-600">Capacity</p>
                        <p class="text-lg font-semibold text-gray-900">Unlimited</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enrolled Students Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Enrolled Students ({{ $batch->enrollments->count() }})</h3>
        </div>
        
        @if($batch->enrollments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enrollment Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($batch->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-white font-semibold text-sm">{{ substr($enrollment->student->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->full_name }}</div>
                                    <div class="text-sm text-gray-500">Aadhar: {{ $enrollment->student->aadhar_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $enrollment->student->email }}</div>
                            <div class="text-sm text-gray-500">{{ $enrollment->student->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $enrollment->enrollment_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $enrollment->enrollment_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : ($enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                <i class="fas {{ $enrollment->status === 'active' ? 'fa-check-circle' : ($enrollment->status === 'completed' ? 'fa-graduation-cap' : 'fa-times-circle') }} mr-1"></i>
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.students.show', $enrollment->student) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                   title="View Student">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($enrollment->status === 'active')
                                <button onclick="dropStudent({{ $enrollment->id }})" 
                                        class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                        title="Drop Student">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                                @endif
                                <button onclick="removeStudent({{ $enrollment->id }})" 
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                        title="Remove Student">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-users text-4xl mb-4"></i>
                <p class="text-lg font-medium">No students enrolled</p>
                <p class="text-sm">Students will appear here once they enroll in this batch.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function dropStudent(enrollmentId) {
        if (confirm('Are you sure you want to drop this student from the batch? This will mark the enrollment as dropped but keep the data.')) {
            // Create a form to submit via POST with _method PATCH
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/enrollments/${enrollmentId}/drop`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            form.appendChild(methodField);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    }

    function removeStudent(enrollmentId) {
        if (confirm('⚠️ WARNING: This will permanently remove the student from this batch and ALL related data (payments, assessment results, certificates). This action cannot be undone!\n\nType "DELETE" in the next prompt to confirm.')) {
            const confirmation = prompt('Type DELETE to confirm permanent removal:');
            if (confirmation === 'DELETE') {
                // Create a form to submit via POST with _method DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/enrollments/${enrollmentId}/remove`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
            }
        }
    }
</script>
@endsection
