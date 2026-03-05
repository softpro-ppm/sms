@extends('emails.layouts.softpro')
@section('title', 'Assessment Result')
@section('header')
    <h1>{{ $result->is_passed ? 'Congratulations! You Passed!' : 'Assessment Completed' }}</h1>
    <p>{{ $result->assessment?->title ?? optional($result->assessment?->course)->name ?? 'Assessment' }} - {{ $result->is_passed ? 'Pass' : 'Result' }}</p>
@endsection
@section('content')
    <p>Hello {{ $result->student->full_name }},</p>
    <p>Your assessment has been evaluated. Here is your result:</p>
    <div class="info-box" style="border-left-color: {{ $result->is_passed ? '#16a34a' : '#dc2626' }}; background: {{ $result->is_passed ? '#f0fdf4' : '#fef2f2' }};">
        <div class="info-row"><span class="info-label">Course :</span> <span class="info-value">{{ optional($result->assessment?->course)->name ?? optional($result->enrollment?->batch)->course?->name ?? 'N/A' }}</span></div>
        <div class="info-row"><span class="info-label">Score :</span> <span class="info-value">{{ $result->correct_answers }}/{{ $result->total_questions }} ({{ number_format($result->percentage, 1) }}%)</span></div>
        <div class="info-row"><span class="info-label">Grade :</span> <span class="info-value">{{ $result->grade }}</span></div>
        <div class="info-row"><span class="info-label">Result :</span> <span class="info-value"><span class="status-badge {{ $result->is_passed ? 'status-pass' : 'status-fail' }}">{{ $result->is_passed ? 'Passed' : 'Not Passed' }}</span></span></div>
    </div>
    @if($result->is_passed)
        <p>Congratulations! You've successfully passed. Your certificate will be available soon.</p>
        <a href="{{ url('/student/certificates') }}" class="cta-button">View Certificates</a>
    @else
        <p>You can reattempt the assessment. Log in to your portal and try again when ready.</p>
        <a href="{{ url('/student/assessments') }}" class="cta-button">View Assessments</a>
    @endif
@endsection
