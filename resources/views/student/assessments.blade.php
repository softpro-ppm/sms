@extends('layouts.student')

@section('title', 'Exam Results')
@section('page-title', 'Exams')

@section('content')
<div class="space-y-6">
    <!-- Available Re-assessments -->
    @if($reassessments->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Available Re-assessments</h3>
                    <span class="text-sm text-gray-500">{{ $reassessments->count() }} failed assessments</span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($reassessments as $reassessment)
                        <div class="flex items-center justify-between p-4 border border-orange-200 rounded-lg bg-orange-50">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $reassessment['assessment']->title }}</h4>
                                <p class="text-sm text-gray-500">{{ $reassessment['course']->name }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-redo mr-1"></i>
                                        Re-assessment Available
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('student.assessments.take', $reassessment['assessment']->id) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                    <i class="fas fa-redo mr-2"></i>
                                    Retake Exam
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Exam Results</h2>
                <p class="text-gray-600 mt-1">View all your assessment results and performance.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">Total: {{ $assessmentResults->total() }} assessments</span>
            </div>
        </div>
    </div>

    <!-- Exam Results List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($assessmentResults->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Score
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assessmentResults as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-purple-500 to-purple-600 flex items-center justify-center">
                                                <i class="fas fa-clipboard-check text-white"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $result->assessment->title ?? 'Exam' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $result->total_questions }} questions
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $result->enrollment->batch->course->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $result->enrollment->batch->batch_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $result->correct_answers }}/{{ $result->total_questions }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ number_format($result->percentage, 1) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $result->grade === 'A+' ? 'bg-green-100 text-green-800' : 
                                           ($result->grade === 'A' ? 'bg-blue-100 text-blue-800' : 
                                           ($result->grade === 'B' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-{{ $result->is_passed ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('student.assessments.show', $result) }}" 
                                       class="text-primary-600 hover:text-primary-900">
                                        <i class="fas fa-eye mr-1"></i>
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assessmentResults->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $assessmentResults->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300">
                    <i class="fas fa-clipboard-check text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No assessments completed</h3>
                <p class="mt-2 text-gray-500">You haven't completed any assessments yet.</p>
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

    <!-- Performance Summary -->
    @if($assessmentResults->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800">Average Score</p>
                            <p class="text-2xl font-bold text-blue-900">
                                {{ number_format($assessmentResults->avg('percentage'), 1) }}%
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Passed</p>
                            <p class="text-2xl font-bold text-green-900">
                                {{ $assessmentResults->where('is_passed', true)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Failed</p>
                            <p class="text-2xl font-bold text-red-900">
                                {{ $assessmentResults->where('is_passed', false)->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-trophy text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-purple-800">Best Grade</p>
                            <p class="text-2xl font-bold text-purple-900">
                                {{ $assessmentResults->max('grade') ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
