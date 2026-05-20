@extends('layouts.student')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                    <i class="fas fa-book text-[10px]"></i>
                    My courses
                </div>
                <h2 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Track your enrolled courses, fees, and learning access.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">See what you are enrolled in, whether your fees are clear, and jump back into lessons when they are available.</p>
            </div>
            <div class="text-sm text-slate-500">{{ $enrollments->total() }} courses</div>
        </div>
    </section>

    <!-- Enrollments List -->
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Course Details
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Batch
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Enrollment Date
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Fees
                            </th>
                            <th class="px-5 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                                Learning
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                                                <i class="fas fa-book"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $enrollment->display_course_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Enrollment #{{ $enrollment->enrollment_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $enrollment->batch->batch_name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $enrollment->effective_start_date?->format('M d, Y') ?? '—' }} —
                                        {{ $enrollment->effective_end_date?->format('M d, Y') ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-900">
                                    {{ $enrollment->enrollment_date->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                        {{ $enrollment->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 
                                           ($enrollment->status === 'completed' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>Total: ₹{{ number_format($enrollment->total_fee) }}</div>
                                        <div class="text-green-600">Paid: ₹{{ number_format($enrollment->paid_amount) }}</div>
                                        @if($enrollment->outstanding_amount > 0)
                                            <div class="text-red-600">Due: ₹{{ number_format($enrollment->outstanding_amount) }}</div>
                                        @else
                                            <div class="text-green-600">Fully Paid</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                    @php
                                        $learnCourse = $enrollment->course;
                                        $hasLms = $learnCourse && $learnCourse->lmsHostHasActiveLessons();
                                    @endphp
                                    @if($hasLms)
                                        <a href="{{ Route::has('student.learn.resume') ? route('student.learn.resume', $enrollment) : route('student.learn.outline', $enrollment) }}" class="inline-flex items-center gap-2 rounded-2xl border border-primary-200 bg-primary-50 px-3 py-2 text-sm font-medium text-primary-700 transition hover:border-primary-300 hover:bg-primary-100" title="Opens your last lesson, or the course outline">
                                            <i class="fas fa-book-reader text-xs"></i> Open lessons
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($enrollments->hasPages())
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-300">
                    <i class="fas fa-book text-6xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No courses enrolled</h3>
                <p class="mt-2 text-gray-500">You haven't enrolled in any courses yet.</p>
                <div class="mt-6">
                    <a href="{{ route('student.dashboard') }}" 
                       class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
