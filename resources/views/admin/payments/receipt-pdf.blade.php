<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $payment->payment_receipt_number }}</title>
    <style>
        @page { size: A5 landscape; margin: 5mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8.2pt;
            line-height: 1.18;
            color: #0f172a;
        }
        .receipt {
            border: 1px solid #dbe2ea;
            border-radius: 6px;
            padding: 7px 9px 5px;
            page-break-inside: avoid;
        }
        .header {
            width: 100%;
            border-bottom: 1px solid #dbe2ea;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }
        .header td { vertical-align: top; }
        .logo {
            height: 28px;
            width: auto;
            display: block;
        }
        .brand-name {
            font-size: 12.5pt;
            font-weight: 700;
            color: #1d4ed8;
            letter-spacing: 0.3px;
        }
        .brand-sub {
            font-size: 7.1pt;
            color: #64748b;
            margin-top: 1px;
        }
        .header-right {
            text-align: right;
            width: 42%;
        }
        .receipt-title {
            font-size: 10.5pt;
            font-weight: 700;
            color: #111827;
        }
        .receipt-meta {
            margin-top: 2px;
            font-size: 7.5pt;
            color: #475569;
        }
        .two-col {
            width: 100%;
            margin-bottom: 6px;
        }
        .two-col td {
            width: 50%;
            vertical-align: top;
        }
        .two-col td:first-child { padding-right: 4px; }
        .two-col td:last-child { padding-left: 4px; }
        .panel {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px 6px;
        }
        .panel-title {
            font-size: 6.6pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 3px;
        }
        table.info {
            width: 100%;
            border-collapse: collapse;
        }
        table.info td {
            padding: 0;
            vertical-align: top;
        }
        table.info td.label {
            width: 32%;
            color: #64748b;
        }
        table.info td.value {
            font-weight: 600;
            color: #0f172a;
        }
        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px 0;
            margin: 2px -5px 6px;
        }
        .summary td {
            width: 33.33%;
        }
        .summary-card {
            border: 1px solid #dbe2ea;
            border-radius: 6px;
            padding: 5px 6px;
            text-align: center;
        }
        .summary-label {
            font-size: 6.6pt;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
        }
        .summary-value {
            font-size: 12.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 1px;
        }
        .summary-sub {
            margin-top: 1px;
            font-size: 7.1pt;
            color: #475569;
        }
        .summary-card.balance .summary-value {
            color: #dc2626;
        }
        .history {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .history-head {
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .history-title {
            font-size: 6.6pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }
        .history-sub {
            margin-top: 1px;
            font-size: 7.8pt;
            font-weight: 600;
            color: #111827;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.4pt;
        }
        table.data th,
        table.data td {
            padding: 3px 5px;
            border-bottom: 1px solid #edf2f7;
            text-align: left;
        }
        table.data th {
            background: #f8fafc;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            font-size: 6.5pt;
            font-weight: 700;
        }
        table.data tr:last-child td {
            border-bottom: none;
        }
        .amt {
            text-align: right;
            white-space: nowrap;
        }
        .remarks {
            margin-top: 4px;
            font-size: 7pt;
            color: #475569;
        }
        .footer {
            width: 100%;
            margin-top: 4px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 7pt;
            color: #64748b;
            page-break-inside: avoid;
        }
        .footer td { vertical-align: bottom; }
        .footer-note {
            font-size: 7pt;
        }
        .sign {
            text-align: right;
            width: 38%;
        }
        .signature-img {
            width: 30mm;
            height: auto;
            display: block;
            margin-left: auto;
            margin-bottom: 1px;
        }
        .sign-line {
            width: 30mm;
            border-top: 1px solid #475569;
            margin-left: auto;
            margin-bottom: 1px;
        }
        .sign-label {
            color: #334155;
            font-size: 6.9pt;
        }
    </style>
</head>
<body>
@php
    $rs = 'Rs. ';
    $enrollment = $payment->enrollment;
    $courseName = $enrollment?->batch?->course?->name ?? 'N/A';
    $batchName = $enrollment?->batch?->batch_name ?? 'N/A';
    $asOf = \Carbon\Carbon::parse($payment->approved_at ?? $payment->created_at);
    $allocationService = app(\App\Services\PaymentAllocationService::class);
    $totalFee = (float) ($enrollment?->total_fee ?? $payment->amount);
    $discountTotal = $enrollment ? $allocationService->getTotalDiscount($enrollment, $asOf) : 0.0;
    $totalPaid = $enrollment ? $allocationService->getApprovedPaymentTotal($enrollment, $asOf) : (float) $payment->amount;
    $balance = $enrollment ? $allocationService->getTotalOutstandingAt($enrollment, $asOf) : 0.0;
    $netPayable = max(0, round($totalFee - $discountTotal, 2));
    $paymentHistory = collect();

    if ($enrollment) {
        $paymentHistory = $enrollment->payments()
            ->where('status', 'approved')
            ->orderBy('created_at')
            ->where('created_at', '<=', $asOf)
            ->take(4)
            ->get();
    }

    $certificateSignatureFile = public_path('images/signatures/director-signature.png');
    $signaturePath = file_exists($certificateSignatureFile)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($certificateSignatureFile))
        : null;
@endphp

<div class="receipt">
    <table class="header" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 56%;">
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="width: 54px;">
                            <img src="{{ public_path('images/logo/Logo_png.png') }}" alt="SoftPro Logo" class="logo">
                        </td>
                        <td>
                            <div class="brand-name">SoftPro Skill Solutions</div>
                            <div class="brand-sub">Payment receipt</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-right">
                <div class="receipt-title">Receipt #{{ $payment->payment_receipt_number }}</div>
                <div class="receipt-meta">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') }}</div>
                @if($enrollment?->enrollment_number)
                    <div class="receipt-meta">Enrollment #{{ $enrollment->enrollment_number }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="two-col" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="panel">
                    <div class="panel-title">Student</div>
                    <table class="info">
                        <tr>
                            <td class="label">Name</td>
                            <td class="value">{{ $payment->student?->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Phone</td>
                            <td class="value">{{ $payment->student?->whatsapp_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Method</td>
                            <td class="value">{{ $payment->payment_method_label }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="panel">
                    <div class="panel-title">Course</div>
                    <table class="info">
                        <tr>
                            <td class="label">Course</td>
                            <td class="value">{{ $courseName }}</td>
                        </tr>
                        <tr>
                            <td class="label">Batch</td>
                            <td class="value">{{ $batchName }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">{{ ucfirst($payment->status) }}</td>
                        </tr>
                        @if($discountTotal > 0)
                        <tr>
                            <td class="label">Discount</td>
                            <td class="value">{{ $rs }}{{ number_format($discountTotal, 0) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table class="summary" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Paid this receipt</div>
                    <div class="summary-value">{{ $rs }}{{ number_format((float) $payment->amount, 0) }}</div>
                    <div class="summary-sub">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Total paid</div>
                    <div class="summary-value">{{ $rs }}{{ number_format($totalPaid, 0) }}</div>
                    <div class="summary-sub">
                        @if($discountTotal > 0)
                            Net payable {{ $rs }}{{ number_format($netPayable, 0) }}
                        @else
                            Course fee {{ $rs }}{{ number_format($totalFee, 0) }}
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <div class="summary-card balance">
                    <div class="summary-label">Balance</div>
                    <div class="summary-value">{{ $rs }}{{ number_format($balance, 0) }}</div>
                    <div class="summary-sub">After this payment</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="history">
        <div class="history-head">
            <div class="history-title">Installment history</div>
            <div class="history-sub">Approved payments for this enrollment</div>
        </div>
        <table class="data">
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 24%;">Receipt</th>
                <th style="width: 20%;">Date</th>
                <th class="amt" style="width: 22%;">Amount</th>
                <th class="amt" style="width: 26%;">Balance</th>
            </tr>
            @forelse($paymentHistory as $index => $historyPayment)
                @php
                    $historyAsOf = \Carbon\Carbon::parse($historyPayment->approved_at ?? $historyPayment->created_at);
                    $balanceAfter = $allocationService->getTotalOutstandingAt($enrollment, $historyAsOf);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $historyPayment->payment_receipt_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($historyPayment->approved_at ?? $historyPayment->created_at)->format('d M Y') }}</td>
                    <td class="amt">{{ $rs }}{{ number_format((float) $historyPayment->amount, 0) }}</td>
                    <td class="amt">{{ $rs }}{{ number_format($balanceAfter, 0) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;">No approved payment history available.</td>
                </tr>
            @endforelse
        </table>
    </div>

    @if($payment->remarks)
        <div class="remarks">Remarks: {{ \Illuminate\Support\Str::limit($payment->remarks, 50) }}</div>
    @endif

    <table class="footer" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="footer-note">Valid for approved payments only.</div>
            </td>
            <td class="sign">
                @if($signaturePath)
                    <img src="{{ $signaturePath }}" class="signature-img" alt="Signature">
                @endif
                <div class="sign-line"></div>
                <div class="sign-label">Authorized Signatory</div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
