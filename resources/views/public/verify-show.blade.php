<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Verified - {{ $student->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4 mb-4">
                <img src="{{ asset('images/logo/Logo_png.png') }}" alt="Logo" class="h-14 w-auto bg-white rounded-lg p-2">
                <div>
                    <h1 class="text-2xl font-bold">SOFTPRO SKILL SOLUTIONS</h1>
                    <p class="text-sm text-blue-200">Skill Development & Training Institute</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-green-50 border-b border-green-200 px-6 py-4 flex items-center gap-3">
                <div class="flex-shrink-0 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-green-800">Verified</h2>
                    <p class="text-sm text-green-600">Student identity confirmed</p>
                </div>
            </div>

            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="flex-shrink-0 mx-auto sm:mx-0">
                        @php
                            $photoDoc = $student->documents()->where('document_type', 'photo')->first();
                            $photoUrl = ($photoDoc && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoDoc->file_path))
                                ? route('verify.photo', $enrollment->enrollment_number) : null;
                        @endphp
                        @if($photoUrl)
                            <img src="{{ $photoUrl }}" alt="Photo" class="w-28 h-32 object-cover rounded-lg border-2 border-gray-200">
                        @else
                            <div class="w-28 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Name</span>
                            <p class="text-lg font-bold text-gray-900">{{ $student->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Enrollment No</span>
                            <p class="text-base font-mono font-semibold text-blue-600">{{ $enrollment->enrollment_number }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Mobile</span>
                            <p class="text-base text-gray-900">
                                @php
                                    $mobile = $student->whatsapp_number ?? $student->phone;
                                @endphp
                                {{ $mobile ? '*******' . substr($mobile, -4) : 'N/A' }}
                            </p>
                        </div>
                        @if($startDateFormatted)
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Start Date</span>
                            <p class="text-base text-gray-900">{{ $startDateFormatted }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Valid Till</span>
                            <p class="text-base text-gray-900">{{ $validTillFormatted }}</p>
                        </div>
                        @endif
                        @if($student->email)
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Email</span>
                            <p class="text-base text-gray-900">
                                @php
                                    $parts = explode('@', $student->email);
                                    $local = $parts[0] ?? '';
                                    $domain = $parts[1] ?? '';
                                    $masked = strlen($local) > 2 ? substr($local, 0, 2) . '***@' . $domain : '***@' . $domain;
                                @endphp
                                {{ $masked }}
                            </p>
                        </div>
                        @endif
                        @if($student->gender)
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Gender</span>
                            <p class="text-base text-gray-900">{{ ucfirst($student->gender) }}</p>
                        </div>
                        @endif
                        @if($student->date_of_birth)
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Date of Birth</span>
                            <p class="text-base text-gray-900">{{ $student->date_of_birth->format('d-m-Y') }}</p>
                        </div>
                        @endif
                        @if($student->address || $student->city)
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Address</span>
                            <p class="text-base text-gray-900">
                                {{ trim(implode(', ', array_filter([$student->address, $student->city, $student->state, $student->pincode]))) ?: 'N/A' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('verify.index') }}" class="text-blue-600 hover:underline text-sm">
                <i class="fas fa-search mr-1"></i> Verify another student
            </a>
        </div>
    </div>
</body>
</html>
