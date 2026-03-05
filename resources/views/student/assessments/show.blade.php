@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'Exams')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Student Dashboard</h2>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-pink-600 text-sm"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">Baleti Janaki</span>
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </div>
        </div>
    </div>

    <!-- Exam Result Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $result->assessment->title }}</h3>
                <p class="text-gray-600 mt-1">{{ $result->enrollment->batch->course->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Completed on</div>
                <div class="font-semibold text-gray-900">{{ $result->completed_at ? $result->completed_at->format('M d, Y H:i') : 'N/A' }}</div>
            </div>
        </div>

        <!-- Score Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-percentage text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Score</h3>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($result->percentage, 1) }}%</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Correct</h3>
                <p class="text-2xl font-bold text-green-600">{{ $result->correct_answers }}/{{ $result->total_questions }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Incorrect</h3>
                <p class="text-2xl font-bold text-red-600">{{ $result->wrong_answers }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Time Taken</h3>
                <p class="text-2xl font-bold text-orange-600">{{ $result->time_taken_minutes }}m</p>
            </div>
        </div>

        <!-- Grade and Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade</h3>
                <div class="flex items-center">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-full text-lg font-bold
                        {{ $result->grade === 'A+' ? 'bg-green-100 text-green-800' : 
                           ($result->grade === 'A' ? 'bg-blue-100 text-blue-800' : 
                           ($result->grade === 'B' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                        {{ $result->grade }}
                    </span>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                <div class="flex items-center">
                    @if($result->is_passed)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            Passed
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-2"></i>
                            Failed
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Questions:</span>
                        <span class="font-medium">{{ $result->total_questions }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Marks:</span>
                        <span class="font-medium">{{ $result->total_marks }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Correct Answers:</span>
                        <span class="font-medium text-green-600">{{ $result->correct_answers }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Percentage:</span>
                        <span class="font-medium">{{ number_format($result->percentage, 1) }}%</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Wrong Answers:</span>
                        <span class="font-medium text-red-600">{{ $result->wrong_answers }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Time Taken:</span>
                        <span class="font-medium">{{ $result->time_taken_minutes }} minutes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
