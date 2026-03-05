<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiple Matches - Student Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4 mb-4">
                <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SoftPro Logo" class="h-14 w-auto bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-2xl font-bold">SOFTPRO SKILL SOLUTIONS</h1>
                    <p class="text-sm text-blue-200">Student Verification</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('verify.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Search
            </a>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-amber-800 flex items-center">
                <i class="fas fa-users mr-2"></i>
                Multiple records found
            </h3>
            <p class="text-amber-700 text-sm mt-1">More than one student matches your search. Please select your record using the unique details below.</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Aadhar</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Enrollment(s)</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $student->full_name }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($student->aadhar_number)
                                    **** **** {{ substr($student->aadhar_number, -4) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($student->enrollments as $enrollment)
                                        <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-mono rounded">
                                            {{ $enrollment->enrollment_number }}
                                        </span>
                                    @endforeach
                                    @if($student->enrollments->isEmpty())
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('verify.result', $student) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-4 text-sm text-gray-500 text-center">
            <i class="fas fa-info-circle mr-1"></i>
            Use Aadhar number or Enrollment number for a unique search result.
        </p>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} SoftPro Skill Solutions. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
