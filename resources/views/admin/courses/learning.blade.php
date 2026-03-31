@extends('layouts.admin')

@section('title', 'Learning: '.$course->name)
@section('page-title', 'Course learning (LMS)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $course->name }}</h2>
            <p class="text-gray-600 mt-1">
                @if(auth()->user()->is_super_admin)
                    Add modules and lessons here. Students only see items marked <span class="font-medium text-gray-800">active</span>.
                @else
                    View-only outline. Catalogue LMS is maintained by a super admin.
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->is_super_admin)
                <a href="{{ route('admin.courses.learning.modules.create', $course) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-sm font-medium shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Add module
                </a>
            @endif
            <a href="{{ route('admin.courses.show', $course) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Course details
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-900 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($modules->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-4 text-sm space-y-2">
            <p>No learning modules yet.</p>
            @if(auth()->user()->is_super_admin)
                <p>
                    <a href="{{ route('admin.courses.learning.modules.create', $course) }}" class="font-semibold text-amber-950 underline">Create the first module</a>
                    or seed sample content: <code class="bg-amber-100 px-1 rounded">php artisan db:seed --class=MSOfficeCourseSeeder</code>
                </p>
            @else
                <p>A super admin can add modules and lessons, or run the sample seeder on the server.</p>
            @endif
        </div>
    @else
        @foreach($modules as $module)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden {{ !$module->is_active ? 'opacity-90 ring-1 ring-amber-200' : '' }}">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                            @if(!$module->is_active)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">Inactive</span>
                            @endif
                            <span class="text-xs text-gray-500">order {{ $module->sort_order }}</span>
                        </div>
                        @if($module->summary)
                            <p class="text-sm text-gray-600 mt-1">{{ $module->summary }}</p>
                        @endif
                    </div>
                    @if(auth()->user()->is_super_admin)
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <a href="{{ route('admin.courses.learning.lessons.create', [$course, $module]) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-300 text-gray-800 hover:bg-gray-50">
                                <i class="fas fa-file-alt mr-1.5"></i> Add lesson
                            </a>
                            <a href="{{ route('admin.courses.learning.modules.edit', [$course, $module]) }}"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-gray-300 text-gray-800 hover:bg-gray-50">
                                <i class="fas fa-edit mr-1.5"></i> Edit module
                            </a>
                            <form method="POST" action="{{ route('admin.courses.learning.modules.destroy', [$course, $module]) }}"
                                  class="inline"
                                  onsubmit="return confirm('Delete this module and all of its lessons? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-red-200 text-red-700 hover:bg-red-50">
                                    <i class="fas fa-trash mr-1.5"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                <ul class="divide-y divide-gray-100">
                    @forelse($module->lessons as $lesson)
                        <li class="px-4 py-3 text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 {{ !$lesson->is_active ? 'bg-amber-50/40' : '' }}">
                            <div class="min-w-0 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="text-gray-900 font-medium">{{ $lesson->title }}</span>
                                <span class="text-xs text-gray-500 capitalize">{{ $lesson->lesson_type === 'video_link' ? 'Video' : 'Article' }}</span>
                                @if(!$lesson->is_active)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">Inactive</span>
                                @endif
                                @if($lesson->estimated_minutes)
                                    <span class="text-gray-500 text-xs">{{ $lesson->estimated_minutes }} min</span>
                                @endif
                                <span class="text-xs text-gray-400">#{{ $lesson->sort_order }}</span>
                            </div>
                            @if(auth()->user()->is_super_admin)
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('admin.courses.learning.lessons.edit', [$course, $module, $lesson]) }}"
                                       class="text-xs font-medium text-primary-700 hover:text-primary-900">Edit</a>
                                    <form method="POST" action="{{ route('admin.courses.learning.lessons.destroy', [$course, $module, $lesson]) }}"
                                          class="inline"
                                          onsubmit="return confirm('Delete this lesson? Student progress rows for it will be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </li>
                    @empty
                        <li class="px-4 py-3 text-sm text-gray-500">No lessons in this module yet.
                            @if(auth()->user()->is_super_admin)
                                <a href="{{ route('admin.courses.learning.lessons.create', [$course, $module]) }}" class="text-primary-700 font-medium hover:underline ml-1">Add one</a>
                            @endif
                        </li>
                    @endforelse
                </ul>
            </div>
        @endforeach
    @endif
</div>
@endsection
