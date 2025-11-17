@extends('layout.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Complete Your Payment</h4>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <h5>Order #{{ $orderId }}</h5>
                    <p class="text-muted">Total Amount: Rp {{ number_format($payment->amount, 2, ',', '.') }}</p>
                </div>
                
                <div id="snap-container" class="mb-4">
                    <!-- Snap payment widget will be loaded here -->
                    <div class="alert alert-info">
                        <p>Loading payment methods...</p>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <small>
                        <strong>Note:</strong> 
                        Please complete your payment within 24 hours. 
                        The payment page will automatically close after successful payment.
                    </small>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('transaction.show', $orderId) }}" class="btn btn-secondary">
                        ← Back to Order Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" 
        src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const snapToken = "{{ $snapToken }}";
        
        // Embed Snap payment widget
        window.snap.embed(snapToken, {
            embedId: 'snap-container',
            onSuccess: function(result) {
                console.log('Payment success:', result);
                // Redirect to success page
                window.location.href = "{{ route('transaction.midtrans.finish') }}?order_id={{ $orderId }}";
            },
            onPending: function(result) {
                console.log('Payment pending:', result);
                window.location.href = "{{ route('transaction.midtrans.pending') }}?order_id={{ $orderId }}";
            },
            onError: function(result) {
                console.log('Payment error:', result);
                window.location.href = "{{ route('transaction.midtrans.error') }}?order_id={{ $orderId }}";
            },
            onClose: function() {
                console.log('Payment widget closed');
                // User closed the widget without completing payment
            }
        });
    });
</script>
@endsection