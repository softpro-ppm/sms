<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.pwa-meta')
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

        .sidebar-compact .sidebar-brand {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .sidebar-compact .sidebar-brand-toggle {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .sidebar-compact .sidebar-item {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            transform: none !important;
        }

        .sidebar-compact .sidebar-item i {
            margin-right: 0 !important;
        }

        .sidebar-compact .sidebar-label,
        .sidebar-compact .sidebar-chevron,
        .sidebar-compact .sidebar-submenu,
        .sidebar-compact .sidebar-user-text {
            display: none !important;
        }

        .sidebar-compact .sidebar-badge {
            position: absolute;
            top: 0.45rem;
            right: 0.55rem;
            margin-left: 0 !important;
            min-width: 1.1rem;
            height: 1.1rem;
            padding: 0 0.2rem;
            font-size: 0.65rem;
            line-height: 1.1rem;
        }

        .sidebar-compact .sidebar-user-card {
            justify-content: center;
        }

        .sidebar-compact .sidebar-user-avatar {
            margin-right: 0 !important;
        }

        /* Force-hide mobile overlay on desktop - prevents stuck overlay bug */
        @media (min-width: 1024px) {
            .mobile-sidebar-overlay { display: none !important; pointer-events: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    @if(session()->has('impersonation'))
    <div class="fixed top-0 left-0 right-0 z-[60] bg-amber-400 text-amber-950 px-3 py-2.5 flex flex-wrap items-center justify-center gap-3 text-sm shadow-md border-b border-amber-500">
        <span class="text-center"><i class="fas fa-user-secret mr-1" aria-hidden="true"></i> Viewing <strong>{{ session('impersonation.training_partner_name') }}</strong> as {{ auth()->user()->name }}. Actions in this session affect this partner only.</span>
        <form method="POST" action="{{ route('admin.impersonation.leave') }}" class="shrink-0">
            @csrf
            <button type="submit" class="px-3 py-1.5 bg-amber-950 text-amber-50 rounded-lg text-xs font-semibold hover:bg-black transition-colors">Return to Super Admin</button>
        </form>
    </div>
    @endif
    <div id="admin-pwa-install-prompt" class="fixed bottom-4 right-4 z-40 hidden w-[22rem] max-w-[calc(100vw-2rem)] rounded-2xl border border-gray-200 bg-white p-4 shadow-lg">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-950">Install SoftPro Workspace</p>
                <p class="mt-1 text-sm leading-5 text-gray-600">Install the admin workspace for faster access on desktop or mobile.</p>
            </div>
            <button type="button" id="admin-pwa-dismiss" class="rounded-full p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" id="admin-install-btn" class="inline-flex items-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-primary-700">
                <i class="fas fa-download mr-2 text-xs"></i>
                Install app
            </button>
        </div>
        <p id="admin-ios-install-tip" class="mt-3 hidden text-xs leading-5 text-gray-500">
            On iPhone or iPad, tap <strong>Share</strong> and choose <strong>Add to Home Screen</strong>.
        </p>
    </div>
    <div class="flex h-screen @if(session()->has('impersonation')) pt-11 @endif"
         x-data="{ sidebarOpen: false, desktopSidebarCollapsed: false }"
         x-init="if (window.innerWidth >= 1024) { sidebarOpen = false }"
         @resize.window="if (window.innerWidth >= 1024) { sidebarOpen = false }"
         @keydown.escape.window="sidebarOpen = false">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 bg-gradient-to-b from-primary-800 to-primary-900 transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col"
             x-cloak
             :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', desktopSidebarCollapsed ? 'lg:w-[88px] sidebar-compact' : 'lg:w-64', 'w-64']">
            
            <!-- Logo -->
            <div class="sidebar-brand relative flex items-center justify-between h-16 px-4 bg-primary-900">
                <div class="flex items-center space-x-2 overflow-hidden">
                    <!-- SoftPro Logo -->
                    <img src="{{ asset('images/logo/Logo_png.png') }}" 
                         alt="SoftPro Logo" 
                         class="h-8 w-auto bg-white rounded-lg p-1 shadow-sm">
                    <span class="sidebar-label text-white font-bold text-lg whitespace-nowrap">SoftPro</span>
                </div>
                <button type="button"
                        @click="desktopSidebarCollapsed = !desktopSidebarCollapsed"
                        class="sidebar-brand-toggle hidden lg:inline-flex items-center justify-center h-9 w-9 rounded-lg text-primary-100 hover:bg-white/10 transition"
                        :title="desktopSidebarCollapsed ? 'Expand menu' : 'Collapse menu'">
                    <i class="fas" :class="desktopSidebarCollapsed ? 'fa-angles-right' : 'fa-thumbtack'"></i>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8 px-4 flex-1 overflow-y-auto pb-6">
                <ul class="space-y-2">
                    @if(auth()->user()->is_super_admin)
                    <li>
                        <a href="{{ route('admin.super.dashboard') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.super.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Super Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.help') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.help') ? 'active' : '' }}">
                            <i class="fas fa-circle-question w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">How it works</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.super.training-partners.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.super.training-partners.*') ? 'active' : '' }}">
                            <i class="fas fa-building w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Training Partners</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.super.impersonation-log.index') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.super.impersonation-log.*') ? 'active' : '' }}">
                            <i class="fas fa-user-secret w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Impersonation log</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.courses.index') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Courses &amp; LMS</span>
                        </a>
                    </li>
                    @else
                    {{-- Admin / Reception navigation --}}
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.help') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.help') ? 'active' : '' }}">
                            <i class="fas fa-circle-question w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">How it works</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.students.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.student-deletion-requests.*') ? 'active' : '' }}">
                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Students</span>
                            @if(($studentAttentionCount ?? 0) > 0)
                                <span class="sidebar-badge ml-auto bg-warning-500 text-white text-xs px-2 py-1 rounded-full">{{ $studentAttentionCount ?? 0 }}</span>
                            @endif
                        </a>
                    </li>
                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('admin.student-deletion-requests.index') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.student-deletion-requests.*') ? 'active' : '' }}">
                            <i class="fas fa-user-shield w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Deletion Requests</span>
                            @if(($pendingDeletionRequests ?? 0) > 0)
                                <span class="sidebar-badge ml-auto bg-warning-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingDeletionRequests }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.legacy-students.index') }}"
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.legacy-students.*') ? 'active' : '' }}">
                            <i class="fas fa-archive w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Legacy Students</span>
                        </a>
                    </li>
                    @endif
                    
                    <li class="relative">
                        <a href="{{ route('admin.payments.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Payments</span>
                            @if(($pendingPayments ?? 0) > 0)
                                <span class="sidebar-badge ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingPayments ?? 0 }}</span>
                            @endif
                            <i class="sidebar-chevron fas fa-chevron-down ml-auto text-xs"></i>
                        </a>
                        
                        <!-- Submenu -->
                        <div class="sidebar-submenu ml-4 mt-2 space-y-1 {{ request()->routeIs('admin.payments.*') ? 'block' : 'hidden' }}">
                            <a href="{{ route('admin.payments.index') }}" 
                               class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-primary-700 {{ request()->routeIs('admin.payments.index') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-list w-4 h-4 mr-2"></i>
                                <span class="sidebar-label">All Payments</span>
                            </a>
                            <a href="{{ route('admin.payments.pending') }}" 
                               class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-primary-700 {{ request()->routeIs('admin.payments.pending') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-clock w-4 h-4 mr-2"></i>
                                <span class="sidebar-label">Pending Payments</span>
                            </a>
                            <a href="{{ route('admin.payments.pending-approvals') }}"
                               class="sidebar-item flex items-center px-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-primary-700 {{ request()->routeIs('admin.payments.pending-approvals') ? 'bg-primary-700' : '' }}">
                                <i class="fas fa-user-check w-4 h-4 mr-2"></i>
                                <span class="sidebar-label">Pending Approvals</span>
                                @if(($pendingPayments ?? 0) > 0)
                                    <span class="sidebar-badge ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingPayments ?? 0 }}</span>
                                @endif
                            </a>
                        </div>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.batches.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.batches.*') ? 'active' : '' }}">
                            <i class="fas fa-layer-group w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Batches</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.results.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Results</span>
                        </a>
                    </li>

                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('admin.courses.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Courses</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.question-banks.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.question-banks.*') ? 'active' : '' }}">
                            <i class="fas fa-database w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Question Banks</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.assessments.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Exams</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Reports</span>
                        </a>
                    </li>
                    @endif
                    
                    <li>
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}">
                            <i class="fas fa-certificate w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Certificates</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.whatsapp.inbox') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.whatsapp.inbox') ? 'active' : '' }}">
                            <i class="fab fa-whatsapp w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">WhatsApp Inbox</span>
                        </a>
                    </li>
                    
                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="sidebar-item relative flex items-center px-4 py-3 text-white rounded-lg {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog w-5 h-5 mr-3"></i>
                            <span class="sidebar-label">Settings</span>
                        </a>
                    </li>
                    @endif
                    @endif
                </ul>
            </nav>
            
            <!-- User Info -->
            <div class="mt-auto p-4 bg-primary-900">
                <div class="sidebar-user-card flex items-center space-x-3">
                    <div class="sidebar-user-avatar w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="sidebar-user-text flex-1 min-w-0">
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
                <div class="max-w-[1600px] mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-white py-3 px-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <nav class="flex flex-wrap gap-4 text-sm">
                        <a href="{{ route('verify.index') }}" class="text-gray-600 hover:text-gray-900" target="_blank">Verify Student</a>
                        <a href="{{ route('privacy') }}" class="text-gray-600 hover:text-gray-900" target="_blank">Privacy</a>
                        <a href="{{ route('terms') }}" class="text-gray-600 hover:text-gray-900" target="_blank">Terms</a>
                    </nav>
                    <p class="text-sm text-gray-500">© {{ date('Y') }} Student Management System</p>
                </div>
            </footer>
        </div>

        <!-- Mobile Sidebar Overlay (safe defaults even if Alpine fails) -->
        <div
            class="mobile-sidebar-overlay fixed inset-0 z-40 bg-gray-600 bg-opacity-75 opacity-0 pointer-events-none"
            style="display: none;"
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="sidebarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
            @click="sidebarOpen = false"
        ></div>
    </div>
    
    <!-- Global Notifications -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <!-- Global Notification Script -->
    <script>
        // Auto-hide notifications after 5-10 seconds
        function showNotification(message, type = 'success', duration = 7000) {
            if (!message || !String(message).trim()) return;
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
        
        // Show session notifications on page load (skip empty messages to avoid stray green box)
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success') && trim(session('success')))
                showNotification({!! json_encode(session('success')) !!}, 'success', 7000);
            @endif
            
            @if(session('error') && trim(session('error')))
                showNotification({!! json_encode(session('error')) !!}, 'error', 7000);
            @endif
            
            @if(session('warning') && trim(session('warning')))
                showNotification({!! json_encode(session('warning')) !!}, 'warning', 7000);
            @endif
            
            @if(session('info') && trim(session('info')))
                showNotification({!! json_encode(session('info')) !!}, 'info', 7000);
            @endif

            initAdminPwaInstallPrompt();
        });

        function initAdminPwaInstallPrompt() {
            const card = document.getElementById('admin-pwa-install-prompt');
            const installButton = document.getElementById('admin-install-btn');
            const dismissButton = document.getElementById('admin-pwa-dismiss');
            const iosTip = document.getElementById('admin-ios-install-tip');
            const dismissKey = 'admin-pwa-install-dismissed';
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
            const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
            let deferredInstallPrompt = null;

            dismissButton?.addEventListener('click', () => {
                localStorage.setItem(dismissKey, '1');
                card.classList.add('hidden');
            });

            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                deferredInstallPrompt = event;

                if (!isStandalone && !localStorage.getItem(dismissKey)) {
                    card.classList.remove('hidden');
                }
            });

            if (isIos && !isStandalone && !localStorage.getItem(dismissKey)) {
                iosTip.classList.remove('hidden');
                card.classList.remove('hidden');
                installButton.classList.add('hidden');
            }

            installButton?.addEventListener('click', async () => {
                if (!deferredInstallPrompt) return;

                deferredInstallPrompt.prompt();
                const choice = await deferredInstallPrompt.userChoice;
                if (choice.outcome === 'accepted') {
                    localStorage.setItem(dismissKey, '1');
                    card.classList.add('hidden');
                }
                deferredInstallPrompt = null;
            });
        }
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
