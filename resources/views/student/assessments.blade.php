@extends('layouts.student')

@section('title', 'Exam Results')
@section('page-title', 'Exams')

@section('content')
<div class="space-y-6">
    @if(isset($examStatusEnrollments) && $examStatusEnrollments->count() > 0)
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-slate-50 to-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white/80">
                <h3 class="text-lg font-semibold text-gray-900">Exam eligibility</h3>
                <p class="text-sm text-gray-600 mt-1">You can start or retake an exam only when every item below is satisfied for that enrollment.</p>
            </div>
            <div class="p-6 space-y-4">
                @foreach($examStatusEnrollments as $examEnr)
                    @php $ch = $examEnr->exam_eligibility_checklist; @endphp
                    <div class="rounded-xl border {{ $ch['can_take'] ? 'border-emerald-200 bg-emerald-50/40' : 'border-gray-200 bg-white' }} p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $examEnr->display_course_name }}</h4>
                                @if($examEnr->batch?->batch_name)
                                    <p class="text-sm text-gray-500">{{ $examEnr->batch->batch_name }}</p>
                                @endif
                            </div>
                            @if($ch['can_take'])
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 w-fit">
                                    <i class="fas fa-unlock"></i> Ready for exam
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-900 w-fit">
                                    <i class="fas fa-lock"></i> Not yet eligible
                                </span>
                            @endif
                        </div>
                        <ul class="grid sm:grid-cols-2 gap-2 text-sm">
                            @include('student.learn.partials.exam-readiness-row', ['variant' => 'light', 'ok' => $ch['institute_eligible'], 'label' => 'Institute marked exam-eligible'])
                            @include('student.learn.partials.exam-readiness-row', ['variant' => 'light', 'ok' => $ch['fee_fully_paid'], 'label' => 'Fees fully paid'])
                            @include('student.learn.partials.exam-readiness-row', ['variant' => 'light', 'ok' => $ch['batch_ended'], 'label' => 'Batch end date passed'])
                            @include('student.learn.partials.exam-readiness-row', ['variant' => 'light', 'ok' => $ch['within_exam_window'], 'label' => 'Inside exam window (1 year after batch end)'])
                            @include('student.learn.partials.exam-readiness-row', ['variant' => 'light', 'ok' => $ch['online_lessons_complete'], 'label' => $ch['lms_progress'] ? 'All online lessons done ('.$ch['lms_progress']['completed'].'/'.$ch['lms_progress']['total'].')' : 'Online lessons (none or N/A)'])
                        </ul>
                        @if($ch['is_legacy'])
                            <p class="mt-3 text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">Legacy enrollment: online exams may not apply. Contact your centre if you need help.</p>
                        @endif
                        @if(! $ch['online_lessons_complete'] && $ch['lms_progress'])
                            <a href="{{ route('student.enrollments') }}" class="mt-4 inline-flex text-sm font-medium text-primary-600 hover:text-primary-800">
                                <i class="fas fa-book-reader mr-2"></i> Continue lessons under My courses
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
                                <p class="text-sm text-gray-500">{{ $reassessment['display_course_name'] }}</p>
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
                                        {{ $result->enrollment->display_course_name }}
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
