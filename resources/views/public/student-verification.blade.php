<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Verification - SoftPro Skill Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-softpro text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4 mb-4">
                <img src="{{ asset('images/logo/Logo_png.png') }}" 
                     alt="SoftPro Logo" 
                     class="h-14 w-auto bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-2xl font-bold">SOFTPRO SKILL SOLUTIONS</h1>
                    <p class="text-sm text-blue-200">Skill Development & Training Institute</p>
                </div>
            </div>
            <p class="text-center text-blue-200 mt-2">Student Verification Portal</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-2xl mx-auto">
            <!-- Search Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-softpro px-8 py-6 text-white">
                    <h2 class="text-2xl font-bold mb-2">Verify Your Enrollment</h2>
                    <p class="text-blue-200">Search by full name, phone number, or Aadhar number</p>
                </div>
                
                <div class="p-8">
                    <form method="POST" action="{{ route('verify.search') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Search Input -->
                        <div>
                            <label for="search_term" class="block text-sm font-medium text-gray-700 mb-2">
                                Search Details <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="search_term" 
                                       name="search_term" 
                                       value="{{ old('search_term') }}"
                                       class="block w-full px-4 py-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg @error('search_term') border-red-500 @enderror"
                                       placeholder="Enter Full Name, Phone Number, or Aadhar Number"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                            @error('search_term')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Search Examples -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Search criteria:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li><i class="fas fa-user mr-2"></i>Full Name</li>
                                <li><i class="fas fa-phone mr-2"></i>Phone / WhatsApp Number</li>
                                <li><i class="fas fa-id-card mr-2"></i>Aadhar Number</li>
                                <li><i class="fas fa-hashtag mr-2"></i>Enrollment Number (e.g., SP20253000)</li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-softpro text-white py-4 px-6 rounded-lg font-semibold text-lg hover:opacity-90 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-search mr-2"></i>
                            Search & Verify
                        </button>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    About Student Verification
                </h3>
                <div class="space-y-3 text-gray-600">
                    <p>This portal allows you to verify your enrollment details and view your academic progress at SoftPro Skill Solutions.</p>
                    <p>You can search using any of your registration details to access your complete profile information.</p>
                    <p>All information displayed is for verification purposes only and is kept confidential.</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-phone text-green-600 mr-3"></i>
                    Need Help?
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                    <div>
                        <p><i class="fas fa-phone mr-2"></i>Phone: 7799773656</p>
                        <p><i class="fas fa-envelope mr-2"></i>Email: skill.softpro@gmail.com</p>
                    </div>
                    <div>
                        <p><i class="fas fa-map-marker-alt mr-2"></i>Parvathipuram Manyam</p>
                        <p><i class="fas fa-globe mr-2"></i>Andhra Pradesh, 535501</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} SoftPro Skill Solutions. All rights reserved.</p>
            <p class="text-gray-400 mt-2">Student Management System</p>
        </div>
    </footer>

    <!-- Auto-dismissing notifications -->
    @if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <script>
        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            const notifications = document.querySelectorAll('.fixed.top-4.right-4');
            notifications.forEach(notification => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.5s';
                setTimeout(() => notification.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
