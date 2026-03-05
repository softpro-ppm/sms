<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Softpro Student Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>.bg-softpro { background: linear-gradient(135deg, #0B2A4A 0%, #123B66 100%); }</style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-softpro text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('images/logo/Logo_png.png') }}" alt="SoftPro Logo" class="h-12 w-auto bg-white rounded-lg p-2 mx-auto">
            </a>
            <h1 class="text-2xl font-bold mt-3">Privacy Policy</h1>
            <p class="text-blue-200 text-sm mt-1">Softpro Student Management System</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>
        <p class="text-sm text-gray-500 mb-8">Last updated: {{ now()->format('F d, Y') }}</p>

        <div class="space-y-6 text-gray-700">
            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">1. Introduction</h2>
                <p>Softpro ("we", "our", or "us") operates the Student Management System at sms.softpromis.com. This Privacy Policy explains how we collect, use, store, and protect your personal information when you use our services.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">2. Information We Collect</h2>
                <p>We collect the following types of information:</p>
                <ul class="list-disc pl-6 space-y-1 mt-2">
                    <li><strong>Personal identification:</strong> Name, email address, phone/WhatsApp number, Aadhar number, date of birth, gender, address</li>
                    <li><strong>Academic information:</strong> Qualifications, course enrollments, batch details, assessment results</li>
                    <li><strong>Financial information:</strong> Payment records and transaction history</li>
                    <li><strong>Documents:</strong> Photos, certificates, and documents you upload</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">3. How We Use Your Information</h2>
                <p>We use your information to:</p>
                <ul class="list-disc pl-6 space-y-1 mt-2">
                    <li>Process student registration and course enrollments</li>
                    <li>Manage payments and issue receipts</li>
                    <li>Send notifications via email and WhatsApp (course updates, payment confirmations, assessment results, certificates)</li>
                    <li>Generate certificates and ID cards</li>
                    <li>Verify student credentials publicly</li>
                    <li>Comply with legal and regulatory requirements</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">4. WhatsApp Integration</h2>
                <p>We use Meta's WhatsApp Business API to send you notifications and updates. By providing your WhatsApp number, you consent to receive messages from us. Your phone number is used only for service-related communications and is not shared with third parties for marketing purposes.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">5. Data Storage and Security</h2>
                <p>Your data is stored securely on our servers. We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">6. Data Retention</h2>
                <p>We retain your personal data for as long as necessary to provide our services and comply with legal obligations. You may request deletion of your data by contacting us.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">7. Your Rights</h2>
                <p>You have the right to:</p>
                <ul class="list-disc pl-6 space-y-1 mt-2">
                    <li>Access your personal data</li>
                    <li>Correct inaccurate data</li>
                    <li>Request deletion of your data</li>
                    <li>Opt out of marketing communications</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">8. Contact Us</h2>
                <p>For privacy-related questions or to exercise your rights, contact us at:</p>
                <p class="mt-2">
                    <strong>Email:</strong> info@softpro.co.in<br>
                    <strong>Website:</strong> <a href="https://sms.softpromis.com" class="text-primary-600 hover:underline">sms.softpromis.com</a>
                </p>
            </section>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 flex gap-4 flex-wrap">
            <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-file-contract mr-2"></i>Terms of Service
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
