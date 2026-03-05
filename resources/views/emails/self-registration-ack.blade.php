@extends('emails.layouts.softpro')
@section('title', 'Registration Received')
@section('header')
    <h1>Registration Received!</h1>
    <p>We've received your application</p>
@endsection
@section('content')
    <p>Hello {{ $student->full_name }},</p>
    <p>Thank you for registering with Softpro. Your application has been received and is under review.</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Name :</span> <span class="info-value">{{ $student->full_name }}</span></div>
        <div class="info-row"><span class="info-label">Email :</span> <span class="info-value">{{ $student->email }}</span></div>
        <div class="info-row"><span class="info-label">Status :</span> <span class="info-value"><span class="status-badge" style="background:#fef3c7;color:#92400e;">Pending</span></span></div>
    </div>
    <p><strong>Login credentials (for when approved):</strong></p>
    <ul>
        <li>Username: {{ $student->email }}</li>
        <li>Password: Your phone number</li>
    </ul>
    <p>You will receive an email when your account is approved. Once approved, you can log in and enroll in courses.</p>
    <p>For any queries, please contact our support team.</p>
@endsection
