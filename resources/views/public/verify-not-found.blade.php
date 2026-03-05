<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Not Found - Softpro Skill Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4">
                <img src="{{ asset('images/logo/Logo_png.png') }}" alt="Logo" class="h-12 w-auto bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-xl font-bold">SOFTPRO SKILL SOLUTIONS</h1>
                    <p class="text-sm text-blue-200">Student Verification</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12 max-w-md">
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-500 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Student Not Found</h2>
            <p class="text-gray-600 mb-4">No student found with enrollment number: <span class="font-mono font-semibold">{{ $enrollment_no }}</span></p>
            <p class="text-sm text-gray-500 mb-6">The ID may be invalid or has been removed. Please check the enrollment number and try again.</p>
            <a href="{{ route('verify.index') }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>
                Try Another Search
            </a>
        </div>
    </div>
</body>
</html>
