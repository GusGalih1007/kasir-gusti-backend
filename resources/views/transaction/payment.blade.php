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
                            <p><strong>Total Amount: Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</strong></p>
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
                                <p><strong>Customer:</strong> {{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
                                <p><strong>Date:</strong> {{ $order->order_date }}</p>
                                <hr>
                                <p><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <p><strong>Discount:</strong> Rp {{ number_format($order->discount, 0, ',', '.') }}</p>
                                <p><strong>Final Amount:</strong> Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                                <hr>
                                <p><strong>Payment Status:</strong> 
                                    <span class="badge bg-{{ $order->payment->status === 'completed' ? 'success' : ($order->payment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ strtoupper($order->payment->status) }}
                                    </span>
                                </p>
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
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    const snapToken = '{{ $order->payment->snap_token }}';
    
    // Render Midtrans Snap
    window.snap.pay(snapToken, {
        onSuccess: function(result) {
            console.log('Payment success:', result);
            window.location.href = '{{ route('transaction.payment.callback') }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=success';
        },
        onPending: function(result) {
            console.log('Payment pending:', result);
            window.location.href = '{{ route('transaction.payment.callback') }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=pending';
        },
        onError: function(result) {
            console.log('Payment error:', result);
            window.location.href = '{{ route('transaction.payment.callback') }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=error';
        },
        onClose: function() {
            console.log('Payment popup closed');
        }
    });

    // Check payment status
    document.getElementById('check-status').addEventListener('click', function() {
        fetch('{{ route('transaction.check-status', $order->order_id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'completed') {
                    window.location.reload();
                } else {
                    alert('Payment status: ' + data.status);
                }
            });
    });
</script>
@endsection