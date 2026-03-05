<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen"
         x-data="{ sidebarOpen: false }"
         x-init="if (window.innerWidth >= 768) { sidebarOpen = false }"
         @resize.window="if (window.innerWidth >= 768) { sidebarOpen = false }"
         @keydown.escape.window="sidebarOpen = false">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-primary-800 to-primary-900 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
             x-cloak
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 px-4 bg-primary-900">
                <div class="flex items-center space-x-2">
                    <!-- SoftPro Logo -->
                    <img src="{{ asset('images/logo/Logo_png.png') }}" 
                         alt="SoftPro Logo" 
                         class="h-8 w-auto bg-white rounded-lg p-1 shadow-sm">
                    <span class="text-white font-bold text-lg">SoftPro</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8 px-4 flex-1 overflow-y-auto pb-6">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('student.dashboard') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('student.profile') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                            <i class="fas fa-user w-5 h-5 mr-3"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('student.enrollments') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.enrollments') ? 'active' : '' }}">
                            <i class="fas fa-book w-5 h-5 mr-3"></i>
                            <span>My Courses</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('student.payments') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.payments') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-5 h-5 mr-3"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('student.assessments') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.assessments') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check w-5 h-5 mr-3"></i>
                            <span>Exams</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('student.certificates') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.certificates') ? 'active' : '' }}">
                            <i class="fas fa-certificate w-5 h-5 mr-3"></i>
                            <span>Certificates</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('student.id-card') }}" target="_blank"
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('student.id-card*') ? 'active' : '' }}">
                            <i class="fas fa-id-card w-5 h-5 mr-3"></i>
                            <span>ID Card</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- User Info -->
            <div class="mt-auto p-4 bg-primary-900">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-primary-200 truncate">Student</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                            <i class="fas fa-bars w-5 h-5"></i>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Student Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden md:block text-sm font-medium">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="py-1">
                                    <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden opacity-0 pointer-events-none"
         :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
         @click="sidebarOpen = false"></div>
    
    <!-- Global Notifications -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <!-- Global Notification Script -->
    <script>
        // Auto-hide notifications after 5-10 seconds
        function showNotification(message, type = 'success', duration = 7000) {
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
            notification.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${icons[type]} mr-2"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="removeNotification(this)" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.getElementById('notification-container').appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto-hide after duration
            setTimeout(() => {
                removeNotification(notification.querySelector('button'));
            }, duration);
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
        
        // Show session notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success', 7000);
            @endif
            
            @if(session('error'))
                showNotification('{{ session('error') }}', 'error', 7000);
            @endif
            
            @if(session('warning'))
                showNotification('{{ session('warning') }}', 'warning', 7000);
            @endif
            
            @if(session('info'))
                showNotification('{{ session('info') }}', 'info', 7000);
            @endif
        });
    </script>
    
    @yield('scripts')
</body>
</html>
