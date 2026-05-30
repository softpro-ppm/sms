<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Partner Revenue Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 15px; margin: 22px 0 8px; }
        .muted { color: #6b7280; }
        .summary { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .summary td { border: 1px solid #e5e7eb; padding: 10px; }
        .summary .label { color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .summary .value { font-size: 18px; font-weight: bold; margin-top: 4px; }
        table.ledger { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .ledger th { background: #f3f4f6; color: #374151; text-align: left; font-size: 10px; text-transform: uppercase; }
        .ledger th, .ledger td { border: 1px solid #e5e7eb; padding: 6px; vertical-align: top; }
        .amount-negative { color: #047857; font-weight: bold; }
        .amount-positive { color: #1d4ed8; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Partner Revenue Report</h1>
    <div class="muted">
        {{ $trainingPartner->name }}{{ $trainingPartner->code ? ' (' . $trainingPartner->code . ')' : '' }}
        · Generated {{ now()->format('d M Y, h:i A') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Platform revenue</div>
                <div class="value">Rs. {{ number_format($summary['approval_revenue'], 2) }}</div>
            </td>
            <td>
                <div class="label">This month</div>
                <div class="value">Rs. {{ number_format($summary['approval_revenue_month'], 2) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Wallet recharges</div>
                <div class="value">Rs. {{ number_format($summary['recharges'], 2) }}</div>
            </td>
            <td>
                <div class="label">Current wallet balance</div>
                <div class="value">Rs. {{ number_format($summary['wallet_balance'], 2) }}</div>
            </td>
        </tr>
    </table>

    <h2>Recent Ledger</h2>
    <table class="ledger">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td>{{ $tx->created_at?->format('Y-m-d') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $tx->type)) }}</td>
                <td class="{{ $tx->amount < 0 ? 'amount-negative' : 'amount-positive' }}">
                    {{ $tx->amount < 0 ? '-' : '+' }}Rs. {{ number_format(abs((float) $tx->amount), 2) }}
                </td>
                <td>{{ $tx->balance_after !== null ? 'Rs. ' . number_format((float) $tx->balance_after, 2) : '-' }}</td>
                <td>{{ $tx->description ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5">No revenue or wallet transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
