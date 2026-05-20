@extends('layouts.admin')

@section('title', 'Courses Management')
@section('page-title', 'Courses')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-book text-[10px] text-primary-600"></i>
                        Courses Queue
                    </div>
                    <h2 class="mt-3 text-xl font-semibold leading-tight text-slate-900 md:text-2xl">Manage course catalog, fees, and course activity.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review active courses, check enrollment volume, and manage course settings from a single catalog view.</p>
                </div>
                @if(auth()->user()->is_super_admin)
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.courses.create') }}" 
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus"></i>
                        Add course
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total Courses</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['total_courses'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Course records in catalog</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-700">Active Courses</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['active_courses'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Visible for batch usage</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-700">Total Batches</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['total_batches'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Batches linked to courses</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">Total Enrollments</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['total_enrollments'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Students across all courses</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Courses Table -->
    <div class="rounded-[20px] border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-900">All Courses</h3>
            <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search course..."
                           class="w-64 rounded-xl border border-gray-300 px-4 py-2.5 pl-10 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <i class="fas fa-search absolute left-3.5 top-3 text-gray-400"></i>
                </div>
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm text-gray-600">Rows</label>
                    <select id="per_page" name="per_page"
                            data-live-rows
                            class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @foreach([10,20,50,100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Course Details
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Fees
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Statistics
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Status
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($courses as $course)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $course->name }}</div>
                                    <div class="mt-0.5 text-sm text-gray-500">{{ Str::limit($course->description, 50) ?: 'No description' }}</div>
                                    @if($course->duration_days)
                                        <div class="mt-1 text-xs text-gray-400">
                                            <i class="fas fa-clock mr-1"></i>{{ $course->duration_days }} days
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium text-base">₹{{ number_format($course->course_fee) }}</div>
                                <div class="mt-0.5 text-xs text-gray-500">Course Fee</div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-purple-600">{{ $course->batches_count }}</div>
                                        <div class="text-xs text-gray-500">Batches</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-orange-600">{{ $course->enrollments_count }}</div>
                                        <div class="text-xs text-gray-500">Enrollments</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if(auth()->user()->is_super_admin)
                            <form method="POST" action="{{ route('admin.courses.toggle-status', $course) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors duration-200 {{ $course->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    <i class="fas {{ $course->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $course->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.courses.learning', $course) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 transition hover:bg-emerald-100"
                                   title="Lessons (LMS){{ ($course->learning_modules_count ?? 0) > 0 ? ' — '.$course->learning_modules_count.' modules' : '' }}">
                                    <i class="fas fa-book-reader"></i>
                                </a>
                                <a href="{{ route('admin.courses.show', $course) }}" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->is_super_admin)
                                <a href="{{ route('admin.courses.edit', $course) }}" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50"
                                   title="Edit Course">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" 
                                      class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                            title="Delete Course">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-book text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No courses found</p>
                                <p class="text-sm">Get started by creating your first course.</p>
                                @if(auth()->user()->is_super_admin)
                                <a href="{{ route('admin.courses.create') }}" 
                                   class="inline-flex items-center px-4 py-2 mt-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Course
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $courses->links() }}
        </div>
        @endif
    </div>
</div>

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
