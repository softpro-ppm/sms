<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management System') - SOFTPRO</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Yellow top strip (softpro.co.in style) -->
    <div class="bg-amber-400 text-black text-sm py-2 px-4 text-center">
        <span>Admissions Open. Limited seats available.</span>
        <a href="{{ route('verify.index') }}" class="font-semibold underline ml-2 hover:text-gray-800">Verify Student</a>
    </div>

    <!-- Black header with logo -->
    <header class="bg-gray-900 text-white px-4 sm:px-6 py-4 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-10 w-auto bg-white rounded-lg p-1">
            <span class="font-bold text-xl">SOFTPRO</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('verify.index') }}" class="bg-amber-400 text-black px-4 py-2 rounded font-medium hover:bg-amber-300 transition-colors">
                Verify Student
            </a>
            <a href="{{ route('login') }}" class="bg-amber-400 text-black px-4 py-2 rounded font-medium hover:bg-amber-300 transition-colors">
                Student Login
            </a>
        </div>
    </header>

    <div class="min-h-[calc(100vh-120px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo and heading -->
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-16 w-auto bg-white rounded-xl p-2 shadow-lg">
                </div>
                <h2 class="text-2xl font-bold text-gray-900">
                    @yield('heading', 'Welcome')
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    @yield('subheading', 'Student Management System')
                </p>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    @yield('footer', '© ' . date('Y') . ' Student Management System. All rights reserved.')
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    @yield('scripts')
</body>
</html>
