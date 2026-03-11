@extends('layouts.admin')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $student->full_name }}</h2>
            <p class="text-gray-600 mt-1">Aadhar: {{ $student->aadhar_number }} • Student ID: #{{ $student->id }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.students.edit', $student) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>
                Edit Student
            </a>
            <form action="{{ route('admin.students.reset-password', $student) }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition-colors duration-200"
                        onclick="return confirm('Reset password to phone number: {{ $student->whatsapp_number }}?')">
                    <i class="fas fa-key mr-2"></i>
                    Reset Password
                </button>
            </form>
            <a href="{{ route('admin.students.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Students
            </a>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Left Side - Student Information (3 columns) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Personal Information Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                        <p class="text-sm text-gray-600">Student Details</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->full_name }}</p>
                </div>
                @if($student->father_name)
                <div>
                            <label class="text-sm font-medium text-gray-500">Father's Name</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->father_name }}</p>
                </div>
                @endif
                <div>
                            <label class="text-sm font-medium text-gray-500">Aadhar Number</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->aadhar_number }}</p>
                </div>
                        @if($student->gender)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Gender</label>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($student->gender) }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="space-y-4">
                @if($student->date_of_birth)
                    <div>
                            <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $student->date_of_birth->format('M d, Y') }}</p>
                    </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Age</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->age . ' years' : 'N/A' }}</p>
                    </div>
                @endif
                        @if($student->qualification)
                <div>
                            <label class="text-sm font-medium text-gray-500">Qualification</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->qualification }}</p>
                        </div>
                        @endif
                        @if(($student->credit_balance ?? 0) > 0)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Credit Balance</label>
                            <p class="text-lg font-semibold text-green-600">₹{{ number_format($student->credit_balance, 0) }}</p>
                            <p class="text-xs text-gray-500">From dropped/removed enrollments — can be applied to new course</p>
                        </div>
                        @endif
                </div>
            </div>
        </div>

            <!-- Contact Information Card -->
        <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                </div>
                <div>
                        <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                        <p class="text-sm text-gray-600">Communication Details</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                        <label class="text-sm font-medium text-gray-500">Email Address</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $student->email }}</p>
                </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">WhatsApp Number</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $student->whatsapp_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Address Information Card -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Address Information</h3>
                        <p class="text-sm text-gray-600">Location Details</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        @if($student->address)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Address</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->address }}</p>
                        </div>
                        @endif
                        @if($student->city)
                        <div>
                            <label class="text-sm font-medium text-gray-500">City</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->city }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="space-y-4">
                        @if($student->state)
                        <div>
                            <label class="text-sm font-medium text-gray-500">State</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->state }}</p>
                        </div>
                        @endif
                        @if($student->pincode)
                <div>
                            <label class="text-sm font-medium text-gray-500">Pincode</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $student->pincode }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                <div>
                        <h3 class="text-lg font-semibold text-gray-900">Student Documents</h3>
                        <p class="text-sm text-gray-600">Uploaded Documents & Files</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $photoDoc = $student->documents()->where('document_type', 'photo')->first();
                        $aadharDoc = $student->documents()->where('document_type', 'aadhar')->first();
                        $certDoc = $student->documents()->where('document_type', 'qualification_certificate')->first();
                    @endphp
                    
                    <!-- Photo Document -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-camera text-blue-600 mr-2"></i>
                                <h4 class="font-semibold text-gray-900">Photo</h4>
                            </div>
                            @if($photoDoc)
                            <div class="flex space-x-1">
                                <button onclick="viewDocument('{{ route('admin.documents.view', $photoDoc) }}', 'Photo', false)" 
                                        class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="updateDocument({{ $photoDoc->id }}, 'photo')" 
                                        class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded bg-green-50 hover:bg-green-100">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="removeDocument({{ $photoDoc->id }})" 
                                        class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded bg-red-50 hover:bg-red-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        @if($photoDoc)
                            <div class="text-center">
                                <img src="{{ route('admin.documents.view', $photoDoc) }}" 
                                     alt="Student Photo" 
                                     class="w-full h-24 object-cover rounded mb-2">
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">No photo uploaded</p>
                                <button onclick="uploadDocument('photo')" class="text-blue-600 hover:text-blue-800 text-xs mt-2">
                                    <i class="fas fa-plus mr-1"></i>Upload Photo
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Aadhar Document -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-id-card text-green-600 mr-2"></i>
                                <h4 class="font-semibold text-gray-900">Aadhar Card</h4>
                            </div>
                            @if($aadharDoc)
                            <div class="flex space-x-1">
                                <button onclick="viewDocument('{{ route('admin.documents.view', $aadharDoc) }}', 'Aadhar Card', {{ str_contains($aadharDoc->file_path, '.pdf') ? 'true' : 'false' }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="updateDocument({{ $aadharDoc->id }}, 'aadhar')" 
                                        class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded bg-green-50 hover:bg-green-100">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="removeDocument({{ $aadharDoc->id }})" 
                                        class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded bg-red-50 hover:bg-red-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        @if($aadharDoc)
                            <div class="text-center">
                                <div class="flex items-center justify-center h-24 bg-gray-100 rounded mb-2">
                                    @if(str_contains($aadharDoc->file_path, '.pdf'))
                                        <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                    @else
                                        <img src="{{ route('admin.documents.view', $aadharDoc) }}" 
                                             alt="Aadhar Document" 
                                             class="w-full h-24 object-cover rounded">
                    @endif
                </div>
                                <p class="text-xs text-gray-600 truncate">{{ $aadharDoc->original_name }}</p>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-id-card text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">No Aadhar uploaded</p>
                                <button onclick="uploadDocument('aadhar')" class="text-blue-600 hover:text-blue-800 text-xs mt-2">
                                    <i class="fas fa-plus mr-1"></i>Upload Aadhar
                                </button>
                </div>
                        @endif
                    </div>
                    
                    <!-- Certificate Document -->
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <i class="fas fa-certificate text-purple-600 mr-2"></i>
                                <h4 class="font-semibold text-gray-900">Certificate</h4>
                            </div>
                            @if($certDoc)
                            <div class="flex space-x-1">
                                <button onclick="viewDocument('{{ route('admin.documents.view', $certDoc) }}', 'Qualification Certificate', {{ str_contains($certDoc->file_path, '.pdf') ? 'true' : 'false' }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="updateDocument({{ $certDoc->id }}, 'qualification_certificate')" 
                                        class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded bg-green-50 hover:bg-green-100">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="removeDocument({{ $certDoc->id }})" 
                                        class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded bg-red-50 hover:bg-red-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                        @if($certDoc)
                            <div class="text-center">
                                <div class="flex items-center justify-center h-24 bg-gray-100 rounded mb-2">
                                    @if(str_contains($certDoc->file_path, '.pdf'))
                                        <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                                    @else
                                        <img src="{{ route('admin.documents.view', $certDoc) }}" 
                                             alt="Certificate Document" 
                                             class="w-full h-24 object-cover rounded">
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 truncate">{{ $certDoc->original_name }}</p>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-certificate text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">No certificate uploaded</p>
                                <button onclick="uploadDocument('certificate')" class="text-blue-600 hover:text-blue-800 text-xs mt-2">
                                    <i class="fas fa-plus mr-1"></i>Upload Certificate
                                </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enrollments Section -->
            @if($student->enrollments->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Enrollments</h3>
                        <p class="text-sm text-gray-600">Course & Batch Information</p>
                    </div>
        </div>
        
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($student->enrollments as $enrollment)
                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $enrollment->batch->course->name ?? 'N/A' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $enrollment->batch->batch_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($enrollment->status === 'active') bg-green-100 text-green-800
                                @elseif($enrollment->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Enrollment Date:</span>
                                <span class="font-medium">{{ $enrollment->enrollment_date->format('M d, Y') }}</span>
                            </div>
                            @if($enrollment->enrollment_number)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Enrollment #:</span>
                                <span class="font-mono font-semibold text-blue-600">{{ $enrollment->enrollment_number }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Fee:</span>
                                <span class="font-medium">₹{{ number_format($enrollment->total_fee) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Paid Amount:</span>
                                <span class="font-medium text-green-600">₹{{ number_format($enrollment->paid_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Outstanding:</span>
                                <span class="font-medium text-red-600">₹{{ number_format($enrollment->outstanding_amount) }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-3 border-t">
                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    <span>{{ $enrollment->batch->start_date->format('M d') ?? 'N/A' }} - {{ $enrollment->batch->end_date->format('M d, Y') ?? 'N/A' }}</span>
                                </div>
                                <div class="flex space-x-2">
                                @if($enrollment->status === 'active')
                                    <button onclick="dropEnrollment({{ $enrollment->id }})" 
                                            class="text-orange-600 hover:text-orange-800 text-xs px-2 py-1 rounded bg-orange-50 hover:bg-orange-100"
                                            title="Drop from batch">
                                        <i class="fas fa-user-minus mr-1"></i>Drop
                                        </button>
                                @endif
                                    <button onclick="removeEnrollment({{ $enrollment->id }})" 
                                            class="text-red-600 hover:text-red-800 text-xs px-2 py-1 rounded bg-red-50 hover:bg-red-100"
                                            title="Remove enrollment completely">
                                        <i class="fas fa-trash mr-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Enrollment Management Actions -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex flex-wrap gap-3">
                        <button onclick="showEnrollModal()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Enroll in New Batch
                        </button>
                        
                        @if($student->enrollments->count() > 0)
                        <button onclick="showForceDeleteModal()" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Force Delete Student
                        </button>
                        @endif
                    </div>
                </div>
        </div>
        @else
            <!-- No Enrollments State -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Enrollments</h3>
                    <p class="text-gray-600 mb-6">This student is not enrolled in any batch yet.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="showEnrollModal()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Enroll in Batch
                        </button>
                        <button onclick="showForceDeleteModal()" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Force Delete Student
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Side - Student Photo (1 column) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Photo</h3>
                    @php
                        $photoDocument = $student->documents()->where('document_type', 'photo')->first();
                    @endphp
                    @if($photoDocument)
                        <div class="relative">
                            <img src="{{ route('admin.documents.view', $photoDocument) }}" 
                                 alt="{{ $student->full_name }} Photo" 
                                 class="w-32 h-40 object-cover mx-auto border-2 border-black shadow-lg"
                                 style="width: 3.5cm; height: 4.5cm; border: 2px solid black;">
                            <div class="mt-3">
                                <div class="flex justify-center space-x-2 mt-3">
                                    <button onclick="viewDocument('{{ route('admin.documents.view', $photoDocument) }}', 'Photo', false)" 
                                            class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                    <button onclick="updateDocument({{ $photoDocument->id }}, 'photo')" 
                                            class="text-green-600 hover:text-green-800 text-xs px-2 py-1 rounded bg-green-50 hover:bg-green-100">
                                        <i class="fas fa-edit mr-1"></i>Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8">
                            <i class="fas fa-camera text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-500 mb-3">No photo uploaded</p>
                            <button onclick="uploadDocument('photo')" 
                                    class="text-blue-600 hover:text-blue-800 text-sm px-3 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                <i class="fas fa-plus mr-1"></i>Upload Photo
                            </button>
                        </div>
                @endif
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Student ID Card</h3>
                    <p class="text-xs text-gray-600 mb-3">Download the official student ID card with photo and details.</p>
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('admin.students.id-card', $student) }}" target="_blank"
                           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            <i class="fas fa-id-card mr-2"></i>View ID Card
                        </a>
                        <a href="{{ route('admin.students.id-card.download', $student) }}"
                           class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>Download ID Card
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document View Modal -->
<div id="documentModal" class="fixed inset-0 bg-black bg-opacity-75 z-[9999] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-6xl w-full max-h-[95vh] overflow-y-auto shadow-2xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Document View</h3>
                    <button onclick="closeDocumentModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-2 hover:bg-gray-100 rounded-full">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <div class="mb-6">
                    <img id="modalImage" src="" alt="Document" class="max-w-full h-auto mx-auto shadow-lg hidden rounded-lg">
                    <iframe id="modalPdf" src="" class="w-full h-[80vh] hidden border rounded-lg shadow-lg"></iframe>
                </div>
                <div class="flex justify-center space-x-3">
                    <a id="modalDownload" href="#" download
                       class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium hidden">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                    <button onclick="closeDocumentModal()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-times mr-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold" id="uploadTitle">Upload Document</h3>
                    <button onclick="closeUploadModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="documentType" name="document_type">
                    <input type="hidden" id="documentId" name="document_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                        <input type="file" id="fileInput" name="file" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" accept="image/*,.pdf">
                    </div>
                    
                    <div id="cropSection" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Crop Photo</label>
                        <p class="text-xs text-gray-500 mb-2">Select a photo above — the crop dialog will open automatically. Crop to 3.5 x 4.5 cm for certificate.</p>
                        <input type="hidden" id="cropData" name="crop_data">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Photo Crop Modal -->
<div id="photoCropModal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Crop Photo</h3>
                        <p class="text-sm text-gray-600">Required size: 3.5 x 4.5 cm (for certificate generation)</p>
                    </div>
                    <button type="button" onclick="closePhotoCropModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mb-4 max-h-[60vh] overflow-hidden">
                    <img id="photoCropImage" src="" alt="Crop" class="max-w-full block" style="max-height: 50vh;">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePhotoCropModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="applyPhotoCrop()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.12/dist/cropper.min.css">
<script>
    function viewDocument(url, title, isPdf = false) {
        try {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalImage').classList.add('hidden');
            document.getElementById('modalPdf').classList.add('hidden');
            const downloadLink = document.getElementById('modalDownload');
            if (downloadLink) {
                downloadLink.href = url + (url.includes('?') ? '&' : '?') + 'download=1';
                downloadLink.classList.remove('hidden');
            }
            
            // Check if URL is valid
            if (!url || url === '') {
                alert('Document not found or URL is invalid.');
                return;
            }
            
            if (isPdf) {
                document.getElementById('modalPdf').src = url;
                document.getElementById('modalPdf').classList.remove('hidden');
            } else {
                document.getElementById('modalImage').src = url;
                document.getElementById('modalImage').classList.remove('hidden');
            }
            
            document.getElementById('documentModal').classList.remove('hidden');
        } catch (error) {
            console.error('Error viewing document:', error);
            alert('Error loading document. Please try again.');
        }
    }

    function closeDocumentModal() {
        document.getElementById('documentModal').classList.add('hidden');
        document.getElementById('modalImage').src = '';
        document.getElementById('modalPdf').src = '';
        const downloadLink = document.getElementById('modalDownload');
        if (downloadLink) {
            downloadLink.classList.add('hidden');
            downloadLink.removeAttribute('href');
        }
    }

    let uploadCropper = null;

    function uploadDocument(type) {
        // Map display types to actual document types
        const typeMapping = {
            'photo': 'photo',
            'aadhar': 'aadhar', 
            'certificate': 'qualification_certificate'
        };
        
        const actualType = typeMapping[type] || type;
        const displayName = type === 'qualification_certificate' ? 'Qualification Certificate' : 
                           type.charAt(0).toUpperCase() + type.slice(1);
        
        document.getElementById('uploadTitle').textContent = 'Upload ' + displayName;
        document.getElementById('documentType').value = actualType;
        document.getElementById('documentId').value = '';
        document.getElementById('fileInput').value = '';
        document.getElementById('cropData').value = '';
        
        if (actualType === 'photo') {
            document.getElementById('cropSection').classList.remove('hidden');
            document.getElementById('fileInput').accept = 'image/*';
        } else {
            document.getElementById('cropSection').classList.add('hidden');
            document.getElementById('fileInput').accept = '.pdf,.jpg,.jpeg,.png';
        }
        
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function updateDocument(documentId, type) {
        // Map display types to actual document types
        const typeMapping = {
            'photo': 'photo',
            'aadhar': 'aadhar', 
            'certificate': 'qualification_certificate'
        };
        
        const actualType = typeMapping[type] || type;
        const displayName = type === 'qualification_certificate' ? 'Qualification Certificate' : 
                           type.charAt(0).toUpperCase() + type.slice(1);
        
        document.getElementById('uploadTitle').textContent = 'Update ' + displayName;
        document.getElementById('documentType').value = actualType;
        document.getElementById('documentId').value = documentId;
        document.getElementById('fileInput').value = '';
        document.getElementById('cropData').value = '';
        
        if (actualType === 'photo') {
            document.getElementById('cropSection').classList.remove('hidden');
            document.getElementById('fileInput').accept = 'image/*';
        } else {
            document.getElementById('cropSection').classList.add('hidden');
            document.getElementById('fileInput').accept = '.pdf,.jpg,.jpeg,.png';
        }
        
        document.getElementById('uploadModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('photoCropModal').classList.add('hidden');
        document.getElementById('uploadForm').reset();
        if (uploadCropper) {
            uploadCropper.destroy();
            uploadCropper = null;
        }
    }

    function openPhotoCropModal(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('photoCropImage');
            img.src = e.target.result;
            document.getElementById('photoCropModal').classList.remove('hidden');
            setTimeout(() => {
                if (uploadCropper) {
                    uploadCropper.destroy();
                }
                uploadCropper = new Cropper(img, {
                    aspectRatio: 3.5 / 4.5,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    minCropBoxWidth: 100,
                    minCropBoxHeight: 128,
                    toggleDragModeOnDblclick: false,
                });
            }, 100);
        };
        reader.readAsDataURL(file);
    }

    function closePhotoCropModal() {
        document.getElementById('photoCropModal').classList.add('hidden');
        if (uploadCropper) {
            uploadCropper.destroy();
            uploadCropper = null;
        }
    }

    function applyPhotoCrop() {
        if (uploadCropper) {
            const cropData = uploadCropper.getData();
            document.getElementById('cropData').value = JSON.stringify(cropData);
            closePhotoCropModal();
        }
    }

    function removeDocument(documentId) {
        if (confirm('Are you sure you want to remove this document?')) {
            fetch(`/admin/students/{{ $student->id }}/documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error removing document: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error removing document');
            });
        }
    }

    // When photo file is selected, open crop modal
    document.getElementById('fileInput').addEventListener('change', function() {
        const docType = document.getElementById('documentType').value;
        const file = this.files[0];
        if (docType === 'photo' && file && file.type.startsWith('image/')) {
            openPhotoCropModal(file);
        }
    });

    // Handle form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const documentId = document.getElementById('documentId').value;
        const url = documentId ? 
            `/admin/students/{{ $student->id }}/documents/${documentId}` : 
            `/admin/students/{{ $student->id }}/documents`;
        
        // Use POST with _method spoofing for PUT - PHP doesn't parse multipart/form-data for PUT requests
        if (documentId) {
            formData.append('_method', 'PUT');
        }
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error uploading document: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading document');
        });
    });

    // Enrollment Management Functions
    function dropEnrollment(enrollmentId) {
        if (confirm('Are you sure you want to drop this student from the batch? This will mark the enrollment as dropped but keep the data.')) {
            // Create a form to submit via POST with _method PATCH
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/enrollments/${enrollmentId}/drop`;
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            form.appendChild(methodField);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    }

    function removeEnrollment(enrollmentId) {
        if (confirm('⚠️ WARNING: This will permanently remove the enrollment and ALL related data (payments, assessment results, certificates). This action cannot be undone!\n\nType "DELETE" in the next prompt to confirm.')) {
            const confirmation = prompt('Type DELETE to confirm permanent removal:');
            if (confirmation === 'DELETE') {
                // Create a form to submit via POST with _method DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/enrollments/${enrollmentId}/remove`;
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Add method override
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                // Submit form
                document.body.appendChild(form);
                form.submit();
            }
        }
    }

    function showForceDeleteModal() {
        document.getElementById('forceDeleteModal').classList.remove('hidden');
    }

    function closeForceDeleteModal() {
        document.getElementById('forceDeleteModal').classList.add('hidden');
        document.getElementById('forceDeleteForm').reset();
    }

    function showEnrollModal() {
        document.getElementById('enrollModal').classList.remove('hidden');
    }

    function closeEnrollModal() {
        document.getElementById('enrollModal').classList.add('hidden');
        document.getElementById('enrollForm').reset();
    }

    // Client-side validation for force delete - only prevent if invalid
    document.getElementById('forceDeleteForm')?.addEventListener('submit', function(e) {
        const inp = document.getElementById('confirmation');
        if (inp && inp.value.trim() !== 'REMOVE') {
            e.preventDefault();
            alert('Please type REMOVE (in capital letters) to confirm.');
        }
    });

    // Handle enrollment form - validate only; allow native submit so server redirect works
    const enrollForm = document.getElementById('enrollForm');
    if (enrollForm) {
        enrollForm.addEventListener('submit', function(e) {
            const courseId = document.getElementById('course_id').value;
            const batchId = document.getElementById('batch_id').value;
            const enrollmentDate = document.querySelector('input[name="enrollment_date"]').value;
            const totalFee = document.getElementById('courseFeeInput').value;

            if (!courseId || !batchId || !enrollmentDate || !totalFee) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            // Form submits normally; server redirects to payment create page
        });
    }
</script>

<!-- Force Delete Modal -->
<div id="forceDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000]">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Force Delete Student</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    ⚠️ <strong>WARNING:</strong> This will permanently delete the student and ALL related data including:
                </p>
                <ul class="text-sm text-gray-600 text-left mb-4 space-y-1">
                    <li>• All enrollments and batch assignments</li>
                    <li>• All payment records and allocations</li>
                    <li>• All assessment results and certificates</li>
                    <li>• All uploaded documents and photos</li>
                    <li>• User account and login credentials</li>
                </ul>
                <p class="text-sm text-red-600 font-medium mb-4">This action cannot be undone!</p>
                
                <form id="forceDeleteForm" method="POST" action="{{ route('admin.students.force-delete.post', $student) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="font-mono bg-gray-100 px-2 py-1 rounded">REMOVE</span> to confirm:
                        </label>
                        <input type="text" id="confirmation" name="confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                               placeholder="Type REMOVE here" required autocomplete="off">
                    </div>
                    
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="closeForceDeleteModal()"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-trash mr-2"></i>Force Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Modal -->
<div id="enrollModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-[10000]">
    <div class="relative top-10 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Enroll Student in Batch</h3>
                <button onclick="closeEnrollModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="enrollForm" action="{{ route('admin.students.enroll', $student) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                        <select name="course_id" id="course_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a course</option>
                            @foreach(\App\Models\Course::where('is_active', true)->orderBy('name')->get() as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                        <select name="batch_id" id="batch_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a batch</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Date</label>
                        <input type="date" name="enrollment_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course Fee (₹)</label>
                        <input type="number" id="courseFeeInput" name="total_fee" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50"
                               placeholder="0.00" readonly required>
                        <p class="text-xs text-gray-500 mt-1">
                            <span id="feeBreakdown">Total will be: Registration Fee (₹100) + Course Fee + Exam Fee (₹100)</span>
                        </p>
                    </div>

                    @if(($student->credit_balance ?? 0) > 0)
                    <div id="creditApplySection" class="border border-green-200 rounded-lg p-3 bg-green-50">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-wallet text-green-600 mr-1"></i>Apply Credit Balance
                        </label>
                        <p class="text-xs text-gray-600 mb-2">Available: <strong>₹{{ number_format($student->credit_balance, 0) }}</strong></p>
                        <input type="number" id="creditToApplyInput" name="credit_to_apply" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="0" value="0">
                        <p class="text-xs text-gray-500 mt-1" id="creditApplyHint">Enter amount to apply (max: min of credit balance and total fee)</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                        <textarea name="remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEnrollModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-graduation-cap mr-2"></i>Enroll Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const studentCreditBalance = {{ ($student->credit_balance ?? 0) }};

    // Load course fee and batches when course is selected
    const courseSelect = document.getElementById('course_id');
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
    const courseId = this.value;
    const batchSelect = document.getElementById('batch_id');
    const courseFeeInput = document.getElementById('courseFeeInput');
    const feeBreakdown = document.getElementById('feeBreakdown');
    const creditToApplyInput = document.getElementById('creditToApplyInput');
    const creditApplyHint = document.getElementById('creditApplyHint');
    
    // Clear existing options and reset fee
    batchSelect.innerHTML = '<option value="">Select a batch</option>';
    courseFeeInput.value = '';
    feeBreakdown.textContent = 'Total will be: Registration Fee (₹100) + Course Fee + Exam Fee (₹100)';
    if (creditToApplyInput) creditToApplyInput.value = '0';
    
    if (courseId) {
        // Load course details to get fee
        fetch(`/admin/api/course-details/${courseId}`)
            .then(response => response.json())
            .then(course => {
                if (course.success) {
                    courseFeeInput.value = course.data.course_fee;
                    feeBreakdown.textContent = `Total will be: Registration Fee (₹${course.data.registration_fee}) + Course Fee (₹${course.data.course_fee}) + Exam Fee (₹${course.data.assessment_fee}) = ₹${course.data.total_fee}`;
                    if (creditToApplyInput && studentCreditBalance > 0) {
                        const maxCredit = Math.min(studentCreditBalance, course.data.total_fee);
                        creditToApplyInput.max = maxCredit;
                        creditApplyHint.textContent = `Max applicable: ₹${Math.round(maxCredit)}`;
                    }
                }
            })
            .catch(error => {
                console.error('Error loading course details:', error);
            });
        
        // Load batches for the course
        fetch(`/admin/api/student-batches/by-course?course_id=${courseId}`)
            .then(response => response.json())
            .then(batches => {
                batches.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent = `${batch.batch_name} (${batch.start_date} - ${batch.end_date}) - ${batch.max_students || 'Unlimited'} students`;
                    batchSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading batches:', error);
            });
        }
    });
    }

});
</script>
@endsection
