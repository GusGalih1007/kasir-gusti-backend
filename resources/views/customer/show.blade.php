@extends('layout.app')

@section('title', 'Detail Customer - ' . $customer->first_name . ' ' . $customer->last_name)

@section('content')
        <div class="row">
            <!-- Informasi Utama -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-user me-2"></i>Informasi Personal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Nama Depan</label>
                                    <p class="mb-0 fs-6">{{ $customer->first_name }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Email</label>
                                    <p class="mb-0 fs-6">
                                        @if($customer->email)
                                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                {{ $customer->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Status Member</label>
                                    <p class="mb-0">
                                        @if($customer->is_member)
                                            <span class="badge bg-success fs-6">
                                                <i class="fas fa-crown me-1"></i>Member
                                            </span>
                                        @else
                                            <span class="badge bg-secondary fs-6">Non-Member</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Nama Belakang</label>
                                    <p class="mb-0 fs-6">{{ $customer->last_name }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Telepon</label>
                                    <p class="mb-0 fs-6">
                                        @if($customer->phone)
                                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                                {{ $customer->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Tipe Membership</label>
                                    <p class="mb-0 fs-6">
                                        {{ $customer->member->membership ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-map-marker-alt me-2"></i>Alamat
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($customer->alamat)
                            <div class="alert alert-light border">
                                <p class="mb-0 fs-6">{{ $customer->alamat }}</p>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <p class="mb-0">Alamat belum diisi</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Sistem -->
                <div class="card mt-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-database me-2"></i>Informasi Sistem
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Dibuat Oleh</label>
                                    <p class="mb-0 fs-6">{{ $customer->userCreator->username ?? 'System' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Tanggal Dibuat</label>
                                    <p class="mb-0 fs-6">{{ $customer->created_at->format('d F Y H:i') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Diupdate Oleh</label>
                                    <p class="mb-0 fs-6">{{ $customer->userUpdator->username ?? '-' }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-bold text-muted small">Terakhir Update</label>
                                    <p class="mb-0 fs-6">{{ $customer->updated_at->format('d F Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Statistik -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-chart-bar me-2"></i>Statistik Order
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="display-4 fw-bold text-success mb-2">
                            {{ $customer->order->count() }}
                        </div>
                        <p class="text-muted">Total Orders</p>

                        @if($customer->order->count() > 0)
                            @php
    $completedOrders = $customer->order->where('status', 'completed')->count();
    $pendingOrders = $customer->order->where('status', 'pending')->count();
                            @endphp
                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="border-end">
                                        <div class="fw-bold text-success">{{ $completedOrders }}</div>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-warning">{{ $pendingOrders }}</div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-4 text-white">
                            <i class="fas fa-cogs me-2"></i>Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">

                            @if (App\Helpers\PermissionHelper::hasPermission('create', 'transaction'))
                            <a href="{{ route('transaction.create', ['customer_id' => $customer->customer_id]) }}"
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Buat Order Baru
                            </a>
                            @endif

                            @if (App\Helpers\PermissionHelper::hasPermission('read', 'transaction'))
                            <a href="{{ route('transaction.index', ['customer' => $customer->customer_id]) }}"
                               class="btn btn-info btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Lihat Semua Orders
                            </a>
                            @endif

                            <div class="btn-group" role="group">
                            @if (App\Helpers\PermissionHelper::hasPermission('update'))
                            <a href="{{ route('customer.edit', $customer->customer_id) }}"
                               class="btn btn-outline-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif

                            @if (App\Helpers\PermissionHelper::hasPermission('delete'))
                            <form action="{{ route('customer.destroy', $customer->customer_id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Terbaru -->
                @if($customer->order->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-4 text-white">
                                <i class="fas fa-clock me-2"></i>Order Terbaru
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($customer->order->take(3) as $order)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order #{{ $order->order_id }}</h6>
                                        <small class="text-muted">{{ $order->created_at->format('d/m') }}</small>
                                    </div>
                                    <p class="mb-1 small">
                                        Status:
                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </p>
                                    <small class="text-muted">
                                        Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </small>
                                    <div class="mt-2">
                                        @if (App\Helpers\PermissionHelper::hasPermission('read', 'transaction'))
                                        <a href="{{ route('transaction.show', $order->order_id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            <a href="{{ route('customer.index') }}" class="btn btn-light col-12">Back</a>
            </div>
        </div>

    <style>
    .info-item {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 0.5rem;
    }
    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    </style>
@endsection
