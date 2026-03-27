@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
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
                                    <a href="{{ route('admin.courses.create') }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">Create a course</a>
                                    <p class="text-slate-600 text-xs mt-0.5">Fees, duration, and assessment settings.</p>
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
