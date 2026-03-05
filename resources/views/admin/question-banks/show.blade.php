@extends('layouts.admin')

@section('title', 'Question Details')
@section('page-title', 'Question Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Question Details</h2>
            <p class="text-gray-600 mt-1">View question information</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.question-banks.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Questions
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Question Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Question Information</h3>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Question Text:</h4>
                        <p class="text-gray-800 text-lg leading-relaxed">{{ $questionBank->question_text }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 border border-gray-200 rounded-lg {{ $questionBank->correct_answer == 'A' ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                            <h5 class="font-semibold text-gray-900 mb-2">Option A:</h5>
                            <div class="flex items-center justify-between">
                                <p class="text-gray-800 {{ $questionBank->correct_answer == 'A' ? 'font-semibold text-green-800' : '' }}">
                                    {{ $questionBank->option_a }}
                                </p>
                                @if($questionBank->correct_answer == 'A')
                                    <i class="fas fa-check-circle text-green-600"></i>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg {{ $questionBank->correct_answer == 'B' ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                            <h5 class="font-semibold text-gray-900 mb-2">Option B:</h5>
                            <div class="flex items-center justify-between">
                                <p class="text-gray-800 {{ $questionBank->correct_answer == 'B' ? 'font-semibold text-green-800' : '' }}">
                                    {{ $questionBank->option_b }}
                                </p>
                                @if($questionBank->correct_answer == 'B')
                                    <i class="fas fa-check-circle text-green-600"></i>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg {{ $questionBank->correct_answer == 'C' ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                            <h5 class="font-semibold text-gray-900 mb-2">Option C:</h5>
                            <div class="flex items-center justify-between">
                                <p class="text-gray-800 {{ $questionBank->correct_answer == 'C' ? 'font-semibold text-green-800' : '' }}">
                                    {{ $questionBank->option_c }}
                                </p>
                                @if($questionBank->correct_answer == 'C')
                                    <i class="fas fa-check-circle text-green-600"></i>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg {{ $questionBank->correct_answer == 'D' ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                            <h5 class="font-semibold text-gray-900 mb-2">Option D:</h5>
                            <div class="flex items-center justify-between">
                                <p class="text-gray-800 {{ $questionBank->correct_answer == 'D' ? 'font-semibold text-green-800' : '' }}">
                                    {{ $questionBank->option_d }}
                                </p>
                                @if($questionBank->correct_answer == 'D')
                                    <i class="fas fa-check-circle text-green-600"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Question Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Question Metadata</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Course</h6>
                        <p class="mt-1 text-sm text-gray-900">{{ $questionBank->course->name }}</p>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Subject</h6>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $questionBank->subject }}
                        </span>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Difficulty Level</h6>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $questionBank->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : 
                               ($questionBank->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($questionBank->difficulty_level) }}
                        </span>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Status</h6>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $questionBank->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $questionBank->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Correct Answer</h6>
                        <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $questionBank->correct_answer }}
                        </span>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Created</h6>
                        <p class="mt-1 text-sm text-gray-900">{{ $questionBank->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Last Updated</h6>
                        <p class="mt-1 text-sm text-gray-900">{{ $questionBank->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.question-banks.edit', $questionBank) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Question
                    </a>
                    
                    <form action="{{ route('admin.question-banks.toggle-status', $questionBank) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 {{ $questionBank->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-{{ $questionBank->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $questionBank->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.question-banks.destroy', $questionBank) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this question?')"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Question
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection