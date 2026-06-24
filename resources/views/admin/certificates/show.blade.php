@extends('layouts.admin')

@section('title', 'Certificate Details')
@section('page-title', 'Certificate Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Certificate Details</h2>
            <p class="text-gray-600 mt-1">View and manage certificate information</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.certificates.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Certificates
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Certificate Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Certificate Preview (Training Certification format) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Certificate Preview</h3>
                </div>
                <div class="p-6">
                    <div class="rounded-lg border border-gray-200 overflow-auto bg-gray-100" style="max-height: 900px;">
                        <iframe src="{{ route('admin.certificates.preview', $certificate) }}"
                                class="border-0 bg-white shadow-sm block"
                                style="min-width: 1100px; width: 1100px; height: 778px;"
                                title="Certificate Preview"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Certificate Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Certificate Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-center">
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium 
                            {{ $certificate->is_issued ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            <i class="fas fa-{{ $certificate->is_issued ? 'check-circle' : 'clock' }} mr-2"></i>
                            {{ $certificate->is_issued ? 'Issued' : 'Pending' }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Certificate Number:</span>
                            <span class="text-sm font-medium font-mono">{{ $certificate->certificate_number ?: 'Not Generated' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Issue Date:</span>
                            <span class="text-sm font-medium">{{ $certificate->issue_date ? $certificate->issue_date->format('M d, Y') : 'Not Issued' }}</span>
                        </div>
                        <div class="flex justify-between gap-3">
                            <span class="text-sm text-gray-500">Physical Copy:</span>
                            <span class="text-right text-sm font-medium">
                                @if($certificate->physical_copy_issued_at)
                                    <span class="text-green-700">Issued</span>
                                @elseif($certificate->is_issued)
                                    <span class="text-amber-700">Pending</span>
                                @else
                                    <span class="text-gray-500">Not Ready</span>
                                @endif
                            </span>
                        </div>
                        @if($certificate->physical_copy_issued_at)
                            <div class="flex justify-between gap-3">
                                <span class="text-sm text-gray-500">Physical Issued On:</span>
                                <span class="text-right text-sm font-medium">{{ $certificate->physical_copy_issued_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between gap-3">
                                <span class="text-sm text-gray-500">Marked By:</span>
                                <span class="text-right text-sm font-medium">{{ $certificate->physicalCopyIssuedBy?->name ?? 'N/A' }}</span>
                            </div>
                            @if($certificate->physical_copy_notes)
                                <div>
                                    <span class="text-sm text-gray-500">Handover Note:</span>
                                    <p class="mt-1 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $certificate->physical_copy_notes }}</p>
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Created:</span>
                            <span class="text-sm font-medium">{{ $certificate->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Last Updated:</span>
                            <span class="text-sm font-medium">{{ $certificate->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Student Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                            <span class="text-lg font-medium text-white">{{ substr($certificate->student->full_name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ $certificate->student->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $certificate->student->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Email:</span>
                            <span class="text-sm font-medium">{{ $certificate->student->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Phone:</span>
                            <span class="text-sm font-medium">{{ $certificate->student->phone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Status:</span>
                            <span class="text-sm font-medium">{{ ucfirst($certificate->student->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course & Batch Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course & Batch</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Course</h4>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->course->name }}</p>
                        @if($certificate->course->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $certificate->course->description }}</p>
                        @endif
                    </div>

                    @if($certificate->batch)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Batch</h4>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $certificate->batch->batch_name }}</p>
                        @php
                            $certEnrollment = $certificate->enrollment;
                            $periodStart = $certEnrollment?->effective_start_date ?? $certificate->batch?->start_date;
                            $periodEnd = $certEnrollment?->effective_end_date ?? $certificate->batch?->end_date;
                        @endphp
                        <div class="text-sm text-gray-500 mt-1">
                            <div>Start Date: {{ $periodStart?->format('M d, Y') ?? '—' }}</div>
                            <div>End Date: {{ $periodEnd?->format('M d, Y') ?? '—' }}</div>
                        </div>
                    </div>
                    @endif

                    @if($certificate->assessmentResult)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Exam Result</h4>
                        <div class="mt-1 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Score:</span>
                                <span class="text-sm font-medium">{{ $certificate->assessmentResult->percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Grade:</span>
                                <span class="text-sm font-medium">{{ $certificate->assessmentResult->grade }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Status:</span>
                                <span class="text-sm font-medium">{{ $certificate->assessmentResult->is_passed ? 'Passed' : 'Failed' }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if(!$certificate->is_issued)
                        <form action="{{ route('admin.certificates.generate', $certificate) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-certificate mr-2"></i>
                                Generate Certificate
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.certificates.download', $certificate) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>
                            Download Certificate
                        </a>

                        @if(!$certificate->physical_copy_issued_at)
                            <form action="{{ route('admin.certificates.physical-copy', $certificate) }}" method="POST" class="space-y-2 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
                                @csrf
                                @method('PATCH')
                                <label for="physical_copy_notes" class="block text-sm font-medium text-emerald-900">Physical copy handover note</label>
                                <input type="text"
                                       name="physical_copy_notes"
                                       id="physical_copy_notes"
                                       maxlength="255"
                                       placeholder="Optional"
                                       class="w-full rounded-lg border border-emerald-200 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                                <button type="submit"
                                        onclick="return confirm('Mark physical certificate copy as issued to this student?')"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i class="fas fa-hand-holding mr-2"></i>
                                    Mark Physical Copy Issued
                                </button>
                            </form>
                        @else
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800">
                                <i class="fas fa-hand-holding mr-2"></i>
                                Physical copy issued to student
                            </div>
                        @endif
                        
                        <form action="{{ route('admin.certificates.revoke', $certificate) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to revoke this certificate?')"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-ban mr-2"></i>
                                Revoke Certificate
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.students.show', $certificate->student) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        View Student
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
