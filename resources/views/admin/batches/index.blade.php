@extends('layouts.admin')

@section('title', 'Batches Management')
@section('page-title', 'Batches')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-layer-group text-[10px] text-primary-600"></i>
                        Batches Queue
                    </div>
                    <h2 class="mt-3 text-xl font-semibold leading-tight text-slate-900 md:text-2xl">Manage course batches, schedules, and seat usage.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review active and running batches, monitor student counts, and update schedules from one operational view.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.batches.create') }}" 
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus"></i>
                        Add batch
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total Batches</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['total_batches'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Batch records in scope</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-700">Active Batches</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['active_batches'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Currently marked active</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <i class="fas fa-play-circle"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-blue-700">Running Batches</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['running_batches'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">In progress today</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4">
            <div class="flex items-end justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">Total Students</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $stats['total_students'] }}</p>
                    <p class="mt-1 text-sm text-slate-600">Students across batches</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Batches Table -->
    <div class="rounded-[20px] border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-base font-semibold text-gray-900">All Batches</h3>
            <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative">
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search batch, course..."
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
                            Batch Details
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Course
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Schedule
                        </th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-[0.16em]">
                            Students
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
                    @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $batch->batch_name }}</div>
                                    <div class="mt-0.5 text-sm text-gray-500">Batch ID: #{{ $batch->id }}</div>
                                    @if($batch->max_students)
                                        <div class="mt-1 text-xs text-gray-400">
                                            <i class="fas fa-users mr-1"></i>Max: {{ $batch->max_students }} students
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-3 flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 text-blue-700">
                                    <i class="fas fa-book text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $batch->course->name }}</div>
                                    <div class="mt-0.5 text-xs text-gray-500">₹{{ number_format($batch->course->course_fee) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-calendar-start text-green-500 mr-2"></i>
                                    <span>{{ $batch->start_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-end text-red-500 mr-2"></i>
                                    <span>{{ $batch->end_date->format('M d, Y') }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Duration: {{ $batch->start_date->diffInDays($batch->end_date) }} days
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-orange-600">{{ $batch->enrollments_count }}</div>
                                        <div class="text-xs text-gray-500">Enrolled</div>
                                    </div>
                                    @if($batch->max_students)
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-600">{{ $batch->max_students - $batch->enrollments_count }}</div>
                                            <div class="text-xs text-gray-500">Available</div>
                                        </div>
                                    @endif
                                </div>
                                @if($batch->max_students && $batch->enrollments_count >= $batch->max_students)
                                    <div class="text-xs text-red-600 mt-1 font-medium">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Full
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="space-y-1">
                                <form method="POST" action="{{ route('admin.batches.toggle-status', $batch) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors duration-200 {{ $batch->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                        <i class="fas {{ $batch->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                        {{ $batch->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                                <div class="text-xs">
                                    @if($batch->start_date > now())
                                        <span class="text-blue-600 font-medium">Upcoming</span>
                                    @elseif($batch->end_date < now())
                                        <span class="text-gray-600 font-medium">Completed</span>
                                    @else
                                        <span class="text-green-600 font-medium">Running</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.batches.show', $batch) }}" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.batches.edit', $batch) }}" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50"
                                   title="Edit Batch">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}" 
                                      class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this batch? This action cannot be undone.')">
                                    @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-700 transition hover:bg-rose-100"
                                            title="Delete Batch">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-layer-group text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No batches found</p>
                                <p class="text-sm">Get started by creating your first batch.</p>
                                <a href="{{ route('admin.batches.create') }}" 
                                   class="inline-flex items-center px-4 py-2 mt-4 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Batch
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($batches->hasPages())
        <div class="px-5 py-4 border-t border-gray-200">
            {{ $batches->links() }}
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
