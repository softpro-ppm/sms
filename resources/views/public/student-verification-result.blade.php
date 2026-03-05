<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Verification Result - {{ $student->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }
        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4 mb-4">
                <img src="{{ asset('images/logo/Logo_png.png') }}" 
                     alt="SoftPro Logo" 
                     class="h-14 w-auto bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-2xl font-bold">SOFTPRO SKILL SOLUTIONS</h1>
                    <p class="text-sm text-blue-200">Student Verification Result</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('verify.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Search
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Student Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Student Profile Card -->
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-3"></i>
                        Student Profile
                    </h3>
                    
                    @php
                        $photoDoc = $student->documents()->where('document_type', 'photo')->first();
                        $firstEnrollment = $student->enrollments->first();
                        $photoUrl = ($photoDoc && $firstEnrollment && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoDoc->file_path))
                            ? route('verify.photo', $firstEnrollment->enrollment_number) : null;
                    @endphp
                    <div class="flex flex-col sm:flex-row gap-6 mb-6">
                        @if($photoUrl)
                            <div class="flex-shrink-0">
                                <img src="{{ $photoUrl }}" alt="{{ $student->full_name }}" class="w-28 h-36 object-cover rounded-lg border-2 border-gray-200">
                            </div>
                        @endif
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Full Name</label>
                                <p class="text-lg text-gray-900">{{ $student->full_name }}</p>
                            </div>
                            @if($student->father_name)
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Father's Name</label>
                                <p class="text-lg text-gray-900">{{ $student->father_name }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Date of Birth</label>
                                <p class="text-lg text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->format('d-m-Y') : 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Gender</label>
                                <p class="text-lg text-gray-900">{{ ucfirst($student->gender ?? 'Not specified') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Qualification</label>
                                <p class="text-lg text-gray-900">{{ $student->qualification ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Email</label>
                                <p class="text-lg text-gray-900">
                                    @if($student->email)
                                        @php
                                            $parts = explode('@', $student->email);
                                            $local = $parts[0] ?? '';
                                            $domain = $parts[1] ?? '';
                                            $masked = strlen($local) > 2 ? substr($local, 0, 2) . '***@' . $domain : '***@' . $domain;
                                        @endphp
                                        {{ $masked }}
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Phone</label>
                                <p class="text-lg text-gray-900">
                                    @if($student->phone)
                                        *******{{ substr($student->phone, -4) }}
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">WhatsApp</label>
                                <p class="text-lg text-gray-900">
                                    @if($student->whatsapp_number)
                                        *******{{ substr($student->whatsapp_number, -4) }}
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Aadhar Number</label>
                                <p class="text-lg text-gray-900">
                                    @if($student->aadhar_number)
                                        **** **** {{ substr(preg_replace('/\D/', '', $student->aadhar_number), -4) }}
                                    @else
                                        Not provided
                                    @endif
                                </p>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Enrollment Details -->
                @if($student->enrollments && $student->enrollments->count() > 0)
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                        Enrollment Details
                    </h3>
                    
                    @foreach($student->enrollments as $enrollment)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 {{ $loop->last ? '' : 'mb-4' }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-semibold text-gray-800">
                                {{ $enrollment->batch->course->name ?? 'Unknown Course' }}
                            </h4>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($enrollment->status === 'active') bg-green-100 text-green-800
                                @elseif($enrollment->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600"><strong>Enrollment Number:</strong></p>
                                <p class="text-lg font-mono font-bold text-blue-600">{{ $enrollment->enrollment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Batch:</strong></p>
                                <p class="text-lg text-gray-900">{{ $enrollment->batch->batch_name ?? 'Unknown Batch' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Enrollment Date:</strong></p>
                                <p class="text-lg text-gray-900">{{ $enrollment->enrollment_date->format('d-m-Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Batch Duration:</strong></p>
                                <p class="text-lg text-gray-900">
                                    {{ $enrollment->batch->start_date->format('d-m-Y') }} to {{ $enrollment->batch->end_date->format('d-m-Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Exam Results -->
                @if($student->assessmentResults && $student->assessmentResults->count() > 0)
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-purple-600 mr-3"></i>
                        Exam Results
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-200 px-4 py-3 text-left text-sm font-semibold text-gray-700">Exam</th>
                                    <th class="border border-gray-200 px-4 py-3 text-center text-sm font-semibold text-gray-700">Date</th>
                                    <th class="border border-gray-200 px-4 py-3 text-center text-sm font-semibold text-gray-700">Score</th>
                                    <th class="border border-gray-200 px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->assessmentResults as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-200 px-4 py-3 text-sm text-gray-700">
                                        {{ $result->assessment->title ?? 'Unknown Exam' }}
                                    </td>
                                    <td class="border border-gray-200 px-4 py-3 text-sm text-gray-700 text-center">
                                        {{ $result->created_at->format('d-m-Y') }}
                                    </td>
                                    <td class="border border-gray-200 px-4 py-3 text-sm text-gray-700 text-center font-semibold">
                                        @if($result->total_questions && $result->correct_answers !== null)
                                            {{ $result->correct_answers }}/{{ $result->total_questions }} ({{ number_format($result->percentage ?? 0, 1) }}%)
                                        @else
                                            {{ $result->percentage !== null ? number_format($result->percentage, 1) . '%' : 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="border border-gray-200 px-4 py-3 text-center">
                                        @if($result->completed_at || $result->percentage !== null)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <i class="fas fa-{{ $result->is_passed ? 'check' : 'times' }} mr-1"></i>{{ $result->is_passed ? 'Passed' : 'Failed' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-purple-600 mr-3"></i>
                        Exam Results
                    </h3>
                    <div class="text-center py-8">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">No assessment results available yet.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Additional Information -->
            <div class="space-y-6">

                <!-- Verification Status -->
                <div class="info-card bg-green-50 border border-green-200">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-shield-alt text-green-600 mr-3"></i>
                        Verification Status
                    </h3>
                    <div class="space-y-2">
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Student Record Verified</span>
                        </div>
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Enrollment Details Confirmed</span>
                        </div>
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Academic Records Validated</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-200">
                        <p class="text-xs text-green-600">
                            <i class="fas fa-clock mr-1"></i>
                            Verified on {{ now()->format('d-m-Y H:i:s') }}
                        </p>
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
</body>
</html>
