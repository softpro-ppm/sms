<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SOFTPRO Student Management System</title>
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
                    fontFamily: { 'sans': ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <!-- Yellow top strip -->
    <div class="bg-amber-400 text-black text-sm py-2 px-4 text-center">
        <span>Admissions Open. Limited seats available.</span>
        <a href="{{ route('verify.index') }}" class="font-semibold underline ml-2 hover:text-gray-800">Verify Student</a>
    </div>

    <!-- Black header -->
    <header class="bg-gray-900 text-white px-4 sm:px-6 py-4 flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SOFTPRO" class="h-10 w-auto bg-white rounded-lg p-1">
            <span class="font-bold text-xl">SOFTPRO</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('verify.index') }}" class="bg-amber-400 text-black px-4 py-2 rounded font-medium hover:bg-amber-300 transition-colors">Verify Student</a>
            <a href="{{ route('register') }}" class="border border-amber-400 text-amber-400 px-4 py-2 rounded font-medium hover:bg-amber-400 hover:text-black transition-colors">Register</a>
        </div>
    </header>

    @yield('content')

    <footer class="py-4 text-center">
        <p class="text-sm text-gray-600">© {{ date('Y') }} Student Management System. All rights reserved.</p>
    </footer>

    @yield('scripts')
</body>
</html>
