<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen font-sans">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full text-center">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- SoftPro Logo -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/logo/Logo_png.png') }}" 
                         alt="SoftPro Logo" 
                         class="h-20 w-auto bg-white rounded-lg p-2 shadow-lg">
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">SoftPro</h1>
                <p class="text-gray-600 mb-8">Student Management System</p>
                <div class="space-y-4">
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="block w-full bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors duration-200">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
