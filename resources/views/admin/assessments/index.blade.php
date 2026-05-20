@extends('layouts.admin')

@section('title', 'Exams')
@section('page-title', 'Exams')

@section('content')
<div class="space-y-5">
    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-clipboard-list text-[10px]"></i>
                    Exams Queue
                </div>
                <h1 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Track assessments, availability, and recent activity.</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Manage exam definitions, review active assessments, and keep the schedule aligned with course delivery.</p>
            </div>
            <a href="{{ route('admin.assessments.create') }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                <i class="fas fa-plus text-xs"></i>
                Create exam
            </a>
        </div>
        <div class="grid gap-4 border-t border-slate-200 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Total exams</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $stats['total_assessments'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Assessments configured in the system.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Active exams</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $stats['active_assessments'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Live assessments available for delivery.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Inactive</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $stats['inactive_assessments'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Draft or paused assessments.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 px-5 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">Students assessed</p>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $stats['total_students_assessed'] }}</p>
                <p class="mt-2 text-sm text-slate-600">Students with recorded exam attempts.</p>
            </div>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="grid gap-4 px-6 py-5 xl:grid-cols-[260px,minmax(0,1fr)] xl:items-end">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Filters</div>
                <h3 class="mt-3 text-xl font-semibold tracking-tight text-slate-900">Exam status and course coverage</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Search for a specific assessment or reduce the list by status and course.</p>
            </div>
            <form method="GET" action="{{ route('admin.assessments.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <input type="text"
                       name="search"
                       data-live-search
                       value="{{ request('search') }}"
                       placeholder="Search by exam title or course..."
                       class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100 xl:col-span-2">
                <select name="status" data-live-filter class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">All status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <select name="course_id" data-live-filter class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <option value="">All courses</option>
                    @foreach($filterCourses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                <div class="flex flex-wrap items-center gap-3 md:col-span-2 xl:col-span-5">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-slate-500">Rows</label>
                        <select id="per_page" name="per_page" data-live-rows class="rounded-2xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100">
                            @foreach([10,20,50,100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('admin.assessments.index') }}"
                       class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                        <i class="fas fa-times text-xs"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Exam records</div>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Review active and inactive assessments</h3>
            </div>
            <div class="text-sm text-slate-500">{{ $assessments->total() }} total records</div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            #
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Exam Details
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Course
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Exam Info
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Status
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Date & Time
                        </th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assessments as $index => $assessment)
                    <tr class="assessment-row hover:bg-gray-50 transition-colors duration-200" 
                        data-assessment-title="{{ strtolower($assessment->title) }}"
                        data-course-name="{{ strtolower($assessment->course ? $assessment->course->name : 'n/a') }}"
                        data-assessment-status="{{ $assessment->is_active ? '1' : '0' }}">
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-500">
                            {{ ($assessments->currentPage() - 1) * $assessments->perPage() + $index + 1 }}
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $assessment->title }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $assessment->id }}</div>
                                    @if($assessment->description)
                                        <div class="text-xs text-gray-400 mt-1">{{ Str::limit($assessment->description, 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($assessment->course)
                                    <div class="font-medium">{{ $assessment->course->name }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $assessment->course->id }}</div>
                                @else
                                    <div class="font-medium text-gray-400">No Course</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium">Time: {{ $assessment->time_limit_minutes }} min</div>
                                <div class="text-gray-500">Questions: {{ $assessment->total_questions }}</div>
                                <div class="text-xs text-gray-400">Pass: {{ $assessment->passing_percentage }}%</div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($assessment->is_active)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    <i class="fas fa-pause-circle mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div>Created {{ $assessment->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $assessment->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.assessments.show', $assessment) }}" 
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-blue-600 transition hover:border-blue-300 hover:bg-blue-100"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="{{ route('admin.assessments.edit', $assessment) }}" 
                                   class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-100"
                                   title="Edit Exam">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form method="POST" action="{{ route('admin.assessments.toggle-status', $assessment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border {{ $assessment->is_active ? 'border-amber-200 bg-amber-50 text-amber-600 hover:border-amber-300 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-600 hover:border-emerald-300 hover:bg-emerald-100' }} transition"
                                            title="{{ $assessment->is_active ? 'Deactivate' : 'Activate' }} Exam">
                                        <i class="fas fa-{{ $assessment->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.assessments.destroy', $assessment) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition hover:border-rose-300 hover:bg-rose-100"
                                            title="Delete Exam"
                                            onclick="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No assessments found</p>
                                <p class="text-sm">Start by creating an assessment for a course</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assessments->hasPages())
            <div class="px-5 py-4 border-t border-gray-200">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Auto-dismissing notifications -->
@if(session('success'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     x-init="setTimeout(() => show = false, 5000)"
     class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif
@endsection

 
