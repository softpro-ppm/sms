@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@php
    $timelineStateClasses = [
        'complete' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'current' => 'border-primary-200 bg-primary-50 text-primary-900',
        'upcoming' => 'border-gray-200 bg-white text-gray-700',
    ];

    $timelineIconClasses = [
        'complete' => 'bg-emerald-500 text-white',
        'current' => 'bg-primary-600 text-white',
        'upcoming' => 'bg-gray-100 text-gray-500',
    ];

    $courseToneClasses = [
        'emerald' => 'border-emerald-200 bg-emerald-50/70 text-emerald-900',
        'amber' => 'border-amber-200 bg-amber-50/80 text-amber-900',
        'violet' => 'border-violet-200 bg-violet-50/80 text-violet-900',
        'sky' => 'border-sky-200 bg-sky-50/80 text-sky-900',
        'teal' => 'border-teal-200 bg-teal-50/80 text-teal-900',
    ];
@endphp

@section('content')
<div class="space-y-6">
    <section class="overflow-hidden rounded-[22px] border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 border-b border-slate-200 bg-slate-50 px-6 py-6 lg:grid-cols-[1.5fr_0.9fr] lg:px-7 lg:py-7">
            <div class="space-y-5">
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-medium text-slate-600">
                    <i class="fas fa-sparkles text-[10px] text-primary-600"></i>
                    {{ $journey['badge'] }}
                </span>

                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Student Dashboard</p>
                    <h2 class="max-w-3xl text-2xl font-semibold leading-tight text-slate-900 md:text-[28px]">{{ $journey['title'] }}</h2>
                    <p class="max-w-2xl text-sm leading-6 text-slate-600 md:text-[15px]">{{ $journey['description'] }}</p>
                    <p class="max-w-2xl text-sm leading-6 text-slate-500">{{ $journey['meta'] }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $journey['action']['url'] }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                        <i class="fas {{ $journey['action']['icon'] }}"></i>
                        {{ $journey['action']['label'] }}
                    </a>
                    @if($journey['secondary_action'])
                        <a href="{{ $journey['secondary_action']['url'] }}"
                           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            <i class="fas {{ $journey['secondary_action']['icon'] }}"></i>
                            {{ $journey['secondary_action']['label'] }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 self-start">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Active Courses</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['active_enrollments'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Live batches in your portal</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Outstanding</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">₹{{ number_format($stats['outstanding_amount']) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $stats['pending_payments'] }} pending payment items</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Exam Ready</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['available_assessments'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $stats['pending_assessments'] }} still in progress</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Certificates</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $stats['certificates_earned'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $stats['pending_certificates'] }} awaiting issue</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[20px] border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary-700">Course Progress</p>
                <h3 class="mt-1 text-xl font-semibold text-gray-900">Your current stage in the course lifecycle</h3>
            </div>
            <p class="max-w-xl text-sm leading-6 text-gray-500">Every step below is powered by the same enrollment, payment, learning, exam, and certificate rules already active in your account.</p>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($journeyTimeline as $step)
                <div class="rounded-2xl border p-4 {{ $timelineStateClasses[$step['state']] ?? $timelineStateClasses['upcoming'] }}">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $timelineIconClasses[$step['state']] ?? $timelineIconClasses['upcoming'] }}">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold">{{ $step['label'] }}</h4>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide {{ $step['state'] === 'complete' ? 'bg-emerald-100 text-emerald-800' : ($step['state'] === 'current' ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $step['state'] === 'complete' ? 'Done' : ($step['state'] === 'current' ? 'Now' : 'Next') }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm leading-6 {{ $step['state'] === 'upcoming' ? 'text-gray-500' : 'text-current/80' }}">{{ $step['note'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.45fr_0.95fr]">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Course Actions</p>
                    <h3 class="mt-1 text-2xl font-bold text-gray-900">Active courses and next actions</h3>
                </div>
                <a href="{{ route('student.enrollments') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:text-primary-800">
                    View all courses
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="mt-6 space-y-4">
                @forelse($courseJourneyCards->take(3) as $card)
                    <article class="rounded-3xl border p-5 {{ $courseToneClasses[$card['tone']] ?? $courseToneClasses['sky'] }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-current/90">{{ $card['state'] }}</span>
                                    <span class="rounded-full bg-white/70 px-3 py-1 text-xs font-medium text-current/80">{{ $card['status'] }}</span>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">{{ $card['title'] }}</h4>
                                    <p class="mt-1 text-sm text-gray-600">
                                        @if($card['batch'])
                                            {{ $card['batch'] }}
                                        @else
                                            Self-paced enrollment
                                        @endif
                                        @if($card['date_range'])
                                            · {{ $card['date_range'] }}
                                        @endif
                                    </p>
                                </div>
                                <p class="max-w-2xl text-sm leading-6 text-gray-700">{{ $card['note'] }}</p>
                            </div>

                            <a href="{{ $card['next_action']['url'] }}"
                               class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-gray-800">
                                <i class="fas {{ $card['next_action']['icon'] }}"></i>
                                {{ $card['next_action']['label'] }}
                            </a>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            <div class="rounded-2xl bg-white/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Fee position</p>
                                @if($card['outstanding_amount'] > 0)
                                    <p class="mt-2 text-lg font-bold text-gray-900">₹{{ number_format($card['outstanding_amount']) }} due</p>
                                    <p class="mt-1 text-sm text-gray-600">Payment approval keeps the rest of the journey unlocked.</p>
                                @else
                                    <p class="mt-2 text-lg font-bold text-gray-900">Fully paid</p>
                                    <p class="mt-1 text-sm text-gray-600">No payment blocker on this enrollment.</p>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-white/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Learning progress</p>
                                @if($card['progress'])
                                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-primary-600" style="width: {{ $card['progress']['percent'] }}%"></div>
                                    </div>
                                    <p class="mt-2 text-lg font-bold text-gray-900">{{ number_format($card['progress']['percent'], 0) }}%</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $card['progress']['completed'] }} / {{ $card['progress']['total'] }} lessons completed</p>
                                @elseif($card['has_lessons'])
                                    <p class="mt-2 text-lg font-bold text-gray-900">Ready to start</p>
                                    <p class="mt-1 text-sm text-gray-600">Your online lessons are available as soon as you open the course.</p>
                                @else
                                    <p class="mt-2 text-lg font-bold text-gray-900">No online lessons</p>
                                    <p class="mt-1 text-sm text-gray-600">This course journey moves through batch milestones instead.</p>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-white/70 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Enrollment number</p>
                                <p class="mt-2 text-lg font-bold text-gray-900">{{ $card['enrollment']->enrollment_number }}</p>
                                <p class="mt-1 text-sm text-gray-600">Use this when discussing the course with your centre.</p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white text-primary-700 shadow-sm">
                            <i class="fas fa-book-open text-2xl"></i>
                        </div>
                        <h4 class="mt-4 text-lg font-semibold text-gray-900">No active course journey yet</h4>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Once your institute enrolls you, course, payment, and exam milestones will show up here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Summary</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-900">Current student status</h3>

                <div class="mt-5 space-y-4">
                    <div class="rounded-2xl bg-amber-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-amber-900">Payment status</p>
                                <p class="mt-1 text-2xl font-bold text-amber-950">₹{{ number_format($stats['outstanding_amount']) }}</p>
                                <p class="mt-1 text-sm text-amber-800">
                                    {{ $stats['pending_payments'] > 0 ? $stats['pending_payments'].' payment(s) waiting for approval.' : 'No payment approval is currently pending.' }}
                                </p>
                            </div>
                            <i class="fas fa-credit-card text-xl text-amber-600"></i>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-violet-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-violet-900">Exam status</p>
                                <p class="mt-1 text-2xl font-bold text-violet-950">{{ $stats['available_assessments'] }}</p>
                                <p class="mt-1 text-sm text-violet-800">
                                    {{ $stats['available_assessments'] > 0 ? 'Assessment opportunity is open right now.' : 'No exam is open right now.' }}
                                </p>
                            </div>
                            <i class="fas fa-clipboard-check text-xl text-violet-600"></i>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-emerald-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-emerald-900">Certificate status</p>
                                <p class="mt-1 text-2xl font-bold text-emerald-950">{{ $stats['certificates_earned'] }}</p>
                                <p class="mt-1 text-sm text-emerald-800">
                                    {{ $stats['pending_certificates'] > 0 ? $stats['pending_certificates'].' result(s) may still convert into certificates.' : 'Issued certificates are ready to download here.' }}
                                </p>
                            </div>
                            <i class="fas fa-certificate text-xl text-emerald-600"></i>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Recent Payments</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Latest payment activity</h3>
                    </div>
                    <a href="{{ route('student.payments') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">All payments</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($payments->take(3) as $payment)
                        <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">₹{{ number_format($payment->amount) }}</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($payment->payment_type) }} · {{ $payment->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($payment->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                            No payments yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Assessments</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Exam results and upcoming assessments</h3>
                </div>
                <a href="{{ route('student.assessments') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">Assessment centre</a>
            </div>

            <div class="mt-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-primary-50 p-4">
                        <p class="text-sm font-semibold text-primary-800">Passed assessments</p>
                        <p class="mt-2 text-3xl font-bold text-primary-950">{{ $stats['passed_assessments'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">Pending exam windows</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['pending_assessments'] }}</p>
                    </div>
                </div>

                @if($availableAssessments->isNotEmpty())
                    <div class="rounded-2xl border border-violet-200 bg-violet-50/70 p-4">
                        <p class="text-sm font-semibold text-violet-900">Next exam you can take</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $availableAssessments->first()['assessment']->title }}</p>
                        <p class="mt-1 text-sm text-violet-800">{{ $availableAssessments->first()['display_course_name'] }}</p>
                    </div>
                @endif

                <div class="space-y-3">
                    @forelse($assessmentResults->take(3) as $result)
                        <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $result->assessment->title ?? 'Assessment' }}</p>
                                <p class="text-sm text-gray-500">{{ $result->enrollment->display_course_name }} · {{ number_format($result->percentage, 1) }}%</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $result->is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $result->is_passed ? 'Passed' : 'Needs retake' }}
                            </span>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                            No completed exams yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[24px] border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary-700">Certificates</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">Issued certificates and pending milestones</h3>
                </div>
                <a href="{{ route('student.certificates') }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">All certificates</a>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-sm font-semibold text-emerald-800">Issued certificates</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-950">{{ $stats['certificates_earned'] }}</p>
                    <p class="mt-1 text-sm text-emerald-700">Ready for download and verification.</p>
                </div>
                <div class="rounded-2xl bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-800">Awaiting issue</p>
                    <p class="mt-2 text-3xl font-bold text-amber-950">{{ $stats['pending_certificates'] }}</p>
                    <p class="mt-1 text-sm text-amber-700">Passed results that may still convert into certificates.</p>
                </div>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($certificates->take(3) as $certificate)
                    <div class="flex items-center justify-between rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $certificate->enrollment?->display_course_name ?? $certificate->course->name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : 'Issue pending' }}
                            </p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $certificate->is_issued ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                        No certificates yet.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
