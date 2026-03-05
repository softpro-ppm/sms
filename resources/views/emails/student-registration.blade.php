@extends('emails.layouts.softpro')
@section('title', 'Welcome - Registration Confirmation')
@section('header')
    <h1>Welcome to Softpro!</h1>
    <p>Your registration has been successfully processed</p>
@endsection
@section('content')
    <p>Hello {{ $student->full_name }},</p>
    <p>Welcome to Softpro. We're excited to have you on board!</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Name :</span> <span class="info-value">{{ $student->full_name }}</span></div>
        <div class="info-row"><span class="info-label">Email :</span> <span class="info-value">{{ $student->email }}</span></div>
        <div class="info-row"><span class="info-label">Phone :</span> <span class="info-value">{{ $student->whatsapp_number }}</span></div>
        <div class="info-row"><span class="info-label">Status :</span> <span class="info-value"><span class="status-badge status-{{ $student->status === 'approved' ? 'pass' : 'fail' }}">{{ ucfirst($student->status) }}</span></span></div>
    </div>
    <div class="info-box" style="border-left-color: #16a34a; background: #f0fdf4;">
        <h4 style="margin-top:0;">🔐 Login Credentials</h4>
        <div class="info-row"><span class="info-label">Username (Email) :</span> <span class="info-value">{{ $loginCredentials['email'] }}</span></div>
        <div class="info-row"><span class="info-label">Password :</span> <span class="info-value">{{ $loginCredentials['password'] }}</span></div>
    </div>
    <div class="info-box" style="border-left-color: #ea580c;">
        <h4 style="margin-top:0;">🚀 Next Steps</h4>
        @if($student->status === 'approved')
            <p>Login and enroll in courses, complete payment, and take assessments.</p>
            <a href="{{ url('/login') }}" class="cta-button">Login to Portal</a>
        @else
            <p>Wait for admin approval. You'll receive an email when your account is activated.</p>
        @endif
    </div>
@endsection
