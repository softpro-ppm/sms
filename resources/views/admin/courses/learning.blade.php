@extends('layouts.admin')

@section('title', 'Learning: '.$course->name)
@section('page-title', 'Course learning (LMS)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $course->name }}</h2>
            <p class="text-gray-600 mt-1">Modules and lessons stored in the database. Edit content in future releases or re-seed via artisan.</p>
        </div>
        <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Course details
        </a>
    </div>

    @if($modules->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-4 text-sm">
            No learning modules yet. Run: <code class="bg-amber-100 px-1 rounded">php artisan db:seed --class=MSOfficeCourseSeeder</code> after migrating.
        </div>
    @else
        @foreach($modules as $module)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                    @if($module->summary)
                        <p class="text-sm text-gray-600 mt-1">{{ $module->summary }}</p>
                    @endif
                </div>
                <ul class="divide-y divide-gray-100">
                    @foreach($module->lessons as $lesson)
                        <li class="px-4 py-2 text-sm text-gray-800 flex justify-between gap-4">
                            <span>{{ $lesson->title }}</span>
                            @if($lesson->estimated_minutes)
                                <span class="text-gray-500 shrink-0">{{ $lesson->estimated_minutes }} min</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif
</div>
@endsection
