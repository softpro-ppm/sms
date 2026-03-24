@extends('emails.layouts.softpro')
@section('title', 'Partner Approved')
@section('header')
    <h1>Training Partner Approved!</h1>
    <p>Your organization has been approved as a Softpro Training Partner</p>
@endsection
@section('content')
    <p>Hello {{ $trainingPartner->contact_name ?: $trainingPartner->name }},</p>
    <p>Great news! <strong>{{ $trainingPartner->name }}</strong> has been approved as a Softpro Training Partner.</p>
    <div class="info-box" style="border-left-color: #16a34a; background: #f0fdf4;">
        <h4 style="margin-top:0;">Partner Details</h4>
        <div class="info-row"><span class="info-label">Partner Code :</span> <span class="info-value">{{ $trainingPartner->code }}</span></div>
        <div class="info-row"><span class="info-label">Type :</span> <span class="info-value">{{ $trainingPartner->type }}</span></div>
    </div>
    @if(!empty($loginCredentials))
    <div class="info-box" style="border-left-color: #16a34a; background: #f0fdf4;">
        <h4 style="margin-top:0;">🔐 Admin Login Credentials</h4>
        <div class="info-row"><span class="info-label">Email :</span> <span class="info-value">{{ $loginCredentials['email'] }}</span></div>
        <div class="info-row"><span class="info-label">Password :</span> <span class="info-value">{{ $loginCredentials['password'] }}</span></div>
        <p class="text-sm text-gray-600 mt-2" style="margin-top: 8px; font-size: 13px; color: #475569;">Please change your password after first login.</p>
    </div>
    @endif
    <p>You can now access the admin portal to manage students, batches, enrollments, and more.</p>
    <a href="{{ url('/login') }}" class="cta-button">Login to Admin Portal</a>
    <p>From your admin dashboard, go to <strong>Settings → Staff Users</strong> to add reception staff for your center.</p>
    @if(empty($loginCredentials))
    <p>If you need login credentials, please contact us at <a href="mailto:info@softpro.co.in">info@softpro.co.in</a>.</p>
    @endif
@endsection
