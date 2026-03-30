@extends('layouts.student')

@section('title', 'Lessons: '.$course->name)
@section('page-title', 'Course lessons')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $course->name }}</h2>
                <p class="text-gray-600 text-sm mt-1">Enrollment #{{ $enrollment->enrollment_number }} · {{ $enrollment->batch?->batch_name ?? '—' }}</p>
                @if($course->description)
                    <p class="text-gray-600 text-sm mt-2">{{ $course->description }}</p>
                @endif
            </div>
            <a href="{{ route('student.enrollments') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium shrink-0">← My courses</a>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($modules as $module)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                    @if($module->summary)
                        <p class="text-sm text-gray-600 mt-1">{{ $module->summary }}</p>
                    @endif
                </div>
                <ol class="divide-y divide-gray-100">
                    @foreach($module->lessons as $i => $lesson)
                        <li>
                            <a href="{{ route('student.learn.lesson', [$enrollment, $lesson]) }}"
                               class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-primary-50/50 transition">
                                <span class="text-sm font-medium text-gray-900">
                                    <span class="text-gray-400 mr-2">{{ $i + 1 }}.</span>{{ $lesson->title }}
                                </span>
                                @if($lesson->estimated_minutes)
                                    <span class="text-xs text-gray-500 shrink-0">~{{ $lesson->estimated_minutes }} min</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endforeach
    </div>
</div>
@endsection
