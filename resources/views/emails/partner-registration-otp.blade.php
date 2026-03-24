@extends('emails.layouts.softpro')

@section('header')
<h1>Email Verification</h1>
<p>Partner Registration – SOFTPRO Student Management System</p>
@endsection

@section('content')
<p>Your one-time password (OTP) for partner registration verification is:</p>
<div class="info-box" style="text-align: center; font-size: 28px; font-weight: 700; letter-spacing: 8px; font-family: monospace;">
    {{ $otp }}
</div>
<p>This OTP is valid for {{ $expiryMinutes }} minutes. Do not share it with anyone.</p>
<p>If you did not request this OTP, please ignore this email.</p>
@endsection
