<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 15px; 
            font-size: 14px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            padding-bottom: 8px; 
            border-bottom: 2px solid #333; 
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        .filters { 
            margin-bottom: 15px; 
            padding: 8px; 
            background: #f8f9fa; 
            border-radius: 4px;
            font-size: 12px;
        }
        .filters h4 { 
            margin: 0 0 8px 0;
            font-size: 14px;
        }
        .filters p { 
            margin: 3px 0; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
            font-size: 12px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 6px 4px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold;
            font-size: 11px;
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .summary { 
            margin-top: 15px; 
            padding: 10px; 
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }
        .summary p {
            margin: 5px 0;
        }
        
        /* BUTTON STYLES */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 8px 16px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, 
                        border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
            text-decoration: none;
            margin: 0 5px;
        }
        
        .btn:focus {
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn:hover {
            text-decoration: none;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            color: #fff;
            background-color: #0056b3;
            border-color: #004085;
        }
        
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            color: #fff;
            background-color: #545b62;
            border-color: #4e555b;
        }
        
        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            color: #fff;
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-danger:hover {
            color: #fff;
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        .btn-warning {
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
        }
        
        .btn-warning:hover {
            color: #212529;
            background-color: #e0a800;
            border-color: #d39e00;
        }
        
        .btn-info {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        
        .btn-info:hover {
            color: #fff;
            background-color: #138496;
            border-color: #117a8b;
        }
        
        .btn-light {
            color: #212529;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }
        
        .btn-light:hover {
            color: #212529;
            background-color: #e2e6ea;
            border-color: #dae0e5;
        }
        
        .btn-dark {
            color: #fff;
            background-color: #343a40;
            border-color: #343a40;
        }
        
        .btn-dark:hover {
            color: #fff;
            background-color: #23272b;
            border-color: #1d2124;
        }
        
        /* Button Sizes */
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }
        
        .btn-lg {
            padding: 12px 24px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 6px;
        }
        
        /* Button Outline Variants */
        .btn-outline-primary {
            color: #007bff;
            background-color: transparent;
            background-image: none;
            border-color: #007bff;
        }
        
        .btn-outline-primary:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            background-color: transparent;
            background-image: none;
            border-color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        /* Button States */
        .btn:disabled,
        .btn.disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        /* Button Group */
        .btn-group {
            position: relative;
            display: inline-flex;
            vertical-align: middle;
        }
        
        .btn-group > .btn {
            position: relative;
            flex: 1 1 auto;
        }
        
        .btn-group > .btn:first-child {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        
        .btn-group > .btn:last-child {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        
        .btn-group > .btn:not(:first-child):not(:last-child) {
            border-radius: 0;
        }
        
        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 10px;
        }
        
        .badge-pill {
            padding-right: 8px;
            padding-left: 8px;
            border-radius: 10rem;
        }
        
        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }
        
        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }
        
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
        
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        
        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }
        
        .badge-light {
            color: #212529;
            background-color: #f8f9fa;
        }
        
        .badge-dark {
            color: #fff;
            background-color: #343a40;
        }
        
        /* Text Colors */
        .text-primary { color: #007bff !important; }
        .text-secondary { color: #6c757d !important; }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-info { color: #17a2b8 !important; }
        .text-light { color: #f8f9fa !important; }
        .text-dark { color: #343a40 !important; }
        .text-muted { color: #6c757d !important; }
        .text-white { color: #fff !important; }
        
        /* Background Colors */
        .bg-primary { background-color: #007bff !important; }
        .bg-secondary { background-color: #6c757d !important; }
        .bg-success { background-color: #28a745 !important; }
        .bg-danger { background-color: #dc3545 !important; }
        .bg-warning { background-color: #ffc107 !important; }
        .bg-info { background-color: #17a2b8 !important; }
        .bg-light { background-color: #f8f9fa !important; }
        .bg-dark { background-color: #343a40 !important; }
        .bg-white { background-color: #fff !important; }
        
        /* Alert Styles */
        .alert {
            position: relative;
            padding: 12px 16px;
            margin-bottom: 16px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-primary {
            color: #004085;
            background-color: #cce5ff;
            border-color: #b8daff;
        }
        
        .alert-secondary {
            color: #383d41;
            background-color: #e2e3e5;
            border-color: #d6d8db;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
        
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        
        /* Form Controls */
        .form-control {
            display: block;
            width: 100%;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-label {
            display: inline-block;
            margin-bottom: 4px;
            font-weight: 500;
        }

        @media print {
            body { 
                margin: 10px; 
                font-size: 12px;
            }
            .no-print { 
                display: none; 
            }
            .header {
                margin-bottom: 10px;
                padding-bottom: 5px;
            }
            .header h1 {
                font-size: 20px;
            }
            table {
                margin-bottom: 10px;
                font-size: 10px;
            }
            th, td {
                padding: 4px 3px;
            }
            .summary {
                margin-top: 10px;
                padding: 8px;
            }
            
            /* Hide buttons in print */
            .btn {
                display: none !important;
            }
        }
        
        /* Compact styling */
        .compact-table th,
        .compact-table td {
            padding: 4px 3px;
        }
        
        .compact-summary p {
            margin: 3px 0;
        }
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
        @foreach($filters as $key => $value)
            <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
        @endforeach
    </div>
    @endif

    <table class="compact-table">
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
                    <span class="badge badge-{{ $item->status === 'completed' ? 'success' : ($item->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    @if($item->order_date instanceof \Carbon\Carbon)
                        {{ $item->order_date->format('d M Y H:i') }}
                    @else
                        {{ \Carbon\Carbon::parse($item->order_date)->format('d M Y H:i') }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary compact-summary">
        <p><strong>Total Records:</strong> {{ $totalTransactions }}</p>
        <p><strong>Total Bill:</strong> Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
        <p><strong>Total Payment:</strong> Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 15px;">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
        <button onclick="window.location.reload()" class="btn btn-info">Refresh</button>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
        
        // After printing, close window if desired (optional)
        // window.onafterprint = function() {
        //     setTimeout(function() {
        //         window.close();
        //     }, 500);
        // };
    </script>
</body>
</html>