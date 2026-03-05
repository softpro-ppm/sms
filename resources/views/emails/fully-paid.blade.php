@extends('emails.layouts.softpro')
@section('title', 'Fees Fully Paid')
@section('header')
    <h1>Fees Fully Paid!</h1>
    <p>You're now eligible for assessment</p>
@endsection
@section('content')
    @php $batch = $enrollment->batch; $course = $batch->course; @endphp
    <p>Hello {{ $enrollment->student->full_name }},</p>
    <p>All fees for your enrollment have been paid. You are now eligible to take the assessment for <strong>{{ $course->name }}</strong>.</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Course :</span> <span class="info-value">{{ $course->name }}</span></div>
        <div class="info-row"><span class="info-label">Batch :</span> <span class="info-value">{{ $batch->batch_name }}</span></div>
        <div class="info-row"><span class="info-label">Status :</span> <span class="info-value"><span class="status-badge status-pass">Eligible</span></span></div>
    </div>
    <p>You can take the assessment once your batch has ended. Log in to your student portal and go to Assessments when ready.</p>
    <a href="{{ url('/student/assessments') }}" class="cta-button">View Assessments</a>
@endsection
