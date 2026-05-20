@extends('layouts.student')

@section('title', 'Exam Results')
@section('page-title', 'Exams')

@section('content')
<div class="space-y-5">
    @if(isset($examStatusEnrollments) && $examStatusEnrollments->count() > 0)
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Eligibility</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Exam eligibility</h3>
                <p class="mt-1 text-sm text-slate-600">You can start or retake an exam only when every item below is satisfied for that enrollment.</p>
            </div>
            <div class="p-6 space-y-4">
                @foreach($examStatusEnrollments as $examEnr)
                    @php $ch = $examEnr->exam_eligibility_checklist; @endphp
                    <div class="rounded-2xl border {{ $ch['can_take'] ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-white' }} p-5">
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
        </section>
    @endif

    <!-- Available Re-assessments -->
    @if($reassessments->count() > 0)
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Re-assessments</div>
                        <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Available re-assessments</h3>
                    </div>
                    <span class="text-sm text-slate-500">{{ $reassessments->count() }} failed assessments</span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($reassessments as $reassessment)
                        <div class="flex items-center justify-between rounded-2xl border border-orange-200 bg-orange-50 p-4">
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
                                   class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                                    <i class="fas fa-redo mr-2"></i>
                                    Retake Exam
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Header -->
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-clipboard-check text-[10px]"></i>
                    Exams
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review your exam results and current exam status.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Check readiness, revisit passed and failed attempts, and open detailed result sheets for completed assessments.</p>
            </div>
            <div class="text-sm text-slate-500">{{ $assessmentResults->total() }} assessments</div>
        </div>
    </section>

    <!-- Exam Results List -->
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @if($assessmentResults->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Exam
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Course
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Score
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Grade
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Date
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assessmentResults as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-violet-50 text-violet-700">
                                                <i class="fas fa-clipboard-check"></i>
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
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $result->enrollment->display_course_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $result->enrollment->batch->batch_name }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $result->correct_answers }}/{{ $result->total_questions }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ number_format($result->percentage, 1) }}%
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                        {{ $result->grade === 'A+' ? 'bg-emerald-50 text-emerald-700' : 
                                           ($result->grade === 'A' ? 'bg-blue-50 text-blue-700' : 
                                           ($result->grade === 'B' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700')) }}">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                        {{ $result->is_passed ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        <i class="fas fa-{{ $result->is_passed ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-900">
                                    {{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('student.assessments.show', $result) }}" 
                                       class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                                        <i class="fas fa-eye text-xs"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($assessmentResults->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
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
                       class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Performance Summary -->
    @if($assessmentResults->count() > 0)
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-5">
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Summary</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Performance summary</h3>
            </div>
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-4">
                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
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
                
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
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
                
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
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
                
                <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4">
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
        </section>
    @endif
</div>
@endsection
