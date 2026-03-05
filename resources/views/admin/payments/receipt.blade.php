<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_receipt_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }
            body { 
                margin: 0; 
                background: white !important;
                font-size: 8px;
                line-height: 1.0;
                height: 100vh;
                overflow: hidden;
            }
            .no-print { 
                display: none !important; 
            }
            .print-receipt { 
                box-shadow: none !important;
                margin: 0 !important;
                max-width: none !important;
                border-radius: 0 !important;
                page-break-inside: avoid;
                height: 100vh;
                overflow: hidden;
            }
            .receipt-container {
                background: white !important;
                padding: 0 !important;
                height: 100vh;
                overflow: hidden;
            }
            .bg-gradient-to-r { 
                background: linear-gradient(to right, #667eea, #764ba2) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .receipt-content {
                padding: 0.2rem !important;
                height: calc(100vh - 60px) !important;
                overflow: hidden !important;
            }
            .info-section {
                margin-bottom: 0.2rem !important;
                page-break-inside: avoid;
            }
            .section-content {
                padding: 0.2rem !important;
            }
            .amount-highlight {
                padding: 0.2rem !important;
                margin: 0.2rem 0 !important;
            }
        }
        
        .receipt-container {
            background: #f8fafc;
            min-height: 100vh;
            padding: 0.2rem;
        }
        
        .receipt-card {
            background: white;
            border-radius: 1px;
            box-shadow: 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 210mm;
            margin: 0 auto;
            height: 100vh;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.2rem;
            text-align: center;
            position: relative;
        }
        .receipt-header .logo-right {
            position: absolute;
            top: 0.2rem;
            right: 0.5rem;
        }
        
        .receipt-content {
            padding: 0.2rem;
            height: calc(100% - 60px);
            overflow: hidden;
        }
        
        .info-section {
            margin-bottom: 0.2rem;
            border: 1px solid #e5e7eb;
            border-radius: 1px;
            overflow: hidden;
        }
        
        .section-header {
            background: #f3f4f6;
            padding: 0.1rem 0.2rem;
            font-weight: 600;
            font-size: 0.6rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section-content {
            padding: 0.2rem;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.2rem;
        }
        
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.1rem;
        }
        
        .grid-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 0.1rem;
        }
        
        .field {
            margin-bottom: 0.1rem;
        }
        
        .field-label {
            font-size: 0.55rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        .field-value {
            font-size: 0.6rem;
            color: #111827;
            font-weight: 600;
        }
        
        .amount-highlight {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border-radius: 2px;
            padding: 0.5rem;
            text-align: center;
            margin: 0.5rem 0;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.25rem;
            border-radius: 2px;
            font-weight: 600;
            font-size: 0.65rem;
        }
        
        .status-approved {
            background: #10b981;
            color: white;
        }
        
        .status-pending {
            background: #f59e0b;
            color: white;
        }
        
        .status-rejected {
            background: #ef4444;
            color: white;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.5rem;
        }
        
        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 0.1rem;
            text-align: left;
        }
        
        .table th {
            background: #f9fafb;
            font-weight: 600;
        }
        
        .table .text-right {
            text-align: right;
        }
        
        .table .text-center {
            text-align: center;
        }
        
        .footer-section {
            margin-top: 0.2rem;
            padding: 0.2rem;
            background: #f9fafb;
            border-radius: 1px;
            font-size: 0.5rem;
        }
        
        .signature-section {
            margin-top: 0.2rem;
            display: flex;
            justify-content: space-between;
            align-items: end;
        }
        
        .signature-box {
            text-align: center;
            width: 80px;
        }
        
        .signature-line {
            border-top: 1px solid #374151;
            margin-bottom: 0.05rem;
            height: 1px;
        }
        
        .terms-section {
            margin-top: 0.2rem;
            font-size: 0.45rem;
            color: #6b7280;
            line-height: 1.0;
        }
        
        .contact-info {
            font-size: 0.45rem;
            color: #6b7280;
        }
    </style>
