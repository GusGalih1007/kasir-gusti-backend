<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .filters { margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .filters h4 { margin: 0 0 10px 0; }
        .filters p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Report</h1>
        <p>Generated on: {{ date('d M Y H:i:s') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h4>Applied Filters:</h4>
        @if(isset($filters['start_date']))
            <p><strong>Start Date:</strong> {{ $filters['start_date'] }}</p>
        @endif
        @if(isset($filters['end_date']))
            <p><strong>End Date:</strong> {{ $filters['end_date'] }}</p>
        @endif
        @if(isset($filters['payment_method']))
            <p><strong>Payment Method:</strong> {{ ucfirst($filters['payment_method']) }}</p>
        @endif
        @if(isset($filters['status']))
            <p><strong>Status:</strong> {{ ucfirst($filters['status']) }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Customer Name</th>
                <th>Membership</th>
                <th class="text-right">Total Bill</th>
                <th class="text-right">Total Payment</th>
                <th>Payment Method</th>
                <th>Cashier</th>
                <th class="text-center">Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->order_id }}</td>
                <td>{{ $item->customer->first_name . ' ' . $item->customer->last_name }}</td>
                <td>{{ $item->customer->member->membership ?? 'Non-Member' }}</td>
                <td class="text-right">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->payment->amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($item->payment->payment_method) }}</td>
                <td>{{ $item->userId->username }}</td>
                <td class="text-center">
                    @switch($item->status)
                        @case('completed')
                            <span class="badge badge-success">Complete</span>
                        @break
                        @case('pending')
                            <span class="badge badge-warning">Pending</span>
                        @break
                        @default
                            <span class="badge badge-danger">Failed</span>
                    @endswitch
                </td>
                <td>{{ $item->order_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h4>Summary</h4>
        <p><strong>Total Transactions:</strong> {{ $totalTransactions }}</p>
        <p><strong>Total Bill Amount:</strong> Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
        <p><strong>Total Payment Received:</strong> Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
    </div>
</body>
</html>