@extends('layouts.admin')

@section('title', 'Student Queue')
@section('page-title', 'Student Queue')

@section('content')
<div class="space-y-8">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5 text-slate-900">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-primary-700">
                        <i class="fas fa-user-check text-[10px]"></i>
                        Student Queue
                    </div>
                    <h2 class="mt-3 text-xl font-semibold leading-tight md:text-2xl">Review registrations, missing records, and enrollment readiness.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Approve students, fix missing documents, and move approved students into batches without switching between multiple screens.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-user-plus"></i>
                        Register student
                    </a>
                    @if(auth()->user()->is_admin || auth()->user()->is_super_admin)
                        <a href="{{ route('admin.student-deletion-requests.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 transition hover:bg-slate-50">
                            <i class="fas fa-user-shield"></i>
                            Deletion requests
                        </a>
                    @else
                        <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-800 transition hover:bg-slate-50">
                            <i class="fas fa-layer-group"></i>
                            Batch operations
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.students.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-4 transition hover:bg-slate-50">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">All Students</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['total_students']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Registered student records</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-4 transition hover:bg-slate-50">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Pending Approval</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['pending_students']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Waiting for admin review</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.students.index', ['queue' => 'ready_for_enrollment']) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-4 transition hover:bg-slate-50">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Ready for Enrollment</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['ready_for_enrollment']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Approved with no active batch</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.students.index', ['queue' => 'missing_photo']) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-4 transition hover:bg-slate-50">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Missing Photo</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['missing_photo']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Cannot approve until uploaded</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-700">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-4 shadow-sm">
        <div class="border-b border-gray-100 pb-3">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Filters</p>
            <h3 class="mt-1 text-base font-semibold text-gray-900">Admissions and approvals</h3>
            <p class="mt-1 text-sm text-gray-600">Search by student, queue, or approval status.</p>
        </div>

        <form method="GET" class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1.35fr)_180px_180px_110px_auto]">
                <div class="relative">
                    <input type="text"
                           name="search"
                           data-live-search
                           value="{{ request('search') }}"
                           placeholder="Search name, email, Aadhaar, WhatsApp"
                           class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pl-10 text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <select name="queue" data-live-filter class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    <option value="">All queues</option>
                    <option value="admissions_today" {{ $queue === 'admissions_today' ? 'selected' : '' }}>Admissions today</option>
                    <option value="pending_approval" {{ $queue === 'pending_approval' ? 'selected' : '' }}>Pending approval</option>
                    <option value="ready_for_enrollment" {{ $queue === 'ready_for_enrollment' ? 'selected' : '' }}>Ready for enrollment</option>
                    <option value="missing_documents" {{ $queue === 'missing_documents' ? 'selected' : '' }}>Missing documents</option>
                    <option value="missing_photo" {{ $queue === 'missing_photo' ? 'selected' : '' }}>Missing photo</option>
                    <option value="active_students" {{ $queue === 'active_students' ? 'selected' : '' }}>Approved students</option>
                </select>

                <select name="status" data-live-filter class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    <option value="">All statuses</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <select id="per_page" name="per_page" data-live-rows class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-200">
                    @foreach([10,20,50,100] as $size)
                        <option value="{{ $size }}" {{ (int) request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }} rows</option>
                    @endforeach
                </select>

                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                    Reset
                </a>
        </form>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex flex-col gap-2 border-b border-gray-200 px-5 py-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Student Records</p>
                <h3 class="mt-1 text-base font-semibold text-gray-900">Review and act on current student records</h3>
            </div>
            <div class="text-sm text-gray-500">
                Showing {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} of {{ $students->total() }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Student</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Status</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Enrollments</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Registration</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($students as $student)
                        <tr class="align-top transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700">
                                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</p>
                                        <p class="mt-0.5 text-sm text-gray-600">{{ $student->email ?: 'No email recorded' }}</p>
                                        <p class="mt-0.5 text-sm text-gray-500">{{ $student->whatsapp_number ?: 'No WhatsApp recorded' }}</p>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">Aadhar: {{ $student->aadhar_number ?: 'Not added' }}</span>
                                            <span class="rounded-full {{ $student->has_photo ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} px-2.5 py-0.5 text-xs font-medium">
                                                {{ $student->has_photo ? 'Photo uploaded' : 'Photo missing' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="space-y-1.5">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $student->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($student->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        @if($student->status === 'approved')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @elseif($student->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @endif
                                        {{ ucfirst($student->status) }}
                                    </span>
                                    <p class="text-xs {{ $student->user && $student->user->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                        <i class="fas {{ $student->user && $student->user->is_active ? 'fa-user-check' : 'fa-user-times' }} mr-1"></i>
                                        {{ $student->user && $student->user->is_active ? 'Account active' : 'Account inactive' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="space-y-1.5">
                                    <p class="text-lg font-semibold leading-none text-violet-700">{{ $student->enrollments_count }}</p>
                                    <p class="text-[11px] uppercase tracking-[0.16em] text-slate-500">Active enrollments</p>
                                    @if($student->enrollments_count > 0)
                                        <div class="space-y-0.5 text-sm text-gray-600">
                                            @foreach($student->enrollments->take(2) as $enrollment)
                                                <p>{{ $enrollment->batch->batch_name }} · {{ $enrollment->batch->course->name }}</p>
                                            @endforeach
                                            @if($student->enrollments_count > 2)
                                                <p class="text-xs text-slate-500">+{{ $student->enrollments_count - 2 }} more</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-amber-700">No active batch yet</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="space-y-0.5 text-sm text-gray-600">
                                    <p class="font-medium text-gray-900">{{ $student->created_at->format('M d, Y') }}</p>
                                    <p>{{ $student->created_at->diffForHumans() }}</p>
                                    @if($student->approved_at)
                                        <p class="text-emerald-700">Approved {{ $student->approved_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-medium text-primary-700 transition hover:border-primary-300 hover:bg-primary-50">
                                        <i class="fas fa-eye text-xs"></i>
                                        Open
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 font-medium text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                                        <i class="fas fa-edit text-xs"></i>
                                        Edit
                                    </a>

                                    @if($student->status === 'pending')
                                        @if($student->has_photo)
                                            <form method="POST" action="{{ route('admin.students.approve', $student) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-2.5 py-1.5 font-medium text-white transition hover:bg-emerald-700">
                                                    <i class="fas fa-check text-xs"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('admin.students.show', $student) }}#documents" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 px-2.5 py-1.5 font-medium text-amber-800 transition hover:bg-amber-200">
                                                <i class="fas fa-camera text-xs"></i>
                                                Upload photo
                                            </a>
                                        @endif

                                        <form method="POST" action="{{ route('admin.students.reject', $student) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-100 px-2.5 py-1.5 font-medium text-rose-800 transition hover:bg-rose-200">
                                                <i class="fas fa-times text-xs"></i>
                                                Reject
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
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No students found</p>
                                    <p class="text-sm">Try another filter or add a new student record.</p>
                                    <a href="{{ route('admin.students.create') }}" class="mt-4 inline-flex items-center rounded-2xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Register student
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $students->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
