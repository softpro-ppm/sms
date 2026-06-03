<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/logo/Logo_png.png') }}" type="image/png">
    <title>Student Verification — {{ $student->full_name }}</title>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-600 mr-3"></i>
                        Verified learner
                    </h3>
                    @php
                        $photoDoc = $student->documents->firstWhere('document_type', 'photo');
                        $firstEnrollment = $student->enrollments->first();
                        $photoUrl = ($photoDoc && $firstEnrollment && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoDoc->file_path))
                            ? route('verify.photo', $firstEnrollment->enrollment_number) : null;
                    @endphp
                    <div class="flex flex-col sm:flex-row gap-6 items-start">
                        @if($photoUrl)
                            <div class="flex-shrink-0">
                                <img src="{{ $photoUrl }}" alt="{{ $student->full_name }}" class="w-28 h-36 object-cover rounded-lg border-2 border-gray-200">
                            </div>
                        @endif
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Full name</label>
                            <p class="text-xl text-gray-900 font-semibold">{{ $student->full_name }}</p>
                        </div>
                    </div>
                </div>

                @if($student->enrollments && $student->enrollments->count() > 0)
                <div class="info-card">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                        Enrollment details
                    </h3>
                    @foreach($student->enrollments as $enrollment)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 last:mb-0">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-semibold text-gray-800">
                                {{ $enrollment->display_course_name }}
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
                                <p class="text-sm text-gray-600"><strong>Enrollment number</strong></p>
                                <p class="text-lg font-mono font-bold text-blue-600">{{ $enrollment->enrollment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Batch</strong></p>
                                <p class="text-lg text-gray-900">{{ $enrollment->batch->batch_name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Enrollment date</strong></p>
                                <p class="text-lg text-gray-900">{{ $enrollment->enrollment_date->format('d-m-Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><strong>Training period</strong></p>
                                <p class="text-lg text-gray-900">
                                    {{ $enrollment->effective_start_date?->format('d-m-Y') ?? '—' }} to {{ $enrollment->effective_end_date?->format('d-m-Y') ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="info-card bg-green-50 border border-green-200">
                    <h3 class="text-xl font-bold text-green-800 mb-4 flex items-center">
                        <i class="fas fa-shield-alt text-green-600 mr-3"></i>
                        Verification
                    </h3>
                    <p class="text-sm text-green-800 mb-3">This record matches our enrolment register for the learner and programme details shown.</p>
                    <p class="text-xs text-green-600">
                        <i class="fas fa-clock mr-1"></i>
                        Checked {{ now()->format('d-m-Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} SoftPro Skill Solutions. All rights reserved.</p>
            <p class="text-gray-400 mt-2">Student Management System</p>
        </div>
    </footer>
</body>
</html>
