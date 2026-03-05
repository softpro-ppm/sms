@extends('layouts.auth')

@section('title', 'Register')
@section('heading', 'Create your account')
@section('subheading', 'Join our institute today')

@section('content')
<form method="POST" action="{{ route('register') }}" class="space-y-6">
    @csrf
    
    <!-- Aadhar Number -->
    <div>
        <label for="aadhar_number" class="block text-sm font-medium text-gray-700 mb-2">
            Aadhar Number <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-id-card text-gray-400"></i>
            </div>
            <input id="aadhar_number" 
                   name="aadhar_number" 
                   type="text" 
                   maxlength="12"
                   required 
                   value="{{ old('aadhar_number') }}"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('aadhar_number') border-red-500 @enderror"
                   placeholder="Enter 12-digit Aadhar number"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   pattern="[0-9]{12}">
        </div>
        @error('aadhar_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Full Name -->
    <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
            Full Name <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user text-gray-400"></i>
            </div>
            <input id="full_name" 
                   name="full_name" 
                   type="text" 
                   required 
                   value="{{ old('full_name') }}"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('full_name') border-red-500 @enderror"
                   placeholder="Enter your full name"
                   oninput="capitalizeWords(this)"
                   onblur="capitalizeWords(this)">
        </div>
        @error('full_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Father Name -->
    <div>
        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
            Father's Name
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-user-friends text-gray-400"></i>
            </div>
            <input id="father_name" 
                   name="father_name" 
                   type="text" 
                   value="{{ old('father_name') }}"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('father_name') border-red-500 @enderror"
                   placeholder="Enter father's name"
                   oninput="capitalizeWords(this)"
                   onblur="capitalizeWords(this)">
        </div>
        @error('father_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Gender and Qualification -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Gender -->
        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                Gender <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-venus-mars text-gray-400"></i>
                </div>
                <select id="gender" 
                        name="gender" 
                        required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('gender') border-red-500 @enderror">
                    <option value="" {{ old('gender') == '' ? 'selected' : '' }}>Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Qualification -->
        <div>
            <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">
                Qualification <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-graduation-cap text-gray-400"></i>
                </div>
                <select id="qualification" 
                        name="qualification" 
                        required
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('qualification') border-red-500 @enderror">
                    <option value="" {{ old('qualification') == '' ? 'selected' : '' }}>Select Qualification</option>
                    <option value="ITI" {{ old('qualification') == 'ITI' ? 'selected' : '' }}>ITI</option>
                    <option value="Post Graduate" {{ old('qualification') == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                    <option value="Below SSC" {{ old('qualification') == 'Below SSC' ? 'selected' : '' }}>Below SSC</option>
                    <option value="SSC" {{ old('qualification') == 'SSC' ? 'selected' : '' }}>SSC</option>
                    <option value="Intermediate" {{ old('qualification') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Graduation" {{ old('qualification') == 'Graduation' ? 'selected' : '' }}>Graduation</option>
                    <option value="B Tech" {{ old('qualification') == 'B Tech' ? 'selected' : '' }}>B Tech</option>
                    <option value="Diploma" {{ old('qualification') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                </select>
            </div>
            @error('qualification')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input id="email" 
                   name="email" 
                   type="email" 
                   autocomplete="email" 
                   required 
                   value="{{ old('email') }}"
                   inputmode="email"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-500 @enderror"
                   placeholder="Enter your email"
                   oninput="validateEmail(this)"
                   onblur="normalizeEmail(this)">
        </div>
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- WhatsApp Number -->
    <div>
        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">
            WhatsApp Number <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fab fa-whatsapp text-gray-400"></i>
            </div>
            <input id="whatsapp_number" 
                   name="whatsapp_number" 
                   type="tel" 
                   required
                   maxlength="10"
                   value="{{ old('whatsapp_number') }}"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('whatsapp_number') border-red-500 @enderror"
                   placeholder="Enter 10-digit WhatsApp number"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
        @error('whatsapp_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500">This will be used as your login password</p>
    </div>

    <!-- Date of Birth -->
    <div>
        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
            Date of Birth
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-calendar text-gray-400"></i>
            </div>
            <input id="date_of_birth" 
                   name="date_of_birth" 
                   type="date" 
                   value="{{ old('date_of_birth') }}"
                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('date_of_birth') border-red-500 @enderror">
        </div>
        @error('date_of_birth')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Address -->
    <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
            Address
        </label>
        <textarea id="address" 
                  name="address" 
                  rows="3"
                  class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('address') border-red-500 @enderror"
                  placeholder="Enter your address"
                  onblur="capitalizeWords(this)">{{ old('address') }}</textarea>
        @error('address')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- City, State, Pincode -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                City
            </label>
            <input id="city" 
                   name="city" 
                   type="text" 
                   value="{{ old('city') }}"
                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('city') border-red-500 @enderror"
                   placeholder="City"
                   onblur="capitalizeWords(this)">
            @error('city')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                State
            </label>
            <input id="state" 
                   name="state" 
                   type="text" 
                   value="{{ old('state') }}"
                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('state') border-red-500 @enderror"
                   placeholder="State"
                   onblur="capitalizeWords(this)">
            @error('state')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">
                Pincode
            </label>
            <input id="pincode" 
                   name="pincode" 
                   type="text" 
                   maxlength="10"
                   value="{{ old('pincode') }}"
                   class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pincode') border-red-500 @enderror"
                   placeholder="Pincode">
            @error('pincode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Terms and Conditions -->
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <input id="terms" 
                   name="terms" 
                   type="checkbox" 
                   required
                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
        </div>
        <div class="ml-3 text-sm">
            <label for="terms" class="text-gray-700">
                I agree to the 
                <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 hover:text-primary-500">Terms of Service</a>
                and 
                <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 hover:text-primary-500">Privacy Policy</a>
            </label>
        </div>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" 
                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <i class="fas fa-user-plus text-primary-500 group-hover:text-primary-400"></i>
            </span>
            Create Account
        </button>
    </div>

    <!-- Login Link -->
    <div class="text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                Sign in here
            </a>
        </p>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Capitalize first letter of each word - works for Full Name, Address, City, State
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
</script>
@endsection
