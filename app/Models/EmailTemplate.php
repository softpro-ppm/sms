<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['slug', 'name', 'subject', 'header_html', 'body_html', 'variables'];

    protected $casts = ['variables' => 'array'];

    public static function getDefaultTemplates(): array
    {
        return [
            'student-registration' => [
                'name' => '1. Registration (Admin Create)',
                'subject' => 'Welcome to Softpro - Registration Confirmation',
                'header_html' => "<h1>Welcome to Softpro!</h1>\n<p>Your registration has been successfully processed</p>",
                'body_html' => "<p>Hello {{ \$student->full_name }},</p>\n<p>Welcome to Softpro. We're excited to have you on board!</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Name :</span> <span class=\"info-value\">{{ \$student->full_name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Email :</span> <span class=\"info-value\">{{ \$student->email }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Phone :</span> <span class=\"info-value\">{{ \$student->whatsapp_number }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Status :</span> <span class=\"info-value\"><span class=\"status-badge status-{{ \$student->status === 'approved' ? 'pass' : 'fail' }}\">{{ ucfirst(\$student->status) }}</span></span></div>\n</div>\n<div class=\"info-box\" style=\"border-left-color: #16a34a; background: #f0fdf4;\">\n    <h4 style=\"margin-top:0;\">🔐 Login Credentials</h4>\n    <div class=\"info-row\"><span class=\"info-label\">Username (Email) :</span> <span class=\"info-value\">{{ \$loginCredentials['email'] }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Password :</span> <span class=\"info-value\">{{ \$loginCredentials['password'] }}</span></div>\n</div>\n<div class=\"info-box\" style=\"border-left-color: #ea580c;\">\n    <h4 style=\"margin-top:0;\">🚀 Next Steps</h4>\n    @if(\$student->status === 'approved')\n        <p>Login and enroll in courses, complete payment, and take assessments.</p>\n        <a href=\"{{ url('/login') }}\" class=\"cta-button\">Login to Portal</a>\n    @else\n        <p>Wait for admin approval. You'll receive an email when your account is activated.</p>\n    @endif\n</div>",
                'variables' => ['$student', '$loginCredentials'],
            ],
            'self-registration-ack' => [
                'name' => '2. Self-Registration Acknowledgement',
                'subject' => 'Softpro - Registration Received',
                'header_html' => "<h1>Registration Received!</h1>\n<p>We've received your application</p>",
                'body_html' => "<p>Hello {{ \$student->full_name }},</p>\n<p>Thank you for registering with Softpro. Your application has been received and is under review.</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Name :</span> <span class=\"info-value\">{{ \$student->full_name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Email :</span> <span class=\"info-value\">{{ \$student->email }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Status :</span> <span class=\"info-value\"><span class=\"status-badge\" style=\"background:#fef3c7;color:#92400e;\">Pending</span></span></div>\n</div>\n<p><strong>Login credentials (for when approved):</strong></p>\n<ul>\n    <li>Username: {{ \$student->email }}</li>\n    <li>Password: Your phone number</li>\n</ul>\n<p>You will receive an email when your account is approved. Once approved, you can log in and enroll in courses.</p>\n<p>For any queries, please contact our support team.</p>",
                'variables' => ['$student'],
            ],
            'account-approved' => [
                'name' => '3. Account Approved',
                'subject' => 'Softpro - Your Account is Approved!',
                'header_html' => "<h1>Account Approved!</h1>\n<p>You can now log in to your student portal</p>",
                'body_html' => "<p>Hello {{ \$student->full_name }},</p>\n<p>Great news! Your account has been approved. You can now log in and enroll in courses.</p>\n<div class=\"info-box\" style=\"border-left-color: #16a34a; background: #f0fdf4;\">\n    <h4 style=\"margin-top:0;\">🔐 Login Credentials</h4>\n    <div class=\"info-row\"><span class=\"info-label\">Username (Email) :</span> <span class=\"info-value\">{{ \$loginCredentials['email'] }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Password :</span> <span class=\"info-value\">{{ \$loginCredentials['password'] }}</span></div>\n</div>\n<a href=\"{{ url('/login') }}\" class=\"cta-button\">Login to Portal</a>\n<p>After logging in, you can browse courses, enroll in batches, make payments, and take assessments.</p>",
                'variables' => ['$student', '$loginCredentials'],
            ],
            'enrollment-confirmation' => [
                'name' => '4. Enrollment Confirmation',
                'subject' => 'Softpro - Enrollment Confirmation',
                'header_html' => "<h1>Enrollment Confirmed!</h1>\n<p>You're enrolled in {{ optional(\$enrollment->batch->course)->name ?? 'the course' }}</p>",
                'body_html' => "@php \$batch = \$enrollment->batch; \$course = \$batch->course; @endphp\n<p>Hello {{ \$enrollment->student->full_name }},</p>\n<p>You have been successfully enrolled. Here are your enrollment details:</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Enrollment Number :</span> <span class=\"info-value\">{{ \$enrollment->enrollment_number }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Course :</span> <span class=\"info-value\">{{ \$course->name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Batch :</span> <span class=\"info-value\">{{ \$batch->batch_name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Total Fee :</span> <span class=\"info-value\">₹{{ number_format(\$enrollment->total_fee, 2) }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Outstanding :</span> <span class=\"info-value\">₹{{ number_format(\$enrollment->outstanding_amount, 2) }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Enrollment Date :</span> <span class=\"info-value\">{{ \$enrollment->enrollment_date->format('M d, Y') }}</span></div>\n</div>\n<p>Complete your payment to become eligible for assessments. You can view payment details and make payments from your student portal.</p>\n<a href=\"{{ url('/student/payments') }}\" class=\"cta-button\">View Payments</a>",
                'variables' => ['$enrollment'],
            ],
            'payment-approved' => [
                'name' => '5. Payment Approved',
                'subject' => 'Softpro - Payment Approved (Receipt #{{ $payment->payment_receipt_number }})',
                'header_html' => "<h1>Payment Approved</h1>\n<p>Receipt #{{ \$payment->payment_receipt_number }}</p>",
                'body_html' => "@php \$enrollment = \$payment->enrollment; @endphp\n<p>Hello {{ \$payment->student->full_name }},</p>\n<p>Your payment has been approved. Here are the details:</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Receipt Number :</span> <span class=\"info-value\">{{ \$payment->payment_receipt_number }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Amount :</span> <span class=\"info-value\">₹{{ number_format(\$payment->amount, 2) }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Course :</span> <span class=\"info-value\">{{ \$enrollment?->batch?->course?->name ?? 'N/A' }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Outstanding :</span> <span class=\"info-value\">₹{{ number_format(\$enrollment?->outstanding_amount ?? 0, 2) }}</span></div>\n</div>\n@if(\$enrollment && \$enrollment->outstanding_amount <= 0)\n    <div class=\"info-box\" style=\"border-left-color: #16a34a; background: #f0fdf4;\">\n        <h4 style=\"margin-top:0;\">✅ Fees Fully Paid!</h4>\n        <p>You are now eligible to take the assessment. Visit your portal to attempt the exam after your batch ends.</p>\n    </div>\n@endif\n<a href=\"{{ url('/student/payments') }}\" class=\"cta-button\">View Payments</a>",
                'variables' => ['$payment'],
            ],
            'fully-paid' => [
                'name' => '6. Fully Paid (Assessment Eligible)',
                'subject' => "Softpro - Fees Fully Paid! You're Eligible for Assessment",
                'header_html' => "<h1>Fees Fully Paid!</h1>\n<p>You're now eligible for assessment</p>",
                'body_html' => "@php \$batch = \$enrollment->batch; \$course = \$batch->course; @endphp\n<p>Hello {{ \$enrollment->student->full_name }},</p>\n<p>All fees for your enrollment have been paid. You are now eligible to take the assessment for <strong>{{ \$course->name }}</strong>.</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Course :</span> <span class=\"info-value\">{{ \$course->name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Batch :</span> <span class=\"info-value\">{{ \$batch->batch_name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Status :</span> <span class=\"info-value\"><span class=\"status-badge status-pass\">Eligible</span></span></div>\n</div>\n<p>You can take the assessment once your batch has ended. Log in to your student portal and go to Assessments when ready.</p>\n<a href=\"{{ url('/student/assessments') }}\" class=\"cta-button\">View Assessments</a>",
                'variables' => ['$enrollment'],
            ],
            'assessment-result' => [
                'name' => '7. Assessment Result (Pass/Fail)',
                'subject' => "Softpro - Assessment {{ \$result->is_passed ? 'Passed' : 'Completed' }}: {{ \$result->assessment?->title ?? optional(\$result->assessment?->course)->name ?? 'Exam' }}",
                'header_html' => "<h1>{{ \$result->is_passed ? 'Congratulations! You Passed!' : 'Assessment Completed' }}</h1>\n<p>{{ \$result->assessment?->title ?? optional(\$result->assessment?->course)->name ?? 'Assessment' }} - {{ \$result->is_passed ? 'Pass' : 'Result' }}</p>",
                'body_html' => "<p>Hello {{ \$result->student->full_name }},</p>\n<p>Your assessment has been evaluated. Here is your result:</p>\n<div class=\"info-box\" style=\"border-left-color: {{ \$result->is_passed ? '#16a34a' : '#dc2626' }}; background: {{ \$result->is_passed ? '#f0fdf4' : '#fef2f2' }};\">\n    <div class=\"info-row\"><span class=\"info-label\">Course :</span> <span class=\"info-value\">{{ optional(\$result->assessment?->course)->name ?? optional(\$result->enrollment?->batch)->course?->name ?? 'N/A' }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Score :</span> <span class=\"info-value\">{{ \$result->correct_answers }}/{{ \$result->total_questions }} ({{ number_format(\$result->percentage, 1) }}%)</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Grade :</span> <span class=\"info-value\">{{ \$result->grade }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Result :</span> <span class=\"info-value\"><span class=\"status-badge {{ \$result->is_passed ? 'status-pass' : 'status-fail' }}\">{{ \$result->is_passed ? 'Passed' : 'Not Passed' }}</span></span></div>\n</div>\n@if(\$result->is_passed)\n    <p>Congratulations! You've successfully passed. Your certificate will be available soon.</p>\n    <a href=\"{{ url('/student/certificates') }}\" class=\"cta-button\">View Certificates</a>\n@else\n    <p>You can reattempt the assessment. Log in to your portal and try again when ready.</p>\n    <a href=\"{{ url('/student/assessments') }}\" class=\"cta-button\">View Assessments</a>\n@endif",
                'variables' => ['$result'],
            ],
            'certificate-issued' => [
                'name' => '8. Certificate Issued',
                'subject' => "Softpro - Certificate Ready: {{ \$certificate->course->name ?? 'Course' }}",
                'header_html' => "<h1>Certificate Ready!</h1>\n<p>Congratulations on completing {{ \$certificate->course->name ?? 'the course' }}</p>",
                'body_html' => "<p>Hello {{ \$certificate->student->full_name }},</p>\n<p>Your certificate has been issued. You can view and download it from your student portal.</p>\n<div class=\"info-box\">\n    <div class=\"info-row\"><span class=\"info-label\">Course :</span> <span class=\"info-value\">{{ \$certificate->course->name }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Certificate Number :</span> <span class=\"info-value\">{{ \$certificate->certificate_number ?? 'N/A' }}</span></div>\n    <div class=\"info-row\"><span class=\"info-label\">Issue Date :</span> <span class=\"info-value\">{{ \$certificate->issue_date?->format('M d, Y') ?? 'N/A' }}</span></div>\n</div>\n<a href=\"{{ url('/student/certificates/' . \$certificate->id . '/view') }}\" class=\"cta-button\">View Certificate</a>",
                'variables' => ['$certificate'],
            ],
        ];
    }
}
