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

                    <!-- Check Status Section for Midtrans Pending Payments -->
                    @if($order->payment->payment_method === 'midtrans' && in_array($order->payment->status, ['pending', 'challenge']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1">Payment Status Check</h5>
                                            <p class="card-text mb-0">Last checked: <span id="lastChecked">Never</span></p>
                                        </div>
                                        <div>
                                            <button id="checkStatusBtn" class="btn btn-info">
                                                <i class="fas fa-sync-alt"></i> Check Status Now
                                            </button>
                                            <div id="checkStatusSpinner" class="spinner-border spinner-border-sm text-info d-none" role="status">
                                                <span class="visually-hidden">Checking...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="statusResult" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
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
                                            <td>:
                                                @if($order->order_date instanceof \Carbon\Carbon)
                                                    {{ $order->order_date }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($order->order_date) }}
                                                @endif
                                            </td>
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
                                                            <img src="https://placehold.co/50"
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
                                                    class="badge bg-{{ $order->payment->payment_method === 'cash' ? 'success' : 'primary' }}"
                                                    id="paymentMethodBadge">
                                                    {{ strtoupper($order->payment->payment_method) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status</strong></td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : ($order->payment->status === 'pending' ? 'warning' : ($order->payment->status === 'challenge' ? 'warning' : 'danger')) }}"
                                                    id="paymentStatusBadge">
                                                    {{ strtoupper($order->payment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if ($order->payment->payment_date)
                                            <tr>
                                                <td><strong>Payment Date</strong></td>
                                                <td class="text-end">
                                                    @if($order->payment->payment_date instanceof \Carbon\Carbon)
                                                        {{ $order->payment->payment_date->format('d M Y, H:i') }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($order->payment->payment_date)->format('d M Y, H:i') }}
                                                    @endif
                                                </td>
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

                                    @if ($order->payment->status === 'challenge' && $order->payment->payment_method === 'midtrans')
                                        <div class="alert alert-warning mt-3">
                                            <h6>Payment Challenge</h6>
                                            <p class="mb-2">This payment is being challenged. Please check the payment status regularly.</p>
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
                    <td>
                        @if($order->order_date instanceof \Carbon\Carbon)
                            {{ $order->order_date->format('d/m/Y H:i') }}
                        @else
                            {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}
                        @endif
                    </td>
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
                            <td>{{ $detail->product->product_name }}</td>
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

        // Check Status Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const checkStatusBtn = document.getElementById('checkStatusBtn');
            const checkStatusSpinner = document.getElementById('checkStatusSpinner');
            const statusResult = document.getElementById('statusResult');
            const lastChecked = document.getElementById('lastChecked');
            const paymentStatusBadge = document.getElementById('paymentStatusBadge');

            if (checkStatusBtn) {
                checkStatusBtn.addEventListener('click', function() {
                    // Show loading state
                    checkStatusBtn.disabled = true;
                    checkStatusSpinner.classList.remove('d-none');
                    statusResult.innerHTML = '<div class="alert alert-info">Checking payment status with payment provider...</div>';

                    // Make API call to check status
                    fetch(`/api/transaction/{{ $order->order_id }}/check-status`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);

                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                            }).catch(() => {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('API response:', data);

                        // Update last checked time
                        lastChecked.textContent = new Date().toLocaleString();

                        // Show result
                        if (data.success) {
                            if (data.status_changed) {
                                statusResult.innerHTML = `
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i>
                                        <strong>Status Updated Successfully!</strong><br>
                                        Previous Status: <span class="badge bg-${getStatusColor(data.previous_status)}">${data.previous_status.toUpperCase()}</span><br>
                                        New Status: <span class="badge bg-${getStatusColor(data.payment_status)}">${data.payment_status.toUpperCase()}</span><br>
                                        ${data.message}
                                    </div>
                                `;
                            } else {
                                statusResult.innerHTML = `
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Status Check Complete</strong><br>
                                        Current Status: <span class="badge bg-${getStatusColor(data.payment_status)}">${data.payment_status.toUpperCase()}</span><br>
                                        ${data.message}
                                    </div>
                                `;
                            }

                            // Update payment status badge
                            if (paymentStatusBadge) {
                                paymentStatusBadge.className = `badge bg-${getStatusColor(data.payment_status)}`;
                                paymentStatusBadge.textContent = data.payment_status.toUpperCase();
                            }

                            // Reload page after 3 seconds if status changed to completed
                            if (data.payment_status === 'completed') {
                                statusResult.innerHTML += `
                                    <div class="alert alert-warning mt-2">
                                        <i class="fas fa-sync-alt"></i> Page will refresh automatically...
                                    </div>
                                `;
                                setTimeout(() => {
                                    window.location.reload();
                                }, 3000);
                            }
                        } else {
                            statusResult.innerHTML = `
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Status Check Completed</strong><br>
                                    ${data.message}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error checking status:', error);
                        let errorMessage = 'Failed to check payment status. ';

                        if (error.message.includes('404') || error.message.includes('not found')) {
                            errorMessage += 'The transaction was not found in the payment system.';
                        } else if (error.message.includes('401')) {
                            errorMessage += 'Authentication failed with payment provider.';
                        } else if (error.message.includes('Network')) {
                            errorMessage += 'Network connection error. Please check your internet connection.';
                        } else {
                            errorMessage += error.message;
                        }

                        statusResult.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i>
                                <strong>Error!</strong><br>
                                ${errorMessage}
                            </div>
                        `;
                    })
                    .finally(() => {
                        // Reset button state
                        checkStatusBtn.disabled = false;
                        checkStatusSpinner.classList.add('d-none');
                    });
                });
            }

            function getStatusColor(status) {
                switch(status) {
                    case 'completed': return 'success';
                    case 'pending': return 'warning';
                    case 'challenge': return 'warning';
                    case 'failed': return 'danger';
                    default: return 'secondary';
                }
            }

            // Auto refresh if payment is pending (for midtrans)
            @if ($order->payment->status === 'pending' && $order->payment->payment_method === 'midtrans')
                let autoRefreshCount = 0;
                const maxAutoRefresh = 10;

                function autoRefreshStatus() {
                    if (autoRefreshCount < maxAutoRefresh) {
                        setTimeout(() => {
                            if (checkStatusBtn && !checkStatusBtn.disabled) {
                                console.log(`Auto-refresh ${autoRefreshCount + 1}/${maxAutoRefresh}`);
                                checkStatusBtn.click();
                                autoRefreshCount++;
                                autoRefreshStatus();
                            }
                        }, 30000); // Refresh every 30 seconds
                    }
                }

                // Start auto refresh after 10 seconds
                setTimeout(autoRefreshStatus, 10000);
            @endif
        });

        // Handle receipt download
        document.getElementById('download-receipt')?.addEventListener('click', function(e) {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            this.disabled = true;

            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
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

        /* Status check styling */
        #statusResult .alert {
            margin-bottom: 0;
        }

        .spinner-border {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endsection
