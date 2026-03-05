@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ $user->name }}!</h2>
                <p class="text-primary-100 mt-1">Here's what's happening with your courses and progress.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">My Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_enrollments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Available Exams</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['available_assessments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Certificates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['certificates_earned'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Enrollments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">My Courses</h3>
                    <a href="{{ route('student.enrollments') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($enrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($enrollments->take(3) as $enrollment)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $enrollment->batch->course->name }}</h4>
                                    @if($enrollment->batch)
                                        <p class="text-sm text-gray-500">Batch: {{ $enrollment->batch->name }}</p>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Enrolled</p>
                                    <p class="text-xs text-gray-400">{{ $enrollment->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-book text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No courses enrolled yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Recent Payments</h3>
                    <a href="{{ route('student.payments') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($payments->take(3) as $payment)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">₹{{ number_format($payment->amount) }}</h4>
                                    <p class="text-sm text-gray-500">{{ $payment->payment_type }}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $payment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                           ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-credit-card text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No payments found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Available Exams -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Available Exams</h3>
                    <a href="{{ route('student.assessments') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($availableAssessments->count() > 0)
                    <div class="space-y-4">
                        @foreach($availableAssessments->take(3) as $assessmentData)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $assessmentData['assessment']->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $assessmentData['course']->name }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($assessmentData['is_reassessment'] ?? false)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-redo mr-1"></i>
                                                Re-assessment
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Ready to Take
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('student.assessments.take', $assessmentData['assessment']->id) }}" 
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white {{ ($assessmentData['is_reassessment'] ?? false) ? 'bg-orange-600 hover:bg-orange-700' : 'bg-primary-600 hover:bg-primary-700' }}">
                                        {{ ($assessmentData['is_reassessment'] ?? false) ? 'Retake Exam' : 'Take Exam' }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-check text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No assessments available yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Exams -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Pending Exams</h3>
                    <span class="text-sm text-gray-500">{{ $stats['pending_assessments'] }} pending</span>
                </div>
            </div>
            <div class="p-6">
                @if($pendingAssessments->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingAssessments->take(3) as $assessmentData)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $assessmentData['assessment']->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $assessmentData['course']->name }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $assessmentData['days_remaining'] }} days left
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-gray-500">Batch ends: {{ $assessmentData['batch']->end_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No pending assessments</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Exam Results -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Exam Results</h3>
                    <a href="{{ route('student.assessments') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($assessmentResults->count() > 0)
                    <div class="space-y-4">
                        @foreach($assessmentResults->take(3) as $result)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $result->assessment->title ?? 'Exam' }}</h4>
                                    <p class="text-sm text-gray-500">{{ $result->enrollment->batch->course->name }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $result->grade === 'A+' ? 'bg-green-100 text-green-800' : 
                                               ($result->grade === 'A' ? 'bg-blue-100 text-blue-800' : 
                                               ($result->grade === 'B' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                            {{ $result->grade }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ number_format($result->percentage, 1) }}%</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-check text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No assessments completed yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Certificates -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">My Certificates</h3>
                    <a href="{{ route('student.certificates') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($certificates->count() > 0)
                    <div class="space-y-4">
                        @foreach($certificates->take(3) as $certificate)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $certificate->course->name }}</h4>
                                    @if($certificate->batch)
                                        <p class="text-sm text-gray-500">Batch: {{ $certificate->batch->name }}</p>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $certificate->is_issued ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <i class="fas fa-{{ $certificate->is_issued ? 'check-circle' : 'clock' }} mr-1"></i>
                                        {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    @if($certificate->is_issued)
                                        <a href="{{ route('student.certificates.download', $certificate) }}" 
                                           class="text-primary-600 hover:text-primary-700">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <p class="text-sm text-gray-500">{{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-certificate text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No certificates earned yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('student.profile') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Update Profile</span>
                </a>

                <a href="{{ route('student.enrollments') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-book text-green-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">View Courses</span>
                </a>

                <a href="{{ route('student.payments') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-credit-card text-yellow-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Payment History</span>
                </a>

                <a href="{{ route('student.certificates') }}" 
                   class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-certificate text-purple-600"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">My Certificates</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
