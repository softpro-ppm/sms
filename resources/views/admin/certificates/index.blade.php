@extends('layouts.admin')

@section('title', 'Certificates')
@section('page-title', 'Certificates')

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-certificate text-[10px] text-primary-600"></i>
                        Certificates Queue
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">Issue, review, and track certificate records.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Use this queue to review pending certificates, issue completed records, and filter certificate history by course, batch, student, and date.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/admin/certificates/sample') }}"
                       target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-eye"></i>
                        Sample certificate
                    </a>
                    <a href="{{ route('admin.certificates.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus"></i>
                        Create certificate
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Total Records</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_certificates']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Certificate entries in scope</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-700">Issued</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['issued_certificates']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Ready for download or verification</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-amber-700">Pending</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_certificates']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Still waiting to be issued</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-700">Students</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['total_students']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Unique students represented</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-sky-700">This Month</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($stats['this_month']) }}</p>
                <p class="mt-1 text-sm text-slate-500">Certificates updated this month</p>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
        <div class="border-b border-gray-100 pb-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Queue Filters</p>
                <h3 class="mt-1 text-base font-semibold text-gray-900">Certificates filters</h3>
            </div>
            <p class="mt-1 max-w-xl text-sm leading-6 text-gray-500">Search by student, queue, course, batch, and issue date.</p>
        </div>

        <form method="GET" action="{{ route('admin.certificates.index') }}" class="mt-4 space-y-3">
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
                    <label for="batch_id" class="mb-1 block text-sm font-medium text-gray-700">Batch</label>
                    <select name="batch_id" id="batch_id" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">All Batches</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Queue</label>
                    <select name="status" id="status" class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <option value="">All Records</option>
                        @if($statusFilterIssued)
                            <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                        @endif
                        @if($statusFilterPending)
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        @endif
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
                           placeholder="Name or email..." value="{{ request('student_search') }}">
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
                    <a href="{{ route('admin.certificates.index') }}"
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
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Certificate Records</p>
                    <h3 class="mt-1 text-base font-semibold text-gray-900">Review pending and issued certificates</h3>
                </div>
                <p class="text-sm text-gray-500">{{ number_format($certificates->total()) }} total records</p>
            </div>
        </div>

        @if($certificates->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Student</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Course</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Batch</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Certificate Number</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Status</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Physical Copy</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Issue Date</th>
                            <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($certificates as $certificate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3.5 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-sm font-semibold text-blue-700">
                                            {{ substr($certificate->student->full_name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $certificate->student->full_name }}</div>
                                            <div class="mt-0.5 text-sm text-gray-500">{{ $certificate->student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $certificate->course->name }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-700">{{ $certificate->batch ? $certificate->batch->batch_name : 'N/A' }}</td>
                                <td class="px-5 py-3.5 text-sm text-gray-700">
                                    <span class="font-mono">{{ $certificate->certificate_number ?: 'Not Generated' }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $certificate->is_issued ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        <i class="fas fa-{{ $certificate->is_issued ? 'check-circle' : 'clock' }} mr-1"></i>
                                        {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($certificate->physical_copy_issued_at)
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                                <i class="fas fa-hand-holding mr-1"></i>
                                                Physical Issued
                                            </span>
                                            <div class="text-xs text-gray-500">{{ $certificate->physical_copy_issued_at->format('M d, Y') }}</div>
                                        </div>
                                    @elseif($certificate->is_issued)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">
                                            <i class="fas fa-box-open mr-1"></i>
                                            Physical Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                                            <i class="fas fa-lock mr-1"></i>
                                            Not Ready
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500">{{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('admin.certificates.show', $certificate) }}"
                                           class="inline-flex items-center justify-center rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-sm text-blue-700 transition hover:bg-blue-100">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$certificate->is_issued)
                                            <form action="{{ route('admin.certificates.generate', $certificate) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-sm text-emerald-700 transition hover:bg-emerald-100"
                                                        onclick="return confirm('Generate certificate for this student?')">
                                                    <i class="fas fa-certificate"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.certificates.download', $certificate) }}"
                                               class="inline-flex items-center justify-center rounded-lg border border-violet-200 bg-violet-50 px-2.5 py-1.5 text-sm text-violet-700 transition hover:bg-violet-100">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @unless($certificate->physical_copy_issued_at)
                                                <form action="{{ route('admin.certificates.physical-copy', $certificate) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-sm text-emerald-700 transition hover:bg-emerald-100"
                                                            onclick="return confirm('Mark physical certificate copy as issued to this student?')"
                                                            title="Mark physical copy issued">
                                                        <i class="fas fa-hand-holding"></i>
                                                    </button>
                                                </form>
                                            @endunless
                                            <form action="{{ route('admin.certificates.revoke', $certificate) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-sm text-rose-700 transition hover:bg-rose-100"
                                                        onclick="return confirm('Revoke this certificate?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-200 px-5 py-4">
                @if($certificates->hasPages())
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ $certificates->firstItem() }} to {{ $certificates->lastItem() }} of {{ $certificates->total() }} results
                        </div>
                        <div>{{ $certificates->withQueryString()->links() }}</div>
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Showing {{ $certificates->count() }} of {{ $certificates->total() }} certificate{{ $certificates->total() !== 1 ? 's' : '' }}
                    </div>
                @endif
            </div>
        @else
            <div class="px-6 py-14 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-certificate text-xl"></i>
                </div>
                <h3 class="mt-4 text-base font-semibold text-gray-900">No Certificates Found</h3>
                <p class="mt-2 text-sm text-gray-500">No certificate records match your current filters.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.certificates.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-plus"></i>
                        Create certificate
                    </a>
                </div>
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

    document.getElementById('batch_id').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('status').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('per_page').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection
