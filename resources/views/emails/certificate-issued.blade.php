@extends('emails.layouts.softpro')
@section('title', 'Certificate Ready')
@section('header')
    <h1>Certificate Ready!</h1>
    <p>Congratulations on completing {{ $certificate->course->name ?? 'the course' }}</p>
@endsection
@section('content')
    <p>Hello {{ $certificate->student->full_name }},</p>
    <p>Your certificate has been issued. You can view and download it from your student portal.</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Course :</span> <span class="info-value">{{ $certificate->course->name }}</span></div>
        <div class="info-row"><span class="info-label">Certificate Number :</span> <span class="info-value">{{ $certificate->certificate_number ?? 'N/A' }}</span></div>
        <div class="info-row"><span class="info-label">Issue Date :</span> <span class="info-value">{{ $certificate->issue_date?->format('M d, Y') ?? 'N/A' }}</span></div>
    </div>
    <a href="{{ url('/student/certificates/' . $certificate->id . '/view') }}" class="cta-button">View Certificate</a>
@endsection
