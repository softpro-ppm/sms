@extends('layouts.student')

@section('title', $lesson->title)
@section('page-title', $course->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('student.learn.outline', $enrollment) }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">← All lessons</a>
            <h2 class="text-xl font-bold text-gray-900 mt-2">{{ $lesson->title }}</h2>
            @if($lesson->estimated_minutes)
                <p class="text-xs text-gray-500 mt-1">About {{ $lesson->estimated_minutes }} minutes</p>
            @endif
        </div>
    </div>

    @if($lesson->video_url)
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Video</p>
            <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline break-all">{{ $lesson->video_url }}</a>
        </div>
    @endif

    <article class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">
        <div class="max-w-none text-gray-800">
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
    <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4">
        @if($prev)
            <a href="{{ route('student.learn.lesson', [$enrollment, $prev]) }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800">← Previous</a>
        @else
            <span></span>
        @endif
        @if($next)
            <a href="{{ route('student.learn.lesson', [$enrollment, $next]) }}" class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-800 sm:ml-auto">Next →</a>
        @endif
    </div>
</div>
@endsection
