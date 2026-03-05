@extends('layouts.admin')

@section('title', 'Edit Question')
@section('page-title', 'Edit Question')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Question</h2>
            <p class="text-gray-600 mt-1">Update question information</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.question-banks.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Questions
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Question Details</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.question-banks.update', $questionBank) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Course <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" id="course_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $questionBank->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="subject" id="subject" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subject') border-red-500 @enderror" 
                               value="{{ old('subject', $questionBank->subject) }}" placeholder="e.g., MS Word, MS Excel" required>
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Question <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question_text" id="question_text" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('question_text') border-red-500 @enderror" 
                              placeholder="Enter the question text..." required>{{ old('question_text', $questionBank->question_text) }}</textarea>
                    @error('question_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="option_a" class="block text-sm font-medium text-gray-700 mb-2">
                            Option A <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="option_a" id="option_a" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('option_a') border-red-500 @enderror" 
                               value="{{ old('option_a', $questionBank->option_a) }}" required>
                        @error('option_a')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="option_b" class="block text-sm font-medium text-gray-700 mb-2">
                            Option B <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="option_b" id="option_b" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('option_b') border-red-500 @enderror" 
                               value="{{ old('option_b', $questionBank->option_b) }}" required>
                        @error('option_b')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="option_c" class="block text-sm font-medium text-gray-700 mb-2">
                            Option C <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="option_c" id="option_c" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('option_c') border-red-500 @enderror" 
                               value="{{ old('option_c', $questionBank->option_c) }}" required>
                        @error('option_c')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="option_d" class="block text-sm font-medium text-gray-700 mb-2">
                            Option D <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="option_d" id="option_d" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('option_d') border-red-500 @enderror" 
                               value="{{ old('option_d', $questionBank->option_d) }}" required>
                        @error('option_d')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-2">
                            Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <select name="correct_answer" id="correct_answer" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('correct_answer') border-red-500 @enderror" 
                                required>
                            <option value="">Select Correct Answer</option>
                            <option value="A" {{ old('correct_answer', $questionBank->correct_answer) == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('correct_answer', $questionBank->correct_answer) == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('correct_answer', $questionBank->correct_answer) == 'C' ? 'selected' : '' }}>C</option>
                            <option value="D" {{ old('correct_answer', $questionBank->correct_answer) == 'D' ? 'selected' : '' }}>D</option>
                        </select>
                        @error('correct_answer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Difficulty Level <span class="text-red-500">*</span>
                        </label>
                        <select name="difficulty_level" id="difficulty_level" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('difficulty_level') border-red-500 @enderror" 
                                required>
                            <option value="easy" {{ old('difficulty_level', $questionBank->difficulty_level) == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ old('difficulty_level', $questionBank->difficulty_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ old('difficulty_level', $questionBank->difficulty_level) == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                        @error('difficulty_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.question-banks.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection