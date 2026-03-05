<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $payment->payment_receipt_number }}</title>
    @php $rs = 'Rs. '; @endphp
    <style>
        @page { size: A4; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; line-height: 1.2; color: #1f2937; padding: 4mm; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 3px; margin-bottom: 4px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .header .logo-left { height: 70px; width: auto; }
        .header .brand { text-align: center; padding: 0 10px; }
        .header h1 { font-size: 18pt; color: #2563eb; margin: 0; }
        .header .sub { font-size: 9pt; color: #6b7280; }
        .meta { font-size: 9pt; margin-bottom: 4px; }
        .meta .right { float: right; }
        .section { border: 1px solid #e5e7eb; margin-bottom: 4px; }
        .section-title { background: #f3f4f6; padding: 2px 5px; font-weight: bold; font-size: 9pt; }
        .section-body { padding: 3px 5px; }
        table.info { width: 100%; font-size: 9pt; border-collapse: collapse; }
        table.info td { padding: 1px 4px 1px 0; vertical-align: top; }
        table.info td:first-child { width: 28%; color: #6b7280; }
        table.info td:last-child { font-weight: 600; }
        .amount-box { background: #10b981; color: white; text-align: center; padding: 4px; margin: 3px 0; font-size: 12pt; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; font-size: 9pt; }
        table.data th, table.data td { border: 1px solid #e5e7eb; padding: 2px 4px; text-align: left; }
        table.data th { background: #f9fafb; font-weight: 600; }
        table.data .amt { text-align: right; }
        .two-col { width: 100%; }
        .two-col td { width: 50%; vertical-align: top; padding-right: 5px; }
        .footer { margin-top: 4px; padding-top: 4px; border-top: 1px solid #e5e7eb; font-size: 9pt; }
        .footer table { width: 100%; }
        .footer .sig { border-top: 1px solid #374151; width: 45mm; margin: 0 auto; }
        .signature-img { height: 35px; margin: 0; display: block; vertical-align: bottom; }
        .footer .company-name { font-size: 11pt; font-weight: 700; }
        .outstanding-highlight { background: #fef2f2; color: #dc2626; font-weight: bold; }
        .terms { font-size: 9pt; color: #6b7280; margin-top: 3px; }
        .compact-history { max-height: 40mm; }
        .section, .footer { page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        $aadhar = $payment->student?->aadhar_number ?? '';
        $maskedAadhar = (strlen($aadhar) >= 12) ? (str_repeat('X', 8) . substr($aadhar, -4)) : ($aadhar ?: 'N/A');
    @endphp
    <div class="header">
        <table class="header-table" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 90px;">
                    <img src="{{ public_path('images/logo/Logo_png.png') }}" alt="SoftPro Logo" class="logo-left">
                </td>
                <td>
                    <div class="brand">
                        <h1>SOFTPRO SKILL SOLUTIONS</h1>
                        <p class="sub">Payment Receipt</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="meta">
        Receipt #{{ $payment->payment_receipt_number }}
        @if($payment->enrollment && $payment->enrollment->enrollment_number)
        | Enrollment #{{ $payment->enrollment->enrollment_number }}
        @endif
        <span class="right">Date: {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') }}</span>
    </div>

    <table class="two-col" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Student Information</div>
                    <div class="section-body">
                        <table class="info">
                            <tr><td>Name</td><td>{{ $payment->student ? $payment->student->full_name : 'N/A' }}</td></tr>
                            <tr><td>Email</td><td>{{ $payment->student ? $payment->student->email : 'N/A' }}</td></tr>
                            <tr><td>Phone</td><td>{{ $payment->student ? $payment->student->whatsapp_number : 'N/A' }}</td></tr>
                            <tr><td>Aadhar</td><td>{{ $maskedAadhar }}</td></tr>
                        </table>
                    </div>
                </div>

                @if($payment->enrollment)
                <div class="section">
                    <div class="section-title">Course & Batch</div>
                    <div class="section-body">
                        <table class="info">
                            <tr><td>Course</td><td>{{ $payment->enrollment->batch && $payment->enrollment->batch->course ? $payment->enrollment->batch->course->name : 'N/A' }}</td></tr>
                            <tr><td>Batch</td><td>{{ $payment->enrollment->batch ? $payment->enrollment->batch->batch_name : 'N/A' }}</td></tr>
                            <tr><td>Period</td><td>
                                @if($payment->enrollment->batch)
                                    {{ \Carbon\Carbon::parse($payment->enrollment->batch->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($payment->enrollment->batch->end_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </td></tr>
                        </table>
                    </div>
                </div>
                @endif
            </td>
            <td>
                <div class="section">
                    <div class="section-title">Payment Details</div>
                    <div class="section-body">
                        <div class="amount-box">{{ $rs }}{{ number_format($payment->amount, 0) }}</div>
                        <table class="info">
                            <tr><td>Type</td><td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td></tr>
                            <tr><td>Method</td><td>{{ $payment->payment_method ?? 'Cash/UPI' }}</td></tr>
                            <tr><td>Status</td><td>{{ ucfirst($payment->status) }}</td></tr>
                            @if($payment->enrollment)
                            <tr><td>Total/Paid</td><td>{{ $rs }}{{ number_format($payment->enrollment->total_fee ?? 0, 0) }} / {{ $rs }}{{ number_format($payment->enrollment->paid_amount ?? 0, 0) }}</td></tr>
                            <tr><td>Outstanding</td><td>{{ $rs }}{{ number_format($payment->enrollment->outstanding_amount ?? 0, 0) }}</td></tr>
                            @endif
                            @if($payment->approved_at)
                            <tr><td>Approved</td><td>{{ \Carbon\Carbon::parse($payment->approved_at)->format('d M Y') }}</td></tr>
                            @endif
                        </table>
                        @if($payment->remarks)
                        <div style="margin-top:3px;font-size:9pt;">Remarks: {{ \Illuminate\Support\Str::limit($payment->remarks, 40) }}</div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    @if($payment->enrollment)
    @php
        $paymentHistory = $payment->enrollment->payments()
            ->where('status', 'approved')
            ->orderBy('created_at')
            ->take(6)
            ->get();
        $totalFee = (float) ($payment->enrollment->total_fee ?? 0);
        $runningPaid = 0;
    @endphp
    @if($paymentHistory->isNotEmpty())
    <div class="section compact-history">
        <div class="section-title">Payment History</div>
        <div class="section-body">
            <table class="data">
                <tr><th>Sl.</th><th>Receipt #</th><th>Date</th><th class="amt">Amt</th><th class="amt outstanding-highlight">Balance</th><th>Status</th></tr>
                @foreach($paymentHistory as $index => $pmt)
                @php $runningPaid += (float) $pmt->amount; $balanceAfter = max(0, $totalFee - $runningPaid); @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pmt->payment_receipt_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($pmt->approved_at ?? $pmt->created_at)->format('d M Y') }}</td>
                    <td class="amt">{{ $rs }}{{ number_format($pmt->amount, 0) }}</td>
                    <td class="amt outstanding-highlight">{{ $rs }}{{ number_format($balanceAfter, 0) }}</td>
                    <td>{{ $pmt->id === $payment->id ? 'This' : 'Paid' }}</td>
                </tr>
                @endforeach
                @php $totalPaid = $payment->enrollment->payments()->where('status','approved')->sum('amount'); $finalOutstanding = max(0, $totalFee - $totalPaid); @endphp
                <tr style="font-weight:bold;background:#f3f4f6;">
                    <td colspan="3">Total Paid</td>
                    <td class="amt">{{ $rs }}{{ number_format($totalPaid, 0) }}</td>
                    <td class="amt outstanding-highlight">{{ $rs }}{{ number_format($finalOutstanding, 0) }}</td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    @endif
    @endif

    @if($payment->allocations && $payment->allocations->count() > 0)
    <div class="section">
        <div class="section-title">This Payment Allocation</div>
        <div class="section-body">
            <table class="data">
                <tr><th>Fee Type</th><th class="amt">Amount</th></tr>
                @foreach($payment->allocations as $a)
                <tr><td>{{ $a->fee_type_display }}</td><td class="amt">{{ $rs }}{{ number_format($a->allocated_amount ?? 0, 0) }}</td></tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    @php
        $signaturePath = \App\Services\SignatureImageService::getTransparentSignaturePath();
    @endphp
    <div class="footer">
        <table>
            <tr>
                <td>
                    <div>Contact: 7799773656 | info@softpro.co.in</div>
                    <div>18-86-B, Main Road, Opp Style Bazar, Parvathipuram Manyam, AP - 535501</div>
                    <div class="terms">Valid for approved payments only. Fees non-refundable. Report disputes within 7 days.</div>
                </td>
                <td style="text-align:center; width: 50mm;">
                    @if($signaturePath)
                    <img src="{{ $signaturePath }}" class="signature-img" alt="Signature">
                    @endif
                    <div class="sig"></div>
                    <div>Authorized Signatory</div>
                    <div class="company-name">SoftPro Skill Solutions</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
