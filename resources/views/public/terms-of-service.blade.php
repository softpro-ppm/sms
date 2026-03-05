<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Softpro Student Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>.bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SoftPro Logo" class="h-12 w-auto bg-white rounded-lg p-2 mx-auto">
            </a>
            <h1 class="text-2xl font-bold mt-3">Terms of Service</h1>
            <p class="text-blue-200 text-sm mt-1">Softpro Student Management System</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Terms of Service</h1>
            <p class="text-sm text-gray-500 mb-8">Last updated: {{ now()->format('F d, Y') }}</p>

            <div class="space-y-6 text-gray-700">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">1. Acceptance of Terms</h2>
                    <p>By accessing or using the Softpro Student Management System ("Service") at sms.softpromis.com, you agree to be bound by these Terms of Service. If you do not agree, please do not use the Service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">2. Use of Service</h2>
                    <p>You agree to use the Service only for lawful purposes related to student registration, course enrollment, payments, assessments, and certificates. You must not:</p>
                    <ul class="list-disc pl-6 space-y-1 mt-2">
                        <li>Provide false or misleading information</li>
                        <li>Impersonate any person or entity</li>
                        <li>Attempt to gain unauthorized access to systems or other accounts</li>
                        <li>Use the Service for any illegal or unauthorized purpose</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">3. Account and Registration</h2>
                    <p>You are responsible for maintaining the confidentiality of your login credentials. You must notify us immediately of any unauthorized use of your account. We reserve the right to suspend or terminate accounts that violate these terms.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">4. Fees and Payments</h2>
                    <p>Course fees and payment terms are as communicated at the time of enrollment. All fees are non-refundable unless otherwise stated. Payment disputes must be reported within 7 days of the transaction.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">5. Intellectual Property</h2>
                    <p>All content, materials, and resources provided through the Service are owned by Softpro or its licensors. You may not copy, distribute, or misuse any materials without our written permission.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">6. Privacy</h2>
                    <p>Your use of the Service is also governed by our <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Privacy Policy</a>, which explains how we collect and use your data.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">7. Limitation of Liability</h2>
                    <p>To the extent permitted by law, Softpro shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Service. Our liability is limited to the amount paid by you for the relevant service.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">8. Changes to Terms</h2>
                    <p>We may update these Terms of Service from time to time. Continued use of the Service after changes constitutes acceptance of the revised terms. We will notify users of significant changes.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">9. Contact Us</h2>
                    <p>For questions about these Terms of Service:</p>
                    <p class="mt-2">
                        <strong>Email:</strong> info@softpro.co.in<br>
                        <strong>Website:</strong> <a href="https://sms.softpromis.com" class="text-blue-600 hover:underline">sms.softpromis.com</a>
                    </p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex gap-4">
                <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
                </a>
                <a href="{{ route('data-deletion') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-trash-alt mr-2"></i>Data Deletion
                </a>
                <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
