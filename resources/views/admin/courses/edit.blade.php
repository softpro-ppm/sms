@extends('layouts.admin')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Edit Course</h2>
                    <p class="text-blue-100 text-sm">Update course information</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Course Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Course Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $course->name) }}"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                       placeholder="Enter course name">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
                          placeholder="Enter course description">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fees Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Fees</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Course Fee -->
                    <div>
                        <label for="course_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Course Fee (₹) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">₹</span>
                            </div>
                            <input type="number" 
                                   id="course_fee" 
                                   name="course_fee" 
                                   value="{{ old('course_fee', $course->course_fee) }}"
                                   min="0" 
                                   step="0.01"
                                   class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_fee') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('course_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Registration Fee -->
                    <div>
                        <label for="registration_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Registration Fee (₹) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">₹</span>
                            </div>
                            <input type="number" 
                                   id="registration_fee" 
                                   name="registration_fee" 
                                   value="{{ old('registration_fee', $course->registration_fee) }}"
                                   min="0" 
                                   step="0.01"
                                   class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('registration_fee') border-red-500 @enderror"
                                   placeholder="100.00">
                        </div>
                        @error('registration_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exam Fee -->
                    <div>
                        <label for="assessment_fee" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Fee (₹) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">₹</span>
                            </div>
                            <input type="number" 
                                   id="assessment_fee" 
                                   name="assessment_fee" 
                                   value="{{ old('assessment_fee', $course->assessment_fee) }}"
                                   min="0" 
                                   step="0.01"
                                   class="block w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('assessment_fee') border-red-500 @enderror"
                                   placeholder="100.00">
                        </div>
                        @error('assessment_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total Fee Display -->
                <div class="mt-4 p-4 bg-primary-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-primary-700">Total Course Fee:</span>
                        <span class="text-lg font-bold text-primary-900" id="total-fee-display">₹{{ number_format($course->total_fee, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                    Duration (Days)
                </label>
                <input type="number" 
                       id="duration_days" 
                       name="duration_days" 
                       value="{{ old('duration_days', $course->duration_days) }}"
                       min="1"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('duration_days') border-red-500 @enderror"
                       placeholder="Enter duration in days">
                @error('duration_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active Course
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Uncheck to deactivate this course</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.courses.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Course
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Calculate total fee dynamically
    function calculateTotalFee() {
        const courseFee = parseFloat(document.getElementById('course_fee').value) || 0;
        const registrationFee = parseFloat(document.getElementById('registration_fee').value) || 0;
        const assessmentFee = parseFloat(document.getElementById('assessment_fee').value) || 0;
        
        const total = courseFee + registrationFee + assessmentFee;
        document.getElementById('total-fee-display').textContent = '₹' + total.toFixed(2);
    }

    // Add event listeners
    document.getElementById('course_fee').addEventListener('input', calculateTotalFee);
    document.getElementById('registration_fee').addEventListener('input', calculateTotalFee);
    document.getElementById('assessment_fee').addEventListener('input', calculateTotalFee);

    // Calculate on page load
    calculateTotalFee();
</script>
@endsection
