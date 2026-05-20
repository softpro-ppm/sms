@extends('layouts.admin')

@section('title', 'Exam Results')
@section('page-title', 'Exam Results')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-clipboard-check text-[10px] text-primary-600"></i>
                        Results Queue
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">Review pass, fail, and score trends from one results queue.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Filter results by course, exam, student, status, and date so you can review performance and move eligible students into the next certification step.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.results.export', request()->query()) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-download"></i>
                        Export results
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total Results</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_results']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Completed assessments in scope</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-700">Passed</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['passed_results']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Students who cleared the exam</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-rose-700">Failed</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['failed_results']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Results that need follow-up</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-700">Average Score</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['average_percentage'], 1) }}%</p>
                <p class="mt-1 text-sm text-slate-500">Average percentage across results</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">Students</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_students']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Unique students represented</p>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
        <div class="border-b border-gray-100 pb-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Queue Filters</p>
                <h3 class="mt-1 text-base font-semibold text-gray-900">Results filters</h3>
            </div>
            <p class="mt-1 text-sm text-gray-500">Search by student, exam, course, status, and completion date.</p>
        </div>

        <form method="GET" action="{{ route('admin.results.index') }}" class="mt-4 space-y-3">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label for="course_id" class="mb-1 block text-sm font-medium text-gray-700">Course</label>
                    <select name="course_id" id="course_id" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="assessment_id" class="mb-1 block text-sm font-medium text-gray-700">Exam</label>
                    <select name="assessment_id" id="assessment_id" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">All Exams</option>
                        @foreach($assessments as $assessment)
                            <option value="{{ $assessment->id }}" {{ request('assessment_id') == $assessment->id ? 'selected' : '' }}>
                                {{ $assessment->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">All Status</option>
                        <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Passed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="mb-1 block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="date_from" id="date_from"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                           value="{{ request('date_from') }}">
                </div>
                <div>
                    <label for="date_to" class="mb-1 block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="date_to" id="date_to"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                           value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_160px_auto]">
                <div>
                    <label for="student_search" class="mb-1 block text-sm font-medium text-gray-700">Student Search</label>
                    <input type="text" name="student_search" id="student_search"
                           class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200"
                           placeholder="Name or enrollment number..." value="{{ request('student_search') }}">
                </div>
                <div>
                    <label for="per_page" class="mb-1 block text-sm font-medium text-gray-700">Rows</label>
                    <select id="per_page" name="per_page"
                            class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        @foreach([10,20,50,100] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-filter"></i>
                        Apply
                    </button>
                    <a href="{{ route('admin.results.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Result Records</p>
                    <h3 class="mt-1 text-base font-semibold text-gray-900">Review completed assessments and pass status</h3>
                </div>
                <p class="text-sm text-gray-500">{{ number_format($results->total()) }} total records</p>
            </div>
        </div>

        @if($results->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Course</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Score</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Grade</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Status</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Date</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-sm font-semibold text-blue-700">
                                            {{ Str::limit($result->student->full_name ?? '', 2, '') }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $result->student->full_name }}</div>
                                            <div class="mt-0.5 text-sm text-gray-500">{{ $result->enrollment->enrollment_number ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $result->enrollment->course->name }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="text-sm text-gray-700">
                                        <div class="font-semibold text-gray-900">{{ $result->correct_answers }}/{{ $result->total_questions }}</div>
                                        <div class="mt-0.5 text-gray-500">{{ number_format($result->percentage, 1) }}%</div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        {{ $result->grade === 'A+' ? 'bg-emerald-100 text-emerald-800' :
                                           ($result->grade === 'A' ? 'bg-blue-100 text-blue-800' :
                                           ($result->grade === 'B' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) }}">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $result->is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $result->passing_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ $result->completed_at ? $result->completed_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.results.show', $result) }}"
                                       class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-sm text-blue-700 transition hover:bg-blue-100">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-5 py-4">
                @if($results->hasPages())
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ $results->firstItem() }} to {{ $results->lastItem() }} of {{ $results->total() }} results
                        </div>
                        <div>{{ $results->withQueryString()->links() }}</div>
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Showing {{ $results->count() }} of {{ $results->total() }} result{{ $results->total() !== 1 ? 's' : '' }}
                    </div>
                @endif
            </div>
        @else
            <div class="px-6 py-14 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                <h3 class="mt-4 text-base font-semibold text-gray-900">No Results Found</h3>
                <p class="mt-2 text-sm text-gray-500">No assessment results match your current filters.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('course_id').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('assessment_id').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection
