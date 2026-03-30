@extends('layouts.student')

@section('title', 'Lessons: '.$course->name)
@section('page-title', 'Course lessons')

@section('content')
@php
    $pct = $progress ? (float) $progress['percent'] : 100;
@endphp
<div class="max-w-3xl mx-auto space-y-6 pb-8">
    {{-- Progress + exam readiness --}}
    <div class="rounded-2xl bg-gradient-to-br from-slate-900 via-primary-900 to-primary-800 text-white shadow-xl overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-primary-200 text-sm font-medium uppercase tracking-wide">Your progress</p>
                    <h2 class="text-2xl sm:text-3xl font-bold mt-1">{{ $course->name }}</h2>
                    <p class="text-primary-100/90 text-sm mt-2">Enrollment <span class="font-mono">{{ $enrollment->enrollment_number }}</span>
                        @if($enrollment->batch?->batch_name)
                            · <span>{{ $enrollment->batch->batch_name }}</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('student.enrollments') }}" class="shrink-0 inline-flex items-center text-sm font-medium text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i> My courses
                </a>
            </div>

            @if($progress)
                <div class="mt-8">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-primary-100">Course completion</span>
                        <span class="font-semibold tabular-nums">{{ $progress['completed'] }} / {{ $progress['total'] }} lessons · {{ number_format($pct, 0) }}%</span>
                    </div>
                    <div class="h-3 rounded-full bg-black/25 overflow-hidden ring-1 ring-white/10">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-300 transition-all duration-500 ease-out"
                             style="width: {{ min(100, $pct) }}%"></div>
                    </div>
                </div>
            @else
                <p class="mt-6 text-sm text-primary-100/80">This course has no numbered online lessons in the catalogue yet.</p>
            @endif

            <div class="mt-8 pt-6 border-t border-white/15">
                <p class="text-sm font-semibold text-primary-100 mb-3">Unlock final exam</p>
                <ul class="grid sm:grid-cols-2 gap-2 text-sm">
                    @include('student.learn.partials.exam-readiness-row', ['variant' => 'dark', 'ok' => $checklist['institute_eligible'], 'label' => 'Institute marked you exam-eligible'])
                    @include('student.learn.partials.exam-readiness-row', ['variant' => 'dark', 'ok' => $checklist['fee_fully_paid'], 'label' => 'Course fee fully paid'])
                    @include('student.learn.partials.exam-readiness-row', ['variant' => 'dark', 'ok' => $checklist['batch_ended'], 'label' => 'Batch end date reached'])
                    @include('student.learn.partials.exam-readiness-row', ['variant' => 'dark', 'ok' => $checklist['within_exam_window'], 'label' => 'Within exam window (1 yr after batch end)'])
                    @include('student.learn.partials.exam-readiness-row', ['variant' => 'dark', 'ok' => $checklist['online_lessons_complete'], 'label' => $checklist['lms_progress'] ? 'All online lessons completed' : 'Online lessons (none for this course)'])
                </ul>
                @if($checklist['can_take'])
                    <p class="mt-4 text-emerald-300 text-sm font-medium flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> You can take the exam from the Exams section.
                    </p>
                @endif
            </div>
        </div>
    </div>

    @if($course->description)
        <p class="text-sm text-gray-600 px-1">{{ $course->description }}</p>
    @endif

    <div class="space-y-5">
        @foreach($modules as $modIndex => $module)
            @php
                $lessons = $module->lessons;
                $doneInMod = $lessons->filter(fn ($l) => $completedLessonIds->has($l->id))->count();
                $modTotal = $lessons->count();
                $modPct = $modTotal > 0 ? round(100 * $doneInMod / $modTotal) : 0;
            @endphp
            <div class="rounded-xl bg-white border border-gray-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold text-primary-600 uppercase tracking-wide">Module {{ $modIndex + 1 }}</p>
                            <h3 class="text-lg font-bold text-gray-900 mt-0.5">{{ $module->title }}</h3>
                            @if($module->summary)
                                <p class="text-sm text-gray-600 mt-1">{{ $module->summary }}</p>
                            @endif
                        </div>
                        @if($modTotal > 0)
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs font-medium text-gray-500 tabular-nums">{{ $doneInMod }}/{{ $modTotal }}</span>
                                <div class="w-20 h-2 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-primary-500 transition-all" style="width: {{ $modPct }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach($lessons as $i => $lesson)
                        @php $done = $completedLessonIds->has($lesson->id); @endphp
                        <li>
                            <a href="{{ route('student.learn.lesson', [$enrollment, $lesson]) }}"
                               class="flex items-center gap-4 px-5 py-4 hover:bg-primary-50/60 transition group">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $done ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500 group-hover:bg-primary-100 group-hover:text-primary-700' }}">
                                    @if($done)
                                        <i class="fas fa-check"></i>
                                    @else
                                        <span class="text-sm font-semibold tabular-nums">{{ $i + 1 }}</span>
                                    @endif
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 group-hover:text-primary-800">{{ $lesson->title }}</p>
                                    @if($lesson->estimated_minutes)
                                        <p class="text-xs text-gray-500 mt-0.5"><i class="far fa-clock mr-1"></i>~{{ $lesson->estimated_minutes }} min</p>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary-400 text-sm shrink-0"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>
@endsection
