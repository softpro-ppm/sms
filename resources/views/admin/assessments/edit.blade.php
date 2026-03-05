@extends('layouts.admin')

@section('title', 'Edit Exam')
@section('page-title', 'Edit Exam')

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
                    <h2 class="text-xl font-semibold">Edit Exam</h2>
                    <p class="text-blue-100 text-sm">Update assessment details</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.assessments.update', $assessment) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" 
                               value="{{ old('title', $assessment->title) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                               placeholder="Enter exam title">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Optional description">{{ old('description', $assessment->description) }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Course Selection -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course</h3>
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Course <span class="text-red-500">*</span>
                    </label>
                    <select id="course_id" name="course_id" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('course_id') border-red-500 @enderror">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $assessment->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Exam Details -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="time_limit_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Duration (Minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="time_limit_minutes" name="time_limit_minutes" 
                               value="{{ old('time_limit_minutes', $assessment->time_limit_minutes) }}"
                               min="1" max="300"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('time_limit_minutes') border-red-500 @enderror">
                        @error('time_limit_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="total_questions" class="block text-sm font-medium text-gray-700 mb-2">
                            Total Questions <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="total_questions" name="total_questions" 
                               value="{{ old('total_questions', $assessment->total_questions) }}"
                               min="1" max="100"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_questions') border-red-500 @enderror">
                        @error('total_questions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="passing_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Passing % <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="passing_percentage" name="passing_percentage" 
                               value="{{ old('passing_percentage', $assessment->passing_percentage) }}"
                               min="1" max="100"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('passing_percentage') border-red-500 @enderror">
                        @error('passing_percentage') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                <select id="is_active" name="is_active" 
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('is_active') border-red-500 @enderror">
                    <option value="1" {{ old('is_active', $assessment->is_active) == true ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $assessment->is_active) == false ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.assessments.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update Exam
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
