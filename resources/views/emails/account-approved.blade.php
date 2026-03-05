@extends('emails.layouts.softpro')
@section('title', 'Account Approved')
@section('header')
    <h1>Account Approved!</h1>
    <p>You can now log in to your student portal</p>
@endsection
@section('content')
    <p>Hello {{ $student->full_name }},</p>
    <p>Great news! Your account has been approved. You can now log in and enroll in courses.</p>
    <div class="info-box" style="border-left-color: #16a34a; background: #f0fdf4;">
        <h4 style="margin-top:0;">🔐 Login Credentials</h4>
        <div class="info-row"><span class="info-label">Username (Email) :</span> <span class="info-value">{{ $loginCredentials['email'] }}</span></div>
        <div class="info-row"><span class="info-label">Password :</span> <span class="info-value">{{ $loginCredentials['password'] }}</span></div>
    </div>
    <a href="{{ url('/login') }}" class="cta-button">Login to Portal</a>
    <p>After logging in, you can browse courses, enroll in batches, make payments, and take assessments.</p>
@endsection
