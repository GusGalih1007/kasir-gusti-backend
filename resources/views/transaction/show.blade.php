@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Transaction ID: {{ $transaction->order_id }}</h4>
                        <span
                            class="badge 
                            @if ($transaction->status == 'completed') bg-success
                            @elseif($transaction->status == 'pending') bg-warning
                            @elseif($transaction->status == 'failed') bg-danger
                            @else bg-secondary @endif">
                            Status: {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <a href="#" class="btn btn-warning">
                            Print
                        </a>
                        <a href="{{ route('transaction.index') }}" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Payment Status Alert -->
                    @if ($transaction->payment->payment_method == 'Midtrans')
                        <div
                            class="alert 
                            @if ($transaction->payment->status == 'complete') alert-success
                            @elseif($transaction->payment->status == 'pending') alert-warning
                            @elseif($transaction->payment->status == 'failed') alert-danger
                            @else alert-info @endif">
                            <strong>Payment Method:</strong> {{ $transaction->payment->payment_method }}
                            | <strong>Status:</strong> {{ ucfirst($transaction->payment->status) }}
                            @if ($transaction->payment->status == 'pending')
                                <br><small>This payment is waiting for confirmation from Midtrans.</small>
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        <h4 class="mb-3">Customer Information</h4>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Name</h6>
                            <p>
                                @if ($transaction->customer)
                                    {{ $transaction->customer->first_name . ' ' . $transaction->customer->last_name }}
                                @else
                                    Walk-in Customer
                                @endif
                            </p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Membership</h6>
                            <p>{{ $transaction->customer->member->membership ?? 'Non-Member' }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer E-mail</h6>
                            <p>{{ $transaction->customer->email ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Customer Phone</h6>
                            <p>{{ $transaction->customer->phone ?? 'N/A' }}</p>
                        </div>

                        @if ($transaction->customer && $transaction->customer->alamat)
                            <div class="mb-3 col-sm-12 col-md-12 col-lg-12">
                                <h6 class="mb-2">Customer Address</h6>
                                <p>{{ $transaction->customer->alamat }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <h4 class="mb-3">Product List</h4>
                        @foreach ($transaction['detail'] as $detail)
                            <div class="mb-4 col-sm-12 col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            {{ $loop->iteration . '. ' . $detail->product->product_name }}</h5>

                                        <div class="row">
                                            <div class="col-6">
                                                <h6 class="mb-2">Category</h6>
                                                <p class="small">{{ $detail->product->category->name ?? 'N/A' }}</p>
                                            </div>

                                            <div class="col-6">
                                                <h6 class="mb-2">Brand</h6>
                                                <p class="small">{{ $detail->product->brand->name ?? 'N/A' }}</p>
                                            </div>

                                            <div class="col-6">
                                                <h6 class="mb-2">Variant</h6>
                                                <p class="small">{{ $detail->variant->variant_name ?? 'N/A' }}</p>
                                            </div>

                                            <div class="col-6">
                                                <h6 class="mb-2">Quantity</h6>
                                                <p class="small">{{ $detail->quantity . ' Item' }}</p>
                                            </div>

                                            <div class="col-6">
                                                <h6 class="mb-2">Price Per Item</h6>
                                                <p class="small">Rp
                                                    {{ number_format($detail->price_at_purchase, 2, ',', '.') }}</p>
                                            </div>

                                            <div class="col-6">
                                                <h6 class="mb-2">Sub-Total</h6>
                                                <p class="small">Rp {{ number_format($detail->total_price, 2, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        <h4 class="mb-3">Cashier Information</h4>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Username</h6>
                            <p>{{ $transaction->userId->username }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Role</h6>
                            <p>{{ $transaction->userId->role->name ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Email</h6>
                            <p>{{ $transaction->userId->email }}</p>
                        </div>

                        <div class="mb-3 col-sm-6 col-md-3 col-lg-3">
                            <h6 class="mb-2">Phone</h6>
                            <p>{{ $transaction->userId->phone ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Transaction Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><strong>Discount</strong></td>
                                                <td>{{ intval($transaction->discount) }}%</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Price</strong></td>
                                                <td>Rp {{ number_format($transaction->total_amount, 2, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Payment Method</strong></td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                        @if ($transaction->payment->payment_method == 'Midtrans') bg-info
                                                        @elseif($transaction->payment->payment_method == 'Cash') bg-success
                                                        @elseif($transaction->payment->payment_method == 'Credit Card') bg-warning
                                                        @else bg-secondary @endif">
                                                        {{ $transaction->payment->payment_method }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Payment Amount</strong></td>
                                                <td>Rp {{ number_format($transaction->payment->amount, 2, ',', '.') }}</td>
                                            </tr>
                                            @if ($transaction->payment->payment_method != 'Midtrans')
                                                <tr>
                                                    <td><strong>Change</strong></td>
                                                    <td>Rp {{ number_format($transaction->change, 2, ',', '.') }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Payment Status</strong></td>
                                                <td>
                                                    <span
                                                        class="badge 
                                                        @if ($transaction->payment->status == 'complete') bg-success
                                                        @elseif($transaction->payment->status == 'pending') bg-warning
                                                        @elseif($transaction->payment->status == 'failed') bg-danger
                                                        @else bg-secondary @endif">
                                                        {{ ucfirst($transaction->payment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if ($transaction->payment->payment_date)
                                                <tr>
                                                    <td><strong>Payment Date</strong></td>
                                                    <td>{{ $transaction->payment->payment_date }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @if ($transaction->payment->payment_method == 'Midtrans' && in_array($transaction->status, ['pending', 'failed']))
                            <div class="col-md-6">
                                <div class="card border-{{ $transaction->status == 'pending' ? 'warning' : 'danger' }}">
                                    <div
                                        class="card-header bg-{{ $transaction->status == 'pending' ? 'warning' : 'danger' }} text-white">
                                        <h5 class="card-title mb-0">
                                            @if ($transaction->status == 'pending')
                                                ⏳ Pending Payment
                                            @else
                                                ❌ Payment Failed/Expired
                                            @endif
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($transaction->status == 'pending')
                                            <p>This transaction is waiting for payment confirmation.</p>
                                            <div class="alert alert-info">
                                                <small>
                                                    <strong>Note:</strong>
                                                    If you haven't completed the payment, click the button below to
                                                    continue.
                                                </small>
                                            </div>
                                        @else
                                            <p>This payment has failed or expired.</p>
                                            <div class="alert alert-warning">
                                                <small>
                                                    <strong>Reason:</strong> The payment was not completed within the
                                                    allowed time frame.
                                                </small>
                                            </div>
                                        @endif

                                        <div class="d-grid gap-2">
                                            {{-- Retry/Continue Payment Button --}}
                                            <a href="{{ route('transaction.retry-payment', $transaction->order_id) }}"
                                                class="btn btn-{{ $transaction->status == 'pending' ? 'success' : 'warning' }}">
                                                @if ($transaction->status == 'pending')
                                                    💳 Continue Payment
                                                @else
                                                    🔄 Retry Payment
                                                @endif
                                            </a>

                                            {{-- Status Check Button --}}
                                            <a href="{{ route('transaction.check-status', $transaction->order_id) }}"
                                                class="btn btn-primary">
                                                🔄 Check Status
                                            </a>

                                            @if ($transaction->status == 'failed')
                                                <a href="{{ route('transaction.create') }}"
                                                    class="btn btn-outline-secondary">
                                                    ➕ Create New Transaction
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @if ($transaction->payment->payment_method == 'Midtrans' && $transaction->status == 'pending')
        <script>
            // Auto-check status every 30 seconds for pending payments
            let checkCount = 0;
            const maxChecks = 10; // Stop after 5 minutes

            function autoCheckStatus() {
                if (checkCount >= maxChecks) {
                    console.log('Stopped auto-checking status after 5 minutes');
                    return;
                }

                setTimeout(() => {
                    fetch('{{ route('transaction.check-status', $transaction->order_id) }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            checkCount++;
                            console.log('Status check result:', data);

                            if (data.updated || data.status !== 'pending') {
                                // Status changed, reload the page
                                location.reload();
                            } else {
                                // Continue checking
                                autoCheckStatus();
                            }
                        })
                        .catch(error => {
                            console.error('Error checking status:', error);
                            checkCount++;
                            autoCheckStatus(); // Continue checking even on error
                        });
                }, 30000); // Check every 30 seconds
            }

            // Add loading states to buttons
            function addButtonLoadingStates() {
                // Status check button
                const statusBtn = document.querySelector('a[href*="check-status"]');
                if (statusBtn) {
                    statusBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const originalText = this.innerHTML;
                        this.innerHTML = '🔄 Checking...';
                        this.classList.add('disabled');

                        fetch(this.href, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.updated || data.status !== 'pending') {
                                    location.reload();
                                } else {
                                    this.innerHTML = originalText;
                                    this.classList.remove('disabled');

                                    showTemporaryMessage(data.message, 'info');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.innerHTML = originalText;
                                this.classList.remove('disabled');
                                showTemporaryMessage('Error checking status', 'danger');
                            });
                    });
                }

                // Retry/Continue payment button
                const retryBtn = document.querySelector('a[href*="retry-payment"]');
                if (retryBtn) {
                    retryBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const originalText = this.innerHTML;
                        this.innerHTML = '⏳ Redirecting...';
                        this.classList.add('disabled');

                        // Show loading message
                        showTemporaryMessage('Redirecting to payment page...', 'info');

                        // Allow user to see the message before redirecting
                        setTimeout(() => {
                            window.location.href = this.href;
                        }, 1000);
                    });
                }
            }

            function showTemporaryMessage(message, type) {
                const tempMsg = document.createElement('div');
                tempMsg.className = `alert alert-${type} mt-2`;
                tempMsg.innerHTML = message;

                const cardBody = document.querySelector('.card-body');
                cardBody.appendChild(tempMsg);

                setTimeout(() => {
                    tempMsg.remove();
                }, 3000);
            }

            // Start auto-checking after page load
            document.addEventListener('DOMContentLoaded', function() {
                autoCheckStatus();
                addButtonLoadingStates();
            });
        </script>
    @endif
@endsection
