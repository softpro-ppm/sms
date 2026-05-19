@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(auth()->user()->is_reception)
<div class="space-y-8">
    <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-primary-700 via-primary-800 to-slate-900 p-8 text-white shadow-2xl shadow-primary-900/20">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.18),_transparent_32%),radial-gradient(circle_at_bottom_left,_rgba(255,255,255,0.14),_transparent_28%)]"></div>
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-primary-100/80">Reception Workspace V3.0</p>
                <h2 class="mt-2 text-3xl font-bold leading-tight md:text-4xl">Front-desk work should feel fast, focused, and clear.</h2>
                <p class="mt-3 max-w-2xl text-base leading-7 text-primary-100/90">This dashboard is shaped around what reception actually does: register students, capture documents, record payments, and hand off approvals to admin.</p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('admin.students.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-primary-800 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:bg-primary-50">
                        <i class="fas fa-user-plus"></i>
                        Register student
                    </a>
                    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/15">
                        <i class="fas fa-credit-card"></i>
                        Record payment
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:w-[360px]">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/70">Admissions Today</p>
                    <p class="mt-3 text-3xl font-bold">{{ $receptionWorkspace['stats']['admissions_today'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/70">New student records created today</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/70">Missing Docs</p>
                    <p class="mt-3 text-3xl font-bold">{{ $receptionWorkspace['stats']['missing_documents'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/70">Students still missing core documents</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/70">Pending Approval</p>
                    <p class="mt-3 text-3xl font-bold">{{ $receptionWorkspace['stats']['pending_approvals'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/70">Students waiting for admin review</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/70">Payments Today</p>
                    <p class="mt-3 text-3xl font-bold">{{ $receptionWorkspace['stats']['payments_today'] ?? 0 }}</p>
                    <p class="mt-1 text-sm text-white/70">Entries captured at the front desk</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Action board</p>
                    <h3 class="mt-1 text-2xl font-bold text-gray-900">Start from the most common front-desk tasks</h3>
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
                <a href="{{ route('admin.students.create') }}" class="group rounded-3xl border border-blue-200 bg-blue-50/70 p-5 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-md">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">New registration</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Capture student details, photo, and documents in one guided intake flow.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-blue-700 group-hover:text-blue-800">
                                Open registration
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.payments.create') }}" class="group rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-50">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-md">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Record payment</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Capture payment details quickly, then hand approval to centre admin.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 group-hover:text-emerald-800">
                                Open payment form
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.students.index') }}" class="group rounded-3xl border border-amber-200 bg-amber-50/70 p-5 transition hover:-translate-y-0.5 hover:border-amber-300 hover:bg-amber-50">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-600 text-white shadow-md">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Missing documents</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">Use the student detail pages below to complete photos, Aadhar, and qualification proof.</p>
                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-amber-700 group-hover:text-amber-800">
                                Open students
                                <i class="fas fa-arrow-right text-xs"></i>
                            </span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.students.index') }}" class="group rounded-3xl border border-violet-200 bg-violet-50/70 p-5 transition hover:-translate-y-0.5 hover:border-violet-300 hover:bg-violet-50">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-md">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">Pending approvals</h4>
                            <p class="mt-2 text-sm leading-6 text-gray-600">See who still needs admin approval and make sure their records are complete before handoff.</p>
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
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Today’s admissions</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-900">Latest students at the front desk</h3>

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
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Payments recorded</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-900">Recent front-desk money entries</h3>

                <div class="mt-5 space-y-3">
                    @forelse(($receptionWorkspace['recent_payments'] ?? collect()) as $payment)
                        <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $payment->student?->full_name ?? 'Student' }}</p>
                                <p class="text-sm text-gray-500">₹{{ number_format($payment->amount) }} · {{ $payment->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($payment->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
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
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Document completion queue</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-900">Students who still need attention before handoff</h3>
            </div>
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                Open full student list
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @forelse(($receptionWorkspace['missing_document_students'] ?? collect()) as $item)
                <div class="rounded-3xl border border-gray-200 bg-gray-50 p-5">
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
<div class="space-y-8">
    <!-- Welcome Section -->
    <div id="welcome-banner" class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-2xl p-8 text-white transition-all duration-500 ease-in-out">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-primary-100 text-lg">Here's what's happening with your institute today.</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <img src="{{ asset('images/logo/Logo.jpg') }}" 
                         alt="SoftPro Logo" 
                         class="w-16 h-16 rounded-full object-cover">
                </div>
            </div>
        </div>
    </div>

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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-sm text-success-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+12% from last month</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Students -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_students']) }}</p>
                    <p class="text-sm text-warning-600 flex items-center mt-1">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Awaiting review</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payments</p>
                    <p class="text-3xl font-bold text-gray-900">₹{{ number_format($stats['total_payments']) }}</p>
                    <p class="text-sm text-success-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>+8% from last month</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_payments']) }}</p>
                    <p class="text-sm text-warning-600 flex items-center mt-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span>Requires approval</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.students.create') }}" class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg hover:from-blue-100 hover:to-blue-200 transition-all duration-200">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Register Student</p>
                    <p class="text-sm text-gray-600">Add new student</p>
                </div>
            </a>
            
            <a href="{{ route('admin.payments.create') }}" class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg hover:from-green-100 hover:to-green-200 transition-all duration-200">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus-circle text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Record Payment</p>
                    <p class="text-sm text-gray-600">Add payment</p>
                </div>
            </a>
            
            <a href="{{ route('admin.payments.index') }}" class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg hover:from-orange-100 hover:to-orange-200 transition-all duration-200">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-double text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Approve Payments</p>
                    <p class="text-sm text-gray-600">{{ $stats['pending_payments'] }} pending</p>
                </div>
            </a>
            
            <a href="{{ route('admin.students.index') }}" class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg hover:from-purple-100 hover:to-purple-200 transition-all duration-200">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-users text-white"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">View Students</p>
                    <p class="text-sm text-gray-600">Manage students</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h3>
            <div class="space-y-4">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-rupee-sign text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</p>
                            <p class="text-sm text-gray-600">₹{{ number_format($payment->amount) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($payment->status === 'approved') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $payment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-money-bill-wave text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent payments</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Students -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Students</h3>
            <div class="space-y-4">
                @forelse($recentStudents as $student)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-semibold">{{ substr($student->full_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                            <p class="text-sm text-gray-600">{{ $student->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($student->status === 'approved') bg-green-100 text-green-800
                            @elseif($student->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($student->status) }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">{{ $student->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent students</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

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
