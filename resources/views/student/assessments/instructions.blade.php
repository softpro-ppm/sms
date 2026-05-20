@extends('layouts.student')

@section('title', 'Exam Instructions')
@section('page-title', 'Exam Instructions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6 sm:px-8 sm:py-7 border-b border-gray-100">
            <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">
                <i class="fas fa-clipboard-list text-[11px]"></i>
                Assessment
            </span>
            <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-semibold text-gray-950">{{ $assessment->title }}</h2>
                    @if($assessment->description)
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ $assessment->description }}</p>
                    @endif
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:min-w-[420px]">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Time limit</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ $assessment->time_limit_minutes }}</p>
                        <p class="text-sm text-gray-500">minutes</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Questions</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ $assessment->total_questions ?? 25 }}</p>
                        <p class="text-sm text-gray-500">multiple choice</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Pass mark</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-950">{{ $assessment->passing_percentage }}%</p>
                        <p class="text-sm text-gray-500">required</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">
            <div class="grid gap-6 lg:grid-cols-[1.35fr_0.9fr]">
                <div class="rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-950">Before you start</h3>
                    </div>
                    <div class="px-5 py-5 space-y-5">
                        @php
                            $steps = [
                                [
                                    'title' => 'Time management',
                                    'body' => 'The timer starts as soon as you begin. Once time ends, the assessment is submitted automatically.',
                                ],
                                [
                                    'title' => 'Question format',
                                    'body' => 'All questions are multiple choice. Choose the best answer for each question and review before you submit.',
                                ],
                                [
                                    'title' => 'Answer changes',
                                    'body' => 'You can move between questions and update your answers any time before the final submission.',
                                ],
                                [
                                    'title' => 'Technical caution',
                                    'body' => 'Keep a stable internet connection and avoid refreshing or closing the tab during the assessment.',
                                ],
                            ];
                        @endphp

                        @foreach($steps as $index => $step)
                            <div class="flex gap-4">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $step['title'] }}</h4>
                                    <p class="mt-1 text-sm leading-6 text-gray-600">{{ $step['body'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50/60">
                    <div class="border-b border-amber-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-amber-950">Rules</h3>
                    </div>
                    <div class="px-5 py-5">
                        <ul class="space-y-3 text-sm leading-6 text-amber-900">
                            <li class="flex gap-3">
                                <i class="fas fa-ban mt-1 text-xs text-amber-700"></i>
                                <span>Do not use outside notes, books, or other resources while taking the assessment.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fas fa-ban mt-1 text-xs text-amber-700"></i>
                                <span>Do not refresh, close, or switch away from the assessment window unless necessary.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fas fa-ban mt-1 text-xs text-amber-700"></i>
                                <span>Do not communicate with others or share questions during the assessment.</span>
                            </li>
                            <li class="flex gap-3">
                                <i class="fas fa-ban mt-1 text-xs text-amber-700"></i>
                                <span>Keep your certificate and result eligibility safe by completing the assessment honestly.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-5 py-5 sm:px-6">
                <label class="flex items-start gap-3 text-sm leading-6 text-gray-700">
                    <input type="checkbox" id="agreeTerms" class="mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>I have read the instructions and I am ready to begin this assessment.</span>
                </label>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">Once you start, the timer begins immediately and cannot be paused.</p>
                    <button id="startExamBtn"
                            disabled
                            class="inline-flex items-center justify-center rounded-xl bg-gray-300 px-5 py-3 text-sm font-semibold text-white transition disabled:cursor-not-allowed"
                            onclick="startExam()">
                        <i class="fas fa-play mr-2 text-xs"></i>
                        Start assessment
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('agreeTerms').addEventListener('change', function() {
    const startBtn = document.getElementById('startExamBtn');
    if (this.checked) {
        startBtn.disabled = false;
        startBtn.className = 'inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-700';
    } else {
        startBtn.disabled = true;
        startBtn.className = 'inline-flex items-center justify-center rounded-xl bg-gray-300 px-5 py-3 text-sm font-semibold text-white transition disabled:cursor-not-allowed';
    }
});

function startExam() {
    if (document.getElementById('agreeTerms').checked) {
        if (confirm('Are you ready to start the assessment? Once you begin, the timer will start and cannot be paused.')) {
            window.location.href = "{{ route('student.assessments.start', $assessment) }}";
        }
    }
}
</script>
@endsection
