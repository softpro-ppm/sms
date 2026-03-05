@extends('layouts.admin')

@section('title', 'Exam Details')
@section('page-title', 'Exam Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $assessment->title }}</h2>
            <p class="text-gray-600">{{ $assessment->course->name ?? 'N/A' }} &bull; Exam ID: #{{ $assessment->id }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.assessments.edit', $assessment) }}" 
               class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit Exam
            </a>
            <a href="{{ route('admin.assessments.index') }}" 
               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Exams
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Exam Configuration -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-cog text-blue-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Exam Configuration</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Course</span>
                    <span class="text-sm font-medium text-gray-900">{{ $assessment->course->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Time Limit</span>
                    <span class="text-sm font-medium text-gray-900">{{ $assessment->time_limit_minutes }} minutes</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Questions per Exam</span>
                    <span class="text-sm font-medium text-gray-900">{{ $assessment->total_questions }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Marks per Question</span>
                    <span class="text-sm font-medium text-gray-900">4</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Marks</span>
                    <span class="text-sm font-medium text-gray-900">{{ $assessment->total_questions * 4 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Passing %</span>
                    <span class="text-sm font-medium text-gray-900">{{ $assessment->passing_percentage }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assessment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $assessment->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Question Bank Info -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-database text-purple-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Question Bank</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Questions Available</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_questions_in_bank'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Subjects</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_subjects'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Sets Generated</span>
                    <span class="text-sm font-medium text-gray-900">3 (auto-randomized)</span>
                </div>
                <div class="pt-2 border-t">
                    @if($stats['total_questions_in_bank'] >= $assessment->total_questions)
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="text-sm font-medium">Ready for exam</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-700">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span class="text-sm font-medium">Need at least {{ $assessment->total_questions }} questions</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-chart-bar text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-900">Results Summary</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Students Attempted</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['students_attempted'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Passed</span>
                    <span class="text-sm font-medium text-green-600">{{ $stats['students_passed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Failed</span>
                    <span class="text-sm font-medium text-red-600">{{ $stats['students_failed'] }}</span>
                </div>
                @if($stats['students_attempted'] > 0)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Pass Rate</span>
                    <span class="text-sm font-medium text-gray-900">{{ round(($stats['students_passed'] / $stats['students_attempted']) * 100, 1) }}%</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($assessment->description)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
        <p class="text-gray-600">{{ $assessment->description }}</p>
    </div>
    @endif

    <!-- Recent Results -->
    @if($assessment->assessmentResults->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($assessment->assessmentResults->sortByDesc('created_at')->take(20) as $result)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $result->student->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $result->total_marks ?? 0 }} / {{ $assessment->total_questions * 4 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($result->percentage ?? 0, 1) }}%</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $result->is_passed ? 'PASS' : 'FAIL' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $result->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Yet</h3>
        <p class="text-gray-600">No students have attempted this exam yet.</p>
    </div>
    @endif
</div>
@endsection
