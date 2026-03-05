<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Deletion Instructions - Softpro Student Management</title>
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
            <h1 class="text-2xl font-bold mt-3">Data Deletion Instructions</h1>
            <p class="text-blue-200 text-sm mt-1">Softpro Student Management System</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">User Data Deletion Instructions</h1>
            <p class="text-sm text-gray-500 mb-8">Last updated: {{ now()->format('F d, Y') }}</p>

            <div class="space-y-6 text-gray-700">
                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Your Right to Delete Data</h2>
                    <p>Under applicable data protection laws (including GDPR), you have the right to request deletion of your personal data from the Softpro Student Management System. This page explains how to exercise that right.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">What Data We Store</h2>
                    <p>We may store the following data associated with your account:</p>
                    <ul class="list-disc pl-6 space-y-1 mt-2">
                        <li>Name, email, phone number, WhatsApp number</li>
                        <li>Aadhar number, date of birth, gender, address</li>
                        <li>Course enrollments, batch details, assessment results</li>
                        <li>Payment records and transaction history</li>
                        <li>Uploaded documents and photos</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">How to Request Data Deletion</h2>
                    <p>To request deletion of your data, follow these steps:</p>
                    <ol class="list-decimal pl-6 space-y-2 mt-2">
                        <li><strong>Send an email</strong> to <a href="mailto:info@softpro.co.in" class="text-blue-600 hover:underline font-medium">info@softpro.co.in</a></li>
                        <li><strong>Subject line:</strong> "Data Deletion Request"</li>
                        <li><strong>Include in your email:</strong>
                            <ul class="list-disc pl-6 mt-2 space-y-1">
                                <li>Your full name</li>
                                <li>Email address registered with us</li>
                                <li>Phone/WhatsApp number registered with us</li>
                                <li>Clear statement: "I request deletion of all my personal data from the Softpro Student Management System"</li>
                            </ul>
                        </li>
                    </ol>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">What Happens After Your Request</h2>
                    <ul class="list-disc pl-6 space-y-1">
                        <li>We will acknowledge your request within <strong>5 business days</strong></li>
                        <li>We will verify your identity to protect your data</li>
                        <li>We will process the deletion within <strong>30 days</strong> of verification</li>
                        <li>Some data may be retained as required by law (e.g., financial records for tax purposes)</li>
                        <li>We will confirm when deletion is complete</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Alternative: Use the Website</h2>
                    <p>If you have an active account, you can also contact us through the Student Management System by logging in and using the contact or support options available in your dashboard.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Contact Us</h2>
                    <p>For data deletion requests or privacy-related questions:</p>
                    <p class="mt-2">
                        <strong>Email:</strong> <a href="mailto:info@softpro.co.in" class="text-blue-600 hover:underline">info@softpro.co.in</a><br>
                        <strong>Website:</strong> <a href="https://sms.softpromis.com" class="text-blue-600 hover:underline">sms.softpromis.com</a>
                    </p>
                </section>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 flex gap-4 flex-wrap">
                <a href="{{ route('privacy') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-shield-alt mr-2"></i>Privacy Policy
                </a>
                <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-file-contract mr-2"></i>Terms of Service
                </a>
                <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
