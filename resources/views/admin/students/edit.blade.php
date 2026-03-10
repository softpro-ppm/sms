@extends('layouts.admin')

@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user-edit text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Edit Student</h2>
                    <p class="text-primary-100 text-sm">Update student information</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.students.update', $student) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Personal Information Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Aadhar Number -->
                    <div>
                        <label for="aadhar_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Aadhar Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="aadhar_number" 
                               name="aadhar_number" 
                               value="{{ old('aadhar_number', $student->aadhar_number) }}"
                               maxlength="12"
                               pattern="[0-9]{12}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('aadhar_number') border-red-500 @enderror"
                               placeholder="Enter 12-digit Aadhar number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('aadhar_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth (dd/mm/yyyy format for India) -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Birth
                        </label>
                        <input type="text" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                               placeholder="dd/mm/yyyy"
                               autocomplete="off"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="gender" 
                                name="gender" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('gender') border-red-500 @enderror">
                            <option value="" {{ old('gender', $student->gender) == '' ? 'selected' : '' }}>Select Gender</option>
                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               value="{{ old('full_name', $student->full_name) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('full_name') border-red-500 @enderror"
                               placeholder="Enter full name"
                               oninput="capitalizeWords(this)"
                               onblur="capitalizeWords(this)">
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Father Name -->
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Father's Name
                        </label>
                        <input type="text" 
                               id="father_name" 
                               name="father_name" 
                               value="{{ old('father_name', $student->father_name) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('father_name') border-red-500 @enderror"
                               placeholder="Enter father's name"
                               oninput="capitalizeWords(this)"
                               onblur="capitalizeWords(this)">
                        @error('father_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Qualification -->
                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                            Qualification <span class="text-red-500">*</span>
                        </label>
                        <select id="qualification" 
                                name="qualification" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('qualification') border-red-500 @enderror">
                            <option value="">Select Qualification</option>
                            <option value="ITI" {{ old('qualification', $student->qualification ?? '') == 'ITI' ? 'selected' : '' }}>ITI</option>
                            <option value="Post Graduate" {{ old('qualification', $student->qualification ?? '') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                            <option value="Below SSC" {{ old('qualification', $student->qualification ?? '') == 'Below SSC' ? 'selected' : '' }}>Below SSC</option>
                            <option value="SSC" {{ old('qualification', $student->qualification ?? '') == 'SSC' ? 'selected' : '' }}>SSC</option>
                            <option value="Intermediate" {{ old('qualification', $student->qualification ?? '') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Graduation" {{ old('qualification', $student->qualification ?? '') == 'Graduation' ? 'selected' : '' }}>Graduation</option>
                            <option value="B Tech" {{ old('qualification', $student->qualification ?? '') == 'B Tech' ? 'selected' : '' }}>B Tech</option>
                            <option value="Diploma" {{ old('qualification', $student->qualification ?? '') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                        </select>
                        @error('qualification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $student->email) }}"
                               required
                               inputmode="email"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                               placeholder="Enter email address"
                               oninput="validateEmail(this)"
                               onblur="normalizeEmail(this)">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- WhatsApp Number -->
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
                            WhatsApp Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" 
                               id="whatsapp_number" 
                               name="whatsapp_number" 
                               value="{{ old('whatsapp_number', $student->whatsapp_number) }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('whatsapp_number') border-red-500 @enderror"
                               placeholder="Enter WhatsApp number (10 digits)"
                               maxlength="10"
                               pattern="[0-9]{10}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               title="Please enter exactly 10 digits">
                        @error('whatsapp_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                
                <div class="space-y-6">
                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea id="address" 
                                  name="address" 
                                  rows="3"
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('address') border-red-500 @enderror"
                                  placeholder="Enter complete address"
                                  onblur="capitalizeWords(this)">{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $student->city) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('city') border-red-500 @enderror"
                                   placeholder="Enter city"
                                   onblur="capitalizeWords(this)">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State
                            </label>
                            <input type="text" 
                                   id="state" 
                                   name="state" 
                                   value="{{ old('state', $student->state) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('state') border-red-500 @enderror"
                                   placeholder="Enter state"
                                   onblur="capitalizeWords(this)">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pincode -->
                        <div>
                            <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">
                                Pincode
                            </label>
                            <input type="text" 
                                   id="pincode" 
                                   name="pincode" 
                                   value="{{ old('pincode', $student->pincode) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pincode') border-red-500 @enderror"
                                   placeholder="Enter pincode (6 digits)"
                                   maxlength="6"
                                   pattern="[0-9]{6}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   title="Please enter exactly 6 digits">
                            @error('pincode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Status</h3>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Registration Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('status') border-red-500 @enderror">
                        <option value="pending" {{ old('status', $student->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status', $student->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $student->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-800">Status Information</span>
                        </div>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Pending:</strong> Student registered but not yet approved</li>
                                <li><strong>Approved:</strong> Student can access their account and enroll in courses</li>
                                <li><strong>Rejected:</strong> Student registration has been rejected</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.students.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-800 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Date of Birth - dd/mm/yyyy format (India), consistent across all devices
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#date_of_birth', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            maxDate: 'today',
            disableMobile: true,
            allowInput: false
        });
    });

    // Function to capitalize first letter of each word - works for Full Name, Address, City, State
    function capitalizeWords(input) {
        if (!input) return;
        if (input.value === undefined || input.value === null || input.value === '') return;
        
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const originalValue = input.value;

        // Capitalize first letter of each word
        input.value = input.value.replace(/\b\w/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });

        if (input.value !== originalValue && typeof start === 'number' && typeof end === 'number') {
            input.setSelectionRange(start, end);
        }
    }
    
    // Make function globally accessible
    window.capitalizeWords = capitalizeWords;

    function validateEmail(input) {
        if (!input) return;
        const value = input.value.trim();
        if (value === '') {
            input.setCustomValidity('');
            return;
        }
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!pattern.test(value)) {
            input.setCustomValidity('Please enter a valid email address');
        } else {
            input.setCustomValidity('');
        }
    }

    function normalizeEmail(input) {
        if (!input) return;
        input.value = input.value.trim().toLowerCase();
        validateEmail(input);
        input.reportValidity();
    }

    // Format Aadhar number input
    document.getElementById('aadhar_number').addEventListener('input', function(e) {
        // Remove any non-numeric characters
        let value = e.target.value.replace(/\D/g, '');
        
        // Limit to 12 digits
        if (value.length > 12) {
            value = value.substring(0, 12);
        }
        
        e.target.value = value;
    });

    // Format phone numbers
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        input.value = value;
    }

    document.getElementById('phone').addEventListener('input', function(e) {
        formatPhoneNumber(e.target);
    });

    document.getElementById('whatsapp_number').addEventListener('input', function(e) {
        formatPhoneNumber(e.target);
    });

    // Format pincode
    document.getElementById('pincode').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });

    // Status change handler
    document.getElementById('status').addEventListener('change', function(e) {
        const status = e.target.value;
        const statusInfo = document.querySelector('.bg-blue-50');
        
        if (status === 'approved') {
            statusInfo.classList.remove('bg-blue-50');
            statusInfo.classList.add('bg-green-50');
            statusInfo.querySelector('.text-blue-600').classList.remove('text-blue-600');
            statusInfo.querySelector('.text-blue-600').classList.add('text-green-600');
            statusInfo.querySelector('.text-blue-800').classList.remove('text-blue-800');
            statusInfo.querySelector('.text-blue-800').classList.add('text-green-800');
            statusInfo.querySelector('.text-blue-700').classList.remove('text-blue-700');
            statusInfo.querySelector('.text-blue-700').classList.add('text-green-700');
        } else if (status === 'rejected') {
            statusInfo.classList.remove('bg-blue-50');
            statusInfo.classList.add('bg-red-50');
            statusInfo.querySelector('.text-blue-600').classList.remove('text-blue-600');
            statusInfo.querySelector('.text-blue-600').classList.add('text-red-600');
            statusInfo.querySelector('.text-blue-800').classList.remove('text-blue-800');
            statusInfo.querySelector('.text-blue-800').classList.add('text-red-800');
            statusInfo.querySelector('.text-blue-700').classList.remove('text-blue-700');
            statusInfo.querySelector('.text-blue-700').classList.add('text-red-700');
        } else {
            statusInfo.classList.remove('bg-green-50', 'bg-red-50');
            statusInfo.classList.add('bg-blue-50');
            statusInfo.querySelector('.text-green-600, .text-red-600').classList.remove('text-green-600', 'text-red-600');
            statusInfo.querySelector('.text-green-600, .text-red-600').classList.add('text-blue-600');
            statusInfo.querySelector('.text-green-800, .text-red-800').classList.remove('text-green-800', 'text-red-800');
            statusInfo.querySelector('.text-green-800, .text-red-800').classList.add('text-blue-800');
            statusInfo.querySelector('.text-green-700, .text-red-700').classList.remove('text-green-700', 'text-red-700');
            statusInfo.querySelector('.text-green-700, .text-red-700').classList.add('text-blue-700');
        }
    });
</script>
@endsection
