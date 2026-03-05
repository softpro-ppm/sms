@extends('emails.layouts.softpro')
@section('title', 'Payment Approved')
@section('header')
    <h1>Payment Approved</h1>
    <p>Receipt #{{ $payment->payment_receipt_number }}</p>
@endsection
@section('content')
    @php $enrollment = $payment->enrollment; @endphp
    <p>Hello {{ $payment->student->full_name }},</p>
    <p>Your payment has been approved. Here are the details:</p>
    <div class="info-box">
        <div class="info-row"><span class="info-label">Receipt Number :</span> <span class="info-value">{{ $payment->payment_receipt_number }}</span></div>
        <div class="info-row"><span class="info-label">Amount :</span> <span class="info-value">₹{{ number_format($payment->amount, 2) }}</span></div>
        <div class="info-row"><span class="info-label">Course :</span> <span class="info-value">{{ $enrollment?->batch?->course?->name ?? 'N/A' }}</span></div>
        <div class="info-row"><span class="info-label">Outstanding :</span> <span class="info-value">₹{{ number_format($enrollment?->outstanding_amount ?? 0, 2) }}</span></div>
    </div>
    @if($enrollment && $enrollment->outstanding_amount <= 0)
        <div class="info-box" style="border-left-color: #16a34a; background: #f0fdf4;">
            <h4 style="margin-top:0;">✅ Fees Fully Paid!</h4>
            <p>You are now eligible to take the assessment. Visit your portal to attempt the exam after your batch ends.</p>
        </div>
    @endif
    <a href="{{ url('/student/payments') }}" class="cta-button">View Payments</a>
@endsection
