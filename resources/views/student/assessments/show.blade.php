@extends('layouts.student')

@section('title', 'Exam Result')
@section('page-title', 'Exam Result')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-6 sm:px-8 sm:py-7 border-b border-gray-100">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-blue-700">
                        <i class="fas fa-square-poll-vertical text-[11px]"></i>
                        Assessment result
                    </span>
                    <h2 class="mt-4 text-2xl font-semibold text-gray-950">{{ $result->assessment->title }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ $result->enrollment->display_course_name }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4 text-sm text-gray-600">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Completed</p>
                    <p class="mt-2 font-semibold text-gray-950">{{ $result->completed_at ? $result->completed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Score</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ number_format($result->percentage, 1) }}%</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Correct</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-700">{{ $result->correct_answers }}</p>
                    <p class="text-sm text-gray-500">out of {{ $result->total_questions }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Incorrect</p>
                    <p class="mt-3 text-3xl font-semibold text-rose-700">{{ $result->wrong_answers }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Time taken</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-950">{{ $result->time_taken_minutes }}m</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.35fr]">
                <div class="rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-950">Outcome</h3>
                    </div>
                    <div class="px-5 py-5 space-y-5">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Grade</p>
                            <div class="mt-3 inline-flex h-12 min-w-12 items-center justify-center rounded-full bg-gray-900 px-4 text-lg font-semibold text-white">
                                {{ $result->grade }}
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Status</p>
                            @if($result->is_passed)
                                <span class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-800">
                                    <i class="fas fa-check-circle text-xs"></i>
                                    Passed
                                </span>
                            @else
                                <span class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-800">
                                    <i class="fas fa-circle-xmark text-xs"></i>
                                    Failed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-950">Result breakdown</h3>
                    </div>
                    <div class="px-5 py-5 grid gap-4 sm:grid-cols-2">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Total questions</span>
                                <span class="font-medium text-gray-950">{{ $result->total_questions }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Total marks</span>
                                <span class="font-medium text-gray-950">{{ $result->total_marks }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Correct answers</span>
                                <span class="font-medium text-emerald-700">{{ $result->correct_answers }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Percentage</span>
                                <span class="font-medium text-gray-950">{{ number_format($result->percentage, 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Wrong answers</span>
                                <span class="font-medium text-rose-700">{{ $result->wrong_answers }}</span>
                            </div>
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3 text-sm">
                                <span class="text-gray-600">Time taken</span>
                                <span class="font-medium text-gray-950">{{ $result->time_taken_minutes }} minutes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
