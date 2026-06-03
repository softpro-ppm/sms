@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(auth()->user()->is_reception)
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-headset text-[10px] text-primary-600"></i>
                        Reception Dashboard
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">Manage student intake and payment entries.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Handle new registrations, complete student records, and record front-desk payments before admin approval.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-user-plus"></i>
                        Register student
                    </a>
                    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-credit-card"></i>
                        Record payment
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.students.index', ['queue' => 'admissions_today']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-blue-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Admissions Today</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $receptionWorkspace['stats']['admissions_today'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-600">New student records</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.students.index', ['queue' => 'missing_documents']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-amber-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Missing Documents</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $receptionWorkspace['stats']['missing_documents'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-600">Students needing updates</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <i class="fas fa-id-card"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-violet-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Pending Approval</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $receptionWorkspace['stats']['pending_approvals'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-600">Waiting for admin review</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.payments.index', ['date_filter' => 'today']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-emerald-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Payments Today</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $receptionWorkspace['stats']['payments_today'] ?? 0 }}</p>
                        <p class="mt-1 text-sm text-slate-600">Entries recorded today</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Quick Actions</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Common reception tasks</h3>
                </div>
                <form action="{{ route('admin.students.index') }}" method="GET" class="flex w-full max-w-sm items-center gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="search" placeholder="Search student by name, email, or Aadhar" class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 pl-11 text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                        Search
                    </button>
                </form>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <a href="{{ route('admin.students.create') }}" class="group rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 shadow-sm">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Register student</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Create a student record and capture photo and document details.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 group-hover:text-blue-800">
                                Open registration
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.payments.create') }}" class="group rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 shadow-sm">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Record payment</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Enter payment details and submit them for admin approval.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 group-hover:text-emerald-800">
                                Open payment form
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.students.index', ['queue' => 'missing_documents']) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-amber-300 hover:shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 shadow-sm">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Complete documents</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Open student records to upload missing photo, Aadhar, or qualification documents.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-700 group-hover:text-amber-800">
                                Open students
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="group rounded-3xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 shadow-sm">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900">Pending approvals</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Review students waiting for admin approval and complete any missing details first.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-violet-700 group-hover:text-violet-800">
                                Open student list
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="space-y-6">
            <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Today’s Admissions</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">Recent student registrations</h3>

                <div class="mt-5 space-y-3">
                    @forelse(($receptionWorkspace['recent_admissions'] ?? collect()) as $student)
                        <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ $student->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('admin.students.show', $student) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Open</a>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                            No new admissions yet today.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Payments Recorded</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">Recent payment entries</h3>

                <div class="mt-5 space-y-3">
                    @forelse(($receptionWorkspace['recent_payments'] ?? collect()) as $payment)
                        <a href="{{ route('admin.payments.show', $payment) }}" class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 transition hover:border-emerald-200 hover:bg-white hover:shadow-sm">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $payment->student?->full_name ?? 'Student' }}</p>
                                <p class="text-sm text-gray-500">₹{{ number_format($payment->amount) }} · {{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($payment->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </a>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                            No payment entries yet today.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>

    <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Document Completion Queue</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">Students needing record updates</h3>
            </div>
            <a href="{{ route('admin.students.index', ['queue' => 'missing_documents']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                Open full student list
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @forelse(($receptionWorkspace['missing_document_students'] ?? collect()) as $item)
                <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $item['student']->full_name }}</h4>
                            <p class="mt-1 text-sm text-gray-500">Created {{ $item['student']->created_at->diffForHumans() }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($item['missing'] as $missing)
                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $missing }} missing</span>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('admin.students.show', $item['student']) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                            <i class="fas fa-folder-open"></i>
                            Open student
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-emerald-300 bg-emerald-50 p-8 text-center lg:col-span-2">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-emerald-700 shadow-sm">
                        <i class="fas fa-check-double text-2xl"></i>
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-emerald-900">No document gaps right now</h4>
                    <p class="mt-2 text-sm leading-6 text-emerald-800">Great shape. Reception has no students missing core documents at the moment.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@else
<div class="space-y-6">
    @php
        $dashboardCardClass = 'group rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-200';
        $queueCardClass = 'group rounded-[20px] border border-slate-200 bg-slate-50 p-5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-200';
    @endphp

    <section id="welcome-banner" class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm transition-all duration-500 ease-in-out">
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
            <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-medium uppercase tracking-[0.16em] text-slate-600">
                        <i class="fas fa-clipboard-check text-[10px] text-primary-600"></i>
                        Admin Dashboard
                    </div>
                    <h2 class="mt-4 text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">Manage approvals, enrollments, and daily operations.</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Review pending students, clear payment approvals, monitor assessments, and keep batches moving without leaving the dashboard.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas fa-user-check"></i>
                        Review students
                    </a>
                    <a href="{{ route('admin.payments.pending-approvals') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-money-check-alt"></i>
                        Pending payments
                    </a>
                    <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-layer-group"></i>
                        Batch operations
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i class="fas fa-chart-line"></i>
                        Reports
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-3 px-6 py-5 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('admin.students.index', ['queue' => 'active_students']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-blue-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Active Students</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['total_students']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Approved student records</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.batches.index') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-violet-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Live Batches</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['active_batches']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Batches currently running</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-emerald-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Collections</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">₹{{ number_format($stats['total_payments']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Approved payments total</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.certificates.index', ['status' => 'issued']) }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 transition hover:border-amber-300 hover:bg-white hover:shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Certificates Issued</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ number_format($stats['certificates_issued']) }}</p>
                        <p class="mt-1 text-sm text-slate-600">Issued certificate records</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                        <i class="fas fa-certificate"></i>
                    </div>
                </div>
            </a>
        </div>
    </section>

    @if(!empty($onboarding['show_modal']))
    @php
        $incomplete = (int) ! $onboarding['course_done']
            + (int) ! $onboarding['batch_done']
            + (int) ! $onboarding['question_bank_done']
            + (int) ! $onboarding['exam_done']
            + (int) ! $onboarding['student_done']
            + (int) ! $onboarding['enrollment_done'];
    @endphp
    <div id="catalog-onboarding-modal"
         class="fixed inset-0 z-[100] hidden overflow-y-auto overflow-x-hidden overscroll-contain"
         role="dialog"
         aria-modal="true"
         aria-labelledby="catalog-onboarding-title"
         data-show="1">
        <div id="catalog-onboarding-backdrop" class="fixed inset-0 z-0 bg-slate-900/65 backdrop-blur-sm transition-opacity cursor-pointer" aria-hidden="true"></div>
        <div class="relative z-10 min-h-full flex items-center justify-center p-4 sm:p-6 lg:p-8 pointer-events-none">
            <div class="relative w-full max-w-6xl flex flex-col lg:flex-row max-h-[calc(100dvh-2rem)] lg:max-h-[min(88dvh,820px)] overflow-hidden rounded-2xl bg-white shadow-[0_25px_80px_-12px_rgba(0,0,0,0.35)] ring-1 ring-slate-200/80 my-4 sm:my-8 pointer-events-auto"
                 style="animation: catalogModalIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards">
                {{-- Left intro: full width on mobile; fixed width on wide screens --}}
                <div class="relative shrink-0 overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-700 px-5 py-6 sm:px-6 sm:py-8 text-white lg:w-[min(38%,380px)] lg:max-w-md lg:flex lg:flex-col lg:justify-center">
                    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, #fff 0%, transparent 45%), radial-gradient(circle at 80% 0%, #f0abfc 0%, transparent 40%);"></div>
                    <div class="relative flex flex-col sm:flex-row sm:items-start lg:flex-col lg:items-stretch gap-4">
                        <div class="flex h-12 w-12 sm:h-14 sm:w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-md ring-1 ring-white/20 mx-auto sm:mx-0 lg:mx-0">
                            <i class="fas fa-rocket text-xl sm:text-2xl text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1 text-center sm:text-left lg:text-left pt-0">
                            <p class="text-xs font-semibold uppercase tracking-wider text-primary-100/90">New centre setup</p>
                            <h2 id="catalog-onboarding-title" class="mt-1 text-lg font-bold leading-tight sm:text-xl lg:text-2xl">Get started — your catalogue is empty</h2>
                            <p class="mt-2 text-xs sm:text-sm text-primary-100/95 leading-relaxed">
                                Build in order: <span class="font-semibold text-white">courses → batches → question bank &amp; exams → students → enrollments</span>. Add reception later from Settings → Staff if needed.
                            </p>
                            <div class="mt-4 inline-flex items-center gap-2 rounded-full bg-black/20 px-3 py-1.5 text-xs font-medium text-white ring-1 ring-white/10 max-w-full">
                                <span class="flex h-2 w-2 shrink-0 rounded-full bg-amber-300 animate-pulse"></span>
                                <span class="text-left leading-snug">
                                    @if($incomplete === 0)
                                        Core setup complete — close when you&rsquo;re ready
                                    @else
                                        {{ $incomplete }} {{ \Illuminate\Support\Str::plural('step', $incomplete) }} left to finish core setup
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Right: scrollable checklist (2 cols on md+) + sticky footer --}}
                <div class="flex min-h-0 min-w-0 flex-1 flex-col bg-white">
                    <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden overscroll-y-contain px-4 py-4 sm:px-6 sm:py-5 touch-pan-y">
                        <ul class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2 md:gap-3">
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-3 {{ !empty($onboarding['course_done']) ? 'border-emerald-100 bg-emerald-50/40' : '' }}">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ !empty($onboarding['course_done']) ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                                    @if(!empty($onboarding['course_done']))<i class="fas fa-check text-xs"></i>@else<i class="fas fa-circle-notch text-xs opacity-80"></i>@endif
                                </span>
                                <div class="min-w-0">
                                    @if(auth()->user()->is_super_admin)
                                        <a href="{{ route('admin.courses.create') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">Create a course</a>
                                        <p class="text-slate-600 text-xs mt-0.5">Global catalogue — fees, duration, LMS. TP staff view-only.</p>
                                    @else
                                        <a href="{{ route('admin.courses.index') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">View courses</a>
                                        <p class="text-slate-600 text-xs mt-0.5">Catalogue is managed by super admin; set per-batch fees when you create a batch.</p>
                                    @endif
                                </div>
                            </li>
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-3 {{ !empty($onboarding['batch_done']) ? 'border-emerald-100 bg-emerald-50/40' : '' }}">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ !empty($onboarding['batch_done']) ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                                    @if(!empty($onboarding['batch_done']))<i class="fas fa-check text-xs"></i>@else<i class="fas fa-circle-notch text-xs opacity-80"></i>@endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.batches.create') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">Add a batch</a>
                                    <p class="text-slate-600 text-xs mt-0.5">Link it to your course.</p>
                                </div>
                            </li>
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-3 md:col-span-2 {{ !empty($onboarding['question_bank_done']) && !empty($onboarding['exam_done']) ? 'border-emerald-100 bg-emerald-50/40' : '' }}">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ !empty($onboarding['question_bank_done']) && !empty($onboarding['exam_done']) ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                                    @if(!empty($onboarding['question_bank_done']) && !empty($onboarding['exam_done']))<i class="fas fa-check text-xs"></i>@else<i class="fas fa-circle-notch text-xs opacity-80"></i>@endif
                                </span>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-800">Question bank &amp; exam</span>
                                    <p class="text-slate-600 text-xs mt-1">
                                        <a href="{{ route('admin.question-banks.create') }}" class="text-primary-700 font-medium hover:underline">Question bank</a>
                                        <span class="text-slate-400"> · </span>
                                        <a href="{{ route('admin.assessments.create') }}" class="text-primary-700 font-medium hover:underline">Exam</a>
                                        <span class="text-slate-500"> — same course.</span>
                                    </p>
                                </div>
                            </li>
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-3 {{ !empty($onboarding['student_done']) ? 'border-emerald-100 bg-emerald-50/40' : '' }}">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ !empty($onboarding['student_done']) ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                                    @if(!empty($onboarding['student_done']))<i class="fas fa-check text-xs"></i>@else<i class="fas fa-circle-notch text-xs opacity-80"></i>@endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.students.create') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">Register students</a>
                                    <p class="text-slate-600 text-xs mt-0.5">Approve when ready.</p>
                                </div>
                            </li>
                            <li class="flex gap-3 rounded-xl border border-slate-100 bg-slate-50/50 px-3 py-3 {{ !empty($onboarding['enrollment_done']) ? 'border-emerald-100 bg-emerald-50/40' : '' }}">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ !empty($onboarding['enrollment_done']) ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-700' }}">
                                    @if(!empty($onboarding['enrollment_done']))<i class="fas fa-check text-xs"></i>@else<i class="fas fa-circle-notch text-xs opacity-80"></i>@endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.batches.index') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">Enroll in a batch</a>
                                    <p class="text-slate-600 text-xs mt-0.5">Open a batch and add students.</p>
                                </div>
                            </li>
                            <li class="flex gap-3 rounded-xl border border-dashed border-slate-200 bg-white px-3 py-3 text-slate-600 md:col-span-2">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-500"><i class="fas fa-plus text-xs"></i></span>
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-700">Optional</span>
                                    <p class="text-xs mt-1">
                                        <a href="{{ route('admin.payments.create') }}" class="text-primary-700 font-medium hover:underline">Record payment</a>
                                        <span class="text-slate-500"> · </span>
                                        <a href="{{ route('admin.settings.users.index') }}" class="text-primary-700 font-medium hover:underline">Add reception</a>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="shrink-0 border-t border-slate-100 bg-slate-50/80 px-4 py-4 sm:px-6">
                        <label class="flex cursor-pointer items-start gap-3 text-sm text-slate-700">
                            <input type="checkbox" id="catalog-onboarding-dont-show" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span><span class="font-medium text-slate-800">Don&rsquo;t show this again</span><span class="block text-xs text-slate-500 mt-0.5">You can still set everything up from the sidebar. This only hides the reminder on dashboard login.</span></span>
                        </label>
                        <button type="button" id="catalog-onboarding-continue" class="mt-4 w-full rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 sm:w-auto sm:min-w-[200px]">
                            Continue to dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes catalogModalIn {
            from { opacity: 0; transform: translateY(1rem) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
    @endif

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="{{ $dashboardCardClass }} hover:border-amber-300">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Pending Students</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['pending_students'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Student records waiting for approval and account activation.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-amber-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Open queue <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.payments.pending-approvals') }}" class="{{ $dashboardCardClass }} hover:border-rose-300">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-rose-700">Pending Payments</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['pending_payments'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Payment entries waiting for approval and receipt confirmation.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-rose-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Open queue <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-700">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.students.index', ['queue' => 'ready_for_enrollment']) }}" class="{{ $dashboardCardClass }} hover:border-blue-300">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Ready for Enrollment</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['ready_for_enrollment'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Approved students who still need to be assigned to a batch.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-blue-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Open queue <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.results.index') }}" class="{{ $dashboardCardClass }} hover:border-violet-300">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-violet-700">Assessment Ready</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['assessment_ready'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Active enrollments that can move into the assessment window.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-violet-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Review results <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.certificates.index', ['status' => 'pending']) }}" class="{{ $dashboardCardClass }} hover:border-emerald-300">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Pending Certificates</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['pending_certificates'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Certificate records prepared but not yet issued to students.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Open certificates <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                    <i class="fas fa-certificate"></i>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.whatsapp.inbox') }}" class="{{ $dashboardCardClass }} hover:border-slate-400">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">Unread WhatsApp</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($adminWorkspace['queue_counts']['unread_whatsapp'] ?? 0) }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Conversations still waiting for follow-up or student linking.</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-slate-700 opacity-80 transition group-hover:translate-x-1 group-hover:opacity-100">
                        Open inbox <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                    <i class="fab fa-whatsapp"></i>
                </div>
            </div>
        </a>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Action Queues</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Daily admin workload</h3>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                    Open reports
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="{{ $queueCardClass }} hover:border-amber-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 shadow-sm">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Student approvals</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Verify documents, wallet eligibility, and activate student access.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-700">{{ number_format($adminWorkspace['queue_counts']['pending_students'] ?? 0) }} waiting <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>

                <a href="{{ route('admin.payments.pending-approvals') }}" class="{{ $queueCardClass }} hover:border-rose-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 shadow-sm">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Finance queue</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Approve front-desk payment records and clear outstanding fee approvals.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rose-700">{{ number_format($adminWorkspace['queue_counts']['pending_payments'] ?? 0) }} pending <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>

                <a href="{{ route('admin.students.index', ['queue' => 'ready_for_enrollment']) }}" class="{{ $queueCardClass }} hover:border-blue-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 shadow-sm">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Enrollment queue</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Place approved students into active batches and monitor low-fill groups.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700">{{ number_format($adminWorkspace['queue_counts']['ready_for_enrollment'] ?? 0) }} ready <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>

                <a href="{{ route('admin.results.index') }}" class="{{ $queueCardClass }} hover:border-violet-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 shadow-sm">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Assessment queue</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Track enrollments that can take the assessment and review recent outcomes.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-violet-700">{{ number_format($adminWorkspace['queue_counts']['assessment_ready'] ?? 0) }} ready <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>

                <a href="{{ route('admin.certificates.index', ['status' => 'pending']) }}" class="{{ $queueCardClass }} hover:border-emerald-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 shadow-sm">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Certificate queue</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Issue pending certificates and check which learners completed the process.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700">{{ number_format($adminWorkspace['queue_counts']['pending_certificates'] ?? 0) }} pending <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>

                <a href="{{ route('admin.whatsapp.inbox') }}" class="{{ $queueCardClass }} hover:border-slate-400">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 shadow-sm">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4 class="mt-4 text-base font-bold text-gray-900">Inbox follow-up</h4>
                    <p class="mt-2 text-sm leading-6 text-gray-600">Reply to unread WhatsApp conversations and link students where needed.</p>
                    <p class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-700">{{ number_format($adminWorkspace['queue_counts']['unread_whatsapp'] ?? 0) }} unread <i class="fas fa-arrow-right text-xs opacity-70 transition group-hover:translate-x-1"></i></p>
                </a>
            </div>
        </div>

        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Batch Health</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Fill and timing checks</h3>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                    {{ number_format($adminWorkspace['queue_counts']['low_fill_batches'] ?? 0) }} low fill
                </span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse(($adminWorkspace['batch_health'] ?? collect()) as $item)
                    <a href="{{ route('admin.batches.show', $item['batch']) }}" class="block rounded-3xl border border-slate-200 bg-slate-50 p-4 transition hover:border-blue-300 hover:bg-white">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ $item['batch']->batch_name }}</h4>
                                <p class="mt-1 text-sm text-gray-600">{{ $item['batch']->course?->name ?? 'Course' }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item['status'] === 'Running' ? 'bg-emerald-100 text-emerald-800' : ($item['status'] === 'Upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700') }}">
                                {{ $item['status'] }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                            <span>{{ number_format($item['batch']->enrollments_count) }} active students</span>
                            @if($item['fill_rate'] !== null)
                                <span>{{ $item['fill_rate'] }}% filled</span>
                            @else
                                <span>No cap set</span>
                            @endif
                        </div>
                        @if($item['fill_rate'] !== null)
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full {{ $item['fill_rate'] < 50 ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ min(100, $item['fill_rate']) }}%"></div>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">
                        No active or upcoming batches to review right now.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Pending Work</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Students and payments waiting now</h3>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Pending students</h4>
                        <a href="{{ route('admin.students.index', ['queue' => 'pending_approval']) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">View all</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($adminWorkspace['pending_students'] ?? collect()) as $student)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $student->full_name }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $student->email ?: 'No email recorded' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Created {{ $student->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No pending student approvals.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Pending payments</h4>
                        <a href="{{ route('admin.payments.pending-approvals') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Open queue</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($adminWorkspace['pending_payments'] ?? collect()) as $payment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $payment->student?->full_name ?? 'Student' }}</p>
                                <p class="mt-1 text-sm text-gray-600">₹{{ number_format($payment->amount) }} awaiting approval</p>
                                <p class="mt-2 text-xs text-slate-500">Submitted {{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No pending payments right now.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Assessment & Certificates</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">What can move next</h3>
                </div>
            </div>

            <div class="mt-6 space-y-6">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Assessment-ready enrollments</h4>
                        <a href="{{ route('admin.results.index') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Results</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($adminWorkspace['assessment_ready'] ?? collect()) as $enrollment)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $enrollment->student?->full_name ?? 'Student' }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $enrollment->display_course_name }} · {{ $enrollment->batch?->batch_name ?? 'Batch' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Batch ended {{ optional($enrollment->batch?->end_date)->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No assessment-ready enrollments at the moment.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900">Pending certificates</h4>
                        <a href="{{ route('admin.certificates.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Certificate queue</a>
                    </div>
                    <div class="space-y-3">
                        @forelse(($adminWorkspace['pending_certificates'] ?? collect()) as $certificate)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="font-semibold text-gray-900">{{ $certificate->student?->full_name ?? 'Student' }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $certificate->enrollment?->display_course_name ?? $certificate->course?->name ?? 'Course' }} · {{ $certificate->batch?->batch_name ?? 'Batch pending' }}</p>
                                <p class="mt-2 text-xs text-slate-500">Queued {{ $certificate->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                                No pending certificates right now.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">WhatsApp Inbox</p>
                <h3 class="mt-1 text-xl font-bold text-gray-900">Unread conversations needing follow-up</h3>
            </div>
            <a href="{{ route('admin.whatsapp.inbox') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                Open inbox
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            @forelse(($adminWorkspace['whatsapp_conversations'] ?? collect()) as $conversation)
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h4 class="text-base font-bold text-gray-900">{{ $conversation->displayName() }}</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $conversation->phone }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                            {{ (int) $conversation->unread_count }} unread
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">
                        {{ \Illuminate\Support\Str::limit($conversation->lastMessage?->body ?? 'Open the inbox to review the conversation.', 90) }}
                    </p>
                    <p class="mt-3 text-xs text-slate-500">
                        Last message {{ optional($conversation->last_message_at)->diffForHumans() ?? 'not available' }}
                    </p>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 lg:col-span-2 xl:col-span-3">
                    No unread WhatsApp conversations right now.
                </div>
            @endforelse
        </div>
    </section>

</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const welcomeBanner = document.getElementById('welcome-banner');
        const catalogModal = document.getElementById('catalog-onboarding-modal');
        const catalogShows = catalogModal && catalogModal.dataset.show === '1';

        if (catalogShows) {
            catalogModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        const closeCatalogModal = () => {
            if (!catalogModal) return;
            catalogModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const dismissUrl = @json(route('admin.dashboard.dismiss-catalog-onboarding'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const confirmCatalogContinue = document.getElementById('catalog-onboarding-continue');
        if (confirmCatalogContinue) {
            confirmCatalogContinue.addEventListener('click', async function () {
                const permanent = document.getElementById('catalog-onboarding-dont-show')?.checked;
                if (permanent && csrfToken) {
                    try {
                        await fetch(dismissUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ dismiss_permanently: true }),
                        });
                    } catch (e) {
                        console.warn('Dismiss catalogue onboarding failed', e);
                    }
                }
                closeCatalogModal();
            });
        }

        const backdrop = document.getElementById('catalog-onboarding-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', closeCatalogModal);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && catalogModal && !catalogModal.classList.contains('hidden')) {
                closeCatalogModal();
            }
        });

        if (welcomeBanner && !catalogShows) {
            setTimeout(() => {
                welcomeBanner.style.opacity = '0';
                welcomeBanner.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    welcomeBanner.style.display = 'none';
                }, 500);
            }, 7000);
        } else if (welcomeBanner && catalogShows) {
            setTimeout(() => {
                welcomeBanner.style.opacity = '0';
                welcomeBanner.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    welcomeBanner.style.display = 'none';
                }, 500);
            }, 12000);
        }
    });
</script>
@endsection
