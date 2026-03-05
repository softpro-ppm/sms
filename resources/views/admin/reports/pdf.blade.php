<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report - {{ ucfirst($report) }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { font-size: 11px; color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>{{ ucfirst($report) }} Report</h1>
    <div class="meta">Generated: {{ $generated_at->format('Y-m-d H:i') }}</div>

    <table>
        <thead>
            <tr>
                @if($report === 'payments')
                    <th>Receipt</th><th>Student</th><th>Course</th><th>Batch</th><th>Amount</th><th>Status</th><th>Date</th>
                @elseif($report === 'enrollments')
                    <th>Enrollment</th><th>Student</th><th>Course</th><th>Batch</th><th>Total</th><th>Paid</th><th>Pending</th><th>Status</th><th>Date</th>
                @elseif($report === 'students')
                    <th>Student</th><th>Email</th><th>Phone</th><th>Aadhar</th><th>Status</th><th>Registered</th>
                @else
                    <th>Student</th><th>Assessment</th><th>Course</th><th>Batch</th><th>Score</th><th>Result</th><th>Completed</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @if($report === 'payments')
                        <td>{{ $row->payment_receipt_number }}</td>
                        <td>{{ $row->student?->full_name }}</td>
                        <td>{{ $row->enrollment?->batch?->course?->name }}</td>
                        <td>{{ $row->enrollment?->batch?->batch_name }}</td>
                        <td>{{ $row->amount }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ optional($row->created_at)->format('Y-m-d') }}</td>
                    @elseif($report === 'enrollments')
                        <td>{{ $row->enrollment_number }}</td>
                        <td>{{ $row->student?->full_name }}</td>
                        <td>{{ $row->batch?->course?->name }}</td>
                        <td>{{ $row->batch?->batch_name }}</td>
                        <td>{{ $row->total_fee }}</td>
                        <td>{{ $row->paid_amount }}</td>
                        <td>{{ $row->outstanding_amount }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ optional($row->enrollment_date)->format('Y-m-d') }}</td>
                    @elseif($report === 'students')
                        <td>{{ $row->full_name }}</td>
                        <td>{{ $row->email }}</td>
                        <td>{{ $row->whatsapp_number }}</td>
                        <td>{{ $row->aadhar_number }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ optional($row->created_at)->format('Y-m-d') }}</td>
                    @else
                        <td>{{ $row->student?->name }}</td>
                        <td>{{ $row->assessment?->title }}</td>
                        <td>{{ $row->enrollment?->batch?->course?->name }}</td>
                        <td>{{ $row->enrollment?->batch?->batch_name }}</td>
                        <td>{{ $row->percentage }}</td>
                        <td>{{ $row->is_passed ? 'Passed' : 'Failed' }}</td>
                        <td>{{ optional($row->completed_at)->format('Y-m-d') }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
