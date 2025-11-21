@extends('layout.app')
@section('title', 'Transaction Details - Order #' . $order->order_id)
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Transaction Details - Order #{{ $order->order_id }}</h4>
                    </div>
                    <div class="header-action">
                        <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Transactions
                        </a>
                        @if ($order->payment->status === 'pending' && $order->payment->payment_method === 'midtrans')
                            <a href="{{ route('transaction.payment', $order->order_id) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card"></i> Complete Payment
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alert Messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Order ID</strong></td>
                                            <td>: {{ $order->order_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Order Date</strong></td>
                                            <td>: {{ $order->order_date }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ strtoupper($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created By</strong></td>
                                            <td>: {{ $order->userCreator->name ?? 'System' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Name</strong></td>
                                            <td>: {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td>: {{ $order->customer->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone</strong></td>
                                            <td>: {{ $order->customer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address</strong></td>
                                            <td>: {{ $order->customer->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Membership</strong></td>
                                            <td>
                                                :
                                                @if ($order->customer->is_member && $order->customer->member)
                                                    <span class="badge bg-success">
                                                        {{ $order->customer->member->membership }}
                                                        ({{ $order->customer->member->discount }}% Discount)
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Non-Member</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th>Variant</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->detail as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($detail->variant->photo)
                                                            <img src="{{ asset('storage/' . $detail->variant->photo) }}"
                                                                alt="{{ $detail->product->product_name }}"
                                                                class="rounded me-3"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <img src="https://via.placeholder.com/50"
                                                                alt="{{ $detail->product->product_name }}"
                                                                class="rounded me-3"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $detail->product->product_name }}</h6>
                                                            <small class="text-muted">Category:
                                                                {{ $detail->product->category->name ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ $detail->variant->variant_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">SKU: {{ $detail->variant->sku }}</small>
                                                </td>
                                                <td class="text-center">Rp
                                                    {{ number_format($detail->price_at_purchase, 0, ',', '.') }}</td>
                                                <td class="text-center">{{ $detail->quantity }}</td>
                                                <td class="text-end">Rp
                                                    {{ number_format($detail->total_price, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="60%"><strong>Subtotal</strong></td>
                                            <td class="text-end">Rp
                                                {{ number_format($order->total_amount + $order->discount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @if ($order->discount > 0)
                                            <tr>
                                                <td><strong>Discount</strong></td>
                                                <td class="text-end text-danger">- Rp
                                                    {{ number_format($order->discount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Total Amount</strong></td>
                                            <td class="text-end"><strong>Rp
                                                    {{ number_format($order->total_amount, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Payment Details</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="60%"><strong>Payment Method</strong></td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-{{ $order->payment->payment_method === 'cash' ? 'success' : 'primary' }}">
                                                    {{ strtoupper($order->payment->payment_method) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status</strong></td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : ($order->payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ strtoupper($order->payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if ($order->payment->payment_date)
                                            <tr>
                                                <td><strong>Payment Date</strong></td>
                                                <td class="text-end">
                                                    {{ $order->payment->payment_date->format('d M Y, H:i') }}</td>
                                            </tr>
                                        @endif
                                        @if ($order->payment->payment_method === 'cash')
                                            <tr>
                                                <td><strong>Amount Paid</strong></td>
                                                <td class="text-end">Rp
                                                    {{ number_format($order->payment->amount + $order->payment->change, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Change</strong></td>
                                                <td class="text-end">Rp
                                                    {{ number_format($order->payment->change, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Final Amount</strong></td>
                                            <td class="text-end"><strong>Rp
                                                    {{ number_format($order->payment->amount, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @if ($order->payment->snap_token && $order->payment->payment_method === 'midtrans')
                                            <tr>
                                                <td><strong>Snap Token</strong></td>
                                                <td class="text-end">
                                                    <small
                                                        class="text-muted">{{ substr($order->payment->snap_token, 0, 20) }}...</small>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>

                                    @if ($order->payment->status === 'pending' && $order->payment->payment_method === 'midtrans')
                                        <div class="alert alert-warning mt-3">
                                            <h6>Pending Payment</h6>
                                            <p class="mb-2">This order is waiting for payment confirmation. Customer needs
                                                to complete the payment via Midtrans.</p>
                                            <a href="{{ route('transaction.payment', $order->order_id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-external-link-alt"></i> Go to Payment Page
                                            </a>
                                        </div>
                                    @endif

                                    @if ($order->payment->status === 'completed' && $order->payment->payment_method === 'midtrans')
                                        <div class="alert alert-success mt-3">
                                            <h6>Payment Completed</h6>
                                            <p class="mb-0">Payment has been successfully processed via Midtrans.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                    @if ($order->payment->status === 'pending' && $order->payment->payment_method === 'midtrans')
                                        <a href="{{ route('transaction.payment', $order->order_id) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-credit-card"></i> Process Payment
                                        </a>
                                    @endif
                                </div>
                                <div>
                                    @if ($order->status === 'completed')
                                        <button class="btn btn-success" onclick="printReceipt()">
                                            <i class="fas fa-print"></i> Print Receipt
                                        </button>
                                        <a href="{{ route('export.receipt.pdf', $order->order_id) }}"
                                            class="btn btn-primary" id="download-receipt">
                                            <i class="fas fa-download"></i> Download Receipt
                                        </a>
                                    @endif
                                    @if (auth()->user()->can('delete-transactions'))
                                        <form action="{{ route('transaction.destroy', $order->order_id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Printable Receipt (Hidden) -->
    <div id="printable-receipt" class="d-none">
        <div class="receipt-container"
            style="max-width: 300px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
            <div class="text-center mb-3">
                <h2 style="margin: 0;">{{ config('app.name', 'Laravel') }}</h2>
                <p style="margin: 5px 0; font-size: 12px;">Transaction Receipt</p>
            </div>

            <hr style="border-top: 1px dashed #000; margin: 10px 0;">

            <table width="100%" style="font-size: 12px; margin-bottom: 10px;">
                <tr>
                    <td><strong>Order ID:</strong></td>
                    <td>{{ $order->order_id }}</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>{{ $order->order_date }}</td>
                </tr>
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td>{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                </tr>
            </table>

            <hr style="border-top: 1px dashed #000; margin: 10px 0;">

            <table width="100%" style="font-size: 12px; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th align="left">Item</th>
                        <th align="center">Qty</th>
                        <th align="right">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->detail as $detail)
                        <tr>
                            <td>{{ $detail->product->product_name }} - {{ $detail->variant->variant_name }}</td>
                            <td align="center">{{ $detail->quantity }}</td>
                            <td align="right">Rp {{ number_format($detail->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr style="border-top: 1px dashed #000; margin: 10px 0;">

            <table width="100%" style="font-size: 12px;">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td align="right">Rp {{ number_format($order->total_amount + $order->discount, 0, ',', '.') }}</td>
                </tr>
                @if ($order->discount > 0)
                    <tr>
                        <td><strong>Discount:</strong></td>
                        <td align="right">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Total:</strong></td>
                    <td align="right"><strong>Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</strong></td>
                </tr>
                @if ($order->payment->payment_method === 'cash')
                    <tr>
                        <td><strong>Cash:</strong></td>
                        <td align="right">Rp
                            {{ number_format($order->payment->amount + $order->payment->change, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Change:</strong></td>
                        <td align="right">Rp {{ number_format($order->payment->change, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>

            <hr style="border-top: 1px dashed #000; margin: 10px 0;">

            <div class="text-center" style="font-size: 11px; margin-top: 15px;">
                <p>Thank you for your purchase!</p>
                <p>{{ config('app.name', 'Laravel') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function printReceipt() {
            const printContent = document.getElementById('printable-receipt').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload();
        }

        // Auto refresh if payment is pending (for midtrans)
        @if ($order->payment->status === 'pending' && $order->payment->payment_method === 'midtrans')
            setTimeout(function() {
                window.location.reload();
            }, 30000); // Refresh every 30 seconds
        @endif
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #printable-receipt,
            #printable-receipt * {
                visibility: visible;
            }

            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
@endsection
