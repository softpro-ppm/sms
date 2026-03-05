@extends('layouts.admin')

@section('title', 'Create Certificate')
@section('page-title', 'Create Certificate')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Certificate</h2>
            <p class="text-gray-600 mt-1">Create a new certificate for a student</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.certificates.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Certificates
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Certificate Details</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.certificates.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Student <span class="text-red-500">*</span>
                        </label>
                        <select name="student_id" id="student_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('student_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->enrollment_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Course <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" id="course_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Batch
                        </label>
                        <select name="batch_id" id="batch_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('batch_id') border-red-500 @enderror">
                            <option value="">Select Batch (Optional)</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('batch_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="assessment_result_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Result
                        </label>
                        <select name="assessment_result_id" id="assessment_result_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('assessment_result_id') border-red-500 @enderror">
                            <option value="">Select Exam Result (Optional)</option>
                            @foreach($assessmentResults as $result)
                                <option value="{{ $result->id }}" {{ old('assessment_result_id') == $result->id ? 'selected' : '' }}>
                                    {{ $result->student->name }} - {{ $result->assessment->title ?? 'Exam' }} ({{ $result->percentage }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('assessment_result_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Only passed assessments are shown</p>
                    </div>
                </div>

                <div>
                    <label for="certificate_content" class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate Content
                    </label>
                    <textarea name="certificate_content" id="certificate_content" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('certificate_content') border-red-500 @enderror" 
                              placeholder="Enter custom certificate content (optional)...">{{ old('certificate_content') }}</textarea>
                    @error('certificate_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty to use default certificate template</p>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.certificates.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Create Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load batches when course changes
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const batchSelect = document.getElementById('batch_id');
        
        if (courseId) {
            fetch(`/admin/api/batches/by-course?course_id=${courseId}`)
                .then(response => response.json())
                .then(batches => {
                    batchSelect.innerHTML = '<option value="">Select Batch (Optional)</option>';
                    batches.forEach(batch => {
                        const option = document.createElement('option');
                        option.value = batch.id;
                        option.textContent = batch.name;
                        batchSelect.appendChild(option);
                    });
                });
        } else {
            batchSelect.innerHTML = '<option value="">Select Batch (Optional)</option>';
        }
    });
</script>
@endsection
