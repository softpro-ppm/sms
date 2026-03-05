@extends('layouts.admin')

@section('title', 'Result Details')
@section('page-title', 'Result Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Result Details</h2>
            <p class="text-gray-600 mt-1">Detailed view of assessment result</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.results.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Results
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Result Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student & Exam Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Student & Exam Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Student Details</h4>
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                                    <span class="text-lg font-medium text-white">{{ substr($result->student->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">{{ $result->student->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $result->student->enrollment_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $result->student->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Exam Details</h4>
                            <div class="space-y-2">
                                <p class="text-lg font-semibold text-gray-900">{{ $result->assessment->title }}</p>
                                <p class="text-sm text-gray-500">{{ $result->enrollment->course->name }}</p>
                                <p class="text-sm text-gray-500">Attempt #{{ $result->attempt_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Performance Summary</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $result->correct_answers }}</div>
                            <div class="text-sm text-gray-500">Correct Answers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $result->wrong_answers }}</div>
                            <div class="text-sm text-gray-500">Wrong Answers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ number_format($result->percentage, 1) }}%</div>
                            <div class="text-sm text-gray-500">Percentage</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $result->total_marks }}</div>
                            <div class="text-sm text-gray-500">Total Marks</div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Overall Progress</span>
                            <span>{{ number_format($result->percentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $result->percentage }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject-wise Performance -->
            @if($result->subject_wise_marks && count($result->subject_wise_marks) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Subject-wise Performance</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($result->subject_wise_marks as $subject => $marks)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $subject }}</h4>
                                <span class="text-sm text-gray-500">{{ $marks['correct'] }}/{{ $marks['total'] }} correct</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Score: {{ $marks['marks'] }}/{{ $marks['total'] * 5 }} marks</span>
                                <span>{{ number_format(($marks['correct'] / $marks['total']) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full" 
                                     style="width: {{ ($marks['correct'] / $marks['total']) * 100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Result Summary Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Result Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="text-4xl font-bold {{ $result->is_passed ? 'text-green-600' : 'text-red-600' }}">
                            {{ $result->grade }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">Final Grade</div>
                    </div>

                    <div class="text-center">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                            {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-{{ $result->is_passed ? 'check-circle' : 'times-circle' }} mr-2"></i>
                            {{ $result->passing_status }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Total Questions:</span>
                            <span class="text-sm font-medium">{{ $result->total_questions }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Time Taken:</span>
                            <span class="text-sm font-medium">{{ $result->time_taken_formatted }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Started At:</span>
                            <span class="text-sm font-medium">{{ $result->started_at ? $result->started_at->format('M d, Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Completed At:</span>
                            <span class="text-sm font-medium">{{ $result->completed_at ? $result->completed_at->format('M d, Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.students.show', $result->student) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        View Student
                    </a>
                    
                    <a href="{{ route('admin.assessments.show', $result->assessment) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        View Exam
                    </a>

                    @if($result->is_passed)
                    <a href="#" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-certificate mr-2"></i>
                        Generate Certificate
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
