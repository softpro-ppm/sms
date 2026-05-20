@extends('layouts.student')

@section('title', 'Student Profile')
@section('page-title', 'Profile')

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="px-6 py-6">
            <div class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">
                <i class="fas fa-user text-[10px]"></i>
                Student profile
            </div>
            <h3 class="mt-3 text-[2rem] font-semibold tracking-tight text-slate-900">Review your details and student status.</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Your profile, enrolment identity, and ID card links are available here for quick reference.</p>
        </div>
    </section>

    <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Profile</div>
            <h4 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Profile information</h4>
            <p class="mt-1 text-sm text-slate-600">View your personal information and contact details.</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Full Name
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        {{ $user->name }}
                    </div>
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Email Address
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        {{ $user->email }}
                    </div>
                </div>
                
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Phone Number
                    </label>
                    <div class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        {{ $student->phone ?? 'Not provided' }}
                    </div>
                </div>
                
                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Address
                    </label>
                    <div class="min-h-[88px] w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                        {{ $student->address ?? 'Not provided' }}
                    </div>
                </div>
            </div>
                
                <!-- Student Information Display -->
                @if($student)
                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <h4 class="mb-3 text-lg font-semibold text-slate-900">Student ID card</h4>
                        <p class="mb-4 text-sm text-slate-600">View or download your current ID card with your latest student details.</p>
                        <div class="flex gap-3 mb-6">
                            <a href="{{ route('student.id-card') }}" target="_blank"
                               class="inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                                <i class="fas fa-id-card mr-2"></i>View ID Card
                            </a>
                            <a href="{{ route('student.id-card.download') }}"
                               class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-800">
                                <i class="fas fa-download mr-2"></i>Download ID Card
                            </a>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <h4 class="mb-4 text-lg font-semibold text-slate-900">Student information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Enrollment Number</label>
                                <p class="text-sm text-slate-900">{{ $student->enrollment_number ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Status</label>
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium 
                                    {{ $student->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 
                                       ($student->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ ucfirst($student->status ?? 'Unknown') }}
                                </span>
                            </div>
                            @if($student->approved_at)
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Approved Date</label>
                                    <p class="text-sm text-slate-900">{{ $student->approved_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-amber-500"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-900">Student profile incomplete</h3>
                                    <div class="mt-2 text-sm text-amber-800">
                                        <p>Your student profile is not yet complete. Please contact the administration to complete your student registration.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            <!-- Information Notice -->
            <div class="mt-8 rounded-2xl border border-blue-200 bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-900">Profile information</h3>
                        <div class="mt-2 text-sm text-blue-800">
                            <p>Your profile information is managed by the administration. If you need to update any details, please contact the admin office.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
