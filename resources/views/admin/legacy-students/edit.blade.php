@extends('layouts.admin')

@section('title', 'Edit Legacy Enrollment')
@section('page-title', 'Edit Legacy Enrollment')

@section('content')
<div class="space-y-6">
    <section class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Legacy record</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $enrollment->student?->full_name ?? 'Student removed' }}</h2>
                <p class="mt-2 text-sm text-slate-600">
                    {{ $enrollment->enrollment_number }} · {{ $enrollment->student?->email ?? 'No email' }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($enrollment->student)
                <a href="{{ route('admin.students.show', $enrollment->student) }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 transition hover:bg-slate-50">
                    <i class="fas fa-user mr-2"></i>Open student
                </a>
                @endif
                <a href="{{ route('admin.legacy-students.index') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                    <i class="fas fa-arrow-left mr-2"></i>Legacy list
                </a>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('admin.legacy-students.update', $enrollment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900">Course and dates</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div>
                    <label for="legacy_course_name" class="block text-sm font-medium text-slate-700">Legacy course name</label>
                    <input id="legacy_course_name" name="legacy_course_name" type="text" required maxlength="255"
                           value="{{ old('legacy_course_name', $enrollment->legacy_course_name) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('legacy_course_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="legacy_link_course_id" class="block text-sm font-medium text-slate-700">Linked LMS/catalogue course</label>
                    <select id="legacy_link_course_id" name="legacy_link_course_id"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">None - use Legacy archive course</option>
                        @foreach($linkCourses as $course)
                            <option value="{{ $course->id }}" @selected((string) old('legacy_link_course_id', $enrollment->legacy_link_course_id) === (string) $course->id)>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('legacy_link_course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="legacy_start_date" class="block text-sm font-medium text-slate-700">Start date</label>
                    <input id="legacy_start_date" name="legacy_start_date" type="date" required
                           value="{{ old('legacy_start_date', optional($enrollment->legacy_start_date)->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('legacy_start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="legacy_end_date" class="block text-sm font-medium text-slate-700">End date</label>
                    <input id="legacy_end_date" name="legacy_end_date" type="date" required
                           value="{{ old('legacy_end_date', optional($enrollment->legacy_end_date)->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('legacy_end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="enrollment_date" class="block text-sm font-medium text-slate-700">Enrollment date</label>
                    <input id="enrollment_date" name="enrollment_date" type="date" required
                           value="{{ old('enrollment_date', optional($enrollment->enrollment_date)->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('enrollment_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" required
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        @foreach(['active' => 'Active', 'completed' => 'Completed', 'dropped' => 'Dropped'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $enrollment->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900">Fees</h3>
            <div class="mt-5 grid gap-5 md:grid-cols-3">
                <div>
                    <label for="registration_fee" class="block text-sm font-medium text-slate-700">Registration fee</label>
                    <input id="registration_fee" name="registration_fee" type="number" min="0" step="0.01" required
                           value="{{ old('registration_fee', $enrollment->registration_fee) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('registration_fee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="course_fee" class="block text-sm font-medium text-slate-700">Course fee</label>
                    <input id="course_fee" name="course_fee" type="number" min="0" step="0.01" required
                           value="{{ old('course_fee', $enrollment->course_fee) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('course_fee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="assessment_fee" class="block text-sm font-medium text-slate-700">Assessment fee</label>
                    <input id="assessment_fee" name="assessment_fee" type="number" min="0" step="0.01" required
                           value="{{ old('assessment_fee', $enrollment->assessment_fee) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @error('assessment_fee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-5 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 md:grid-cols-3">
                <p><span class="font-semibold text-slate-900">Current total:</span> ₹{{ number_format($enrollment->total_fee, 2) }}</p>
                <p><span class="font-semibold text-slate-900">Paid/covered:</span> ₹{{ number_format($enrollment->paid_amount, 2) }}</p>
                <p><span class="font-semibold text-slate-900">Outstanding:</span> ₹{{ number_format($enrollment->outstanding_amount, 2) }}</p>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.legacy-students.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-amber-700">
                Save legacy enrollment
            </button>
        </div>
    </form>
</div>
@endsection