</head>
<body class="receipt-container">
    <div class="receipt-card print-receipt">
        <!-- Header -->
        <div class="receipt-header">
            <img src="{{ asset('images/logo/Logo_png.png') }}" 
                 alt="SoftPro Logo" 
                 class="logo-right h-5 w-auto bg-white rounded p-0.5">
            <h1 class="text-xs font-bold">SOFTPRO SKILL SOLUTIONS</h1>
            <p class="text-xs opacity-90">Student Management System</p>
            <div class="mt-0.5">
                <h2 class="text-xs font-bold">PAYMENT RECEIPT</h2>
                <p class="text-xs">Receipt #{{ $payment->payment_receipt_number }}</p>
                @if($payment->enrollment && $payment->enrollment->enrollment_number)
                <p class="text-xs">Enrollment #{{ $payment->enrollment->enrollment_number }}</p>
                @endif
            </div>
        </div>

        <!-- Content -->
        <div class="receipt-content">

            <!-- Student Information -->
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-user-graduate mr-2"></i>Student Information
                </div>
                <div class="section-content">
                    <div class="grid-2">
                        <div class="field">
                            <div class="field-label">Full Name</div>
                            <div class="field-value">{{ $payment->student ? $payment->student->full_name : 'N/A' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Email</div>
                            <div class="field-value">{{ $payment->student ? $payment->student->email : 'N/A' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">WhatsApp Number</div>
                            <div class="field-value">{{ $payment->student ? $payment->student->whatsapp_number : 'N/A' }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Aadhar Number</div>
                            <div class="field-value">{{ $payment->student ? $payment->student->aadhar_number : 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course & Batch Information -->
            @if($payment->enrollment)
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-book mr-2"></i>Course & Batch Details
                </div>
                <div class="section-content">
                    <div class="grid-2">
                        <div class="field">
                            <div class="field-label">Course</div>
                            <div class="field-value">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->course)
                                    {{ $payment->enrollment->batch->course->name }}
                                @else
                                    No Course Assigned
                                @endif
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">Batch</div>
                            <div class="field-value">
                                @if($payment->enrollment && $payment->enrollment->batch)
                                    {{ $payment->enrollment->batch->batch_name }}
                                @else
                                    No Batch Assigned
                                @endif
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">Batch Start Date</div>
                            <div class="field-value">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->start_date)
                                    {{ \Carbon\Carbon::parse($payment->enrollment->batch->start_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="field">
                            <div class="field-label">Batch End Date</div>
                            <div class="field-value">
                                @if($payment->enrollment && $payment->enrollment->batch && $payment->enrollment->batch->end_date)
                                    {{ \Carbon\Carbon::parse($payment->enrollment->batch->end_date)->format('d M Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Fee Breakdown -->
            @if($payment->enrollment)
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-calculator mr-2"></i>Fee Breakdown
                </div>
                <div class="section-content">
                    <div class="grid-4 mb-0.5">
                        <div class="text-center p-0.5 bg-blue-50 rounded">
                            <div class="text-xs text-blue-600 font-medium">Registration</div>
                            <div class="text-xs font-bold text-blue-900">₹{{ number_format($payment->enrollment->registration_fee ?? 100, 0) }}</div>
                        </div>
                        <div class="text-center p-0.5 bg-green-50 rounded">
                            <div class="text-xs text-green-600 font-medium">Course Fee</div>
                            <div class="text-xs font-bold text-green-900">₹{{ number_format($payment->enrollment->course_fee ?? 0, 0) }}</div>
                        </div>
                        <div class="text-center p-0.5 bg-orange-50 rounded">
                            <div class="text-xs text-orange-600 font-medium">Exam</div>
                            <div class="text-xs font-bold text-orange-900">₹{{ number_format($payment->enrollment->assessment_fee ?? 100, 0) }}</div>
                        </div>
                        <div class="text-center p-0.5 bg-purple-50 rounded">
                            <div class="text-xs text-purple-600 font-medium">Total Fee</div>
                            <div class="text-xs font-bold text-purple-900">₹{{ number_format($payment->enrollment->total_fee ?? 0, 0) }}</div>
                        </div>
                    </div>
                    
                    <div class="grid-3">
                        <div class="text-center p-0.5 bg-green-50 rounded">
                            <div class="text-xs text-green-600 font-medium">Total Paid</div>
                            <div class="text-xs font-bold text-green-900">₹{{ number_format($payment->enrollment->paid_amount ?? 0, 0) }}</div>
                        </div>
                        <div class="text-center p-0.5 bg-red-50 rounded">
                            <div class="text-xs text-red-600 font-medium">Outstanding</div>
                            <div class="text-xs font-bold text-red-900">₹{{ number_format($payment->enrollment->outstanding_amount ?? 0, 0) }}</div>
                        </div>
                        <div class="text-center p-0.5 bg-gray-50 rounded">
                            <div class="text-xs text-gray-600 font-medium">Status</div>
                            <div class="text-xs font-bold {{ ($payment->enrollment->outstanding_amount ?? 0) <= 0 ? 'text-green-600' : 'text-orange-600' }}">
                                {{ ($payment->enrollment->outstanding_amount ?? 0) <= 0 ? 'Fully Paid' : 'Partial' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Transaction History -->
            @if($payment->enrollment)
            @php
                $paymentHistory = $payment->enrollment->payments()
                    ->where('status', 'approved')
                    ->orderBy('created_at')
                    ->get();
            @endphp
            @if($paymentHistory->isNotEmpty())
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-history mr-2"></i>Payment History
                </div>
                <div class="section-content">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sl. No</th>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th class="text-right">Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentHistory as $index => $pmt)
                                <tr class="{{ $pmt->id === $payment->id ? 'bg-blue-50' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pmt->payment_receipt_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pmt->approved_at ?? $pmt->created_at)->format('d M Y') }}</td>
                                    <td class="text-right font-semibold">₹{{ number_format($pmt->amount, 0) }}</td>
                                    <td class="text-center">
                                        @if($pmt->id === $payment->id)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-receipt mr-1"></i>This Receipt
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Paid
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-semibold bg-gray-50">
                                <td colspan="3" class="text-right">Total Paid</td>
                                <td class="text-right">₹{{ number_format($paymentHistory->sum('amount'), 0) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
            @endif

            <!-- Current Payment Details -->
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-credit-card mr-2"></i>Current Payment Details
                </div>
                <div class="section-content">
                    <div class="grid-2">
                        <div class="field">
                            <div class="field-label">Receipt Number</div>
                            <div class="field-value">{{ $payment->payment_receipt_number }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Payment Date</div>
                            <div class="field-value">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="field">
                            <div class="field-label">Payment Type</div>
                            <div class="field-value">{{ ucfirst($payment->payment_type) }}</div>
                        </div>
                        @if($payment->approved_at)
                        <div class="field">
                            <div class="field-label">Approved Date</div>
                            <div class="field-value">{{ \Carbon\Carbon::parse($payment->approved_at)->format('d M Y, h:i A') }}</div>
                        </div>
                        @endif
                    </div>
                    @if($payment->remarks)
                    <div class="field mt-0.5">
                        <div class="field-label">Remarks</div>
                        <div class="field-value bg-gray-50 p-0.5 rounded text-xs">{{ $payment->remarks }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Financial Summary -->
            @if($payment->enrollment)
            <div class="info-section">
                <div class="section-header">
                    <i class="fas fa-calculator mr-2"></i>Financial Summary
                </div>
                <div class="section-content">
                    <div class="space-y-0.5">
                        <div class="flex justify-between items-center py-0.5 border-b border-gray-200">
                            <span class="text-xs font-medium text-gray-600">Total Course Fee</span>
                            <span class="text-xs font-bold text-gray-800">₹{{ number_format($payment->enrollment->total_fee) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5 border-b border-gray-200">
                            <span class="text-xs font-medium text-gray-600">Amount Paid</span>
                            <span class="text-xs font-bold text-green-600">₹{{ number_format($payment->enrollment->paid_amount) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-0.5">
                            <span class="text-xs font-medium text-gray-600">Outstanding Amount</span>
                            <span class="text-xs font-bold text-red-600">₹{{ number_format($payment->enrollment->outstanding_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Contact Information -->
            <div class="footer-section">
                <div class="contact-info">
                    <div class="grid-2 mb-0.5">
                        <div>
                            <p class="font-semibold text-gray-800 mb-0.5">Address:</p>
                            <p>18-86 B, Krishna Nursing Home Back Side</p>
                            <p>Opp More Super Market, Main Road</p>
                            <p>Parvathipuram Manyam, Andhra Pradesh, 535501</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-0.5">Contact:</p>
                            <p><i class="fas fa-phone mr-1"></i>PH: 7799773656</p>
                            <p><i class="fas fa-envelope mr-1"></i>skill.softpro@gmail.com</p>
                            <p><i class="fas fa-globe mr-1"></i>www.softproskills.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="terms-section">
                <p class="font-semibold text-gray-800 mb-0.5">Terms & Conditions:</p>
                <p>1. This receipt is valid only for approved payments. 2. Course fees are non-refundable except under special circumstances. 3. Students must complete full payment before assessments. 4. Any disputes should be reported within 7 days. 5. SoftPro reserves the right to modify fee structure with prior notice.</p>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div>
                    <p class="text-xs text-gray-600 mb-0.5">Generated on</p>
                    <p class="text-xs font-semibold text-gray-800">{{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</p>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p class="text-xs text-gray-600">Authorized Signatory</p>
                    <p class="text-xs font-semibold text-gray-800">SoftPro Skill Solutions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Buttons -->
    <div class="text-center mt-4 no-print space-x-4">
        <a href="{{ auth()->user()->role === 'student' ? route('student.payments.receipt.pdf', $payment) : route('admin.payments.receipt.pdf', $payment) }}" 
           class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded font-semibold hover:bg-green-700 transition-colors">
            <i class="fas fa-file-pdf mr-2"></i>
            Download PDF
        </a>
        <button onclick="downloadReceipt()" 
                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700 transition-colors">
            <i class="fas fa-print mr-2"></i>
            Print
        </button>
    </div>

    <script>
        function downloadReceipt() {
            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            const receiptContent = document.querySelector('.receipt-card').outerHTML;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Payment Receipt - {{ $payment->payment_receipt_number }}</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
                    <style>
                        @page {
                            size: A4;
                            margin: 0.5cm;
                        }
                        body { 
                            margin: 0; 
                            background: white !important;
                            font-size: 8px;
                            line-height: 1.0;
                        }
                        .receipt-card { 
                            box-shadow: none !important;
                            margin: 0 !important;
                            max-width: none !important;
                            border-radius: 0 !important;
                            page-break-inside: avoid;
                        }
                        .receipt-header { position: relative !important; }
                        .receipt-header .logo-right { position: absolute !important; top: 0.2rem !important; right: 0.5rem !important; }
                        .receipt-container {
                            background: white !important;
                            padding: 0 !important;
                        }
                        .bg-gradient-to-r { 
                            background: linear-gradient(to right, #667eea, #764ba2) !important;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .receipt-content {
                            padding: 0.2rem !important;
                        }
                        .info-section {
                            margin-bottom: 0.2rem !important;
                            page-break-inside: avoid;
                        }
                        .section-content {
                            padding: 0.2rem !important;
                        }
                        .amount-highlight {
                            padding: 0.2rem !important;
                            margin: 0.2rem 0 !important;
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() {
                                window.close();
                            };
                        };
                    </script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        }
    </script>
</body>
</html>