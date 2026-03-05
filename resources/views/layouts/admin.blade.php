<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Management System') - Admin Panel</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Global Table Styles -->
    <link href="{{ asset('css/global-table.css') }}" rel="stylesheet">
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
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        warning: {
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
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'bounce-slow': 'bounce 2s infinite',
                        'pulse-slow': 'pulse 3s infinite',
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Global Table JavaScript -->
    <script src="{{ asset('js/global-table.js') }}"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .sidebar-item {
            transition: all 0.3s ease;
        }
        
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-right: 4px solid #fbbf24;
        }
    </style>
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
                        <a href="{{ route('admin.dashboard') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.students.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                            <span>Students</span>
                            @if($pendingStudents ?? 0 > 0)
                                <span class="ml-auto bg-warning-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingStudents ?? 0 }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <li class="relative">
                        <a href="{{ route('admin.payments.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-5 h-5 mr-3"></i>
                            <span>Payments</span>
                            @if($pendingPayments ?? 0 > 0)
                                <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingPayments ?? 0 }}</span>
                            @endif
                            <i class="fas fa-chevron-down ml-auto text-xs"></i>
                        </a>
                        
                        <!-- Submenu -->
                        <div class="ml-4 mt-2 space-y-1 {{ request()->routeIs('admin.payments.*') ? 'block' : 'hidden' }}">
                            <a href="{{ route('admin.payments.index') }}" 
                               class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-primary-700 {{ request()->routeIs('admin.payments.index') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-list w-4 h-4 mr-2"></i>
                                <span>All Payments</span>
                            </a>
                            <a href="{{ route('admin.payments.pending') }}" 
                               class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-primary-700 {{ request()->routeIs('admin.payments.pending') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-clock w-4 h-4 mr-2"></i>
                                <span>Pending Payments</span>
                            </a>
                        </div>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.batches.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.batches.*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group w-5 h-5 mr-3"></i>
                            <span>Batches</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.courses.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book w-5 h-5 mr-3"></i>
                            <span>Courses</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.question-banks.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.question-banks.*') ? 'active' : '' }}">
                            <i class="fas fa-database w-5 h-5 mr-3"></i>
                            <span>Question Banks</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.assessments.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check w-5 h-5 mr-3"></i>
                            <span>Exams</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.results.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                            <span>Results</span>
                        </a>
                    </li>

                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie w-5 h-5 mr-3"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    @endif
                    
                    <li>
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                            <i class="fas fa-certificate w-5 h-5 mr-3"></i>
                            <span>Certificates</span>
                        </a>
                    </li>
                    
                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="sidebar-item flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    @endif
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
                        <p class="text-xs text-primary-200 truncate">{{ ucfirst(auth()->user()->role) }}</p>
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
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full relative">
                                <i class="fas fa-bell w-5 h-5"></i>
                                @if(!empty($topbarNotificationCount))
                                    <span class="absolute -top-1 -right-1 bg-danger-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $topbarNotificationCount }}
                                    </span>
                                @endif
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @if(!empty($topbarNotifications) && $topbarNotifications->count() > 0)
                                        @foreach($topbarNotifications as $notification)
                                            <a href="{{ $notification['url'] ?? '#' }}" class="block p-4 border-b border-gray-100 hover:bg-gray-50">
                                                <div class="flex items-start space-x-3">
                                                    <div class="w-2 h-2 {{ ($notification['type'] ?? 'primary') === 'warning' ? 'bg-warning-500' : 'bg-primary-500' }} rounded-full mt-2"></div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900">{{ $notification['title'] ?? 'Notification' }}</p>
                                                        <p class="text-xs text-gray-500">{{ $notification['message'] ?? '' }}</p>
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            {{ !empty($notification['time']) ? $notification['time']->diffForHumans() : '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="p-4 text-sm text-gray-500">No new notifications.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
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
                                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile
                                    </a>
                                    @if(auth()->user()->is_admin)
                                    <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>Settings
                                    </a>
                                    @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const debounce = (fn, delay = 800) => {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn(...args), delay);
                };
            };

            const submitClosestForm = (element) => {
                const form = element.closest('form');
                if (form) {
                    form.submit();
                }
            };

            const restoreSearchFocus = () => {
                const inputs = Array.from(document.querySelectorAll('input[data-live-search]'));
                const target = inputs.find((input) => input.value && input.value.trim().length > 0);
                if (target) {
                    target.focus();
                    const len = target.value.length;
                    if (typeof target.setSelectionRange === 'function') {
                        target.setSelectionRange(len, len);
                    }
                }
            };

            document.querySelectorAll('input[data-live-search]').forEach((input) => {
                const handler = debounce(() => submitClosestForm(input), 800);
                input.addEventListener('input', handler);
            });

            document.querySelectorAll('select[data-live-rows], select[data-live-filter], input[data-live-filter]').forEach((element) => {
                element.addEventListener('change', () => submitClosestForm(element));
            });

            restoreSearchFocus();
        });
    </script>
    
    @yield('scripts')
</body>
</html>
