<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Order #{{ $order->order_id }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            margin: 0; 
            padding: 10px;
            font-size: 12px;
        }
        .receipt-container { 
            max-width: 280px; 
            margin: 0 auto;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            padding-bottom: 8px; 
            border-bottom: 1px dashed #000; 
        }
        .details { 
            margin-bottom: 12px; 
        }
        .items { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px; 
        }
        .items th, .items td { 
            padding: 4px 2px; 
            text-align: left; 
            border-bottom: 1px dashed #ddd;
        }
        .items th { 
            border-bottom: 1px dashed #000; 
            font-weight: bold;
        }
        .summary { 
            width: 100%; 
            margin-top: 10px;
        }
        .summary td { 
            padding: 2px 0; 
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .footer { 
            text-align: center; 
            margin-top: 15px; 
            padding-top: 8px; 
            border-top: 1px dashed #000; 
            font-size: 10px;
        }
        .line-divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h2 style="margin: 0; font-size: 16px;">{{ config('app.name', 'Laravel') }}</h2>
            <p style="margin: 3px 0; font-size: 11px;">Transaction Receipt</p>
        </div>
        
        <div class="details">
            <table width="100%" style="font-size: 11px;">
                <tr>
                    <td width="40%"><strong>Order ID:</strong></td>
                    <td>{{ $order->order_id }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>
                        @if($order->order_date instanceof \Carbon\Carbon)
                            {{ $order->order_date }}
                        @else
                            {{ \Carbon\Carbon::parse($order->order_date) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                </tr>
                <tr>
                    <td><strong>Cashier:</strong></td>
                    <td>{{ $order->userCreator->username ?? 'System' }}</td>
                </tr>
            </table>
        </div>
        
        <div class="line-divider"></div>
        
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->detail as $detail)
                <tr>
                    <td style="font-size: 10px;">{{ $detail->product->product_name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($detail->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="line-divider"></div>
        
        <table class="summary" style="font-size: 11px;">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">Rp {{ number_format($order->total_amount + $order->discount, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td><strong>Discount:</strong></td>
                <td class="text-right">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Total:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</strong></td>
            </tr>
            @if($order->payment->payment_method === 'cash')
            <tr>
                <td><strong>Cash:</strong></td>
                <td class="text-right">Rp {{ number_format($order->payment->amount + $order->payment->change, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Change:</strong></td>
                <td class="text-right">Rp {{ number_format($order->payment->change, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
        
        <div class="footer">
            <p><strong>Thank you for your purchase!</strong></p>
            <p>{{ config('app.name', 'Laravel') }}</p>
            <p>Generated on: {{ date('d M Y H:i') }}</p>
        </div>
    </div>
</body>
</html>