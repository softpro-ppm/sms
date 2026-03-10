<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - SOFTPRO</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .sp-marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }
        .sp-marquee-text {
            display: inline-block;
            padding-left: 100%;
            animation: sp-marquee 18s linear infinite;
        }
        @keyframes sp-marquee {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Yellow top strip (softpro.co.in style) -->
    <div class="bg-amber-400 text-black text-xs sm:text-sm py-2 px-4 flex items-center justify-between gap-4">
        <div class="sp-marquee-container flex-1">
            <div class="sp-marquee-text">
                Admissions Open. Limited seats available, Enroll Now.
            </div>
        </div>
        <a href="https://softpro.co.in" target="_blank" rel="noopener"
           class="ml-4 inline-flex items-center px-3 py-1 rounded-md bg-gray-900 text-white text-xs sm:text-sm font-semibold shadow-sm hover:bg-gray-800 transition-colors whitespace-nowrap">
            Explore Courses
        </a>
    </div>

    <!-- Black header with logo -->
    <header class="bg-gray-900 text-white px-4 sm:px-6 py-4 flex flex-wrap justify-between items-center gap-4">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-10 w-auto bg-white rounded-lg p-1">
            <span class="font-bold text-xl">SOFTPRO</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('verify.index') }}" class="bg-amber-400 text-black px-4 py-2 rounded font-medium hover:bg-amber-300 transition-colors">
                Verify Student
            </a>
            <a href="{{ route('login') }}" class="bg-amber-400 text-black px-4 py-2 rounded font-medium hover:bg-amber-300 transition-colors">
                Student Login
            </a>
        </div>
    </header>

    <div class="min-h-[calc(100vh-120px)] flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <!-- SoftPro Logo -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/logo/Logo_png.png') }}" 
                         alt="SoftPro Logo" 
                         class="h-20 w-auto bg-white rounded-xl p-2 shadow-lg">
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">SoftPro</h1>
                <p class="text-gray-600 mb-8">Student Management System</p>
                <div class="space-y-4">
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-amber-400 hover:bg-amber-500 text-black px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="block w-full border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                        Register
                    </a>
                    <a href="{{ route('verify.index') }}" 
                       class="block w-full text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors">
                        Verify Student
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 text-center">
        <p class="text-sm text-gray-600">© {{ date('Y') }} Student Management System. All rights reserved.</p>
    </footer>
</body>
</html>
