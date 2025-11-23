@extends('layout.app')
@section('title', 'Payment - Order #' . $order->order_id)
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Payment - Order #{{ $order->order_id }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <h5>Payment Instructions</h5>
                                <p>Please complete your payment using the Midtrans payment gateway below.</p>
                                <p><strong>Total Amount: Rp
                                        {{ number_format($order->payment->amount, 0, ',', '.') }}</strong></p>
                                <p class="mb-0"><small>After payment, you will be automatically redirected. If not, click
                                        "Check Payment Status" below.</small></p>
                            </div>

                            <div id="midtrans-payment">
                                <!-- Midtrans Snap will be rendered here -->
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('transaction.show', $order->order_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Order Details
                                </a>
                                <button id="check-status" class="btn btn-info">
                                    <i class="fas fa-sync"></i> Check Payment Status
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Order ID:</strong> {{ $order->order_id }}</p>
                                    <p><strong>Customer:</strong> {{ $order->customer->first_name }}
                                        {{ $order->customer->last_name }}</p>
                                    <p><strong>Date:</strong> {{ $order->order_date }}</p>
                                    <hr>
                                    <p><strong>Total Amount:</strong> Rp
                                        {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    <p><strong>Discount:</strong> Rp {{ number_format($order->discount, 0, ',', '.') }}</p>
                                    <p><strong>Final Amount:</strong> Rp
                                        {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                                    <hr>
                                    <p><strong>Payment Status:</strong>
                                        <span
                                            class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : ($order->payment->status === 'pending' ? 'warning' : 'danger') }}"
                                            id="status-badge">
                                            {{ strtoupper($order->payment->status) }}
                                        </span>
                                    </p>
                                    <div id="status-message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        const snapToken = '{{ $order->payment->snap_token }}';
        const orderId = '{{ $order->order_id }}';

        // Render Midtrans Snap
        window.snap.pay(snapToken, {
            onSuccess: function (result) {
                console.log('Payment success:', result);
                // Redirect to finish page which will check status automatically
                window.location.href = '{{ route('transaction.payment.finish', $order->order_id) }}';
            },
            onPending: function (result) {
                console.log('Payment pending:', result);
                // Still redirect to finish page to check status
                window.location.href = '{{ route('transaction.payment.finish', $order->order_id) }}';
            },
            onError: function (result) {
                console.log('Payment error:', result);
                // Show error message but don't redirect
                showStatusMessage('Payment failed: ' + (result.status_message || 'Unknown error'), 'error');
            },
            onClose: function () {
                console.log('Payment popup closed without completing');
                // User closed the popup without payment
                showStatusMessage('Payment window closed. You can complete payment later.', 'warning');
            }
        });

        // Check payment status manually
        document.getElementById('check-status').addEventListener('click', function () {
            checkPaymentStatus();
        });

        function checkPaymentStatus() {
            const btn = document.getElementById('check-status');
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
            btn.disabled = true;

            fetch(`/api/transaction/${orderId}/check-status`)
                .then(response => response.json())
                .then(data => {
                    // Update status badge
                    updateStatusBadge(data.status);
                    showStatusMessage(data.message, 'success');

                    // If payment completed, redirect to order page
                    if (data.status === 'completed') {
                        setTimeout(() => {
                            window.location.href = '{{ route('transaction.show', $order->order_id) }}';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showStatusMessage('Error checking payment status', 'error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        function updateStatusBadge(status) {
            const badge = document.getElementById('status-badge');
            let bgClass = 'warning';

            if (status === 'completed') {
                bgClass = 'success';
            } else if (status === 'failed') {
                bgClass = 'danger';
            } else if (status === 'challenge') {
                bgClass = 'info';
            }

            badge.className = `badge bg-${bgClass}`;
            badge.textContent = status.toUpperCase();
        }

        function showStatusMessage(message, type) {
            const messageDiv = document.getElementById('status-message');
            const alertClass = type === 'error' ? 'alert-danger' :
                type === 'warning' ? 'alert-warning' : 'alert-success';

            messageDiv.innerHTML = `
                    <div class="alert ${alertClass} alert-dismissible fade show mt-2">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
        }

        // Auto-check status every 20 seconds if still pending
        setInterval(() => {
            if (document.getElementById('status-badge').textContent === 'PENDING') {
                checkPaymentStatus();
            }
        }, 20000);
    </script>
@endsection
