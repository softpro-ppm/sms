@extends('layouts.student')

@section('title', 'Student Profile')
@section('page-title', 'Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
            <p class="text-sm text-gray-600">View your personal information and contact details.</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ $user->name }}
                    </div>
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ $user->email }}
                    </div>
                </div>
                
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                        {{ $student->phone ?? 'Not provided' }}
                    </div>
                </div>
                
                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                    </label>
                    <div class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 min-h-[76px]">
                        {{ $student->address ?? 'Not provided' }}
                    </div>
                </div>
            </div>
                
                <!-- Student Information Display -->
                @if($student)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Student ID Card</h4>
                        <p class="text-sm text-gray-600 mb-4">Download your official student ID card with photo and course details.</p>
                        <div class="flex gap-3 mb-6">
                            <a href="{{ route('student.id-card') }}" target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-id-card mr-2"></i>View ID Card
                            </a>
                            <a href="{{ route('student.id-card.download') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-download mr-2"></i>Download ID Card
                            </a>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Student Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Enrollment Number</label>
                                <p class="text-sm text-gray-900">{{ $student->enrollment_number ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $student->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($student->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($student->status ?? 'Unknown') }}
                                </span>
                            </div>
                            @if($student->approved_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Approved Date</label>
                                    <p class="text-sm text-gray-900">{{ $student->approved_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Student Profile Incomplete</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Your student profile is not yet complete. Please contact the administration to complete your student registration.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            <!-- Information Notice -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Profile Information</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your profile information is managed by the administration. If you need to update any details, please contact the admin office.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
