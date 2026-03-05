@extends('layouts.admin')

@section('title', 'Add New Student')
@section('page-title', 'Add New Student')

@section('content')
@php
    // Ensure session is started for CSRF token generation
    if (!session()->isStarted()) {
        session()->start();
    }
@endphp
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Add New Student</h2>
                    <p class="text-primary-100 text-sm">Create a new student account</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" onsubmit="return validateCreateStudentForm()">
            @csrf
            
            <!-- Personal Information Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                
                <!-- First Row: Aadhar Number, Full Name and Father Name -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Aadhar Number -->
                    <div>
                        <label for="aadhar_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Aadhar Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="aadhar_number" 
                               name="aadhar_number" 
                               value="{{ old('aadhar_number') }}"
                               maxlength="12"
                               pattern="[0-9]{12}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('aadhar_number') border-red-500 @enderror"
                               placeholder="Enter 12-digit Aadhar number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('aadhar_number')
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
                               value="{{ old('full_name') }}"
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
                               value="{{ old('father_name') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('father_name') border-red-500 @enderror"
                               placeholder="Enter father's name"
                               oninput="capitalizeWords(this)"
                               onblur="capitalizeWords(this)">
                        @error('father_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Second Row: Gender, Date of Birth, Qualification -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="gender" 
                                name="gender" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('gender') border-red-500 @enderror">
                            <option value="" {{ old('gender') == '' ? 'selected' : '' }}>Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of Birth
                        </label>
                        <input type="date" 
                               id="date_of_birth" 
                               name="date_of_birth" 
                               value="{{ old('date_of_birth') }}"
                               max="{{ date('Y-m-d') }}"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
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
                               value="{{ old('email') }}"
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
                               value="{{ old('whatsapp_number') }}"
                               maxlength="10"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('whatsapp_number') border-red-500 @enderror"
                               placeholder="Enter 10-digit WhatsApp number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                                  onblur="capitalizeWords(this)">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City, State, Pincode -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city') }}"
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
                                   value="{{ old('state') }}"
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
                                   value="{{ old('pincode') }}"
                                   maxlength="6"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pincode') border-red-500 @enderror"
                                   placeholder="Enter 6-digit pincode"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('pincode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Upload Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-upload text-blue-600 mr-3"></i>
                    Document Upload
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Photo Upload with Crop -->
                    <div class="upload-section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera mr-2"></i>
                            Photo <span class="text-red-500">*</span>
                        </label>
                        <div id="photo-upload-area" class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <input type="file" id="photo" name="photo" accept="image/*" capture="camera" class="hidden" onchange="handlePhotoUpload(this)">
                            <input type="hidden" id="photo_crop_data" name="photo_crop_data">
                            <div id="photo-placeholder" class="py-8" onclick="openFileDialog('photo')">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Click to upload photo</p>
                                <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG</p>
                            </div>
                            <div id="photo-preview" class="hidden">
                                <div id="photo-crop-container" class="mb-3">
                                    <img id="photo-img" src="" alt="Photo preview" class="w-full h-32 object-cover rounded mb-2">
                                    <div class="flex justify-center space-x-2">
                                        <button type="button" onclick="event.stopPropagation(); openCropModal();" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">
                                            <i class="fas fa-crop mr-1"></i>Crop
                                        </button>
                                        <button type="button" onclick="event.stopPropagation(); removePhoto();" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
                                            <i class="fas fa-trash mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                                <p id="photo-name" class="text-xs text-gray-600"></p>
                            </div>
                        </div>
                        <p id="photo-error" class="mt-1 text-sm text-red-600 hidden">Photo is required.</p>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aadhar Upload -->
                    <div class="upload-section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2"></i>
                            Aadhar Card
                        </label>
                        <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors" onclick="openFileDialog('aadhar')">
                            <input type="file" id="aadhar" name="aadhar" accept=".pdf,.jpg,.jpeg,.png" capture="camera" class="hidden" onchange="handleDocumentUpload(this, 'aadhar')">
                            <div id="aadhar-placeholder" class="py-8">
                                <i class="fas fa-id-card text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Click to upload Aadhar</p>
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG, PNG</p>
                            </div>
                            <div id="aadhar-preview" class="hidden">
                                <div class="flex items-center justify-center h-32 bg-gray-100 rounded mb-2">
                                    <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                </div>
                                <p id="aadhar-name" class="text-xs text-gray-600"></p>
                                <button type="button" onclick="removeDocument('aadhar')" class="text-red-600 hover:text-red-800 text-xs mt-1">
                                    <i class="fas fa-trash mr-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Qualification Certificate Upload -->
                    <div class="upload-section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-certificate mr-2"></i>
                            Qualification Certificate
                        </label>
                        <div class="upload-area border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors" onclick="openFileDialog('certificate')">
                            <input type="file" id="certificate" name="certificate" accept=".pdf,.jpg,.jpeg,.png" capture="camera" class="hidden" onchange="handleDocumentUpload(this, 'certificate')">
                            <div id="certificate-placeholder" class="py-8">
                                <i class="fas fa-certificate text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Click to upload certificate</p>
                                <p class="text-xs text-gray-500 mt-1">PDF, JPG, JPEG, PNG</p>
                            </div>
                            <div id="certificate-preview" class="hidden">
                                <div class="flex items-center justify-center h-32 bg-gray-100 rounded mb-2">
                                    <i class="fas fa-file-pdf text-4xl text-red-500"></i>
                                </div>
                                <p id="certificate-name" class="text-xs text-gray-600"></p>
                                <button type="button" onclick="removeDocument('certificate')" class="text-red-600 hover:text-red-800 text-xs mt-1">
                                    <i class="fas fa-trash mr-1"></i>Remove
                                </button>
                    </div>
                </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.students.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors duration-200 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Create Student
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Photo Crop Modal -->
<div id="cropModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Crop Photo</h3>
                        <p class="text-sm text-gray-600">Required size: 3.5 x 4.5 cm (for certificate generation)</p>
                    </div>
                    <button onclick="closeCropModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <img id="cropImage" src="" alt="Crop" class="max-w-full h-auto">
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeCropModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                        Cancel
                    </button>
                    <button onclick="applyCrop()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
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
    let cropper = null;
    let currentPhotoFile = null;

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

    function openFileDialog(type) {
        document.getElementById(type).click();
    }

    function handlePhotoUpload(input) {
        const file = input.files[0];
        if (file) {
            currentPhotoFile = file;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo-placeholder').classList.add('hidden');
                document.getElementById('photo-preview').classList.remove('hidden');
                document.getElementById('photo-img').src = e.target.result;
                document.getElementById('photo-name').textContent = file.name;
                
                // Show success message
                showNotification('Photo uploaded successfully! Click "Crop" to edit if needed.', 'success');

                // Auto-open crop modal for every photo upload
                openCropModal();
            };
            reader.readAsDataURL(file);
        }
    }

    function handleDocumentUpload(input, type) {
        const file = input.files[0];
        if (file) {
            document.getElementById(type + '-placeholder').classList.add('hidden');
            document.getElementById(type + '-preview').classList.remove('hidden');
            document.getElementById(type + '-name').textContent = file.name;
            
            // Show success message
            showNotification(type.charAt(0).toUpperCase() + type.slice(1) + ' uploaded successfully!', 'success');
        }
    }

    function removePhoto() {
        document.getElementById('photo').value = '';
        document.getElementById('photo-placeholder').classList.remove('hidden');
        document.getElementById('photo-preview').classList.add('hidden');
        document.getElementById('photo_crop_data').value = '';
        currentPhotoFile = null;
        hidePhotoError();
    }

    function removeDocument(type) {
        document.getElementById(type).value = '';
        document.getElementById(type + '-placeholder').classList.remove('hidden');
        document.getElementById(type + '-preview').classList.add('hidden');
    }

    function openCropModal() {
        const photoImg = document.getElementById('photo-img');
        if (photoImg.src) {
            document.getElementById('cropImage').src = photoImg.src;
            document.getElementById('cropModal').classList.remove('hidden');
            
            // Initialize cropper
            setTimeout(() => {
                const image = document.getElementById('cropImage');
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(image, {
                    aspectRatio: 3.5 / 4.5, // 3.5 cm width / 4.5 cm height for certificate photo
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
                    minCropBoxHeight: 128, // Maintain aspect ratio
                    toggleDragModeOnDblclick: false,
                });
            }, 100);
        }
    }

    function closeCropModal() {
        document.getElementById('cropModal').classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    function applyCrop() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            if (canvas) {
                // Update the preview image
                const dataURL = canvas.toDataURL('image/jpeg', 0.9);
                document.getElementById('photo-img').src = dataURL;
                
                // Store crop data for form submission
                const cropData = cropper.getData();
                document.getElementById('photo_crop_data').value = JSON.stringify(cropData);
                
                showNotification('Photo cropped successfully!', 'success');
                closeCropModal();
            }
        }
    }

    function showPhotoError(message = 'Photo is required.') {
        const errorEl = document.getElementById('photo-error');
        const uploadArea = document.getElementById('photo-upload-area');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }
        if (uploadArea) {
            uploadArea.classList.add('border-red-500');
            uploadArea.classList.remove('border-gray-300');
        }
    }

    function hidePhotoError() {
        const errorEl = document.getElementById('photo-error');
        const uploadArea = document.getElementById('photo-upload-area');
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
        if (uploadArea) {
            uploadArea.classList.remove('border-red-500');
            uploadArea.classList.add('border-gray-300');
        }
    }

    function validateCreateStudentForm() {
        const photoInput = document.getElementById('photo');
        if (!photoInput || !photoInput.files || photoInput.files.length === 0) {
            showPhotoError('Photo is required.');
            const uploadArea = document.getElementById('photo-upload-area');
            if (uploadArea) {
                uploadArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        hidePhotoError();
        return true;
    }

    // Global notification function
    function showNotification(message, type = 'success') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const notification = document.createElement('div');
        notification.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg fixed top-4 right-4 z-50 transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[type]} mr-2"></i>
                <span class="flex-1">${message}</span>
                <button onclick="removeNotification(this)" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Slide in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            removeNotification(notification.querySelector('button'));
        }, 5000);
    }

    function removeNotification(button) {
        const notification = button.closest('div');
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
</script>
@endsection
