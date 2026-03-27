@extends('emails.layouts.softpro')
@section('title', 'Enrollment Confirmation')
@section('header')
    <h1>Enrollment Confirmed!</h1>
    <p>You're enrolled in {{ $enrollment->display_course_name }}</p>
@endsection
@section('content')
    @php $batch = $enrollment->batch; @endphp
    <p>Hello {{ $enrollment->student->full_name }},</p>
    <p>You have been successfully enrolled. Here are your enrollment details:</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Enrollment Number :</span> <span class="info-value">{{ $enrollment->enrollment_number }}</span></div>
        <div class="info-row"><span class="info-label">Course :</span> <span class="info-value">{{ $enrollment->display_course_name }}</span></div>
        <div class="info-row"><span class="info-label">Batch :</span> <span class="info-value">{{ $batch->batch_name }}</span></div>
        <div class="info-row"><span class="info-label">Total Fee :</span> <span class="info-value">₹{{ number_format($enrollment->total_fee, 2) }}</span></div>
        <div class="info-row"><span class="info-label">Outstanding :</span> <span class="info-value">₹{{ number_format($enrollment->outstanding_amount, 2) }}</span></div>
        <div class="info-row"><span class="info-label">Enrollment Date :</span> <span class="info-value">{{ $enrollment->enrollment_date->format('M d, Y') }}</span></div>
    </div>
    <p>Complete your payment to become eligible for assessments. You can view payment details and make payments from your student portal.</p>
    <a href="{{ url('/student/payments') }}" class="cta-button">View Payments</a>
@endsection
