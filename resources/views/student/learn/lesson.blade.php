@extends('layouts.student')

@section('title', $lesson->title)
@section('page-title', $course->name)

@section('content')
@php
    $pct = $progress ? (float) $progress['percent'] : 100;
@endphp
<div class="max-w-3xl mx-auto space-y-6 pb-8">
    <div class="flex flex-col gap-4">
        <a href="{{ route('student.learn.outline', $enrollment) }}" class="inline-flex w-fit items-center text-sm font-medium text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i> All lessons
        </a>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-700">
                        <i class="fas fa-book-open text-[10px]"></i>
                        Lesson
                    </span>
                    <h2 class="mt-3 text-xl font-semibold text-gray-950 sm:text-2xl">{{ $lesson->title }}</h2>
                    @if($lesson->estimated_minutes)
                        <p class="text-xs text-gray-500 mt-1"><i class="far fa-clock mr-1"></i>About {{ $lesson->estimated_minutes }} minutes</p>
                    @endif
                </div>
                <div class="flex flex-col items-stretch sm:items-end gap-2 shrink-0">
                    @if($isLessonComplete)
                        <span class="inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-sm font-medium border border-emerald-200">
                            <i class="fas fa-check-circle"></i> Completed
                        </span>
                    @else
                        <div class="flex flex-col items-stretch sm:items-end gap-1">
                            <form method="POST" action="{{ route('student.learn.lesson.complete', [$enrollment, $lesson]) }}">
                                @csrf
                                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700 sm:w-auto">
                                    <i class="fas fa-check"></i> Mark lesson complete
                                </button>
                            </form>
                            <p class="text-xs text-gray-500 text-center sm:text-right max-w-xs sm:max-w-sm">
                                Or use <strong>Next lesson</strong> at the bottom — it marks this lesson done and opens the next automatically.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            @if($progress)
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="flex justify-between text-xs text-gray-600 mb-1.5">
                        <span>Overall course progress</span>
                        <span class="font-semibold text-gray-800 tabular-nums">{{ $progress['completed'] }}/{{ $progress['total'] }} · {{ number_format($pct, 0) }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-primary-400 transition-all duration-500" style="width: {{ min(100, $pct) }}%"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($lesson->video_url)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="mb-2 text-sm font-semibold text-gray-800">Video</p>
            <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline text-sm break-all">{{ $lesson->video_url }}</a>
        </div>
    @endif

    <article class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 sm:p-8">
        <div class="max-w-none text-gray-800 leading-relaxed">
            {!! $lesson->body !!}
        </div>
    </article>

    @php
        $flat = collect();
        foreach ($modules as $m) {
            foreach ($m->lessons as $l) {
                $flat->push($l);
            }
        }
        $idx = $flat->search(fn ($l) => $l->id === $lesson->id);
        $prev = $idx > 0 ? $flat[$idx - 1] : null;
        $next = $idx !== false && $idx < $flat->count() - 1 ? $flat[$idx + 1] : null;
    @endphp
    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-2">
        @if($prev)
            <a href="{{ route('student.learn.lesson', [$enrollment, $prev]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 sm:justify-start">
                <i class="fas fa-arrow-left text-gray-400"></i> Previous lesson
            </a>
        @else
            <span></span>
        @endif
        @if($next)
            <form method="POST" action="{{ route('student.learn.lesson.continue', [$enrollment, $lesson, $next]) }}" class="inline w-full sm:w-auto">
                @csrf
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-medium text-primary-800 transition hover:bg-primary-100 sm:justify-end">
                    Next lesson <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
